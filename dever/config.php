<?php

	class Config {

		private static $data = [
			"db_host" => "localhost",
			"db_user" => "webitis",
			"db_pass" => "web123itis",
			"db_base" => "dever",
			"db_prefix" => "",
		];

		public static function get($name) {
			if (!ake($name, self::$data)) {
				return "";
			}

			return self::$data[$name];
		}

		public static function set($name, $value) {
			self::$data[$name] = $value;
		}

	}
