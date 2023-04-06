<div id="CorpsPage">

<style>



</style>

          <!-- HTML !-->

    <h1>Fusionner un signalement</h1>
    <p><u>Notes:</u> le signalement sélectionné sera supprimé 
    et ses statistiques seront ajoutées à celles du signalement n°<?php echo $id; ?>.</p>

    <table class="styled-table">
        <thead >
            <tr>
                <th>ID</th>
                <th>probleme</th>
                <th>niveau urgence</th>
                <th>adresse</th>
                <th>date signalement</th>
                <th>Compteur signalement</th>
                <th>Etat du Signalement</th>
                <th>Date dernière modification</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($signalements as $s) : ?>
                <tr id="<?php $s->id_signalement?>">
                <!-- class="active-row" -->
                    <td><?php echo ($s->id_signalement); ?></td>
                    <td><?php echo ($s->probleme); ?></td>
                    <td><?php echo ($s->niveau_urgence); ?></td>
                    <td><?php
                    $adresse = '';
                     if( isset($s->numero_maison_proche) && $s->numero_maison_proche!=''){
                        $adresse .= $s->numero_maison_proche.' ';
                     }else{
                        $adresse .= $s->intervalle_numero_debut.' - '.$s->intervalle_numero_fin.' ';
                     }
                     echo $adresse.'<br>'.$s->nom_rue;
                     ?></td>
                    <td><?php echo ($s->date_signalement); ?></td>
                    <td><?php echo ($s->compteur_signalement_total.' ('.$s->compteur_signalement_anonyme.')'); ?></td>

                    <td><?php echo ($s->etat); ?></td>
                    <td><?php echo ($s->date_modification);?></td>
                    <td>  
                        <a onclick="return confirm('En choisissant ce signalement n°<?php echo $s->id_signalement;?> il sera supprimer des données, êtes-vous sûr ?')" href="<?php echo BASE_URL.'/admin/signalements/merge/'.$id.'/'.$s->id_signalement; ?>">Fusionner</a>
                    </td>
                </tr>  
            <?php endforeach;?>
        </tbody>
    </table>
</div>


<script>
    function search_signalement() {

    }
</script>