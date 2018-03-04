<?php

	class User {

		public $id;
		public $login;
		public $last_request;

		/** @var \User */
		public static $user = false;

		private function __construct($data) {
			foreach ($data as $key => $value) {
				$this->$key = $value;
			}
		}

		public static function setLocalUser($user) {
			self::$user = $user;
		}

		public static function getUserById($id) {
			$user = DB::getInstance()->getRow("users", "`id`={$id}");
			if (!$user)
				return false;
			return new User($user);
		}

		public static function getUserByLogin($login) {
			$user = DB::getInstance()->getRow("users", "`login`='{$login}'");
			if (!$user)
				return false;
			return new User($user);
		}

		public static function getUserByToken($token) {
			$user = DB::getInstance()->getRow("users", "`token`='{$token}'");
			if (!$user)
				return false;
			return new User($user);
		}

		public function setLastRequest($request) {
			$this->last_request = $request;
			DB::getInstance()->update("users", "`id`={$this->id}", ["last_request" => $request]);
		}

	}
