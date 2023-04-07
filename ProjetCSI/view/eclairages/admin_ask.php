<link rel="stylesheet" href="<?php echo BASE_URL;?>/css/form.css" />
<link rel="stylesheet" href="<?php echo BASE_URL;?>/css/stat.css" />
<h2>Faire une demande d'éclairage</h2>


<form action="<?php echo BASE_URL.'/admin/eclairages/ask/'.$id ;?>" method="post">
    <label for="id_rue">Rue :</label>
    <select id="selectRue" name="id_rue" required>
        <?php foreach($rues as $r): ?>
            <option value="<?php echo $r->id_rue;?>" type="number"><?php echo $r->nom_rue;?></option>
        <?php endforeach; ?>
    </select>

    <div class="actions">
        <input type="submit" class="btn primary" value="Demande eclairage">
    </div>
</form>



<div id="stats">
    <?php if(isset($administrateurMode)) :?>

	<style type="text/css">
        /* S'occupe de la div stat dans son ensemble */
        #eclairages_stat{
            margin-top: 60px;
            background-color: #f1f1f1;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h3{
            padding : 20px;
        }

        .spanne{
            padding-left : 20px;
        }

	</style>


    <div id="eclairages_stat">

        <h3>Mode Administrateur | Stats</h3>

        <!-- <span class="spanne">Rue</span> 
        <select id="selectRue" name="id_rue_habitant" required>
            <?php foreach($rues as $r): ?>
                <option value="<?php echo $r->id_rue;?>" type="number"><?php echo $r->nom_rue;?></option>
            <?php endforeach; ?>
        </select> -->

        <div class="container">
            <div class="chart__label">Nombre de requêtes par heure</div>
                <div class="chart">
                    <?php 
                    // Récupération des requêtes par heure
                    $req_par_heure = array();
                    foreach($eclairages as $e) {
                        $date_heure_debut = strtotime($e->date_heure_debut);
                        $heure = date('H', $date_heure_debut);
                        if(isset($req_par_heure[$heure])) {
                            $req_par_heure[$heure]++;
                        } else {
                            $req_par_heure[$heure] = 1;
                        }
                    }

                    // Affichage des requêtes par heure sous forme de ligne
                    for($i = 0; $i < 24; $i++) {
                        $heure = sprintf("%02d", $i);
                        $req = isset($req_par_heure[$heure]) ? $req_par_heure[$heure] : 0;
                        $height = $req * 10;
                        echo '<div class="chart__line" style="height: '.$height.'px;"><span>'.$req.'</span></div>';
                    }
                    ?>
                </div>
                <div class="chart__x-axis">
                    <div>00h</div>
                    <div>01h</div>
                    <div>02h</div>
                    <div>03h</div>                    
                    <div>04h</div>
                    <div>05h</div>
                    <div>06h</div>
                    <div>07h</div>
                    <div>08h</div>
                    <div>09h</div>
                    <div>10h</div>
                    <div>11h</div>
                    <div>12h</div>
                    <div>13h</div>
                    <div>14h</div>
                    <div>15h</div>
                    <div>16h</div>
                    <div>17h</div>
                    <div>18h</div>
                    <div>19h</div>
                    <div>20h</div>
                    <div>21h</div>
                    <div>22h</div>
                    <div>23h</div>
                </div>
            </div>


            <?php foreach($eclairages as $e): ?>
                <div id="<?php echo $e->id_eclairage?>" class="eclairage">
                    <span class="debut"><?php echo $e->date_heure_debut;?> || </span>
                    <span class="fin"><?php echo $e->date_heure_debut;?></span>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif;?>
</div>