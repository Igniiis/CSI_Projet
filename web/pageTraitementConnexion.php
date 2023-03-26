<?php
	$host = "localhost";
	$user = "root";
	$password = "";
	$dbname = "projetcsi";
	$bdd = mysqli_connect($host, $user, $password, $dbname);

	if (!$bdd) {
	    die("La connexion a échoué: " . mysqli_connect_error());
	}




	$id = $_POST['id'];
	$mdp = $_POST['mdp'];
	$result = mysqli_query($bdd, "SELECT * FROM compte WHERE id='$id' AND mdp='$mdp'");
	if (mysqli_num_rows($result) > 0) {
	    header('Location: pageAccueil.php');
		exit();
	}
	else {
	    $messageErreur = 'Identifiant ou mot de passe incorrect.';
	    include('pageConnexion.php');
	}

	
	
	mysqli_close($bdd);
?>