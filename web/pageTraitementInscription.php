<?php
	$host = "localhost";
	$user = "root";
	$password = "";
	$dbname = "projetcsi";
	$bdd = mysqli_connect($host, $user, $password, $dbname);

	if (!$bdd) {
	    die("La connexion a échoué: " . mysqli_connect_error());
	}




	$idInscription = $_POST['idInscription'];
	$mdpInscription = $_POST['mdpInscription'];

	if (mysqli_num_rows(mysqli_query($bdd, "SELECT * FROM compte WHERE id='$idInscription' AND mdp='$mdpInscription'")) == 0) {
		mysqli_query($bdd, "INSERT INTO compte (id, mdp) VALUES ('$idInscription', '$mdpInscription')");
		$messageInscriptionValidee = 'Inscription réussie !<br>Connectez-vous !';
		include('pageConnexion.php');
	}
	else {
		$messageInscriptionRefusee = 'Id ou mdp déjà utilisé';
		include('pageConnexion.php');
	}
	

	
	mysqli_close($bdd);
?>