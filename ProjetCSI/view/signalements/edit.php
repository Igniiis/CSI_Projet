<link rel="stylesheet" href="<?php echo BASE_URL;?>/css/form.css" />

<div id="formulaire_CreerSignalement" class="formulaire">

			<h2>Créer un signalement</h2><br>
			<form action="<?php echo BASE_URL.'/signalements/edit';?>" method="post">
				<label for="probleme">Problème *:</label>
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
					<label for="id_rue">Rue *:</label>
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
							<label for="numero_maison_proche">Numéro de maison proche *:</label>
							<input class="nombres" type="number" name="numero_maison_proche" min=1 required>
						</div>

					</div>

				</div>

				
				<div id="partieCoordonnee">
					<input type="hidden" id="compteur_signalement_anonyme" name="compteur_signalement_anonyme" value=1>
					<label for="checking">J'accepte de laisser mes coordonnées</label>
					<input type="checkbox" id="checkin" onclick="afficheCoord();" name="checking">
					
					<div id="corpsCoord" style="display: none;">
						
						<label for="nom_habitant">Nom *</label>
						<input id="nom_h" type="text" name="nom_habitant" value="">
						
						<label for="prenom_habitant">Prenom *</label>
						<input id="prenom_h" type="text" name="prenom_habitant" value="">

						<label for="mail">Adresse email *</label>
						<input id="mail_h" type="email" name="mail" value="">


						<label for="num_adresse_habitant">Adresse :</label>
						
						<label for="num_h">numéro *</label>
						<input id="num_h" type="text" name="num_adresse_habitant" value="" >
						<label for="id_rue_habitant">Rue *:</label>
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
		document.getElementById('compteur_signalement_anonyme').value = 0;
		document.getElementById('num_h').required = true;
		document.getElementById('nom_h').required = true;
		document.getElementById('prenom_h').required = true;
		document.getElementById('mail_h').required = true;
		}else{
		//on supprime la partie coordonnée
		document.getElementById('corpsCoord').style.display = 'none';
		document.getElementById('compteur_signalement_anonyme').value = 1;
		document.getElementById('num_h').required = false;
		document.getElementById('nom_h').required = false;
		document.getElementById('prenom_h').required = false;
		document.getElementById('mail_h').required = false;
	}
}
		</script>