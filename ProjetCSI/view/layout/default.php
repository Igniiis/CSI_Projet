<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/css/style.css" />
    <title><?php echo isset($titre_pour_layout)?$titre_pour_layout:'Projet CSI' ?></title>
</head>
<body>
    <header>
        <div class="topnav">
            <a class="clickable" href="<?php echo BASE_URL.'/accueil' ?>" title="Accueil">Accueil</a>
                    
            <a class="clickable" href="<?php echo BASE_URL.'/signalements' ?>" title="Signalements">Signalements</a>
            <a class="clickable" href="<?php echo BASE_URL.'/eclairages' ?>" title="Eclairages">Eclairages</a>

    
            <a class="clickable" href="<?php echo BASE_URL.'/agents/login'?>" title="Connexion">Connexion</a>    
        </div>
    </header>

    <div id="centre">
        <div class="container">
            <?php echo $this->Session->flash()?>
            <?php echo $content_for_layout ;?>
        </div>
    </div>

</body>
</html>


<style>
    
.topnav {
    overflow: hidden;
    background-color: #333;
  }

</style>