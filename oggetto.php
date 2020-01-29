<?php 
	include_once 'controls.php';
	$c = new Controls();
	
	if(isset($_FILES['image'])) {
		move_uploaded_file(
				$_FILES['image']['tmp_name'],
				$c->ini['imgsDir'].'/'.$_FILES['image']['name']);
		$res = $c->db->dml(
				'INSERT INTO '.$c->getNameTableImmagini().'(Oggetto, Immagine) VALUES(?,?)',
				[$_POST['id'], $_FILES['image']['name']]);
		echo ($res->errorCode() == 0) ? '' : '<pre>'.$res->errorInfo()[2].'</pre>';
	}
	
	if(!$c->isLogged()) $c->redirect('login.php');
	if(isset($_REQUEST['id']))
		$oggetto = $c->db->ql('SELECT * FROM '.$c->getNameTableOggetti().' WHERE ID = ?', [$_REQUEST['id']]);
	if(!isset($_REQUEST['id']) || count($oggetto) < 1) {
		echo 'Fornire un valido paramentro id';
		exit();
	}
	$oggetto = $oggetto[0];
	$immagini = $c->db->ql('SELECT Immagine AS i FROM '.$c->getNameTableImmagini().' WHERE Oggetto = ?', [$_REQUEST['id']]);

?>
<html>
	<head>
		<!-- BOOTSTRAP -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	
		<script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
	
		<script type="text/javascript" src="lib/jquery-3.4.1.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() { $.getScript("js/oggetto.js"); $.getScript("js/misc.js"); });
		</script>
		<link href='css/oggetto.css' rel='stylesheet' type='text/css'>
		<title>Oggetto #<?= $_REQUEST['id']?></title>
	</head>
	<body>
		<button onclick="window.close();" style="margin-bottom: 15px;">Indietro</button>
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-4">
				
					<div id="tabellaDati" class="container">
						<?php 
						foreach ($oggetto as $col => $val)
							echo "<div class=\"row\"><div class=\"col\"><h5>$col:</h5></div><div class=\"col\"><h5><strong>$val</strong></h5></div></div>";
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
								<form action="" method="POST" enctype="multipart/form-data">
									<input type="hidden" name="request" value="addImage">
									<input type="hidden" name="id" value="<?= $_REQUEST['id']?>">
									<input type="file" name="image" value="Aggiungi immagine"><br>
									<input type="submit" value="Invia">
								</form>
							</div>
						</div>
						
					</div>
					
				</div>
			</div>
		</div>
	</body>
</html>