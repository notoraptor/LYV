<?php
require_once('yvl.php');
session_start();
if(isset($_SESSION['drawer'])) {
	$_SESSION['drawer']->drawPng();
};
?>