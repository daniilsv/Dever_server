<?php

	class order extends Controller {
		public function actionIndex() {

			echo "Shit happens";
		}

		public function actionSetState($id = null, $type = null, $value = null) {
			if ($id == null)
				return false;
			$order = DB::getInstance()->getRow("orders", "id=" . $id);
			setJson();
			if ($value == null)
				return false;
			switch ($type) {
				case "P":
					DB::getInstance()->update("couriers", "id=" . $order["courier_id"], ["position" => $value]);
					DB::getInstance()->update("orders", "id=" . $id, ["position" => $value]);
					DB::getInstance()->insert("orders_log", ["order_id" => $id, "type" => $type, "value" => $value]);
					break;
				default:
					DB::getInstance()->insert("orders_log", ["order_id" => $id, "type" => $type, "value" => $value]);
					break;
			}
			return true;
		}

		public function actionGet($id = null) {
			if ($id == null)
				return false;
			$order = DB::getInstance()->getRow("orders", "id=" . $id);
			$order['position'] = json_decode($order['position'], true);
			$order['state'] = json_decode($order['state'], true);
			setJson();
			return $order;
		}

		public function actionView($id = null) {
			if ($id == null)
				return;
			$order = DB::getInstance()->getRow("orders", "id=" . $id);
			if ($order == null)
				return;
			$order['position'] = json_decode($order['position'], true);

			$courier = DB::getInstance()->getRow("couriers", "id=" . $order['courier_id']);

			$log = DB::getInstance()->getRows("orders_log", "order_id=" . $id, "*", "time ASC");

			$data = [];
			$labels = [];
			$temperature = $humidity = $angle = $overload = [];
			foreach ($log as $point) {
				$labels[] = strtotime($point['time']);
				switch ($point["type"]) {
					case "T":
						$temperature[] = $point["value"];
						$humidity[] = end($humidity);
						$angle[] = 0;
						$overload[] = 0;
						break;
					case "H":
						$temperature[] = end($temperature);
						$humidity[] = $point["value"];
						$angle[] = 0;
						$overload[] = 0;
						break;
					case "A":
						$temperature[] = end($temperature);
						$humidity[] = end($humidity);
						$angle[] = $point["value"];
						$overload[] = 0;
						break;
					case "O":
						$temperature[] = end($temperature);
						$humidity[] = end($humidity);
						$angle[] = 0;
						$overload[] = $point["value"];
						break;
				}
			}
			$this->render("order", [
				"order" => $order,
				"courier" => $courier,
				"l" => $labels,
				"t" => $temperature,
				"h" => $humidity,
				"a" => $angle,
				"o" => $overload
			]);
		}

	}

