<link rel="stylesheet" href="<?php echo BASE_URL;?>/css/form.css" />
<div>
	<h1><?php
		if ($id == ''){
			echo 'Créer un article';
		}else{
			echo 'Editer un article';
		}
	?></h1>
</div>
<!-- id_signalement,probleme,numero_maison_proche,intervalle_numero_debut,intervalle_numero_fin,description_probleme,niveau_urgence,date_signalement,compteur_signalement_total,etat,nom_rue -->
<form action="<?php echo BASE_URL.'/admin/signalements/edit/'.$id ;?>" method="post">
	
	<?php echo $this->Form->input('id_signalement',null,array('type' => 'hidden')) ?>
	<?php echo $this->Form->input('probleme','Titre',array('type' => 'select', 'options'=>array(
		"panne d'éclairage public", 'chaussée abîmée', 'trottoir abîmé', 'égout bouché', 'arbre à tailler', 'voiture ventouse', 'autres'
	)));?>
	<?php echo $this->Form->input('description_probleme','description autre', array('type' => 'textarea'))?>
	
	<br>
	<?php echo $this->Form->input('numero_maison_proche','numero de la maison la plus proche'); ?>
	
	<br>
	<label>Intervalle de numéros</label>
	<?php echo $this->Form->input('intervalle_numero_debut', 'début'); ?>
	<?php echo $this->Form->input('intervalle_numero_fin', 'fin'); ?>

	<label for="id_rue">Rue :</label>
	<select id="selectRue" name="id_rue" required>
		<?php foreach($rues as $r): ?>
            <option value="<?php echo $r->id_rue;?>" type="number"><?php echo $r->nom_rue;?></option>
        <?php endforeach; ?>
	</select><br>


	<div class="actions">
		<input type="submit" class="btn primary" value="Envoyer">
	</div>
</form>