<?php namespace HoneyBase\Core\Model;

use mysqli;
use Log;
use Util\Util\NuLog;
use Exception;

/**
 * MysqlAdapter
 */
class MysqlAdaptor {

	var $database;

	// constructer
	function __construct() {

    /* 引数が無いと 'Whoops, looks like something went wrong.' になる */
		// dbaccess
    if(DB_HOST=="") {
      NuLog::error("no database host", __FILE__, __LINE__);
    } elseif (DB_USERNAME=="") {
      NuLog::error("no database username", __FILE__, __LINE__);
    } elseif (DB_PASSWORD=="") {
      NuLog::info("no database password", __FILE__, __LINE__);
    } elseif (DB_DATABASE=="") {
      NuLog::error("no database name", __FILE__, __LINE__);
    }

    $database = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD);
		$sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = `".DB_DATABASE."`;";
		$result = mysqli_query($database, $sql);
		if (1 != $database->affected_rows) {
			/* DB存在しないので作る */
			$sql = "CREATE DATABASE IF NOT EXISTS `" . DB_DATABASE . "` DEFAULT CHARACTER SET utf8;";
			$result = mysqli_query($database, $sql);
		}
    $database = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

		// connect error
		if (mysqli_connect_errno()) {
			error_log("[" . date("Y-m-d h:i:s") . "]ConnectFailed status=" . mysqli_connect_error() . "\n", 3, LOG_PATH);
			exit;
		}

		// database = null;
		if ($database == null) {
			error_log("[" . date("Y-m-d h:i:s") . "]DB接続失敗 status=" . mysqli_connect_error() . "\n", 3, LOG_PATH);
			exit;
		}

		$this->database =& $database;
		$this->database->set_charset('utf8');
	}

	/**
	 * select method
	 * @param		str		$tbl			table name
	 * @param		arr		$data			where
	 * @return		arr					true：array("ret":true, array(0:array, 1:array ...))　false：array("ret":false)
	 */
	function select($tbl, $data = array()) {
		// init
		$where = "";
		$rows = array();
    $bool = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);
		// tbl none
		if (0 == $result->num_rows) {
			return ["flag"=>false, "data"=>array()];
		}

		// create where query
		foreach ($data as $key => $value) {
			if ("" != $where) {
				$where .= " AND ";
			} else {
				$where .= "WHERE ";
			}
			if (NULL === $value) {
				$where .= $key . " IS NULL";
			} else {
				$where .= $key . " = '" . $this->database->real_escape_string($value) . "'";
			}
		}

		// create sql
		$sql = "SELECT * FROM $tbl $where";

		// query
		$result = mysqli_query($this->database, $sql);
		if (gettype($result) != 'boolean' && 0 != $result->num_rows) {
			$rows = $this->normalize($rows, $result);
      $bool = true;
		}

		return ["flag"=>$bool, "data"=>$rows];
	}

	private function normalize ($rows, $result) {
		$mysql_data_type_hash = array(
		    1=>'tinyint',
		    2=>'smallint',
		    3=>'int',
		    4=>'float',
		    5=>'double',
		    7=>'timestamp',
		    8=>'bigint',
		    9=>'mediumint',
		    10=>'date',
		    11=>'time',
		    12=>'datetime',
		    13=>'year',
		    16=>'bit',
		    252=>'text',
		    253=>'varchar',
		    254=>'char',
		    246=>'decimal'
		);

		try {
			while ($row = $result->fetch_assoc()) {
				$j = 0;
				foreach($row as $_key => $_value) {
					$tbl_info = $result->fetch_field_direct($j);
					if($tbl_info->name == $_key){
						if($mysql_data_type_hash[$tbl_info->type] == 'tinyint'){
							if($_value == "0"){
								$row[$_key] = false;
							} elseif ($_value == "1") {
								$row[$_key] = true;
							} else {
								throw new Exception("boolean error");
							}
						}
					}
					$j++;
				}
				$rows[] = $row;
			}
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
		return $rows;
	}

	function joined_select($tbl, $col, $joined_tbl, $joined_col, $data = array()) {
		// init
		$where = "";
		$rows = array();
    $bool = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);


		// tbl none
		if (0 == $result->num_rows) {
			return ["flag"=>false, "data"=>array()];
		}

		// create where query
		foreach ($data as $key => $value) {
			if ("" != $where) {
				$where .= " AND ";
			} else {
				$where .= "WHERE ";
			}
			if (NULL === $value) {
				$where .= $key . " IS NULL";
			} else {
				$where .= $key . " = '" . $this->database->real_escape_string($value) . "'";
			}
		}
		// create sql
		$sql = "SELECT * FROM $tbl $where LEFT JOIN $joined_tbl ON $tbl.$col = $joined_tbl.$joined_col;";

		// query
		$result = mysqli_query($this->database, $sql);

		if (gettype($result) != 'boolean' && 0 != $result->num_rows) {
			$rows = $this->normalize($rows, $result);
      $bool = true;
		}
		return ["flag"=>$bool, "data"=>$rows];
	}

	function ambiguous_select($tbl, $data = array()) {
		// init
		$where = "";
		$rows = array();
    $bool = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);


		// tbl none
		if (0 == $result->num_rows) {
			return ["flag"=>false, "data"=>array()];
		}


		// create where query
		foreach ($data as $key => $value) {
			if ("" != $where) {
				$where .= " AND ";
			} else {
				$where .= "WHERE ";
			}
			if (NULL === $value) {
				$where .= $key . " IS NULL";
			} else {
				$where .= $key . " LIKE '" . $this->database->real_escape_string($value) . "%'";
			}
		}

		// create sql
		$sql = "SELECT * FROM $tbl $where";

		// query
		$result = mysqli_query($this->database, $sql);

		if (gettype($result) != 'boolean' && 0 != $result->num_rows) {
			// loop
			$rows = $this->normalize($rows, $result);
      $bool = true;
		}

		return ["flag"=>$bool, "data"=>$rows];
	}







	function count($tbl, $data = array()) {
		// init
		$where = "";
		$rows = array();
    $bool = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);


		// tbl none
		if (0 == $result->num_rows) {
			return ["flag"=>true, "data"=>0];
		}


		// create where query
		foreach ($data as $key => $value) {
			if ("" != $where) {
				$where .= " AND ";
			} else {
				$where .= "WHERE ";
			}
			if (NULL === $value) {
				$where .= $key . " IS NULL";
			} else {
				$where .= $key . " = '" . $this->database->real_escape_string($value) . "'";
			}
		}

		// create sql
		$sql = "SELECT COUNT(*) FROM $tbl $where";

		// query
		$result = mysqli_query($this->database, $sql);

		if (gettype($result) != 'boolean' && 0 != $result->num_rows) {
			// loop
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
      $bool = true;
		}

		return ["flag"=>$bool, "data"=>$rows[0]['COUNT(*)']];
	}

	function first($tbl, $data = array()) {
		// init
		$where = "";
		$rows = array();
    $bool = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);


		// tbl none
		if (0 == $result->num_rows) {
			return ["flag"=>true, "data"=>null];
		}

		// create sql
		$sql = "SELECT id FROM $tbl ORDER BY id ASC LIMIT 1;";

		// query
		$result = mysqli_query($this->database, $sql);

		if (gettype($result) != 'boolean' && 0 != $result->num_rows) {
			// loop
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
      $bool = true;
		}

		return ["flag"=>$bool, "data"=>$rows[0]['id']];
	}

	function last($tbl, $data = array()) {
		// init
		$where = "";
		$rows = array();
    $bool = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);


		// tbl none
		if (0 == $result->num_rows) {
			return ["flag"=>true, "data"=>null];
		}

		// create sql
		$sql = "SELECT id FROM $tbl ORDER BY id DESC LIMIT 1;";

		// query
		$result = mysqli_query($this->database, $sql);

		if (gettype($result) != 'boolean' && 0 != $result->num_rows) {
			// loop
			while ($row = $result->fetch_assoc()) {
				$rows[] = $row;
			}
      $bool = true;
		}

		return ["flag"=>$bool, "data"=>$rows[0]['id']];
	}


	/**
	 * insert method
	 * @param		str		$tbl			table name
	 * @param		arr		$data			insert data
	 * @return		arr					true：true　false：false
	 */
	function insert($tbl, $data = array()) {

		// init
		$insert_id = 0;
		$insert_str = $values_str = "";
		$sql_param = array();
		$return_flg = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);

		// tbl none
		if (0 == $result->num_rows) {
			// create tbl
			$this->createTable($tbl, $data);
		}

		// count insert rows
		if (0 != count($data)) {
			// create insert query
			foreach ($data as $key => $value) {
				if ("" != $insert_str) {
					$insert_str .= ", ";
					$values_str .= ", ";
				}
				$insert_str .= $key;

				switch ( gettype($value) ) {
			    case NULL:
						$values_str .= "NULL";
						break;
					case "boolean":
						if($value){
							$values_str .= 1;
						} else {
							$values_str .= 0;
						}
						break;
					default:
						$values_str .= "'" . $this->database->real_escape_string($value) . "'";
				}
			}

			// auto commit to OFF
			$this->database->autocommit(FALSE);

			// create sql
			$sql = 'INSERT INTO ' . $tbl . ' (' . $insert_str . ') values (' . $values_str . ');';

			// query

			$result = mysqli_query($this->database, $sql);
			$insert_id = $this->database->insert_id;

			if (1 != $this->database->affected_rows) {
				// Roll back if there were rows affected is not one line
				$this->database->rollback();
        $this->errorReport($sql, __FILE__, __LINE__);
			} else {
				// Had row affected commit if one line
				$this->database->commit();
				$return_flg = true;
			}
		}

		return ["flag"=>$return_flg, "id"=>$insert_id];
	}


	/**
	 * create tbl method
	 * @param		str		$tbl			table name
	 * @param		arr		$data			set
	 * @return		arr					true：true　false：false
	 */
	function createTable($tbl, $data = array()) {

		// init
		$create_str = $values_str = "";
		$sql_param = array();
		$return_flg = false;

		// count insert rows
		if (0 != count($data)) {
			// create insert query

			foreach ($data as $key => $value) {
				switch (gettype($value)) {
					case "integer":
						if(-9999999999 < $value && $value < 99999999999){
							$type = "int(11)";
						} else {
							$type = "bigint(20)";
						}
						break;
					case "string":
						$type = "text";
						break;
					case "double":
						$type = "bigint(20)";
						break;
					case "boolean":
						$type = "boolean";
						break;
				}
				$create_str .= "`" . $key . "` " . $type . ",";
			}

			// create sql
			$sql = "CREATE TABLE IF NOT EXISTS `" . $tbl . "` (`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT," . $create_str . "PRIMARY KEY (`id`), UNIQUE KEY `id` (`id`)) ENGINE=innoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

			// auto commit to OFF
			$this->database->autocommit(FALSE);

			// query
			$result = mysqli_query($this->database, $sql);

			if (0 <= $this->database->affected_rows) {
				// Roll back if there were rows affected is not one line
				$this->database->rollback();
        $this->errorReport($sql, __FILE__, __LINE__);
			} else {
				// Had row affected commit if one line
				$this->database->commit();
				$return_flg = true;
			}
		}
		return ["flag"=>$return_flg];
	}

	public static function createDB(){
		$return_flg = false;
		$sql = "CREATE DATABASE IF NOT EXISTS `" . DB_DATABASE . "` DEFAULT CHARACTER SET utf8;";
		$result = mysqli_query($this->database, $sql);
		if (1 != $this->database->affected_rows) {
      $this->errorReport($sql, __FILE__, __LINE__);
		} else {
			// Had row affected commit if one line
			$this->database->commit();
			$return_flg = true;
		}
		return ["flag"=>$return_flg];
	}



	/**
	 * update method
	 * @param		str		$tbl			table name
	 * @param		int		$id			update id
	 * @param		array		$value			update value
	 * @return		arr					true：true　false：false
	 */
	function update($tbl, $id, $value = array()) {
		// init
		$sql_param = array();
		$return_flg = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);

		// tbl none
		if (0 == $result->num_rows) {
      NuLog::error("There is no table, cannot update target record", __FILE__, __LINE__);
		}


		// count insert rows
		if (0 < $id) {
			// auto commit to OFF
			$this->database->autocommit(FALSE);

			// create sql
      $set_str = 'SET ' . $this->genSetStr($value);
			$sql = 'UPDATE ' . $tbl . ' ' . $set_str . ' WHERE id=' . $id . " ;";

      // 更新
      /* 変化無し・id無し・Schemeと合わない　のときはfalseが返る */
      $existance = $this->select($tbl, ["id"=>$id])["flag"];
      $result = ($existance) ? mysqli_query($this->database, $sql) : null;

			if (1 != $this->database->affected_rows) {
				// Roll back if there were rows affected is not one line
				$this->database->rollback();
        $this->errorReport($sql, __FILE__, __LINE__);
			} else {
				// Had row affected commit if one line
				$this->database->commit();
				$return_flg = true;
			}
		}
		return ["flag"=>$return_flg];
	}

  function genSetStr($data){
    $str = "";
    foreach ($data as $key => $value) {
			switch ( gettype($value) ) {
		    case NULL:
					$value = "NULL";
					break;
				case "boolean":
					if($value){
						$value = 1;
					} else {
						$value = 0;
					}
					break;
				default:
					$value = "'".$this->database->real_escape_string($value)."'";
			}
      $str .= $key . '=' . $value . ", ";
    }
    return trim($str, ", ");
  }






	/**
	 * delete method
	 * @param		str		$tbl			table name
	 * @param		int		$id			delete id
	 * @return		arr					true：true　false：false
	 */
	function delete($tbl, $id) {
		// init
		$sql_param = array();
		$return_flg = false;

		// search tbl
		$sql = "SHOW TABLES FROM " . DB_DATABASE . " LIKE '" . $tbl . "';";
		$result = mysqli_query($this->database, $sql);

		// tbl none
		if (0 == $result->num_rows) {
      NuLog::error("There is no table, cannot remove target record", __FILE__, __LINE__);
		}


		// count insert rows
		if (0 < $id) {
			// auto commit to OFF
			$this->database->autocommit(FALSE);

			// create sql
			$sql = 'DELETE FROM ' . $tbl . ' WHERE id = ' . $id;

			// query
			$result = mysqli_query($this->database, $sql);

			if (1 != $this->database->affected_rows) {
				// Roll back if there were rows affected is not one line
				$this->database->rollback();
        $this->errorReport($sql, __FILE__, __LINE__);
			} else {
				// Had row affected commit if one line
				$this->database->commit();
				$return_flg = true;
			}
		}
		return ["flag"=>$return_flg];
	}







	/*
	* 何でもあり関数
	*/
	function get_db(){
		return $this->$database;
	}

	function sql($sql) {
		// init
		$where = "";
		$rows = array();
    $bool = false;
		// query
		$result = mysqli_query($this->database, $sql);
		if (gettype($result) != 'boolean' && 0 != $result->num_rows) {
			$rows = $this->normalize($rows, $result);
      $bool = true;
		}
		return ["flag"=>$bool, "data"=>$rows];
	}


  function errorReport($sql, $file, $line){
    $msg = "[" . date("Y-m-d h:i:s") . "]Query failed by:" . $sql . "\n";
		error_log($msg, 3, LOG_PATH);
    NuLog::error($msg, $file, $line);
  }

	function mysql_exploit($result){
		$data = ($result != null) ? $result->fetch_field()->social_id : null;
		return $data;
	}


}
