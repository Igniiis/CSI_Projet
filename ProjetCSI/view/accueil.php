<?php $titre_pour_layout = 'Accueil'?>


<div>
    <h1>Projet CSI</h1>
</div>

<div>
    <p>Bienvenue 
    <?php if(isset($name)){
        echo ' '.$name.'';
    }?>
    sur le site du projet de CSI de l'année 2022-2023</p>

</div>