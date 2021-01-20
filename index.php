<?php
    include_once 'controls.php';
    $c = new Controls();

    if(!$c->isLogged()) $c->redirect('login.php');
?>
<html>

    <head>
        <!-- AG-GRID -->
        <script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.min.js"></script>
        
        <!-- JQUERY -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"
  				integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  				crossorigin="anonymous"></script>

        <!-- INDEX -->
        <script defer type="text/javascript" src="./js/misc.js"></script>
        <script defer type="text/javascript" src="js/index.js"></script>
        <link href='css/index.css' rel='stylesheet' type='text/css'>
    </haed>

    <body>
        <div id="navbar" class="h-spaced-flex">
			<!-- <div data-target="ricerca"><h1>Etichette</h1></div> -->
			<div data-target="colonne"><h1>Colonne</h1></div>
			<div data-target="report"><h1>Report</h1></div>
			<div><h1 onclick="newOggetto();">Nuovo oggetto</h1></div>
            <div data-target="readme"><h1>Readme</h1></div>
			<div>
				<form action="login.php" method="post">
					<h1 onclick="$(this).parent().submit();">Logout</h1>
					<input type="hidden" name="logout">
				</form>
			</div>
		</div>

		<div id="navbar-targets">
			<!-- <div data-value="ricerca">
				<h1>Filtra oggetti per etichetta</h1>
				<select name="etichetta">
				<?php
					/*$etichette = $c->db->ql('SELECT * FROM etichette');
					foreach($etichette as $e)
						echo '<option value="'.$e['ID'].'" style="background-color: #'.$e['Colore'].';">'.$e['Nome'].'</option>';*/
				?>
				</select>
				<div class="h-spaced-flex">
					<button onclick="refreshTableData({etichetta:$(this).parent().parent().children('select').val()});">Cerca oggetti per etichetta</button>
					<button onclick="refreshTableData();">Reset</button>
				</div>
			</div> -->
			<div data-value="colonne">
				<?php
					$cols = $c->describeTable($c->getNameTableOggetti());
					$prefCols = $c->getPreferenza('columns');
					foreach($cols as $col)
						echo "<div><input type=\"checkbox\" name=\"$col[Field]\" ".(is_null($prefCols)||in_array($col['Field'], $prefCols)?'checked="checked"':'')." class=\"colonna\" id=\"col$col[Field]\"><label for=\"col$col[Field]\">$col[Field]</label></div>";
				?>
			</div>
			<div data-value="report">
				<button type="button" onclick="setForPrint();">Stampa</button>
				<button type="button" onclick="exportCSV();">Esporta in CSV</button>
			</div>
            <div data-value="readme">
                <h3>Azioni:</h3>
                <ul>
                    <li>Con il doppio click sull'ID dell'oggetto, si accede alla sua pagina dedicata</li>
					<li>Per lo scroll orizzontale utilizzare SHIFT+scroll</li>
                </ul>
			</div>
		</div>

        <div id="table" class="ag-theme-balham-dark"></div>
    </body>

</html>