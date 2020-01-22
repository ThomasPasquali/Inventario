<?php
	header('Content-type: text/txt');
	echo isset($_GET['p']) ? password_hash($_GET['p'], PASSWORD_DEFAULT) : 'Fornire il parametro "p" nel URL ex. http://www.sito.it?p=password';