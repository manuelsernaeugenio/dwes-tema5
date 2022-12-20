<?php

function getImageByID(int $id): array
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
    $sentencia = $mysqli->prepare("SELECT * from imagen WHERE id = ?");
    if (!$sentencia) {
        echo "Error: " . $mysqli->error;
        $mysqli->close(); // cerramos la conexión.
        return [];
    }

    $validacion = $sentencia->bind_param('i', $id); // valor se pasa por referencia, si o si se tiene que añadir una variable.
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

function uploadImageToDB($nombre, $ruta, $usuario): bool
{

    $mysqli = new mysqli('db', 'dwes', 'dwes', 'dwes', 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        return false;
        exit();
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("insert into imagen (nombre, ruta, subido, usuario) value (?, ?, UNIX_TIMESTAMP(), ?)");
    if (!$sentencia) {
        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
        return false;
        exit();
    }

    // 2. Vinculación (bind): dos strings.
    $vinculacion = $sentencia->bind_param("ssi", $nombre, $ruta, $usuario);
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

function deleteImageFromDB($id): bool
{

    $mysqli = new mysqli('db', 'dwes', 'dwes', 'dwes', 3306);
    if ($mysqli->connect_errno) {
        echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        return false;
        exit();
    }

    // 1. Preparación
    $sentencia = $mysqli->prepare("DELETE FROM imagen WHERE id = ?");
    if (!$sentencia) {
        echo "Falló la preparación: (" . $mysqli->errno . ") " . $mysqli->error;
        return false;
        exit();
    }

    // 2. Vinculación (bind): dos strings.
    $vinculacion = $sentencia->bind_param("i", $id);
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
