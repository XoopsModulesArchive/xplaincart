<?php
//require_once dirname(__DIR__, 2) . '/library/config.php';
//require_once dirname(__DIR__) . '/library/functions.php';
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';	//include database_class inside
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class process_product_class
{
    public function process_product()
    {
        $init = new init_class();

        $admin_func = new admin_func_class();

        $admin_func->checkUser();

        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'addProduct':
                $this->addProduct();
                break;
            case 'modifyProduct':
                $this->modifyProduct();
                break;
            case 'deleteProduct':
                $this->deleteProduct();
                break;
            case 'deleteImage':
                $this->deleteImage();
                break;
            default:
                // if action is not defined or unknown
                // move to main product page
                header('Location: index.php');
        }
    }

    public function addProduct()
    {
        $db = new database_class();

        $catId = $_POST['cboCategory'];

        $name = $_POST['txtName'];

        $title = $_POST['txtTitle'];

        $description = $_POST['mtxDescription'];

        $price = str_replace(',', '', (float)$_POST['txtPrice']);

        $qty = (int)$_POST['txtQty'];

        $images = $this->uploadProductImage('fleImage', SRV_ROOT . 'images/product/');

        $mainImage = $images['image'];

        $thumbnail = $images['thumbnail'];

        $sql = 'INSERT INTO ' . PREFIX . "product (cat_id, pd_name,pd_title, pd_description, pd_price, pd_qty, pd_image, pd_thumbnail, pd_date)
		          VALUES ('$catId', '$name','$title', '$description', $price, $qty, '$mainImage', '$thumbnail', NOW())";

        $db->dbQuery($sql);

        header("Location: index.php?catId=$catId");
    }

    /*
        Upload an image and return the uploaded image name
    */

    public function uploadProductImage($inputName, $uploadDir)
    {
        $admin_func = new admin_func_class();

        $image = $_FILES[$inputName];

        $imagePath = '';

        $thumbnailPath = '';

        // if a file is given

        if ('' != trim($image['tmp_name'])) {
            $ext = mb_substr(mb_strrchr($image['name'], '.'), 1); //$extensions[$image['type']];

            // generate a random new file name to avoid name conflict

            $imagePath = md5(mt_rand() * time()) . ".$ext";

            [$width, $height, $type, $attr] = getimagesize($image['tmp_name']);

            // make sure the image width does not exceed the

            // maximum allowed width

            if (LIMIT_PRODUCT_WIDTH && $width > MAX_PRODUCT_IMAGE_WIDTH) {
                $result = $admin_func->createThumbnail($image['tmp_name'], $uploadDir . $imagePath, MAX_PRODUCT_IMAGE_WIDTH);

                $imagePath = $result;
            } else {
                $result = move_uploaded_file($image['tmp_name'], $uploadDir . $imagePath);
            }

            if ($result) {
                // create thumbnail

                $thumbnailPath = md5(mt_rand() * time()) . ".$ext";

                $result = $admin_func->createThumbnail($uploadDir . $imagePath, $uploadDir . $thumbnailPath, THUMBNAIL_WIDTH);

                // create thumbnail failed, delete the image

                if (!$result) {
                    unlink($uploadDir . $imagePath);

                    $imagePath = $thumbnailPath = '';
                } else {
                    $thumbnailPath = $result;
                }
            } else {
                // the product cannot be upload / resized

                $imagePath = $thumbnailPath = '';
            }
        }

        return ['image' => $imagePath, 'thumbnail' => $thumbnailPath];
    }

    /*
        Modify a product
    */

    public function modifyProduct()
    {
        $db = new database_class();

        $productId = (int)$_GET['productId'];

        $catId = $_POST['cboCategory'];

        $name = $_POST['txtName'];

        $title = $_POST['txtTitle'];

        $description = $_POST['mtxDescription'];

        $price = str_replace(',', '', $_POST['txtPrice']);

        $qty = $_POST['txtQty'];

        $images = $this->uploadProductImage('fleImage', SRV_ROOT . 'images/product/');

        $mainImage = $images['image'];

        $thumbnail = $images['thumbnail'];

        // if uploading a new image

        // remove old image

        if ('' != $mainImage) {
            $this->_deleteImage($productId);

            $mainImage = "'$mainImage'";

            $thumbnail = "'$thumbnail'";
        } else {
            // if we're not updating the image

            // make sure the old path remain the same

            // in the database

            $mainImage = 'pd_image';

            $thumbnail = 'pd_thumbnail';
        }

        $sql = 'UPDATE ' . PREFIX . "product 
		          SET cat_id = $catId, pd_name = '$name',pd_title='$title', pd_description = '$description', pd_price = $price, 
				      pd_qty = $qty, pd_image = $mainImage, pd_thumbnail = $thumbnail
				  WHERE pd_id = $productId";

        $db->dbQuery($sql);

        header('Location: index.php');
    }

    /*
        Remove a product
    */

    public function deleteProduct()
    {
        $db = new database_class();

        if (isset($_GET['productId']) && (int)$_GET['productId'] > 0) {
            $productId = (int)$_GET['productId'];
        } else {
            header('Location: index.php');
        }

        // remove any references to this product from

        // ".PREFIX."order_item and ".PREFIX."cart

        $sql = 'DELETE FROM ' . PREFIX . "order_item
		        WHERE pd_id = $productId";

        $db->dbQuery($sql);

        $sql = 'DELETE FROM ' . PREFIX . "cart
		        WHERE pd_id = $productId";

        $db->dbQuery($sql);

        // get the image name and thumbnail

        $sql = 'SELECT pd_image, pd_thumbnail
		        FROM ' . PREFIX . "product
				WHERE pd_id = $productId";

        $result = $db->dbQuery($sql);

        $row = $db->dbFetchAssoc($result);

        // remove the product image and thumbnail

        if ($row['pd_image']) {
            unlink(SRV_ROOT . 'images/product/' . $row['pd_image']);

            unlink(SRV_ROOT . 'images/product/' . $row['pd_thumbnail']);
        }

        // remove the product from database;

        $sql = 'DELETE FROM ' . PREFIX . "product 
		        WHERE pd_id = $productId";

        $db->dbQuery($sql);

        header('Location: index.php?catId=' . $_GET['catId']);
    }

    /*
        Remove a product image
    */

    public function deleteImage()
    {
        $db = new database_class();

        if (isset($_GET['productId']) && (int)$_GET['productId'] > 0) {
            $productId = (int)$_GET['productId'];
        } else {
            header('Location: index.php');
        }

        $deleted = $this->_deleteImage($productId);

        // update the image and thumbnail name in the database

        $sql = 'UPDATE ' . PREFIX . "product
				SET pd_image = '', pd_thumbnail = ''
				WHERE pd_id = $productId";

        $db->dbQuery($sql);

        header("Location: index.php?view=modify&productId=$productId");
    }

    public function _deleteImage($productId)
    {
        $db = new database_class();

        // we will return the status

        // whether the image deleted successfully

        $deleted = false;

        $sql = 'SELECT pd_image, pd_thumbnail 
		        FROM ' . PREFIX . "product
				WHERE pd_id = $productId";

        $result = $db->dbQuery($sql) || die('Cannot delete product image. ' . $GLOBALS['xoopsDB']->error());

        if ($db->dbNumRows($result)) {
            $row = $db->dbFetchAssoc($result);

            extract($row);

            if ($pd_image && $pd_thumbnail) {
                // remove the image file

                $deleted = @unlink(SRV_ROOT . "images/product/$pd_image");

                $deleted = @unlink(SRV_ROOT . "images/product/$pd_thumbnail");
            }
        }

        return $deleted;
    }
}
$process_product = new process_product_class();
$process_product->process_product();
