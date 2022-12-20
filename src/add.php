<?php
session_start();

require 'utils/user.php';
require 'utils/image.php';

if (!$_SESSION || !isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}

/**********************************************************************************************************************
 * Este es el script que añade imágenes en la base de datos. En la tabla "imagen" de la base de datos hay que guardar
 * el nombre que viene vía POST, la ruta de la imagen como se indica más abajo, la fecha de la inserción (función
 * UNIX_TIMESTAMP()) y el identificador del usuario que inserta la imagen (el usuario que está logeado en estos
 * momentos).
 * 
 * ¿Cuál es la ruta de la imagen? ¿De dónde sacamos esta ruta? Te lo explico a continuación:
 * - Busca una forma de asignar un nombre que sea único.
 * - La extensión será la de la imagen original, que viene en $_FILES['imagne']['name'].
 * - Las imágenes se subirán a la carpeta llamada "imagenes/" que ves en el proyecto.
 * - En la base de datos guardaremos la ruta relativa en el campo "ruta" de la tabla "imagen".
 * 
 * Así, si llega por POST una imagen PNG y le asignamosel nombre "imagen1", entonces en el campo "ruta" de la tabla
 * "imagen" de la base de datos se guardará el valor "imagenes/imagen1.png".
 * 
 * Como siempre:
 * 
 * - Si no hay POST, entonces tan solo se muestra el formulario.
 * - Si hay POST con errores se muestra el formulario con los errores y manteniendo el nombre en el campo nombre.
 * - Si hay POST y todo es correcto entonces se guarda la imagen en la base de datos para el usuario logeado.
 * 
 * Esta son las validaciones que hay que hacer sobre los datos POST y FILES que llega por el formulario:
 * - En el nombre debe tener algo (mb_strlen > 0).
 * - La imagen tiene que ser o PNG o JPEG (JPG). Usa FileInfo para verificarlo.
 * 
 * NO VAMOS A CONTROLAR SI YA EXISTE UNA IMAGEN CON ESE NOMBRE. SI EXISTE, SE SOBREESCRIBIRÁ Y YA ESTÁ.
 * 
 * A ESTE SCRIPT SOLO SE PUEDE ACCEDER SI HAY UN USARIO LOGEADO.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que desarrollar toda la lógica de este script.
 */

$imprimirFormulario = true;
$subido = false;
$nombreFichero = null;

// variables de errores.
$errores = [
    'nombre' => null,
    'fichero' => null
];


/*
* Si hay post, revisa que los datos que le hemos pasado tanto nombre como fichero, estén correctos.
*/
if ($_POST) {
    if (isset($_POST['nombre']) && array_key_exists('nombre', $_POST)) {
        $nombreFichero = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : null;
        if ($nombreFichero == null || mb_strlen($nombreFichero) <= 0) {
            $errores['nombre'] = "El nombre no puede estar vacio.";
        }
    } else {
        $errores['nombre'] = "Necesitamos el nombre.";
    }

    if (
        $_FILES && isset($_FILES['imagen']) &&
        $_FILES['imagen']['error'] === UPLOAD_ERR_OK &&
        $_FILES['imagen']['size'] > 0
    ) {
        $fichero = $_FILES['imagen']['tmp_name'];

        $permitido = array('image/png', 'image/jpg', 'image/jpeg');

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_fichero = finfo_file($finfo, $fichero);
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);

        $rutaFicheroDestino = './imagenes/' . $nombreFichero . time() . "." . $extension;

        // Comprobaciones básicas de subida para posteriormente subir o mostrar los errores.
        if (in_array($mime_fichero, $permitido) && !file_exists($rutaFicheroDestino) && $nombreFichero != null) {
            $seHaSubido = move_uploaded_file($fichero, $rutaFicheroDestino);

            $uploadImageToDB = uploadImageToDB($nombreFichero, $rutaFicheroDestino, getUser($_SESSION["usuario"])[0]["id"]);

            if ($seHaSubido && $uploadImageToDB) {
                $subido = true;
                $imprimirFormulario = false;
            } else {
                $errores['fichero'] = "Vaya, ha habido un error. Contacta con los administradores del sitio.";
            }
        } else {
            if (!in_array($mime_fichero, $permitido)) {
                $errores['fichero'] = "La extensión $extension no es válida";
            }

            if (file_exists($rutaFicheroDestino)) {
                $errores['fichero'] = "El fichero ya existe en el servidor.";
            }
        }
    } else {
        $errores['fichero'] = "Necesitamos un fichero.";
    }
}

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: añadir el menú de navegación.
 * - TODO: añadir en el campo del nombre el valor del mismo cuando haya errores en el envío para mantener el nombre
 *         que el usuario introdujo.
 * - TODO: añadir los errores que se produzcan cuando se envíe el formulario debajo de los campos.
 */
?>
<h1>Galería de imágenes</h1>

<?php

$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;

if ($usuario == null) {
    echo <<<END
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    END;
} else {
    echo <<<END
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="add.php"><strong>Añadir imagen</strong></a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="logout.php">Cerrar sesión ($usuario)</a></li>
        </ul>
    END;
} ?>

<?php if ($imprimirFormulario) { ?>
    <form method="post" enctype="multipart/form-data">
        <p>
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?= $_POST && isset($_POST['nombre']) ? $_POST['nombre'] : "" ?>">
        </p>

        <p>
            <?= $errores['nombre']; ?>
        </p>

        <p>
            <label for="imagen">Imagen</label>
            <input type="file" name="imagen" id="imagen">
        </p>

        <p>
            <?= $errores['fichero']; ?>
        </p>

        <p>
            <input type="submit" value="Añadir">
        </p>
    </form>
<?php } else if ($subido) { ?>

    <p>Imagen subida. ¿Quieres subir una de nuevo? Haz click <a href="add.php">aquí</a>.</p>

<?php } ?>