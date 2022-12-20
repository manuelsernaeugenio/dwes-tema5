<?php

/*********************************************************************************************************************
 * Este script realiza el registro del usuario vía el POST del formulario que hay debajo, en la vista.
 * 
 * Cuando llegue POST hay que validarlo y si todo fue bien insertar en la base de datos el usuario.
 * 
 * Requisitos del POST:
 * - El nombre de usuario no tiene que estar vacío y NO PUEDE EXISTIR UN USUARIO CON ESE NOMBRE EN LA BASE DE DATOS.
 * - La contraseña tiene que ser, al menos, de 8 caracteres.
 * - Las contraseñas tiene que coincidir.
 * 
 * La contraseña la tienes que guardar en la base de datos cifrada mediante el algoritmo BCRYPT.
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

include 'utils/user.php';

if ($_SESSION || isset($_SESSION['usuario'])) {
    header('location: index.php');
    exit();
}

$errores = [
    "nombre" => null,
    "clave" => null
];

if ($_POST) {

    $usuario = isset($_POST['nombre']) ? htmlspecialchars(trim($_POST['nombre'])) : null;

    $clave = isset($_POST['clave']) ? htmlentities(trim($_POST['clave'])) : null;
    $claveRepetida = isset($_POST['repite_clave']) ? htmlentities(trim($_POST['repite_clave'])) : null;

    if (validateUser($usuario) && validatePassword($clave, $claveRepetida) && !userExist($usuario)) {

        $registro = signUpUser($usuario, $clave);

        if ($registro) {
            echo "Te has registrado correctamente. Haz <a href='login.php'>login</a>";
        } else {
            echo "Ha habido un error. Contacte con el administrador.";
        }
    } else {

        if (!validateUser($usuario)) {
            $errores['nombre'] = "El nombre de usuario contiene errores.";
        }

        if (!validatePassword($clave, $claveRepetida)) {
            $errores['clave'] = "Revisa bien las contraseña. Tienen que coincidir y ser al menos de 8 carácteres.";
        }

        if (userExist($usuario)) {
            $errores['nombre'] = "Ese nombre de usuario ya está registrado.";
        }
    }
}

/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar en la vista:
 * - TODO: los errores que se produzcan tienen que aparecer debajo de los campos.
 * - TODO: cuando hay errores en el formulario se debe mantener el valor del nombre de usuario en el campo
 *         correspondiente.
 */
?>
<h1>Regístrate</h1>

<?php

$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;

if ($usuario == null) {
    echo <<<END
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="filter.php">Filtrar imágenes</a></li>
            <li><a href="signup.php"><strong>Regístrate</strong></a></li>
            <li><a href="login.php">Login</a></li>
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

<form action="signup.php" method="post">
    <p>
        <label for="nombre">Nombre de usuario</label>
        <input type="text" name="nombre" id="nombre">
    </p>

    <p style="color: red;"><?= $errores['nombre'] ?></p>

    <p>
        <label for="clave">Contraseña</label>
        <input type="password" name="clave" id="clave">
    </p>

    <p>
        <label for="repite_clave">Repite la contraseña</label>
        <input type="password" name="repite_clave" id="repite_clave">
    </p>

    <p style="color: red;"><?= $errores['clave'] ?></p>

    <p>
        <input type="submit" value="Regístrate">
    </p>
</form>