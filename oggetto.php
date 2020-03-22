<?php 
	include_once 'controls.php';
	$c = new Controls();

	if(!$c->isLogged()) $c->redirect('login.php');

	if(isset($_REQUEST['id']))
		$oggetto = $c->db->ql('SELECT * FROM '.$c->getNameTableOggetti().' WHERE ID = ?', [$_REQUEST['id']]);
		
	if(!isset($_REQUEST['id']) || count($oggetto) < 1) {
		echo '<pre>Fornire un valido paramentro id</pre>';
		exit();
	}

	if(isset($_REQUEST['elimina'])) {
		$res = $c->db->dml('DELETE FROM '.$c->getNameTableOggetti().' WHERE ID = ?', [$_REQUEST['id']]);
		if($res->errorCode() == 0) {
			echo '<script>window.close();</script>';
			exit();
		}else 
			echo'<pre>'.$res->errorInfo()[2].'</pre>';
	}
	
	if(isset($_FILES['image'])) {
		$moved = move_uploaded_file(
				$_FILES['image']['tmp_name'],
				$c->ini['imgsDir'].'/'.$_FILES['image']['name']);
		if(!$moved)
			echo 'Not uploaded because of error #'.$_FILES['image']['error'];

		$res = $c->db->dml(
				'INSERT INTO '.$c->getNameTableImmagini().'(Oggetto, Immagine) VALUES(?,?)',
				[$_POST['id'], $_FILES['image']['name']]);
		echo ($res->errorCode() == 0) ? '' : '<pre>'.$res->errorInfo()[2].'</pre>';
	}
	if(!empty($_POST['etichetta']??NULL)) {
		$res = $c->db->dml(
			'INSERT INTO '.$c->getNameTableEtichette().'(Nome, Colore) VALUES(?,?)',
			[$_POST['etichetta'], $_POST['colore']]);
		echo ($res->errorCode() == 0) ? '' : '<pre>'.$res->errorInfo()[2].'</pre>';
	}

	$oggetto = $oggetto[0];
	$immagini = $c->db->ql('SELECT Immagine AS i FROM '.$c->getNameTableImmagini().' WHERE Oggetto = ?', [$_REQUEST['id']]);
	$etichette = $c->db->ql(
		'SELECT e.Nome, e.Colore 
		FROM '.$c->getNameTableEtichetteOggetti().' eo 
		JOIN '.$c->getNameTableEtichette().' e ON e.ID = eo.Etichetta
		WHERE eo.Oggetto = ?', [$_REQUEST['id']]);
	$etichette_tot = $c->db->ql(
		'SELECT * FROM '.$c->getNameTableEtichette().
		' WHERE ID NOT IN 
			(SELECT Etichetta FROM '.$c->getNameTableEtichetteOggetti().' WHERE Oggetto = ?)',
		[$_REQUEST['id']]);
?>
<html>
	<head>
		<!-- BOOTSTRAP -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	
		<script type="text/javascript" src="lib/jquery-3.4.1.min.js"></script>
		<script type="text/javascript" src="lib/jscolor.js"></script>

		<script defer type="text/javascript" src="./js/misc.js"></script>
		<script defer type="text/javascript" src="./js/oggetto.js"></script>
		<link href='./css/oggetto.css' rel='stylesheet' type='text/css'>
		<title>Oggetto #<?= $_REQUEST['id']?></title>
	</head>
	<body>
		<div class="container mb-1">
			<div class="row">
				<div class="col">
					<button onclick="window.close();">Indietro</button>
				</div>
				<div class="col">
					<button onclick="precedenteOggetto();">Precedente</button>
				</div>
				<div class="col">
					<button onclick="prossimoOggetto();">Prossimo</button>
				</div>
				<div class="col">
					<form id="formElimina" action="" method="POST">
						<input type="hidden" name="id" value="<?= $_REQUEST['id']?>">
						<input type="hidden" name="elimina">
						<input type="button" value="Elimina oggetto">
					</form>
				</div>
			</div>
		</div>

		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-4">
				
					<div id="tabellaDati">
						<?php
						foreach ($oggetto as $col => $val) {
							echo "<h5>$col:</h5>";
							if($col == 'ID') echo "<h5 name=\"$col\">$val</h5>";
							else			 echo "<textarea name=\"$col\">$val</textarea>";
						}
						?>
					</div>
					
				</div>
				<div class="col">
				
					<div class="container">
						<div class="row">
							<div class="col">
								<div id="immaginePreview">
									<?php
									$i = 0;
									foreach ($immagini as $immagine)
										echo '<img id="img_'.($i++).'" class="immagine" src="'.$c->ini['imgsDir'].'/'.$immagine['i'].'">';
									?>
								</div>
							</div>
						</div>
						<div class="row align-items-center">
							<div class="col">
								<div id="bottoniFoto" class="row">
									<script type="text/javascript"> var nImmagini = <?= count($immagini) ?>; var oggetto = <?= $_REQUEST['id'] ?>;</script>
								</div>
								<div class="row align-items-center">
									<button id="btnRemoveImg" style="display:<?= count($immagini) > 0 ? 'block' : 'none' ?>;" type="button" onclick="removeImage(selectedIndex);">Elimina immagine selezionata</button>
								</div>
							</div>
							<div class="col">
								<form id="formImmagine" action="" method="POST" enctype="multipart/form-data">
									<h3>Aggiungi immagine:</h3>
									<input type="hidden" name="request" value="addImage">
									<input type="hidden" name="id" value="<?= $_REQUEST['id']?>">
									<input type="file" name="image" value="Aggiungi immagine" onchange = "document.getElementById('formImmagine').submit();">
								</form>
							</div>
						</div>
						
					</div>
					
				</div>
			</div>

			<div class="row align-items-center mt-5">
				<div class="col">
					<h3>Etichette</h3>
				<?php
					if(count($etichette) == 0)
						echo '<h4>Nessuna etichetta associata</h4>';
					else {
						echo '<blockquote class="blockquote text-center">';
						foreach ($etichette as $etichetta)
							echo "<p class=\"mr-3 etichetta\"  style=\"background-color: #$etichetta[Colore];\">$etichetta[Nome]</p>";
						echo '</blockquote>';
					}
				?>
				</div>

				<div class="col">
					<div class="container">
						<div class="row">
							<div class="col">
								<h3>Etichette esistenti</h3>
								
								<div id="hints-etichette" class="list-group">
									<?php
										if(count($etichette_tot) != 0){
											echo '<blockquote class="blockquote text-center">';
											foreach ($etichette_tot as $etichetta) {
												echo '<div style="display:flex;">';
												echo "<button type=\"button\" style=\"background-color: #$etichetta[Colore];\" class=\"list-group-item list-group-item-action hint-etichetta\">$etichetta[Nome]</button>";
												echo '<button type="button" class="btn btn-danger" onclick="deleteLabel('.$etichetta['ID'].')">Elimina</button>';
												echo '</div>';
											}
											echo '</blockquote>';
										}
									?>
								</div>
							</div>
							<div class="col">
								<h3>Nuova etichetta</h3>
								<form id="formEtichette" action="" method="POST">
									<input type="hidden" name="id" value="<?= $_REQUEST['id']?>">
									<div class="container">
										<div class="row" style="margin-bottom: 15px;">
											Nome: <input id="fieldEtichetta" type="text" name="etichetta" required>
										</div>
										<div class="row" style="margin-bottom: 15px;">
											Colore: <input class="jscolor" name="colore" value="FFFFFF">
										</div>
										<div class="row">
											<input type="submit" value="Aggiungi">
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

			</div>

		</div>
	</body>
</html>