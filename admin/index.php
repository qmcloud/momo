<?php
/**
 * 后台入口文件
 */

$upw =empty($_GET['upw'])?"":'&upw='.$_GET['upw'];
 
header("Location: ../index.php?g=admin&m=public&a=login".$upw );

