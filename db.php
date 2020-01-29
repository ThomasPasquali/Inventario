<?php
class DB extends PDO{
	
	function __construct($host, $port, $username, $password, $dbName) {
		parent::__construct("mysql:host=$host;dbname=$dbName;port=$port", $username, $password);
	}
	
	public function ql($sql, $params=NULL, $fetchType = PDO::FETCH_ASSOC) {
		$stmt = $this->prepare($sql);
		$stmt->execute($params);
		
		if($stmt->errorCode() != 0)
			return $stmt->errorInfo();
			
		$righe = [];
		while ($riga = $stmt->fetch($fetchType))
			$righe[] = $riga;
			
		return $righe;
	}
	
	public function dml($sql, $params=NULL) {
		$stmt = $this->prepare($sql);
		$stmt->execute($params);
		return $stmt;
	}
}