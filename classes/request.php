<?php

	class Request {
		private $params = [];
		public $method = "";

		public function __construct() {
			$this->_parseParams();
		}

		/**
		 * @brief Lookup request params
		 * @param string $name Name of the argument to lookup
		 * @param mixed  $default Default value to return if argument is missing
		 * @returns The value from the GET/POST/PUT/DELETE value, or $default if not set
		 */
		public function get($name, $default = null) {
			if (isset($this->params[$name])) {
				return $this->params[$name];
			} else {
				return $default;
			}
		}

		public function has($name) {
			return isset($this->params[$name]);
		}

		private function _parseParams() {
			$this->method = $_SERVER['REQUEST_METHOD'];
			$override = isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : (isset($_GET['method']) ? $_GET['method'] : '');

			if ($this->method == "PUT" || $this->method == "DELETE") {
				parse_str(file_get_contents('php://input'), $this->params);
				$GLOBALS["_{$this->method}"] = $this->params;
				$_REQUEST = $this->params + $_REQUEST;
			} elseif ($this->method == "GET" || $this->method == "POST") {
				$this->params = $_REQUEST;
			}

			if ($this->method == 'POST' && strtoupper($override) == 'PUT') {
				$this->method = 'PUT';
			} elseif ($this->method == 'POST' && strtoupper($override) == 'DELETE') {
				$this->method = 'DELETE';
			} elseif ($this->method == 'POST' && strtoupper($override) == 'PATCH') {
				$this->method = 'PATCH';
			}
		}
	}
