function selectProblemeAutre()
{
    if(document.getElementById('selectProbleme').value =="autres")
    {
        let htmlAjoute = '<label for="description_probleme">Description du problème :</label>\n'
        +'<textarea id="champDescriPb" name="description_probleme" required maxlength="500"></textarea><br>';
        document.getElementById('descriptionProbleme').innerHTML = htmlAjoute; 
    }else{
        document.getElementById('descriptionProbleme').innerHTML = ''; 
    }
}

function affichageNumero(param)
{
    let numeros = document.getElementById('numeros');

    let htmlAdd = '';
    switch (param) {
        case 'num':
            htmlAdd = '<label for="numero_maison_proche">Numéro de maison proche :</label>\n'
                     +'<input class="nombres" type="number" name="numero_maison_proche" min=1 required>';
            break;
    
        case 'intervalle':
            htmlAdd = '<div class="boutonsBloc">\n'
								+'<label for="intervalle_numero_debut">Intervalle numéro début *:</label>\n'
								+'<input class="nombres" type="number" name="intervalle_numero_debut" min=1 required><br>\n'

								+'<label for="intervalle_numero_fin">Intervalle numéro fin *:</label>\n'
								+'<input class="nombres" type="number" name="intervalle_numero_fin" min=1 required><br>\n'
							+'</div>';
                        
            break;

        default:
            break;
    }

    numeros.innerHTML = htmlAdd;
}