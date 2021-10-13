<?php
class dbc extends mysqli{
	
	public function __construct($hostname = null, $username = null, $password = null, $database = null,$port = null,$socket = null) {
		if(defined("TESTENV")){
			return null;
		}
		parent::__construct($hostname, $username, $password, $database, $port, $socket);
	}

	public function query($q, $result_mode = MYSQLI_STORE_RESULT){
		if(defined("TESTENV")){
			//можно проверять правильность SQL запроса, к примеру
			if(substr_count($q,"(") != substr_count($q,")") || substr_count($q,"'")%2 != 0){
				return false;
			}
			return true;
		}
		return parent::query($q, $result_mode);
	}
}
?>