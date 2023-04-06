<link rel="stylesheet" href="<?php echo BASE_URL;?>/css/form.css" />
<div>
	<h1><?php
		$val;
		if ($id == ''){
			$val = false;
			echo 'Créer un article';
		}else{
			$val = true;
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
	<?php echo $this->Form->input('intervalle_numero_debut', 'début', array('type' => 'number')); ?>
	<?php echo $this->Form->input('intervalle_numero_fin', 'fin', array('type' => 'number')); ?>

	<label for="id_rue">Rue :</label>
	<select id="selectRue" name="id_rue" required>
		<?php foreach($rues as $r): ?>
            <option value="<?php echo $r->id_rue;?>" type="number"><?php echo $r->nom_rue;?></option>
        <?php endforeach; ?>
	</select><br>


	<?php if(!$val) :?>
		<input type="hidden" name="compteur_signalement_anonyme" value=1>
	<?php else :?>
		<?php //echo $this->Form->input('compteur_signalement_anonyme',null,array('type' => 'hidden'));?>
	<?php endif; ?>



	<!-- Partie plus complexe -->

	<?php echo $this->Form->input('etat','état',array('type' => 'select', 'options'=>array(
		"pas réalisé", 'en cours', 'réalisé'
	)));?>

	<div id="resolv">
		<?php 
		//if($id!=''){
			//if($this->request->data->etat=='réalisé'){
				echo $this->Form->input('date_resolution','Date de résolution (si résolu)',array('type' => 'date'));
				echo $this->Form->input('description_resolution','Description de la résolution', array('type' => 'textarea'));
			//}
		//}
		?>
	</div>

	<?php 
		if ($val){
			echo '<a href="'.BASE_URL.'/admin/signalements/merge/'.$this->request->data->id_signalement.'"> Fusionner </a> <br><br>';
		}
	?>

	
	<?php 
		if($val){
		if(isset($agent)){
			echo '<span> Dernière modification effectué par '.$agent->nom_agent.' '.$agent->prenom_agent.' le : <br>'.$this->request->data->date_modification.'</span>';
		}else{
			echo '<span> Ce signalement n\'a jamais été modifié </span>';
		}
		echo '<br> <span> Date de création du signalement : <br> '.$this->request->data->date_modification. '</span>';
	}
	?>
		

	
	<div class="actions">
		<input type="submit" class="btn primary" value="Envoyer">
	</div>

</form>


<?php if(isset($habitants)) :?>
	<div id="tab_habitants">
		<table class="styled-table">
			<thead >
				<tr>
					<th>Id Habitant</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Adresse</th>
					<th>Mail</th>
					<th>Num. portable</th>
					<th>Num. fixe</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($habitants as $h) : ?>
					<tr id="<?php $h->id_habitant?>">
					<!-- class="active-row" -->
						<td><?php echo ($h->id_habitant); ?></td>
						<td><?php echo ($h->nom_habitant); ?></td>
						<td><?php echo ($h->prenom_habitant); ?></td>
						<td><?php echo $h->num_adresse_habitant.' '.$rues[$h->id_rue-1]->nom_rue;?></td>
						<td><?php echo ($h->mail); ?></td>
						<td><?php echo ($h->numero_portable); ?></td>
						<td><?php echo ($h->numero_fixe); ?></td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
<?php endif;?>
