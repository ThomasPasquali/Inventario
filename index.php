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
			<div data-target="ricerca"><h1>Ricerca WIP</h1></div>
			<div data-target="filtri"><h1>Filtri WIP</h1></div>
			<div data-target="ordinamenti"><h1>Ordinamenti WIP</h1></div>
			<div data-target="colonne"><h1>Colonne WIP</h1></div>
			<div data-target="report"><h1>Report WIP</h1></div>
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
			<div data-value="ricerca">
				<h1>Oggetti per etichetta</h1>
				<select name="etichetta">
				<?php
					$etichette = $c->db->ql('SELECT * FROM etichette');
					foreach($etichette as $e)
						echo '<option value="'.$e['ID'].'" style="background-color: #'.$e['Colore'].';">'.$e['Nome'].'</option>';
				?>
				</select>
				<div class="h-spaced-flex">
					<button onclick="aggiornaTabella({etichetta:$(this).parent().parent().children('select').val()});">Cerca oggetti per etichetta</button>
					<button onclick="aggiornaTabella();">Reset</button>
				</div>
			</div>
			<div data-value="filtri">
				<ol></ol>
				<div class="h-spaced-flex">
					<button onclick="applyFiltri();">Applica</button>
					<button onclick="resetFiltri();">Reset</button>
					<button onclick="addFiltro();">Aggiungi filtro</button>
				</div>
			</div>
			<div data-value="ordinamenti">
				<ol></ol>
				<div class="h-spaced-flex">
					<button onclick="applyOrdinamenti();">Applica</button>
					<button onclick="resetOrdinamenti();">Reset</button>
					<button onclick="addOrdinamento();">Aggiungi ordinamento</button>
				</div>
			</div>
			<div data-value="colonne">
			</div>
			<div data-value="report">
				<button type="button" onclick="printTable();">Stampa dati tabella</button>
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