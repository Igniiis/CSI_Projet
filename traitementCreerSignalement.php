<?php
	$bdd = pg_connect("host=localhost port=5432 dbname=script user=postgres password=admin");

	$probleme = $_POST['probleme'];
    $id_rue = $_POST['id_rue'];
    $numero_maison_proche = $_POST['numero_maison_proche'];
    $intervalle_numero_debut = $_POST['intervalle_numero_debut'];
    $intervalle_numero_fin = $_POST['intervalle_numero_fin'];
    $description_probleme = $_POST['description_probleme'];
    $niveau_urgence = $_POST['niveau_urgence'];
    $date_signalement = date("Y-m-d");
	$compteur_signalement_anonyme = 0;
	$etat = 'pas réalisé';

	$result = pg_query($bdd, "
		INSERT INTO signalement (probleme, id_rue, numero_maison_proche, intervalle_numero_debut, intervalle_numero_fin, description_probleme, date_signalement) VALUES (
				'$probleme',
				'$id_rue',
				'$numero_maison_proche',
				'$intervalle_numero_debut',
				'$intervalle_numero_fin',
				'$description_probleme',
				'$date_signalement'
			)"
	);

	if ($result) {
		$messageValidation = 'Signalement créé avec succès !';
	    include('pageAccueil.php');
	}
	else {
		$messageErreur = 'Erreur lors de la création du signalement !';
	    include('pageAccueil.php');
	}

    pg_close($bdd);
?>