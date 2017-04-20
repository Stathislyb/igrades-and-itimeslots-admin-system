<?php
//INFO: Do change only ID
//Keep correct username+password of mdasyg
session_start();
$_SESSION['id']="411";
$_SESSION["username"]="fakeusername";
$_SESSION["aem"]="888";
//----
$_SESSION["type"]="0";
$_SESSION["active"]="1";
$_SESSION["password"]="smt";
$_SESSION["fname"]="FAKE VIRTUAL";
$_SESSION["lname"]="USER";

var_dump($_SESSION);
?>
