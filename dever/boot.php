<?php

	define('DEBUG', 1);
	error_reporting(E_ALL);

	include './config.php';
	include '../classes/db.php';
	include '../classes/functions.php';
	include '../classes/controller.php';
	include '../classes/request.php';

	$db = new DB();
