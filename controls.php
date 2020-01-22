<?php
include_once 'db.php';

define('INI_FILE_PATH', __DIR__.'\..\inventario.ini');

class Controls {
	
	public $ini = NULL;
	public $db = NULL;
	
	function __construct() {
		session_start();
		$this->ini = parse_ini_file(INI_FILE_PATH);
		$this->db = new DB($this->ini['host'], $this->ini['port'], $this->ini['username'], $this->ini['password'], $this->ini['dbName']);
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
	
	private function loadPreferenze() {
		$res = $this->db->ql('SELECT Preferenze FROM utenti WHERE Username = ?', [$_SESSION['username']]);
		if(count($res) > 0)
			$_SESSION['preferenze'] = json_decode($res[0]['Preferenze'], TRUE);
	}
	
	private function storePreferenze() {
		$this->db->dml(
				'UPDATE utenti SET Preferenze = ? WHERE Username = ?',
				[json_encode($_SESSION['preferenze']), $_SESSION['username']]);
	}
	
	public function isLogged() {
		return isset($_SESSION['username']);
	}
	
	public function login($username, $password) {
		$res = $this->db->ql('SELECT * FROM utenti WHERE Username = ?', [$username]);
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
	
	//TODO da ottimizzare
	public function describeTable($table) {
		return $this->db->ql('SHOW FULL COLUMNS FROM '.$table);
	}
	
}