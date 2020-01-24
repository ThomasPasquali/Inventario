<?php 
	include_once 'controls.php';
	$c = new Controls();
	
	if(!$c->isLogged()) $c->redirect('login.php');
	
	$colonne = $c->describeTable($c->getNameTableOggetti());
	
	if(isset($_POST['inserimento'])) {
		unset($_POST['inserimento']);
		$sql = 'INSERT INTO '.$c->getNameTableOggetti().' ('.implode(',', array_keys($_POST)).') VALUES (?'.str_repeat(',?', count($_POST)-1).')';
		$values = array_values($_POST);
		for ($i = 0; $i < count($values); $i++)
			if(strlen($values[$i]) == 0)
				$values[$i] = NULL;
		$resInsert = $c->db->dml($sql, $values);
	}
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
		<link href="lib/tabulator/dist/css/tabulator_midnight.min.css" rel="stylesheet">
		
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
		
		<?php
		if(isset($resInsert) && $resInsert->errorCode() != 0) {
		?>
		<div class="alert alert-error alert-dismissible fade show" role="alert" style="color: red;">
			<strong>Errore nell'inserimento: </strong><?= $resInsert->errorInfo()[2] ?>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<?php 	
		}
		?>
	
		<div id="logout">
			<p>Ciao <?= $_SESSION['username'] ?></p>
			<form action="login.php" method="post">
				<button type="submit" name="logout" class="btn btn-info">Logout</button>
			</form>
		</div>
		
		<div id="accordion">
			<div class="card">
				<div class="card-header" id="headingMenu">
					<h5 class="mb-0">
						<button class="btn btn-link" data-toggle="collapse" data-target="#collapseMenu" aria-expanded="true" aria-controls="collapseMenu">
							Men&ugrave; selezione
						</button>
					</h5>
				</div>
				<div id="collapseMenu" class="collapse show" aria-labelledby="headingMenu" data-parent="#accordion">
					<div class="card-body">
						<div id="menu">
							<form id="form-aggiorna">
							<input type="hidden"  name="request" value="query">
							<input type="hidden"  name="col_ID" value="on">
							
							<div class="container">
							
								<div class="row">
				    				<div class="col">
				    					<label># risultati</label>
										<input type="number" name="max_results" value="<?= $c->getPreferenza('max_results') ?>">
				    				</div>
				    				
				    				<label>Colonne:</label>
				    				<?php
											$colonnePreferenze = $c->getPreferenza('columns');
											$i = 0;
											foreach ($colonne as $colonna) 
												if($colonna['Key'] != 'PRI') {
													if($i % 5 == 0) echo '<div class="col">';
													$toCheck = is_null($colonnePreferenze);
													$toCheck = $toCheck || in_array($colonna['Field'], $colonnePreferenze);
													echo '<div class="custom-control custom-checkbox">';
													echo '	<input type="checkbox" name="col_'.$colonna['Field'].'" class="custom-control-input" id="col_'.$colonna['Field'].'"'.($toCheck?' checked="checked"':'').'>';
													echo '	<label class="custom-control-label" for="col_'.$colonna['Field'].'">'.$colonna['Field'].'</label>';
													echo '</div>';
													if(($i+1) % 5 == 0) echo '</div>';
													$i++;
												}
										?>
				    				
									<div class="col">
										<button id="btnAggiorna" type="button" class="btn btn-success">Aggiorna</button>
									</div>
								</div>
								
							</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			
			<div class="card">
				<div class="card-header" id="headingInserimento">
					<h5 class="mb-0">
						<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseInserimento" aria-expanded="false" aria-controls="collapseInserimento">
						Men&ugrave; inserimento
						</button>
					</h5>
				</div>
				<div id="collapseInserimento" class="collapse" aria-labelledby="headingInserimento" data-parent="#accordion">
					<div class="card-body">
						<form action="" method="post">
							<div class="container">
								<div class="row">
				    				<?php
				    					$i = 0;
										foreach ($colonne as $colonna)
											if($colonna['Key'] != 'PRI') {
												if($i % 5 == 0) echo '<div class="col inserimento">';
												echo "<label>$colonna[Field]</label><input type=\"".$c->changeSQLtoHTMLtype($colonna['Type'])."\" name=\"$colonna[Field]\">";
												if(($i+1) % 5 == 0) echo '</div>';
												$i++;
											}
										if($i % 5 != 0) echo '</div>';
									?>
				    			</div>
				    		</div>
				    		<input id="btnInserisci" type="submit" name="inserimento" value="Inserisci">
						</form>
					</div>
				</div>
			</div>
			
		</div>

		<div id="tabella"></div>
		
	</body>
</html>