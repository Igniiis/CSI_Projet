<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="fichierCSS.css">
		<title>Page de connexion</title>
	</head>

	<body>
		<form action="pageTraitementConnexion.php" method="post">
			<label for="id">Identifiant :</label>
			<input type="text" id="id" name="id"><br><br>

			<label for="mdp">Mot de passe :</label>
			<input type="password" id="mdp" name="mdp"><br><br>

			<input type="submit" value="Connexion">

			<?php if (isset($messageErreur)): ?>
		    	<p id="erreur"><?php echo $messageErreur; ?></p>
			<?php endif; ?>
		</form>

		<form action="pageTraitementInscription.php" method="post">
			<label for="idInscription">Identifiant :</label>
			<input type="text" id="idInscription" name="idInscription"><br><br>

			<label for="mdpInscription">Mot de passe :</label>
			<input type="password" id="mdpInscription" name="mdpInscription"><br><br>

			<input type="submit" value="Inscription">

			<?php if (isset($messageInscriptionValidee)): ?>
		    	<p id="inscriptionValidee"><?php echo $messageInscriptionValidee; ?></p>
			<?php endif; ?>
			<?php if (isset($messageInscriptionRefusee)): ?>
		    	<p id="inscriptionRefusee"><?php echo $messageInscriptionRefusee; ?></p>
			<?php endif; ?>
		</form>
	</body>
</html>