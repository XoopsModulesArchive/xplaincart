<?php
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/cat_func_class.php';
$category_arr = [];
$cat_func = new cat_func_class();
// get all categories
$categories = $cat_func->fetchCategories();
// format the categories for display
$categories = $cat_func->formatCategories($categories, $catId);
foreach ($categories as $category) {
    extract($category);

    // now we have $cat_id, $cat_parent_id, $cat_name

    $level = (0 == $cat_parent_id) ? 1 : 2;

    $url = $_SERVER['PHP_SELF'] . "?c=$cat_id";

    // for second level categories we print extra spaces to give

    // indentation look

    if (2 == $level) {
        $cat_name = '&nbsp; &nbsp; &raquo;&nbsp;' . $cat_name;
    }

    // assign id="current" for the currently selected category

    // this will highlight the category name

    $listId = '';

    if ($cat_id == $catId) {
        $listId = ' id="current"';
    }

    $category_arr[] = ['listId' => $listId, 'url' => $url, 'cat_name' => $cat_name];
}
$xoopsTpl->assign('self', $_SERVER['PHP_SELF']);
$xoopsTpl->assign('catdata', $category_arr);
