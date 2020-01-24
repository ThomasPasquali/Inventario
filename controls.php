<?php
include_once 'db.php';

define('INI_FILE_PATH', __DIR__.'\..\inventario.ini');

class Controls {
	
	public $ini = NULL;
	public $db = NULL;
	private $schema, $tableOggetti, $tableUtenti, $tableImmagini;
	
	function __construct() {
		session_start();
		$this->ini = parse_ini_file(INI_FILE_PATH);
		$this->db = new DB($this->ini['host'], $this->ini['port'], $this->ini['username'], $this->ini['password'], $this->ini['dbName']);
		$this->schema = $this->ini['dbName'];
		$this->tableOggetti = $this->ini['tabOggetti'];
		$this->tableUtenti = $this->ini['tabUtenti'];
		$this->tableImmagini = $this->ini['tabImmagini'];
	}
	
	public function redirect($where) {
		header('Location: '.$where);
		exit();
	}
	
	public function getPreferenza($name) {
		return $_SESSION['preferenze'][$name]??NULL;
	}
	
	public function setPreferenza($name, $value) {
		if($value != $this->getPreferenza($name)) {
			$_SESSION['preferenze'][$name] = $value;
			$this->storePreferenze();
		}
	}
	
	public function savePreferenze($preferenze) {
		$_SESSION['preferenze'] = [];
		foreach ($preferenze as $key => $value)
			$_SESSION['preferenze'][$key] = $value;
		$this->storePreferenze();
	}
	
	private function loadPreferenze() {
		$res = $this->db->ql('SELECT Preferenze FROM '.$this->tableUtenti.' WHERE Username = ?', [$_SESSION['username']]);
		if(count($res) > 0)
			$_SESSION['preferenze'] = json_decode($res[0]['Preferenze'], TRUE);
	}
	
	private function storePreferenze() {
		$this->db->dml(
				'UPDATE '.$this->tableUtenti.' SET Preferenze = ? WHERE Username = ?',
				[json_encode($_SESSION['preferenze']), $_SESSION['username']]);
	}
	
	public function isLogged() {
		return isset($_SESSION['username']);
	}
	
	public function login($username, $password) {
		$res = $this->db->ql('SELECT * FROM '.$this->tableUtenti.' WHERE Username = ?', [$username]);
		if(count($res) > 0)
			if(password_verify($password, $res[0]['Password'])) {
				$_SESSION['username'] = $username;
				$this->loadPreferenze();
				return TRUE;
			}
		return FALSE;
	}
	
	public function logout() {
		session_destroy();
	}
	
	public function getColumnDescription($table, $column) {
		$res = $this->db->ql(
				'SELECT column_comment AS c
				FROM information_schema.columns
				WHERE
					table_schema = ?
					AND table_name = ?
					AND column_name = ?',
				[$this->schema, $table, $column]);
		return $res[0]['c']??NULL;
	}
	
	public function describeTable($table) {
		return $this->db->ql('DESCRIBE '.$table);
	}
	
	public function getNameTableOggetti() {
		return $this->tableOggetti;
	}
	
	public function getNameTableUtenti() {
		return $this->tableUtenti;
	}
	
	public function getNameTableImmagini() {
		return $this->tableImmagini;
	}
	
	public function changeSQLtoHTMLtype($sqlType) {
		if(preg_match('/^([a-z]*int\(.*)|(decimal\(.*)/', $sqlType))
			return 'number';
		else if(preg_match('/^date$/', $sqlType))
			return 'date';
		else 
			return 'text';
	}
	
}