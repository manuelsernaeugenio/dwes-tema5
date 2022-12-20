<?php

/**********************************************************************************************************************
 * Este programa, a través del formulario que tienes que hacer debajo, en el área de la vista, realiza el inicio de
 * sesión del usuario verificando que ese usuario con esa contraseña existe en la base de datos.
 * 
 * Para mantener iniciada la sesión dentrás que usar la $_SESSION de PHP.
 * 
 * En el formulario se deben indicar los errores ("Usuario y/o contraseña no válido") cuando corresponda.
 * 
 * Dicho formulario enviará los datos por POST.
 * 
 * Cuando el usuario se haya logeado correctamente y hayas iniciado la sesión, redirige al usuario a la
 * página principal.
 * 
 * UN USUARIO LOGEADO NO PUEDE ACCEDER A ESTE SCRIPT.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */

session_start();

require 'utils/user.php';

$error = "";

if ($_SESSION || isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}

if ($_POST) {
    $usuario = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : null;
    $clave = isset($_POST['clave']) ? htmlentities(trim($_POST['clave'])) : null;

    $claveEncriptada = password_hash($clave, PASSWORD_BCRYPT);
    if (userExist($usuario)) {
        if (password_verify($clave, getUser($usuario)[0]["clave"])) {
            $_SESSION['usuario'] = $usuario;
            header('location: index.php');
        } else {
            $error = "<span style='color: red;'>Usuario y/o contraseña no válidos.</span>";
        }
    } else {
        $error = "<span style='color: red;'>Usuario y/o contraseña no válidos.</span>";
    }
}

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: añadir el menú.
 * - TODO: formulario con nombre de usuario y contraseña.
 */
?>

<h1>Inicia sesión</h1>

<?php

$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;

if ($usuario == null) {
    echo <<<END
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php"><strong>Login</strong></a></li>
        </ul>
    END;
} else {
    echo <<<END
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="add.php">Añadir imagen</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="logout.php">Cerrar sesión ($usuario)</a></li>
        </ul>
    END;
} ?>

<form action="#" method="post">
    <p>
        <label for="nombre">Nombre: </label>
        <input type="text" id="nombre" name="nombre">
    </p>

    <p>
        <label for="nombre">Contraseña: </label>
        <input type="password" id="clave" name="clave">
    </p>

    <p>
        <?= $error ?>
    </p>

    <p>
        <input type="submit" value="Login">
    </p>


</form>