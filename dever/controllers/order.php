<?php

	class order extends Controller {
		public function actionIndex() {

			echo "Shit happens";
		}

		public function actionSetState($id = null, $data = null) {
			if ($id == null)
				return false;
			$order = DB::getInstance()->getRow("orders", "id=" . $id);
			setJson();
			if ($data == null)
				return false;
			$data = urldecode($data);
			$data = str_replace("'", '"', $data);
			$data = json_decode($data, true);
			$upd = [];
			if (ake("position", $data)) {
				$upd["position"] = json_encode($data["position"]);
				DB::getInstance()->update("couriers", "id=" . $order["courier_id"], $upd);
			}
			if (ake("state", $data))
				$upd["state"] = json_encode($data["state"]);
			if ($upd == [])
				return false;
			DB::getInstance()->update("orders", "id=" . $id, $upd);
			DB::getInstance()->insert("orders_log", ["order_id" => $id, "state" => $upd["state"]]);
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
			$courier = DB::getInstance()->getRow("couriers", "id=" . $order['courier_id']);
			$order['position'] = json_decode($order['position'], true);
			$order['state'] = json_decode($order['state'], true);

			$this->render("order", [
				"order" => $order,
				"courier" => $courier
			]);
		}

	}

