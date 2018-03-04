<?php

	class Telegram {
		private $token;
		private $input;
		private $input_chat_id;
		private $reject_next = false;

		const BASE_API_URL = 'https://api.telegram.org/bot';
		private static $instance = null;

		public static final function getInstance() {
			if (self::$instance == null)
				self::$instance = new Telegram();
			return self::$instance;
		}

		/**
		 * Запускает сеанс бота
		 */
		private function __construct() {
			$this->token = Config::get("tg_token");
		}

		/**
		 * Парсит сообщение из php://input и запускает обработчики
		 * @param $input
		 * @return \Telegram
		 */
		public function parseInput($input) {
			$this->input = json_decode($input, true);
			$this->input = $this->input["message"];
			if (!array_key_exists("text", $this->input)) {
				$this->input["text"] = "";
			}

			$this->input_chat_id = $this->input["chat"]["id"];
			return $this;
		}

		/**
		 * Устанавливает обработчик $callback на фразу $phrase в начале сообщения
		 * @param String   $phrase Параметры для контроллера
		 * @param callable $callback Контекст (если не указан, определяется автоматически)
		 * @param bool     $equal
		 * @return \Telegram
		 */
		public function check($phrase, $callback, $equal = false) {
			if ($this->reject_next) {
				return $this;
			}

			if (strpos($this->input["text"], $phrase) === 0 &&
				(!$equal || substr($this->input["text"], strlen($phrase), 1) != "_")) {
				call_user_func_array($callback, [$this, $this->input_chat_id, $this->input, substr($this->input["text"], strlen($phrase))]);
				$this->rejectNext();
			}
			return $this;
		}

		/**
		 * Запускает обработчик $callback
		 * @param callable $callback Обработчик
		 * @return \Telegram
		 */
		public function call($callback) {
			if ($this->reject_next) {
				return $this;
			}

			call_user_func_array($callback, [$this, $this->input_chat_id, $this->input]);
			return $this;
		}

		public function rejectNext() {
			$this->reject_next = true;
		}

		public function allowNext() {
			$this->reject_next = false;
		}

		public function isRejectedNext() {
			return $this->reject_next;
		}

		/**
		 * Устанавливает Web Hook на текущий адрес
		 * @return mixed|null
		 */
		public function setWebHook() {
			$webhook_addr = "https://" . $_SERVER["SERVER_NAME"];
			echo $webhook_addr;
			return $this->sendPost('setWebHook', ['url' => $webhook_addr]);
		}

		/**
		 * Удаляет Web Hook для бота
		 * @return mixed|null
		 */
		public function deleteWebhook() {
			return $this->sendPost('deleteWebhook');
		}

		/**
		 * Получает обновления бота
		 * @return mixed
		 */
		public function getUpdates() {
			$data = file_get_contents($this->buildUrl('getUpdates'));
			return json_decode($data, true);
		}

		/**
		 * Отправляет сообщение $message в чат $charId
		 * @param int    $chatId - ID чата, в который отправляем сообщение
		 * @param String $message - текст сообщения
		 * @param array  $params - дом.параметры (опционально)
		 * @return mixed
		 */
		public function sendMessage($chatId, $message, $params = []) {
			$params['chat_id'] = $chatId;
			$params['text'] = $message;
			$params['parse_mode'] = "HTML";

			$url = $this->buildUrl('sendMessage') . '?' . http_build_query($params);

			$data = file_get_contents($url);
			return json_decode($data, true);
		}

		/**
		 * Отправляет сообщение $message в чат $charId с клавиатурой $keyboard
		 * @param int              $chatId - ID чата, в который отправляем сообщение
		 * @param String           $message - текст сообщения
		 * @param TelegramKeyboard $keyboard - клавиатура Telegram
		 * @param                  $with_cancel
		 * @return mixed
		 */
		public function sendKeyboard($chatId, $message, $keyboard, $with_cancel = true) {
			$params['chat_id'] = $chatId;
			$params['text'] = strip_tags($message);
			$params['reply_markup'] = json_encode([
				                                      "keyboard" => $keyboard->getData($with_cancel),
				                                      "resize_keyboard" => true,
				                                      "one_time_keyboard" => true,
			                                      ]);

			return $this->sendPost("sendMessage", $params);
		}

		/**
		 * Отправляет сообщение $message в чат $charId
		 * @param int    $chatId - ID чата, в который отправляем сообщение
		 * @param String $imgUrl - ссылка на картинку
		 * @param String $caption - подпись картинки (опционально)
		 * @return mixed
		 */
		public function sendImage($chatId, $imgUrl, $caption = "") {
			$params['chat_id'] = $chatId;
			$params['photo'] = $imgUrl;
			$params['caption'] = $caption;
			return $this->sendPost("sendPhoto", $params);
		}

		public function setChatTitle($chatId, $title) {
			$params['chat_id'] = $chatId;
			$params['title'] = $title;
			return $this->sendPost("setChatTitle", $params);
		}

		/**
		 * Отправляет POST запрос в Telegram
		 * @param String $methodName - имя метода в API, который вызываем
		 * @param array  $data - параметры, которые передаем, необязательное поле
		 * @return mixed|null
		 */
		private function sendPost($methodName, $data = []) {
			$url = $this->buildUrl($methodName) . '?' . http_build_query($data);
			$data = file_get_contents($url);
			return json_decode($data, true);
		}

		/**
		 * Формирует URL запроса
		 * @param String $methodName - имя метода в API, который вызываем
		 * @return string - Софрмированный URL для отправки запроса
		 */
		private function buildUrl($methodName) {
			return self::BASE_API_URL . $this->token . '/' . $methodName;
		}

	}

	class TelegramKeyboard {

		private $buttons = [[]];
		private $lineId = 0;

		public function addLine() {
			$this->buttons[] = [];
			$this->lineId++;
			return $this;
		}

		public function addButton($text, $request_contact = false, $request_location = false) {
			$this->buttons[$this->lineId][] = ["text" => $text, "request_contact" => $request_contact, "request_location" => $request_location];
			return $this;
		}

		public function addButtons(array $keys) {
			if (count($this->buttons[$this->lineId]) % 2 == 0) {
				$this->addLine();
			}

			foreach ($keys as $key) {
				call_user_func_array([$this, "addButton"], $key);
				if (count($this->buttons[$this->lineId]) % 2 == 0) {
					$this->addLine();
				}

			}
		}

		public function getData($with_cancel = true) {
			if ($with_cancel)
				$this->addButton("/cancel");
			return $this->buttons;
		}

	}
