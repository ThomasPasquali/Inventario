<?php 
include_once '../controls.php';
$c = new Controls();

if(!$c->isLogged()) $c->redirect('login.php');

header('Content-type: application/json');

switch($_POST['request']??'') {
	
	case 'query':
		//Impostazione preferenze
		foreach ($_POST as $key => $value)
			$c->setPreferenza($key, $value);
			
		//Creazione SQL
		$sql = 'SELECT * FROM oggetti';
		$params = [];
		
		$max_results = $c->getPreferenza('max_results');
		//TODO filtri
		
		if(preg_match('/\d{1,}/', $max_results) && $max_results > 0)
			$sql .= ' LIMIT '.$max_results;
		
		//Richiesta al DB e invio dati in JSON
		$oggetti = $c->db->ql($sql, $params);
		echo json_encode($oggetti);
		exit();
	
	default:
		echo json_encode(NULL);
		exit();
}

