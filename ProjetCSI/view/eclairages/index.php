<link rel="stylesheet" href="<?php echo BASE_URL;?>/css/form.css" />
<h2>Faire une demande d'éclairage</h2>

<form action="<?php echo BASE_URL.'/eclairages'.$id ;?>" method="post">
    <label for="id_rue_habitant">Rue :</label>
    <select id="selectRue" name="id_rue_habitant" required>
        <?php foreach($rues as $r): ?>
            <option value="<?php echo $r->id_rue;?>" type="number"><?php echo $r->nom_rue;?></option>
        <?php endforeach; ?>
    </select>

    <div class="actions">
        <input type="submit" class="btn primary" value="Demande eclairage">
    </div>
</form>
</div>