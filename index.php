<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 5/3/2016
 * Time: 11:44 PM
 */

include 'includes/includes.php';

header("Access-Control-Allow-Origin: *");

$debug = new Debugger("index.php");
$debug->log(" ------Starting Vigilance Api in Debugger Mode -------", 1);

$main = new MainController();
print $main->execute();