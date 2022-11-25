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
    let inputModele = $('#ajout_vehicule_fk_modele');
    // Si lA valeur est vide, on désactive la liste déroulantes des modèles de voitures
    if(value === "") {
        inputModele.attr("disabled", true);
    }
    else {
        // Sinon on verifie si la valeur est un nombre entier
        value = parseInt(value);
        if (Number.isInteger(value)) {
            // Retire l'attribut HTML qui désactive la liste déroulante précédente
            inputModele.removeAttr("disabled");
            // Requête Ajax pour les modèles de voitures
            $.post('infos', {"marqueID": value})
                .done(function(data) {
                    // Concatène les valeurs des modèles dans la liste déroulante précédent puis redéfinit son contenu avec celles-ci
                    let liste = "<option value='' selected='selected'>-- Modèle --</option>";
                    data.donnees.forEach(element => liste += "<option value="+element.id+">"+element.modele+"</option>");
                    inputModele.html(liste);
                });
        }
    }
}