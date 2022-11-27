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
    // Récupération du bouton "submit"
    let btnSubmit = $('#btn-submit');
    // Vérifie si le DOM actuel contient l'élement 'ajout'
    if($("#ajout_vehicule_fk_modele").length > 0) {
        let inputModele = $("#ajout_vehicule_fk_modele");
        // Désactive les élements le temps de la requête Ajax
        inputModele.prop("disabled", true);
        btnSubmit.prop("disabled", true);

        // Si la "marque" est vide
        if(value !== "") {
            // Converti et vérifie si c'est un entier
            value = parseInt(value);
            if (Number.isInteger(value)) {
                // Requête Ajax pour les modèles de voitures
                ajaxQueryModele(inputModele, btnSubmit, value);
            }
        }
    }
    // Sinon si le DOM actuel contient l'élement 'modification'
    else if($("#modification_vehicule_fk_modele").length > 0) {
        let inputModele = $("#modification_vehicule_fk_modele");
        // Désactive les élements le temps de la requête Ajax
        inputModele.prop("disabled", true);
        btnSubmit.prop("disabled", true);

        // Si la "marque" est vide
        if(value !== "") {
            // Converti et vérifie si c'est un entier
            value = parseInt(value);
            if (Number.isInteger(value)) {
                ajaxQueryModele(inputModele, btnSubmit, value);
            }
        }
    }
}

function ajaxQueryModele(inputModele, btnSubmit, value, option) {
    // Requête Ajax pour les modèles de voitures
    $.ajax({
        url : '/vehicule/infos',
        type: 'POST',
        data : {"marqueID": value},
        success: function(html) {
            let liste = "<option value='' selected='selected'>-- Modèle --</option>";
            // Concaténation des "options" du select
            html.donnees.forEach(element => liste += "<option value="+element.id+">"+element.modele+"</option>");
            // Vide les options actuelles du select puis les remplace
            inputModele.empty().append(liste);
            // Réactive les élements
            inputModele.prop("disabled", false);
            btnSubmit.prop("disabled", false);
        }
    });
}