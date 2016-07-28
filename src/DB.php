<?php

namespace Main;

use Mysqli;

class DB {

	public static $connection;
	public static $lastConnect;
	public static $maxSleep = 20;

	private static function reconnect() {
		$continue = false;
		if (self::$lastConnect) {
			if (self::$lastConnect < (time() - self::$maxSleep)) {
				self::$connection->close();

				echo "-- debug: reconnect db connection...".PHP_EOL;
			} else {
				$continue = true;
			}
		}

		if (!$continue) {
			self::$connection = new Mysqli('localhost', 'root', '1', 'itb-chess-evaluation');
			self::$lastConnect = time();
		}
	}

	public static function query($sql, $f) {
		self::reconnect();

		$q = self::$connection->query($sql);
		if ($q->num_rows > 0) {
			while ($row = $q->fetch_assoc()) {
				$f((object) $row);
			}
		}
	}

	public static function insert($table, $data) {
		$sql = "INSERT INTO `".$table."` (";

		$tmp = [];
		foreach (array_keys($data) as $field) {
			$tmp[] = "`".$field."`";
		}
		$sql .= join(", ", $tmp).') VALUES (';

		$tmp = [];
		foreach ($data as $field) {
			$tmp[] = "'".addslashes($field)."'";
		}
		$sql .= join(", ", $tmp).');';

		self::reconnect();
		return self::$connection->query($sql);
	}

	// UPDATE `engine_puzzle` SET `answer_depth_plus_0` = '1.Ne21' WHERE `id` = '1';
	public static function update($table, $id, $data) {
		$sql = "UPDATE `".$table."` SET ";

		$tmp = [];
		foreach ($data as $k => $v) {
			$tmp[] = "`".$k."` = '".addslashes($v)."'";
		}
		$sql .= join(", ", $tmp)." ";
		$sql .= "WHERE `id` = ".$id.";";
		// echo $sql;exit;

		self::reconnect();
		return self::$connection->query($sql);
	}

	public static function loadEngines() {
		$arr = [];

		self::query("SELECT * FROM `engines` WHERE `use` = 'y';", function ($row) use (&$arr) {
			$arr[] = $row;
		});

		return $arr;
	}


	public static function loadPuzzles() {
		$arr = [];

		self::query("SELECT * FROM `puzzles`;", function ($row) use (&$arr) {
			$arr[] = $row;
		});

		return $arr;
	}

	public static function getStatus($engineId, $puzzleId) {
		$status = false;
		self::query(
			"SELECT count(*) as counter FROM `engine_puzzle` WHERE `puzzle` = {$puzzleId} AND `engine` = {$engineId};",
			function ($row) use (&$status) {
				$status = $row->counter > 0;
			}
		);

		return $status;
	}

	public static function alreadyHavePuzzle($fen) {
		$status = false;
		self::query(
			"SELECT count(*) as counter FROM `puzzles` WHERE `fen` = '{$fen}';",
			function ($row) use (&$status) {
				$status = $row->counter > 0;
			}
		);

		return $status;
	}
}
