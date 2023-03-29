<?php
    $bdd = pg_connect("host=localhost port=5432 dbname=script user=postgres password=admin");

    $identifiant = $_POST['identifiant'];
    $motdepasse = pg_escape_string($_POST['motdepasse']);

    $result = pg_query($bdd, "SELECT * FROM agent WHERE id_agent='".$identifiant."' AND mdp_agent='".$motdepasse."'");
    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        if ($row['type_agent'] == 'responsable') {
            class Utilisateur {
                public $nom;
                public $prenom;
                public $type;
                function __construct($nom, $prenom, $type) {
                    $this->nom = $nom;
                    $this->prenom = $prenom;
                    $this->type = $type;
                }
            }
            $utilisateur = new Utilisateur($row['nom_agent'], $row['prenom_agent'], $row['type_agent']);
            include('pageAccueil.php');
        }
    }
    else {
        $messageErreurConnexion = 'Identifiant ou mot de passe incorrect.';
        include('pageAccueil.php');
    }
?>