<div id="CorpsPage">

<style>



</style>

          <!-- HTML !-->
    <form action="<?php echo BASE_URL.'/admin/signalements/edit'?>">
        <button class="button-3">Créer un signalement</button>
    </form>

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
                    <td><?php echo ($s->date_resolution);?></td>
                    <td>
                        <a href="<?php echo BASE_URL.'/admin/signalements/edit/'.$s->id_signalement; ?>">Edit</a>   
                        <a onclick="return confirm('Voulez-vous vraiment supprimer le signalement n°<?php echo $s->id_signalement;?>')" href="<?php echo BASE_URL.'/admin/signalements/delete/'.$s->id_signalement; ?>">Suppr</a>
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