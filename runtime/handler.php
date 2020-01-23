<?php 
include_once '../controls.php';
$c = new Controls();

if(!$c->isLogged()) $c->redirect('login.php');

header('Content-type: application/json');

switch($_POST['request']??'') {
	
	case 'query':
		$prefs = [
			'columns' => [],
			'max_results' => 0
		];
		$matches =[];
		//Parsing preferenze
		foreach ($_POST as $key => $value)
			if(preg_match('/^col_([^;\']*)$/', $key, $matches))
				$prefs['columns'][] = $matches[1];
				
		if(preg_match('/^\d{1,}$/', $_POST['max_results']??'') && intval($_POST['max_results']) > 0)
			$prefs['max_results'] = intval($_POST['max_results']);
		
		//Salvataggio preferenze
		$c->savePreferenze($prefs);
			
		//Creazione SQL
		$sql = 'SELECT '.implode(', ', $prefs['columns']);
		$sql .= ' FROM '.$c->getNameTableOggetti();
		$sql .= ($prefs['max_results'] > 0 ? ' LIMIT '.$prefs['max_results'] : '');		
		
		//Richiesta dati al DB
		$dati = $c->db->ql($sql);
		
		//Richiesta colonne
		$colonne = [];
		foreach ($prefs['columns'] as $colonna)
			$colonne[] = $c->getColumnDescription($c->getNameTableOggetti(), $colonna);
		//Invio colonne e dati in JSON
			echo json_encode(['Dati' => $dati, 'Colonne' => $colonne]);
		exit();
	
	default:
		echo json_encode(NULL);
		exit();
}

