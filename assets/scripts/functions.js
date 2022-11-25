global.boutonModifier = function boutonModifier(num) {
    if(Number.isInteger(num)) {
        let btn = document.getElementById("bouton-modifier");
        btn.href = btn.baseURI+"/modifier/"+num;
        btn.classList.remove('cacher');
        btn.classList.add('afficher');
    }
}