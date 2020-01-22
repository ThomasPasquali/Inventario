<?php 
include_once 'controls.php';
$c = new Controls();

if(!$c->isLogged()) $c->redirect('login.php');

$colonne = $c->describeTable('oggetti');
	
?>
<html>
	<head>
	
		<!-- BOOTSTRAP -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		
		<!-- JQUERY -->
		<script type="text/javascript" src="lib/jquery-3.4.1.min.js"></script>
		<script type="text/javascript" src="lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>
		
		<!-- TABULATOR -->
		<link href="lib/tabulator/dist/css/tabulator.min.css" rel="stylesheet">
		<script type="text/javascript" src="lib/tabulator/dist/js/tabulator.min.js"></script>
		
		<link href='css/index.css' rel='stylesheet' type='text/css'>
		<script type="text/javascript">
			$(document).ready(function() {
				$.getScript("js/misc.js");
			    $.getScript("js/index.js");
			});
		</script>
		
		<title>Inventario Home</title>
	</head>
	<body>
	
		<div id="logout">
			<p>Ciao <?= $_SESSION['username'] ?></p>
			<form action="login.php" method="post">
				<button type="submit" name="logout" class="btn btn-info">Logout</button>
			</form>
		</div>
		
		<div id="menu">
			<form id="form-aggiorna">
			<input type="hidden"  name="request" value="query">
			
			<div class="container">
			
				<div class="row">
    				<div class="col">
    					<label># risultati</label>
						<input type="number" name="max_results" value="<?= $c->getPreferenza('max_results') ?>">
    				</div>
    				
					<div class="col">
						<button id="btnAggiorna" type="button" class="btn btn-success">Aggiorna</button>
					</div>
				</div>
				
				<div class="row">
					<div class="col">
      					<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseColonne" aria-expanded="false" aria-controls="collapseColonne">Colonne</button>
						<div class="collapse" id="collapseColonne">
							<?php
								$colonnePreferenze = $c->getPreferenza('colonne');
								foreach ($colonne as $colonna) {
									$toCheck = is_null($colonnePreferenze);
									$toCheck = $toCheck || in_array($colonna['Field'], $colonnePreferenze);
									echo '<div class="custom-control custom-checkbox">';
									echo '	<input type="checkbox" class="custom-control-input" id="col_'.$colonna['Field'].'"'.($toCheck?' checked="checked"':'').'>';
									echo '	<label class="custom-control-label" for="col_'.$colonna['Field'].'">'.$colonna['Field'].'</label>';
									echo '</div>';
								}
							?>
						</div>
					</div>
					
					<div class="col"></div>
					<div class="col"></div>
	  			</div>
			</div>
				
			
			</form>
		</div>

		<div id="tabella"></div>
		
	</body>
</html>