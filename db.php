<?php
class DB {

	public function __construct() {

		//------------------------------------------//
		ini_set('memory_limit','-1');
		ini_set('max_execution_time', 0);
		//------------------------------------------//

		$hostname = 'localhost';
		$username = 'root';
		$password = '';
		$database = 'phptest';
		$port = NULL;
		
		$this->connection = new \mysqli($hostname, $username, $password, $database, $port);

		if ($this->connection->connect_error) {
			throw new \Exception('Error: ' . $this->connection->error . '<br />Error No: ' . $this->connection->errno);
		}

		$this->connection->set_charset("utf8");
		//$this->connection->query("SET SQL_MODE = ''");


		/**********************************************************************/

		// get local time on Web/PHP server
		$localtime = strtotime(date('Y-m-d H:i:s'));

		//get local time in GMT/UTC (i.e GMT/UTC is set as +0:00 on database and other timezones are set as +/- hours of this)
		$gm_localtime = strtotime(gmdate('Y-m-d H:i:s'));

		//find offset in hours (if any - which allows for Daylight Saving Time or British Summer Time (BST))
		$diff_hours = ($localtime - $gm_localtime) / 3600;

		//Then the Database server needs to be set to this Offset to store/retrieve values as local ones
		$adjust = "SET time_zone = '";

		if ($diff_hours > 0) {
			$adjust .= "+" . ceil($diff_hours);
		} elseif ($diff_hours < 0) {
			$adjust .= floor($diff_hours);
		} else {
			$adjust .= "+0";
		}	
		$adjust .= ":00'";
		
		$this->connection->query($adjust);
	}

	public function query($sql) {
		$query = $this->connection->query($sql);

		if (!$this->connection->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->num_rows = $query->num_rows;
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				$query->close();

				return $result;
			} else {
				return true;
			}
		} else {
			if(DEFAULT_ERROR_MODE==1) {
				throw new \Exception('Error: ' . $this->connection->error  . '<br />Error No: ' . $this->connection->errno . '<br />' . $sql);
			} else {
				echo("We apologize for this problem and hope to have it resolved soon.");
				die("<br/><a href=".HTTPS_SERVER.">Continue</a>");
			}
		}
	}
}