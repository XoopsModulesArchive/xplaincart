<?php
//require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database_class.php';
/*********************************************************
*                 CATEGORY FUNCTIONS
*********************************************************/

/*
    Return the current category list which only shows
    the currently selected category and it's children.
    This function is made so it can also handle deep
    category levels ( more than two levels )
*/
class cat_func_class
{
    //var $navCat;

    //var $cat;

    public function formatCategories($categories, $parentId)
    {
        // $navCat stores all children categories

        // of $parentId

        $navCat = [];

        // expand only the categories with the same parent id

        // all other remain compact

        $ids = [];

        foreach ($categories as $category) {
            if ($category['cat_parent_id'] == $parentId) {
                $navCat[] = $category;
            }

            // save the ids for later use

            $ids[$category['cat_id']] = $category;
        }

        $tempParentId = $parentId;

        // keep looping until we found the

        // category where the parent id is 0

        while (0 != $tempParentId) {
            $parent = [$ids[$tempParentId]];

            $currentId = $parent[0]['cat_id'];

            // get all categories on the same level as the parent

            $tempParentId = $ids[$tempParentId]['cat_parent_id'];

            foreach ($categories as $category) {
                // found one category on the same level as parent

                // put in $parent if it's not already in it

                if ($category['cat_parent_id'] == $tempParentId && !in_array($category, $parent, true)) {
                    $parent[] = $category;
                }
            }

            // sort the category alphabetically

            array_multisort($parent);

            // merge parent and child

            $n = count($parent);

            $navCat2 = [];

            for ($i = 0; $i < $n; $i++) {
                $navCat2[] = $parent[$i];

                if ($parent[$i]['cat_id'] == $currentId) {
                    $navCat2 = array_merge($navCat2, $navCat);
                }
            }

            $navCat = $navCat2;
        }

        return $navCat;
    }

    public function getCategoryList()
    {
        $db = new database_class();

        $sql = 'SELECT cat_id, cat_name,cat_title,cat_description, cat_image
		        FROM ' . PREFIX . 'category
				WHERE cat_parent_id = 0
				ORDER BY cat_name';

        $result = $db->dbQuery($sql);

        $cat = [];

        while (false !== ($row = $db->dbFetchAssoc($result))) {
            extract($row);

            if ($cat_image) {
                $cat_image = CATEGORY_IMAGE_DIR . $cat_image;
            } else {
                $cat_image = BASE_IMAGE_DIR . 'no-image-small.png';
            }

            $cat[] = ['url' => $_SERVER['PHP_SELF'] . '?c=' . $cat_id,
                           'image' => $cat_image,
                           'name' => $cat_name,
                            'title' => $cat_title,
                            'description' => $cat_description, ];
        }

        //$this->cat=$cat;

        return $cat;
    }

    public function getChildCategories($categories, $id, $recursive = true)
    {
        if (null === $categories) {
            $categories = $this->fetchCategories();
        }

        $n = count($categories);

        $child = [];

        for ($i = 0; $i < $n; $i++) {
            $catId = $categories[$i]['cat_id'];

            $parentId = $categories[$i]['cat_parent_id'];

            if ($parentId == $id) {
                $child[] = $catId;

                if ($recursive) {
                    $child = array_merge($child, $this->getChildCategories($categories, $catId));
                }
            }
        }

        return $child;
    }

    public function fetchCategories()
    {
        $db = new database_class();

        $sql = 'SELECT cat_id, cat_parent_id, cat_name,cat_title, cat_image, cat_description
		        FROM ' . PREFIX . 'category
				ORDER BY cat_id, cat_parent_id ';

        $result = $db->dbQuery($sql);

        $cat = [];

        while (false !== ($row = $db->dbFetchAssoc($result))) {
            $cat[] = $row;
        }

        return $cat;
    }
}
