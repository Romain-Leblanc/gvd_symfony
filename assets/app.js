/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import './scripts/jquery.js';
import './select2-4.0.13/select2.min.js';
import './select2-4.0.13/fr.min.js';
import './scripts/functions.js';
import './bootstrap-5.2.2/js/bootstrap.min.js';

// import jquery from 'jquery';
const $ = require('jquery');
// Permet d'utiliser la variable $ de jquery dans les fichiers JS
global.$ = global.jQuery = $;
// Importation de select2 pour les listes déroulantes
require('select2');
$('.select2-value-50').select2({
    language: 'fr',
    dropdownAutoWidth : true,
    width: '50%',
});
$('.select2-value-100').select2({
    language: 'fr',
    dropdownAutoWidth : true,
    width: '100%'
});