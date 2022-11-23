/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './bootstrap-5.2.2/css/bootstrap.min.css';
import './fontawesome-6.2.0/css/all.css';
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import './scripts/jquery.js';
import './scripts/functions.js';
import './bootstrap-5.2.2/js/bootstrap.min.js';

// import jquery from 'jquery';
const $ = require('jquery');
// Permet d'utiliser la variable $ de jquery dans les fichiers JS
global.$ = global.jQuery = $;