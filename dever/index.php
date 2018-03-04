<?php
	include "./boot.php";

	$uri = substr($_SERVER['REQUEST_URI'], 1);

	if (($pos_que = mb_strpos($uri, '?')) !== false) {
		$query_data = [];
		$query_str = mb_substr($uri, $pos_que + 1);
		$uri = mb_substr($uri, 0, $pos_que);
		parse_str($query_str, $query_data);
		$_REQUEST = array_merge($query_data, $_REQUEST);
	}

	$params = [];
	$segments = explode('/', $uri);
	$param_controller = "";
	if (isset($segments[0])) {
		$param_controller = $segments[0];
	}

	$controller = Controller::getController($param_controller);
	$controller->runAction($segments);

