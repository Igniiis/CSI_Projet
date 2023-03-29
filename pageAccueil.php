<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="fichier.css">
		<title>Page d'accueil</title>
	</head>

	<body>
		<?php
			if (!isset($utilisateur)) {
		        echo "<button onclick='afficherFormulaire_Connexion()'>Connexion</button>";
			}
			else if (isset($utilisateur) && $utilisateur->type == "responsable") {
				echo "<p>Utilisateur connecté : " . $utilisateur->nom . " " . $utilisateur->prenom . " " . $utilisateur->type . "</p>";
			}
		?>





		<div class="bandeau">
			<h1>Projet de CSI</h1>
		</div>




		<?php 
			if (isset($messageErreurConnexion)) echo '<div id="formulaire_Connexion" class="formulaire" style="display: block;">';
			else echo '<div id="formulaire_Connexion" class="formulaire" style="display: none;">';
		?>
			<h2>Connexion</h2><br>
			<form action="traitementConnexion.php" method="post">
				<label for="identifiant">Identifiant :</label>
				<input type="text" name="identifiant" required><br>

				<label for="motdepasse">Mot de passe :</label>
				<input type="password" name="motdepasse" required><br>

				<input type="submit" value="Connexion">

				<?php if (isset($messageErreurConnexion)): ?>
					<p id="MsgErreurConnexion"><?php echo $messageErreurConnexion; ?></p>
				<?php endif; ?>
			</form>
		</div>
		




		<div id="boutonsSignalement" class="boutons">
			<button onclick="afficherFormulaire_CreerSignalement()">Créer un signalement</button>
			<button onclick="afficherFormulaire_ModifierSignalement()">Modifier un signalement</button>
			<?php
				if (isset($utilisateur) && $utilisateur->type == "responsable") echo '<button onclick="afficherConsultation()">Consulter les signalements</button>'
			?>
		</div>

		<?php if (isset($messageValidation)): ?>
			<p id="MsgValidation"><?php echo $messageValidation; ?></p>
		<?php endif; ?>
		<?php if (isset($messageErreur)): ?>
			<p id="MsgErreur"><?php echo $messageErreur; ?></p>
		<?php endif; ?>





		<div id="formulaire_CreerSignalement" class="formulaire" style="display: none;">
			<h2>Créer un signalement</h2><br>
			<form action="traitementCreerSignalement.php" method="post">
				<label for="probleme">Problème :</label>
				<select name="probleme" required>
					<option value="panne d'éclairage public">Panne d'éclairage public</option>
					<option value="chaussée abîmée">Chaussée abîmée</option>
					<option value="trottoir abîmé">Trottoir abîmé</option>
					<option value="égout bouché">Égout bouché</option>
					<option value="arbre à tailler">Arbre à tailler</option>
					<option value="voiture ventouse">Voiture ventouse</option>
					<option value="autres">Autres</option>
				</select><br>

				<div class="boutonsBloc">
					<label for="id_rue">Id de rue :</label>
					<input type="number" name="id_rue" required  min="1" step="1"><br>
				
					<label for="numero_maison_proche">Numéro de maison proche :</label>
					<input type="number" name="numero_maison_proche" required  min="1" step="1"><br>
				</div>

				<div class="boutonsBloc">
					<label for="intervalle_numero_debut">Intervalle numéro début :</label>
					<input type="number" name="intervalle_numero_debut" required min="1" step="1"><br>

					<label for="intervalle_numero_fin">Intervalle numéro fin :</label>
					<input type="number" name="intervalle_numero_fin" required min="1" step="1"><br>
				</div>

				<label for="description_probleme">Description du problème :</label>
				<textarea id="champDescriPb" name="description_probleme" required maxlength="500"></textarea><br>

				<label for="niveau_urgence">Niveau d'urgence :</label>
				<select name="niveau_urgence" required>
					<option value="faible">faible</option>
					<option value="moyen">moyen</option>
					<option value="élevé">élevé</option>
					<option value="très urgent">très urgent</option>
				</select><br>

				<input type="submit" value="Envoyer">
			</form>
		</div>





		<?php 
			if (isset($utilisateur) && $utilisateur->type == "responsable") {
				
				echo '<div id="Consulter_Signalement" style="display: none;">';
					
						$bdd = pg_connect("host=localhost port=5432 dbname=script user=postgres password=admin");

						$signalements = pg_query($bdd, "SELECT * FROM signalement");
						
						while ($row = pg_fetch_assoc($signalements)) {
							echo "Id du signalement : " . $row['id_signalement'] . "<br>";
							echo "Problème : " . $row['probleme'] . "<br>";
							echo "Id de rue : " . $row['id_rue'] . "<br>";
							echo "Numéro de maison proche : " . $row['numero_maison_proche'] . "<br>";
							echo "Intervalle numéro début : " . $row['intervalle_numero_debut'] . "<br>";
							echo "Intervalle numéro fin : " . $row['intervalle_numero_fin'] . "<br>";
							echo "Description du problème : " . $row['description_probleme'] . "<br>";
							echo "Niveau d'urgence : " . $row['niveau_urgence'] . "<br>";
							echo "Date du signalement : " . $row['date_signalement'] . "<br>";
							echo "Compteur du signalement : " . $row['compteur_signalement_anonyme'] . "<br>";
							echo "Etat : " . $row['etat'] . "<br>";
							echo "Date de modification : " . $row['date_modification'] . "<br>";
							echo "Description de résolution : " . $row['description_resolution'] . "<br>";
							echo "Date de résolution : " . $row['date_resolution'] . "<br>";
							echo "Id de l'agent : " . $row['id_agent'] . "<br>";
							echo "<br>";
						}
					
				echo '</div>';
			}
		?>



		<script>
			function afficherFormulaire_Connexion() {
				document.getElementById("formulaire_Connexion").style.display = "block";
			}
			function afficherFormulaire_CreerSignalement() {
				document.getElementById("Consulter_Signalement").style.display = "none";
				document.getElementById("formulaire_CreerSignalement").style.display = "block";
			}
			function afficherConsultation() {
				document.getElementById("formulaire_CreerSignalement").style.display = "none";
				document.getElementById("Consulter_Signalement").style.display = "block";
			}
		</script>
	</body>
</html>