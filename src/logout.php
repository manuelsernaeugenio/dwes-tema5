<?php

/**********************************************************************************************************************
 * Este script tan solo tiene que destruir la sesión y volver a la página principal.
 * 
 * UN USUARIO NO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */
session_start();

if (!$_SESSION || !isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}

session_destroy();

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */

echo '<p>Has cerrado la sesión. Ir a <a href="index.php">la página inicial</a></p>';
