<?php


// Función que valida lo que el usuario le introduce.
// Returnea verdadero o falso según los criterios que introduzcamos.
function validateUser($usuario): bool
{
    if (mb_strlen($usuario, "UTF-8") <= 0) {
        return false;
    }

    if (!ctype_alpha($usuario)) {
        return false;
    }

    return true;
}

// Función que valida lo que el usuario le introduce.
// Returnea verdadero o falso según los criterios que introduzcamos.
function validatePassword($clave, $repiteClave): bool
{

    if (mb_strlen($clave, "UTF-8") <= 0 || mb_strlen($clave) < 8) {
        return false;
    }

    if (mb_strlen($repiteClave, "UTF-8") <= 0 || mb_strlen($repiteClave, "UTF-8") < 8) {
        return false;
    }

    if ($clave != $repiteClave) {
        return false;
    }

    return true;
}

// Función que registra a un usuario en una base de datos.
function signUpUser($usuario, $clave): bool
{
    $claveEncryptada = password_hash($clave, PASSWORD_BCRYPT);

    $mysqli = new mysqli('db', 'dwes', 'dwes', 'dwes', 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        return false;
        exit();
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("insert into usuario (nombre, clave) value (?, ?)");
    if (!$sentencia) {
        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
        return false;
        exit();
    }

    // 2. Vinculación (bind): dos strings.
    $vinculacion = $sentencia->bind_param("ss", $usuario, $claveEncryptada);
    if (!$vinculacion) {
        echo "Falló la vinculación de parámetros: (" . $sentencia->errno . ") " . $mysqli->error;
        $sentencia->close();
        return false;
        exit();
    }

    // 3. Ejecución
    $resultado = $sentencia->execute();

    if (!$resultado) {
        echo "Falló al ejecutar la sentencia: " . $mysqli->error;
        $sentencia->close();
        return false;
        exit();
    }

    // 4. Cerramos la sentencia y liberamos recurso
    $sentencia->close();

    // También se cierra la conexión con la base de datos a través del objeto mysqli
    $mysqli->close();

    return true;
}

function userExist($usuario): bool
{
    $mysqli = new mysqli('db', 'dwes', 'dwes', 'dwes', 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    // Preparamos la consulta.
    $sentencia = $mysqli->prepare("SELECT nombre FROM usuario WHERE nombre = ?");
    if (!$sentencia) {
        echo "Error: " . $mysqli->error;
        $mysqli->close(); // cerramos la conexión.
        return false;
    }

    // Enlazamos la variable con la sentencia de arriba.
    // cualquier cosa + el texto + cualquier cosa (cualquier resultado que tenga lo que escribamos)
    $validacion = $sentencia->bind_param('s', $usuario); // valor se pasa por referencia, si o si se tiene que añadir una variable.
    if (!$validacion) {
        echo "Error " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return false;
    }

    // Ejecutamos
    $ejecucion = $sentencia->execute();
    if (!$ejecucion) {
        echo "Error " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return false;
    }

    // Recuperramos las filas obtenidas como resultado
    $resultado = $sentencia->get_result();
    /* var_dump($resultado); */


    if (!$resultado) {
        echo "Error " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return false;
    }

    if ($resultado->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}


function getUser(string $usuario): array
{
    // Miramos la conexión con la base de datos.
    $mysqli = new mysqli('db', 'dwes', 'dwes', 'dwes', 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }

    if ($mysqli->errno) {
        echo "Error con la base de datos.";
        return [];
    }

    // Preparamos la consulta.
    $sentencia = $mysqli->prepare("SELECT * from usuario WHERE nombre = ?");
    if (!$sentencia) {
        echo "Error: " . $mysqli->error;
        $mysqli->close(); // cerramos la conexión.
        return [];
    }

    $validacion = $sentencia->bind_param('s', $usuario); // valor se pasa por referencia, si o si se tiene que añadir una variable.
    if (!$validacion) {
        echo "Error " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    // Ejecutamos
    $ejecucion = $sentencia->execute();
    if (!$ejecucion) {
        echo "Error " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    // Recuperramos las filas obtenidas como resultado
    $resultado = $sentencia->get_result();
    if (!$resultado) {
        echo "Error " . $mysqli->error;
        $sentencia->close();
        $mysqli->close();
        return [];
    }

    $resultadoBusqueda = [];
    while (($fila = $resultado->fetch_assoc()) != null) {
        $resultadoBusqueda[] = $fila;
    }

    return $resultadoBusqueda;
}
