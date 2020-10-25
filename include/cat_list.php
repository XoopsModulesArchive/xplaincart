<?php
require_once __DIR__ . '/library/define.php';
require_once __DIR__ . '/library/cat_func_class.php';
$cat_func = new cat_func_class();
$categoryList = $cat_func->getCategoryList();
$categoriesPerRow = 3;
$cols = $categoriesPerRow;
$rows = 4;
$numCategory = count($categoryList);
$columnWidth = (int)(100 / $categoriesPerRow);
$data = [];
if ($numCategory > 0) {
    $i = 0;

    for ($i; $i < $numCategory; $i++) {
        // we have $url, $image, $name,$title,$description

        extract($categoryList[$i]);

        //$data[]="<a href=\"$url\"><img src=\"$image\" border=\"0\"><br>$name</a>";

        $data[] = ['url' => $url, 'image' => $image, 'name' => $name, 'title' => $title, 'description' => $description];
    }
} else {
    $data = 'No categories yet';
}
$xoopsTpl->assign('data', $data);
$xoopsTpl->assign('columnWidth', $columnWidth);
//$xoopsTpl->assign('cols',$cols);
//$xoopsTpl->assign('rows',$rows);
$xoopsTpl->assign('cols', 1);	//force to 1 column
$xoopsTpl->assign('rows', 10);	//force to 10 rows
