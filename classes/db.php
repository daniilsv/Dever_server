<?php

	class DB {
		/**
		 * Этот объект
		 * @var \DB
		 */
		private static $instance;

		/**
		 * Префикс таблиц
		 * @var string
		 */
		public $prefix;
		private $join;

		/**
		 * Объект, представляющий подключение к серверу MySQL
		 * @var \mysqli
		 */
		private $mysqli;

		/**
		 * @return \DB
		 */
		public static function getInstance() {
			if (self::$instance === null) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			$this->connect();
		}

		private function connect() {
			mysqli_report(MYSQLI_REPORT_STRICT);
			try {
				$this->mysqli = new mysqli(Config::get("db_host"), Config::get("db_user"), Config::get("db_pass"), Config::get("db_base"));
			} catch (Exception $e) {
				$connect_error = $e->getMessage();
				echo $connect_error;
				return false;
			}

			$this->mysqli->set_charset('utf8');

			$this->setTimezone();

			$this->prefix = Config::get("db_prefix");

			return true;
		}

		public function setTimezone() {
			$this->query("SET `time_zone` = '%s'", date('P'));
		}

//============================================================================//
		//============================================================================//

		/**
		 * Подготавливает строку перед запросом
		 *
		 * @param  string $string
		 * @return string
		 */
		public function escape($string) {
			return $this->mysqli->real_escape_string($string);
		}

		/**
		 * Выполняет запрос в базе
		 * @param  string       $sql Строка запроса
		 * @param  array|string $params Аргументы запроса, которые будут переданы в vsprintf
		 * @return boolean
		 */
		public function query($sql, $params = false) {
			$sql = str_replace('{#}', $this->prefix, $sql);

			if ($params) {
				if (!is_array($params)) {
					$params = [$params];
				}

				foreach ($params as $index => $param) {
					if (!is_numeric($param)) {
						$params[$index] = $this->escape($param);
					}
				}

				$sql = vsprintf($sql, $params);
			}
			if (!$this->mysqli) {
				return false;
			}
			if (DEBUG) {
				df($sql);
			}

			$result = $this->mysqli->query($sql);

			if (!$this->mysqli->errno) {
				return $result;
			}
		}

//============================================================================//
		//============================================================================//

		public function numRows($result) {
			return $result->num_rows;
		}

		public function fetchAssoc($result) {
			return $result->fetch_assoc();
		}

		public function fetchRow($result) {
			return $result->fetch_row();
		}

		public function error() {
			return $this->mysqli->error;
		}

//============================================================================//
		//============================================================================//

		/**
		 * Возвращает ID последней вставленной записи из таблицы
		 * @return int
		 */
		public function lastId() {
			return $this->mysqli->insert_id;
		}

//============================================================================//
		//============================================================================//

		/**
		 * Подготавливает значение $value поля $field для вставки в запрос
		 * @param  string $field
		 * @param  string $value
		 * @return string
		 */
		public function prepareValue($field, $value) {
			if (is_bool($value)) {
				$value = (int)$value;
			} elseif ($value === '' || is_null($value)) {
				$value = 'NULL';
			} else {
				$value = $this->escape(trim($value));
				$value = "'{$value}'";
			}

			return $value;
		}

//============================================================================//
		//============================================================================//

		/**
		 * Выполняет запрос UPDATE
		 *
		 * @param  string $table Таблица
		 * @param  string $where Критерии запроса
		 * @param  array  $data Массив[Название поля] = значение поля
		 * @return boolean
		 */
		public function update($table, $where, $data) {
			if (empty($data)) {
				return false;
			}

			foreach ($data as $field => $value) {
				$value = $this->prepareValue($field, $value);
				$set[] = "`{$field}` = {$value}";
			}

			$set = implode(', ', $set);

			$sql = "UPDATE `{#}{$table}` SET {$set} WHERE {$where}";

			if ($this->query($sql)) {
				return true;
			}
			return false;
		}

		/**
		 * Выполняет запрос INSERT
		 *
		 * @param  string $table Таблица
		 * @param  array  $data Массив[Название поля] = значение поля
		 * @return bool
		 */
		public function insert($table, $data) {
			if (empty($data) || !is_array($data)) {
				return false;
			}

			foreach ($data as $field => $value) {
				$fields[] = "`$field`";
				$values[] = $this->prepareValue($field, $value);
			}

			$fields = implode(', ', $fields);
			$values = implode(', ', $values);

			$sql = "INSERT INTO `{#}{$table}` ({$fields})\nVALUES ({$values})";

			if ($this->query($sql)) {
				return $this->lastId();
			}

			return $this->lastId();
		}

		/**
		 * Выполняет запрос INSERT при совпадении PRIMARY или UNIQUE ключа выполняет UPDATE вместо INSERT
		 *
		 * @param string $table Таблица
		 * @param array  $data Массив данных для вставки в таблицу
		 * @param array  $update_data Массив данных для обновления при совпадении ключей
		 * @return bool
		 */
		public function insertOrUpdate($table, $data, $update_data = false) {
			$fields = [];
			$values = [];
			$set = [];
			if (is_array($data)) {
				foreach ($data as $field => $value) {
					$value = $this->prepareValue($field, $value);
					$fields[] = "`$field`";
					$values[] = $value;
					if ($update_data === false) {
						$set[] = "`{$field}` = {$value}";
					}
				}
				$fields = implode(', ', $fields);
				$values = implode(', ', $values);

				$sql = "INSERT INTO {#}{$table} ({$fields})\nVALUES ({$values})";

				if (is_array($update_data)) {
					foreach ($update_data as $field => $value) {
						$value = $this->prepareValue($field, $value);
						$set[] = "`{$field}` = {$value}";
					}
				}
				$set = implode(', ', $set);
				$sql .= " ON DUPLICATE KEY UPDATE {$set}";

				if ($this->query($sql)) {
					return $this->lastId();
				}
			}

			return false;

		}

		/**
		 * Выполняет запрос DELETE
		 * @param  string $table_name Таблица
		 * @param  string $where Критерии запроса
		 * @return type
		 */
		public function delete($table_name, $where) {
			return $this->query("DELETE FROM `{#}{$table_name}` WHERE {$where}");
		}

		/**
		 * Выполняет запрос TRUNCATE
		 * @param  string $table_name Таблица
		 * @return type
		 */
		public function truncate($table_name) {
			return $this->query("TRUNCATE `{#}{$table_name}`");
		}

		/**
		 * Возвращает массив со всеми строками полученными после запроса
		 * @param  string $table_name
		 * @param  string $where
		 * @param  string $fields
		 * @param  string $order
		 * @return boolean|array
		 */
		public function getRows($table_name, $where = '1', $fields = '*', $order = 'id ASC', $group = null) {
			$sql = "SELECT {$fields} FROM {#}{$table_name} ";
			if ($this->join) {
				$sql .= $this->join;
			}
			$this->join = "";
			$sql .= " WHERE {$where}";
			if ($group != null) {
				$sql .= " GROUP BY {$group}";
			}
			if ($order) {
				$sql .= " ORDER BY {$order}";
			}
			$result = $this->query($sql);
			if (!$this->mysqli->errno) {
				$data = [];
				while ($item = $this->fetchAssoc($result)) {
					if (ake("id", $item)) {
						$data[$item['id']] = $item;
					} else {
						$data[] = $item;
					}

				}
				return $data;
			} else {
				return [];
			}
		}

		/**
		 * Возвращает массив с одной строкой из базы
		 * @param  string $table
		 * @param  string $where
		 * @param  string $fields
		 * @param  string $order
		 * @return boolean|array
		 */
		public function getRow($table, $where = '1', $fields = '*', $order = '') {
			$sql = "SELECT {$fields} FROM `{#}{$table}` ";
			if ($this->join) {
				$sql .= $this->join;
			}
			$this->join = "";

			$sql .= " WHERE {$where}";
			if ($order) {
				$sql .= " ORDER BY {$order}";
			}
			$sql .= " LIMIT 1";
			$result = $this->query($sql);
			if ($result) {
				$data = $this->fetchAssoc($result);
				return $data;
			} else {
				return false;
			}
		}

		public function joinRight($table_name, $as, $on) {
			$this->join .= "RIGHT JOIN `{#}{$table_name}` as {$as} ON {$on}\n";
			return $this;
		}

		public function joinLeft($table_name, $as, $on) {
			$this->join .= "LEFT JOIN `{#}{$table_name}` as {$as} ON {$on}\n";
			return $this;
		}

//============================================================================//
		//============================================================================//

		/**
		 * Возвращает одно поле из таблицы в базе
		 *
		 * @param  string $table
		 * @param  string $where
		 * @param  string $field
		 * @param  string $order
		 * @return mixed
		 */
		public function getField($table, $where, $field, $order = '') {
			$row = $this->getRow($table, $where, $field, $order);
			return $row[$field];
		}

		public function getFields($table, $where, $fields = '*', $order = '') {
			$row = $this->getRow($table, $where, $fields, $order);
			return $row;
		}

//============================================================================//
		//============================================================================//

		/**
		 * Возвращает количество строк выведенных запросом
		 * @param  string $table
		 * @param  string $where
		 * @param  int    $limit
		 * @return boolean|int
		 */
		public function getRowsCount($table, $where = '1', $limit = false) {
			$sql = "SELECT COUNT(1) FROM `{#}$table` WHERE $where";
			if ($limit) {
				$sql .= " LIMIT {$limit}";
			}
			$result = $this->query($sql);
			if ($result) {
				$row = $this->fetchRow($result);
				$count = $row[0];
				return $count;
			} else {
				return false;
			}
		}

		public function dropTable($table_name) {
			$sql = "DROP TABLE IF EXISTS `{#}{$table_name}`";

			$this->query($sql);
		}

	}
