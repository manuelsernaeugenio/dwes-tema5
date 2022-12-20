<?php

session_start();
/**********************************************************************************************************************
 * Este script simplemente elimina la imagen de la base de datos y de la carpeta <imagen>
 *
 * La información de la imagen a eliminar viene vía GET. Por GET se tiene que indicar el id de la imagen a eliminar
 * de la base de datos.
 * 
 * Busca en la documentación de PHP cómo borrar un fichero.
 * 
 * Si no existe ninguna imagen con el id indicado en el GET o no se ha inicado GET, este script redirigirá al usuario
 * a la página principal.
 * 
 * En otro caso seguirá la ejecución del script y mostrará la vista de debajo en la que se indica al usuario que
 * la imagen ha sido eliminada.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que desarrollar toda la lógica de este script.
 */

require 'utils/image.php';

$id = htmlspecialchars(trim($_GET['id']));
$currentImage = getImageByID($id);

if (isset($currentImage[0]) && $_GET && $_GET['id'] && isset($_SESSION['usuario'])) {

    $deleteImageFromDB = deleteImageFromDB($id);

    if ($deleteImageFromDB) {
        unlink($currentImage[0]['ruta']); // eliminamos el fichero.
    }
} else {
    header('location: index.php');
    exit();
}



/*********************************************************************************************************************
 * Salida HTML
 */
?>
<h1>Galería de imágenes</h1>

<p>Imagen eliminada correctamente</p>
<p>Vuelve a la <a href="index.php">página de inicio</a></p>