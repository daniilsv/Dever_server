<?php

	function d($o, $halt = false) {
		echo "<pre>" . print_r($o, true) . "</pre>";
		if ($halt) {
			exit;
		}
	}

	function df($o, $halt = false) {
		$fp = fopen("./dump.txt", 'a');
		fwrite($fp, print_r($o, true) . PHP_EOL);
		fclose($fp);
		if ($halt) {
			exit;
		}
	}

	function f() {
		flush();
		ob_flush();
	}

	function redirect($location) {
		header("Location: " . $location);
		exit;
	}

	function redirectJs($location) {
		echo "<script>document.location.href='$location'</script>";
		exit;
	}

	function ake($key, $array) {
		return array_key_exists($key, $array);
	}

	function setPlain() {
		header("Content-Type: text/plain; charset=UTF-8");
	}

	function setHtml() {
		header("Content-Type: text/html; charset=UTF-8");
	}

	function setJson() {
		define("IS_JSON", 1);
		header('Content-Type: application/json; charset=UTF-8');
		header("Access-Control-Allow-Origin: *");
	}

	function getNumberWord($number, $suffix) {
		$keys = [2, 0, 1, 1, 1, 2];
		$mod = $number % 100;
		$suffix_key = ($mod > 7 && $mod < 20) ? 2 : $keys[min($mod % 10, 5)];
		return $suffix[$suffix_key];
	}