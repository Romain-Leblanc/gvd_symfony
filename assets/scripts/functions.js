global.boutonModifier = function boutonModifier(num) {
    // Si la valeur du bouton radio est un nombre entier, on affichage le bouton "modifier"
    if(Number.isInteger(num)) {
        let btn = document.getElementById("bouton-modifier");
        btn.href = btn.baseURI+"/modifier/"+num;
        btn.classList.remove('cacher');
        btn.classList.add('afficher');
    }
}

global.getModeleFromMarque = function getModeleFromMarque(value) {
    if($("#ajout_vehicule_fk_modele").length > 0) {
        inputModele = $("#ajout_vehicule_fk_modele");
        // Désactive la sélection de la requête
        inputModele.attr("disabled", true);

        if(value !== "") {
            // Sinon on vérifie si la valeur est un nombre entier
            value = parseInt(value);
            if (Number.isInteger(value)) {
                // Retire l'attribut HTML qui désactive la liste déroulante précédente
                inputModele.removeAttr("disabled");
                // Requête Ajax pour les modèles de voitures
                $.post('/vehicule/infos', {"marqueID": value})
                    .done(function(data) {
                        // Concatène les valeurs des modèles dans la liste déroulante précédent puis redéfinit son contenu avec celles-ci
                        let liste = "<option value='' selected='selected'>-- Modèle --</option>";
                        data.donnees.forEach(element => liste += "<option value="+element.id+">"+element.modele+"</option>");
                        inputModele.html(liste);
                    });
            }
        }
    }
    else if($("#modification_vehicule_fk_modele").length > 0) {
        inputModele = $("#modification_vehicule_fk_modele");
        // Désactive la sélection de la requête
        inputModele.attr("disabled", true);

        // Si la valeur est vide, on désactive la liste déroulantes des modèles de voitures
        if(value !== "") {
            // Sinon on vérifie si la valeur est un nombre entier
            value = parseInt(value);
            if (Number.isInteger(value)) {
                // Retire l'attribut HTML qui désactive la liste déroulante précédente
                inputModele.removeAttr("disabled");
                // inputModele.prop('disabled',false);
                // Requête Ajax pour les modèles de voitures
                $.ajax({
                    url : '/vehicule/infos',
                    type: 'POST',
                    data : {"marqueID": value},
                    success: function(html) {
                        // console.log(html);
                        let liste = "";
                        html.donnees.forEach(element => liste += "<option value="+element.id+">"+element.modele+"</option>");
                        inputModele.empty().append(liste);
                    }
                });
/*                $.post('/vehicule/infos', {"marqueID": value})
                    .done(function(data) {
                        // Concatène les valeurs des modèles dans la liste déroulante précédent puis redéfinit son contenu avec celles-ci
                        let liste = "";
                        // let liste = "<option value='' selected='selected'>-- Modèle --</option>";
                        data.donnees.forEach(element => liste += "<option value="+element.id+">"+element.modele+"</option>");
                        // console.log(liste);
                        inputModele.empty().append(liste);
                        // inputModele.html(liste);
                    });*/
            }
        }
    }
}