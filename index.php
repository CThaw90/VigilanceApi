<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 5/3/2016
 * Time: 11:44 PM
 */

include 'includes/includes.php';

$main = new MainController();
print $main->execute();
#echo json_encode($_SERVER);
// $json = '{"name": "Chris", "age": 25}';
// var_dump (json_decode($json));
// var_dump (json_decode($json, true));
// foreach (json_decode($json, true) as $key => $value) {
// 	echo $key . "=" . $value . "<br>";
// }
#print preg_match("/^\/vigilance\/api\/organization\/\d{1,}.*/", $_SERVER['REQUEST_URI']);