<?php
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class process_category_class
{
    public function process_category()
    {
        $init = new init_class();

        $shopConfig = $init->getShopConfig();

        $admin_func = new admin_func_class();

        $db = new database_class();

        $admin_func->checkUser();

        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'add':
                $this->addCategory();
                break;
            case 'modify':
                $this->modifyCategory();
                break;
            case 'delete':
                $this->deleteCategory();
                break;
            case 'deleteImage':
                $this->deleteImage();
                break;
            default:
                // if action is not defined or unknown
                // move to main category page
                header('Cache-Control: no-cache, must-revalidate');
                header('Location: index.php');
        }
    }

    /*
        Add a category
    */

    public function addCategory()
    {
        $db = new database_class();

        $name = $_POST['txtName'];

        $title = $_POST['Title'];

        $description = $_POST['mtxDescription'];

        $image = $_FILES['fleImage'];

        $parentId = $_POST['hidParentId'];

        $catImage = $this->uploadImage('fleImage', ABS_CAT_DIR);

        $sql = 'INSERT INTO ' . PREFIX . "category (cat_parent_id, cat_name,cat_title, cat_description, cat_image) 
	              VALUES ($parentId, '$name','$title', '$description', '$catImage')";

        $db->dbQuery($sql) || die('Cannot add category' . $GLOBALS['xoopsDB']->error());

        header('Cache-Control: no-cache, must-revalidate');

        header('Location: index.php?catId=' . $parentId);
    }

    /*
        Upload an image and return the uploaded image name
    */

    public function uploadImage($inputName, $uploadDir)
    {
        $admin_func = new admin_func_class();

        $image = $_FILES[$inputName];

        $imagePath = '';

        // if a file is given

        if ('' != trim($image['tmp_name'])) {
            // get the image extension

            $ext = mb_substr(mb_strrchr($image['name'], '.'), 1);

            // generate a random new file name to avoid name conflict

            $imagePath = md5(mt_rand() * time()) . ".$ext";

            // check the image width. if it exceed the maximum

            // width we must resize it

            $size = getimagesize($image['tmp_name']);

            if ($size[0] > MAX_CATEGORY_IMAGE_WIDTH) {
                $imagePath = $admin_func->createThumbnail($image['tmp_name'], $uploadDir . $imagePath, MAX_CATEGORY_IMAGE_WIDTH);
            } else {
                // move the image to category image directory

                // if fail set $imagePath to empty string

                if (!move_uploaded_file($image['tmp_name'], $uploadDir . $imagePath)) {
                    $imagePath = '';
                }
            }
        }

        return $imagePath;
    }

    /*
        Modify a category
    */

    public function modifyCategory()
    {
        $db = new database_class();

        $catId = (int)$_GET['catId'];

        $name = $_POST['txtName'];

        $title = $_POST['Title'];

        $description = $_POST['mtxDescription'];

        $image = $_FILES['fleImage'];

        $catImage = $this->uploadImage('fleImage', ABS_CAT_DIR);

        // if uploading a new image

        // remove old image

        if ('' != $catImage) {
            $this->_deleteImage($catId);

            $catImage = "'$catImage'";
        } else {
            // leave the category image as it was

            $catImage = 'cat_image';
        }

        $sql = 'UPDATE ' . PREFIX . "category 
	               SET cat_name = '$name',cat_title='$title', cat_description = '$description', cat_image = $catImage
	               WHERE cat_id = $catId";

        $db->dbQuery($sql) || die('Cannot update category. ' . $GLOBALS['xoopsDB']->error());

        header('Cache-Control: no-cache, must-revalidate');

        header('Location: index.php');
    }

    /*
        Remove a category
    */

    public function deleteCategory()
    {
        $db = new database_class();

        if (isset($_GET['catId']) && (int)$_GET['catId'] > 0) {
            $catId = (int)$_GET['catId'];
        } else {
            header('Cache-Control: no-cache, must-revalidate');

            header('Location: index.php');
        }

        // find all the children categories

        $children = $this->getChildren($catId);

        // make an array containing this category and all it's children

        $categories = array_merge($children, [$catId]);

        $numCategory = count($categories);

        // remove all product image & thumbnail

        // if the product's category is in  $categories

        $sql = 'SELECT pd_id, pd_image, pd_thumbnail
		        FROM ' . PREFIX . 'product
				WHERE cat_id IN (' . implode(',', $categories) . ')';

        $result = $db->dbQuery($sql);

        while (false !== ($row = $db->dbFetchAssoc($result))) {
            @unlink(ABS_PD_DIR . $row['pd_image']);

            @unlink(ABS_PD_DIR . $row['pd_thumbnail']);
        }

        // delete the products

        $sql = 'DELETE FROM ' . PREFIX . 'product
				WHERE cat_id IN (' . implode(',', $categories) . ')';

        $db->dbQuery($sql);

        // then remove the categories image

        $this->_deleteImage($categories);

        // finally remove the category from database;

        $sql = 'DELETE FROM ' . PREFIX . 'category 
	            WHERE cat_id IN (' . implode(',', $categories) . ')';

        $db->dbQuery($sql);

        header('Cache-Control: no-cache, must-revalidate');

        header('Location: index.php');
    }

    /*
        Recursively find all children of $catId
    */

    public function getChildren($catId)
    {
        $db = new database_class();

        $sql = 'SELECT cat_id ' .
               'FROM ' . PREFIX . 'category ' .
               "WHERE cat_parent_id = $catId ";

        $result = $db->dbQuery($sql);

        $cat = [];

        if ($db->dbNumRows($result) > 0) {
            while (false !== ($row = $db->dbFetchRow($result))) {
                $cat[] = $row[0];

                // call this function again to find the children

                $cat = array_merge($cat, $this->getChildren($row[0]));
            }
        }

        return $cat;
    }

    /*
        Remove a category image
    */

    public function deleteImage()
    {
        $db = new database_class();

        if (isset($_GET['catId']) && (int)$_GET['catId'] > 0) {
            $catId = (int)$_GET['catId'];
        } else {
            header('Cache-Control: no-cache, must-revalidate');

            header('Location: index.php');
        }

        $this->_deleteImage($catId);

        // update the image name in the database

        $sql = 'UPDATE ' . PREFIX . "category
				SET cat_image = ''
				WHERE cat_id = $catId";

        $db->dbQuery($sql);

        header('Cache-Control: no-cache, must-revalidate');

        header("Location: index.php?view=modify&catId=$catId");
    }

    /*
        Delete a category image where category = $catId
    */

    public function _deleteImage($catId)
    {
        $db = new database_class();

        // we will return the status

        // whether the image deleted successfully

        $deleted = false;

        // get the image(s)

        $sql = 'SELECT cat_image 
	            FROM ' . PREFIX . 'category
	            WHERE cat_id ';

        if (is_array($catId)) {
            $sql .= ' IN (' . implode(',', $catId) . ')';
        } else {
            $sql .= " = $catId";
        }

        $result = $db->dbQuery($sql);

        if ($db->dbNumRows($result)) {
            while (false !== ($row = $db->dbFetchAssoc($result))) {
                // delete the image file

                $deleted = @unlink(ABS_CAT_DIR . $row['cat_image']);
            }
        }

        return $deleted;
    }
}
$process_category = new process_category_class();
$process_category->process_category();
