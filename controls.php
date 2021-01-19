<?php
include_once 'db.php';

define('INI_FILE_PATH', __DIR__.'/../inventario.ini');

class Controls {
	
	public $ini = NULL;
	public $db = NULL;
	private $schema, $tableOggetti, $tableUtenti, $tableImmagini, $tableEtichette, $tableEtichetteOggetti, $viewOggetti;
	private $alerts = [];
	
	function __construct() {
		session_start();
		$this->ini = parse_ini_file(INI_FILE_PATH, true);
		$this->db = new DB($this->ini['db']['host'], $this->ini['db']['port'], $this->ini['db']['username'], $this->ini['db']['password'], $this->ini['db']['dbName']);
		$this->schema = $this->ini['db']['dbName'];
		$this->tableOggetti = $this->ini['db']['tabOggetti'];
		$this->tableUtenti = $this->ini['db']['tabUtenti'];
		$this->tableImmagini = $this->ini['db']['tabImmagini'];
		$this->tableEtichette = $this->ini['db']['tabEtichette'];
		$this->tableEtichetteOggetti = $this->ini['db']['tabEtichetteOggetti'];
		$this->viewOggetti = $this->ini['db']['viewOggetti'];
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

	public function getNameTableEtichette() {
		return $this->tableEtichette;
	}

	public function getNameTableEtichetteOggetti() {
		return $this->tableEtichetteOggetti;
	}

	public function getNameViewOggetti() {
		return $this->viewOggetti;
	}
	
	public function changeSQLtoHTMLtype($sqlType) {
		if(preg_match('/^([a-z]*int\(.*)|(decimal\(.*)/', $sqlType))
			return 'number';
		else if(preg_match('/^date$/', $sqlType))
			return 'date';
		else 
			return 'text';
	}

	/**
	 * @param $type Can be secondary, success, danger, warning, info, light, dark
	 */
	public function addAlert(string $message, string $type = 'primary'){
		$this->alerts[] = "showAlert('$message', '$type');";
	}

	public function getJSforAlerts(){
		if(!empty($this->alerts))
			return '<script>$(document).ready(function() {'.implode(' ', $this->alerts).'});</script>';
	}

	public function getNextImgName($idOggetto) {
		$res = $this->db->ql('SELECT COUNT(*)  AS c FROM immagini_oggetti WHERE Oggetto = ? AND SUBSTRING_INDEX(Immagine , "_", 1) = ?', [$idOggetto, $idOggetto]);
		return $idOggetto.'_'.(intval($res[0]['c'])+1);
	}
	
}