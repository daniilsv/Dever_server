<?php

	class Controller {
		protected $request;

		/**
		 * @param $controller
		 * @return \Controller
		 */
		public static function getController($controller) {
			if (!file_exists("./controllers/{$controller}.php")) {
				$controller = "main";
			}
			include "./controllers/{$controller}.php";
			return new $controller();
		}

		public function __construct() {
			$this->request = new Request();
		}

		/**
		 * @param $segments
		 */
		public final function runAction($segments) {
			$action = "index";
			$params = [];
			if (get_class($this) == "main") {
				if (isset($segments[0]) && strlen($segments[0]) > 0) $action = $segments[0];
				if (count($segments) > 1) $params = array_slice($segments, 1);
			} else {
				if (isset($segments[1]) && strlen($segments[1]) > 0) $action = $segments[1];
				if (count($segments) > 2) $params = array_slice($segments, 2);
			}
			$method = "action" . str_replace(" ", "", ucwords(str_replace("_", " ", $action)));
			if (!method_exists($this, $method)) {
				$this->_methodNotFound($action, $params);
			} else {
				$result = call_user_func_array([$this, $method], $params);
				if (defined("IS_JSON")) {
					echo json_encode($result);
				}
			}
		}

		private function _methodNotFound($action, $params) {
			echo "<h2>Action '$action' of controller '" . get_class($this) . "' not found</h2>";
			echo "<h3>Params:</h3>";
			d($params);
			echo "<h3>Request:</h3>";
			d($this->request);
		}

		public function render($template, $data) {
			if (!file_exists("./views/$template.tpl.php"))
				return;

			extract($data);
			include "./views/$template.tpl.php";
		}
	}