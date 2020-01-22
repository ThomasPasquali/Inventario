<?php 
include_once 'controls.php';
$c = new Controls();

if(isset($_POST['username']) && isset($_POST['password'])) {
	if($c->login($_POST['username'], $_POST['password']))
		$c->redirect('index.php');
	else
		$error = 'Credenziali errate';
}

if(isset($_POST['logout'])) $c->logout();

?>
<html>
	<head>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:700,600' rel='stylesheet' type='text/css'>
		<link href='css/login.css' rel='stylesheet' type='text/css'>
		<title>Inventario Login</title>
	</head>
	<body>
		<form method="post" action="">
			<div class="box">
			<h1>Inventario</h1>
				<input type="text" name="username" value="<?= $_POST['username']??'' ?>" class="field" placeholder="Username">
				<input type="password" name="password" class="field" placeholder="Password">
				<?= isset($error) ? "<h2 class=\"error\">$error</h2>" : '' ?>
				<div class="btn"><input type="submit" value="Accedi"></div>
			</div>
		</form>
	</body>
</html>