<?php 
	include_once 'controls.php';
	$c = new Controls();
	
	if(!$c->isLogged()) $c->redirect('login.php');
?>
<html>
	<head>
	
		<!-- BOOTSTRAP -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		
		<!-- JQUERY -->
		<script type="text/javascript" src="./lib/jquery-3.4.1.min.js"></script>
		<script type="text/javascript" src="./lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>
		
		<!-- TABULATOR -->
		<link href="./lib/tabulator/dist/css/tabulator.min.css" rel="stylesheet">
		<link href="./lib/tabulator/dist/css/tabulator_midnight.min.css" rel="stylesheet">
		<script type="text/javascript" src="./lib/tabulator/dist/js/tabulator.min.js"></script>
		
		<!-- INDEX -->
		<script defer type="text/javascript" src="./js/misc.js"></script>
		<script defer type="text/javascript" src="./js/index.js"></script>
		<link href='css/index.css' rel='stylesheet' type='text/css'>
		
		<title>Inventario</title>
	</head>
	<body>

	<div class="container-fluid">
		<div class="row">
			<div class="col-10">

				<button id="btnCollapseMenu" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseMenu" aria-expanded="false" aria-controls="collapseMenu">Men&ugrave;</button>
				<div class="collapse" id="collapseMenu">
					<div class="card card-body p-1 m-1">
						<div class="container-fluid p-1 m-1">
							<div class="row">

								<div class="col">
									<button id="btnCollapseColonne" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseColonne" aria-expanded="false" aria-controls="collapseColonne">Colonne</button>
									<div class="collapse" id="collapseColonne">
										<div id="colonne" class="card card-body">
										<?php
											$cols = $c->describeTable($c->getNameTableOggetti());
											$prefCols = $c->getPreferenza('columns');
											foreach($cols as $col)
												echo "<div class=\"custom-control custom-checkbox\">
														<input type=\"checkbox\" name=\"$col[Field]\" ".(is_null($prefCols)||in_array($col['Field'], $prefCols)?'checked="checked"':'')." class=\"custom-control-input colonna\" id=\"col$col[Field]\">
														<label class=\"custom-control-label\" for=\"col$col[Field]\">$col[Field]</label>
											  		</div>";
										?>
										</div>
									</div>
								</div>

								<div class="col">
									<div class="container-fluid">
										<div class="row">
											<button type="button" class="btn btn-warning" onclick="printTable();">Stampa dati</button>
										</div>
										<div class="row">
											<button type="button" class="btn btn-warning">Stampa immagini (WIP)</button>
										</div>
									</div>
								</div>
								
								<div class="col">
									<button type="button" class="btn btn-info" onclick="newOggetto();">Nuovo oggetto</button>
								</div>

								<div class="col">
									<button id="btnAggiornaTabella" type="button" class="btn btn-success">Aggiorna tabella</button>
								</div>

							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="col">
				<div class="float-right">
					<p class="mr-2">Benvenuto <?= $_SESSION['username'] ?></p>
					<form action="login.php" method="post">
						<button type="submit" name="logout" class="btn btn-info">Logout</button>
					</form>
				</div>
			</div>

		</div>
		<div class="row">
			<div id="bottomDiv">
				<div id="tabella"></div>
			</div>
		</div>
	</div>
		
	</body>
</html>