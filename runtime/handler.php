<?php 
include_once '../controls.php';
$c = new Controls();

if(!$c->isLogged()) $c->redirect('login.php');

header('Content-type: application/json');

switch($_POST['request']??'') {
	
	case 'query':
		$dati = $c->db->ql('SELECT * FROM oggetti_view');
		echo json_encode($dati);
		exit();
		
	case 'updateValue':
		$res = $c->db->dml(
		'UPDATE '.$c->getNameTableOggetti().
		' SET '.str_replace('/[;\']/', '', $_POST['field']).' = ?'.
		' WHERE ID = ?',
		[in_array($_POST['newVal'], ['', '0000-00-00']) ? NULL : $_POST['newVal'], $_POST['oggetto']]);
		echo ($res->errorCode() == 0) ? 'OK' : $res->errorInfo()[2];
		exit();
		
	case 'removeImage':
		$res = $c->db->dml(
			'DELETE FROM '.$c->getNameTableImmagini().' WHERE Oggetto = ? AND Immagine = ?',
			[$_POST['oggetto'], $_POST['immagine']]);
		if ($res->errorCode() == 0) {
			echo 'OK';
			unlink(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.$c->ini['imgsDir'].DIRECTORY_SEPARATOR.$_POST['immagine']);
		}else
			echo $res->errorInfo()[2];
		exit();

	case 'removeLabel':
		$res = $c->db->dml(
			'DELETE FROM '.$c->getNameTableEtichetteOggetti().' WHERE Oggetto = ? AND Etichetta = (SELECT ID FROM '.$c->getNameTableEtichette().' WHERE Nome = ?)',
			[$_POST['oggetto'], $_POST['etichetta']]);
		echo ($res->errorCode() == 0) ? 'OK' : $res->errorInfo()[2];
		exit();

	case 'setPreference':
		$c->setPreferenza($_POST['name'], $_POST['value']);
		exit();

	case 'newOggetto':
		$res = $c->db->dml('INSERT INTO oggetti VALUES ()');
		if($res->errorCode() == 0) {
			$oggetto = $c->db->ql('SELECT * FROM '.$c->getNameTableOggetti().' ORDER BY ID DESC LIMIT 1')[0];
			$oggetto['Link'] = 'Altro';
			echo json_encode(['status' => 'OK', 'record' => $oggetto]);
		} else
			echo json_encode(['status' => 'ERROR', 'error' => $res->errorInfo()[2]]);
		exit();

	case 'getColoriOggetto':
		$res = $c->db->ql('SELECT Colore AS c FROM etichette_oggetti WHERE Oggetto = ?', [$_POST['oggetto']]);
		echo json_encode($res);
		exit();

	case 'addEtichettaToOggetto':
		$res = $c->db->dml(
			'INSERT INTO '.$c->getNameTableEtichetteOggetti().'(Oggetto, Etichetta) 
			VALUES(?, (SELECT ID FROM '.$c->getNameTableEtichette().' WHERE Nome = ?))', [$_POST['oggetto'], $_POST['nomeEtichetta']]);
		echo json_encode($res);
		exit();
	
	default:
		echo json_encode(NULL);
		exit();
}

