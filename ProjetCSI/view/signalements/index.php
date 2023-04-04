<link rel="stylesheet" href="<?php echo BASE_URL;?>/css/form.css" />

<div id="formulaire_CreerSignalement" class="formulaire">
			<h2>Créer un signalement</h2><br>
			<form action="<?php echo BASE_URL.'/signalements';?>" method="post">
				<label for="probleme">Problème :</label>
				<select id="selectProbleme" onchange="selectProblemeAutre();" name="probleme" required>
					<option value="panne d'éclairage public">Panne d'éclairage public</option>
					<option value="chaussée abîmée">Chaussée abîmée</option>
					<option value="trottoir abîmé">Trottoir abîmé</option>
					<option value="égout bouché">Égout bouché</option>
					<option value="arbre à tailler">Arbre à tailler</option>
					<option value="voiture ventouse">Voiture ventouse</option>
					<option value="autres">Autres</option>
				</select><br>

				<div id="descriptionProbleme">

				</div>

				<div class="boutonsBloc">
					<label for="id_rue">Rue :</label>
					<select id="selectRue" name="id_rue" required>
						<?php foreach($rues as $r): ?>
                			<option value="<?php echo $r->id_rue;?>" type="number"><?php echo $r->nom_rue;?></option>
            			<?php endforeach; ?>
					</select><br>
				
					

					<div id="partie_numero">

						<div>
							<input type="radio" id="radio_num" onchange="affichageNumero('num');" name="drone" checked>
							numero de maison
						</div>
						<div>
							<input type="radio" id="radio_intervalle" onchange="affichageNumero('intervalle');" name="drone" >
							intervalle
						</div>
						<!-- Par défaut c'est l'affichage pour le numéro de la maison proche, mais ca change grace au radio button -->
						<div id="numeros">
							<label for="numero_maison_proche">Numéro de maison proche :</label>
							<input class="nombres" type="text" name="numero_maison_proche" required>
						</div>

					</div>

				</div>

				
				<div id="partieCoordonnee">
					<label for="checking">J'accepte de laisser mes coordonnées</label>
					<input type="checkbox" id="checkin" onclick="afficheCoord();" name="checking">
					
					<div id="corpsCoord" style="display: none;">
						
						<label for="nom_habitant">Nom</label>
						<input id="nom_h" type="text" name="nom_habitant" value=" " required>
						
						<label for="prenom_habitant">Prenom</label>
						<input id="prenom_h" type="text" name="prenom_habitant" value=" " required>

						<label for="mail">Adresse email</label>
						<input id="mail_h" type="email" name="mail" value=" " required>

						<label for="num_adresse_habitant">Adresse :</label>
						<?php echo $this->Form->input('num_adresse_habitant','numéro') ?>
						<label for="id_rue_habitant">Rue :</label>
						<select id="selectRue" name="id_rue_habitant" required>
							<?php foreach($rues as $r): ?>
								<option value="<?php echo $r->id_rue;?>" type="number"><?php echo $r->nom_rue;?></option>
							<?php endforeach; ?>
						</select><br>
							
						<?php echo $this->Form->input('numero_portable','numéro de tel. portable') ?>
						<?php echo $this->Form->input('numero_fixe','numéro de tel. fixe') ?>
					</div>
				</div>
				<br>
				<input type="submit" value="Envoyer">
			</form>
		</div>

		<script src="<?php echo BASE_URL.'/js/signalement.js';?>"></script>
		<script>
function afficheCoord() {
    let a = document.getElementById('checkin');

	if (a.checked) {
		//on affiche la partie coordonnée
		document.getElementById('corpsCoord').style.display = 'block';
		document.getElementById('nom_h').value = null;
		document.getElementById('prenom_h').value = null;
		document.getElementById('mail_h').value = null;
		}else{
		//on supprime la partie coordonnée
		document.getElementById('corpsCoord').style.display = 'none';
		document.getElementById('nom_h').value=" ";
		document.getElementById('prenom_h').value=" ";
		document.getElementById('mail_h').value=" ";
	}
}
		</script>