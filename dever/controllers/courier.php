<?php

	class courier extends Controller {
		public function actionIndex() {

			echo "Shit happens";
		}

		public function actionGet($id) {
			$courier = DB::getInstance()->getRow("couriers", "id=" . $id);
			setJson();
			return $courier;
		}
	}