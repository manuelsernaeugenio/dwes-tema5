<?php

session_start();
/*********************************************************************************************************************
 * Este script muestra un formulario a través del cual se pueden buscar imágenes por el nombre y mostrarlas. Utiliza
 * el operador LIKE de SQL para buscar en el nombre de la imagen lo que llegue por $_GET['nombre'].
 * 
 * Evidentemente, tienes que controlar si viene o no por GET el valor a buscar. Si no viene nada, muestra el formulario
 * de búsqueda. Si viene en el GET el valor a buscar (en $_GET['nombre']) entonces hay que preparar y ejecutar una 
 * sentencia SQL.
 * 
 * El valor a buscar se tiene que mantener en el formulario.
 */

/**********************************************************************************************************************
 * Lógica del programa
 * 
 * Tareas a realizar:
 * - TODO: tienes que realizar toda la lógica de este script
 */

function filtrar(string $texto): array
{
    $mysqli = new mysqli('db', 'dwes', 'dwes', 'dwes', 3306);

    if ($mysqli->connect_errno) {
        echo "Ha habido un error al conectar con la base de datos.";
        exit();
    }

    if ($mysqli->errno) {
        echo "Error con la base de datos.";
        return [];
    }

    // Preparamos la consulta.
    $sentencia = $mysqli->prepare("SELECT id, nombre, ruta, usuario from imagen WHERE nombre like ?");
    if (!$sentencia) {
        echo "Error: " . $mysqli->error;
        $mysqli->close(); // cerramos la conexión.
        return [];
    }

    // Enlazamos la variable con la sentencia de arriba.
    // cualquier cosa + el texto + cualquier cosa (cualquier resultado que tenga lo que escribamos)
    $valor = "%" . $texto . "%";

    $validacion = $sentencia->bind_param('s', $valor); // valor se pasa por referencia, si o si se tiene que añadir una variable.
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

$posts = [];

$texto = $_GET && isset($_GET['nombre']) ? htmlspecialchars(trim($_GET['nombre'])) : "";

if ($_GET && isset($_GET['nombre']) && strlen($texto) > 0) {
    $posts = filtrar($texto);
}


?>

<?php
/*********************************************************************************************************************
 * Salida HTML
 * 
 * Tareas a realizar:
 * - TODO: completa el código de la vista añadiendo el menú de navegación.
 * - TODO: en el formulario falta añadir el nombre que se puso cuando se envió el formulario.
 * - TODO: debajo del formulario tienen que aparecer las imágenes que se han encontrado en la base de datos.
 */
?>
<h1>Galería de imágenes</h1>

<?php

$usuario = $_SESSION && isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : null;

if ($usuario == null) {
    echo <<<END
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="filter.php"><strong>Filtrar imágenes</strong></a></li>
            <li><a href="signup.php">Regístrate</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    END;
} else {
    echo <<<END
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="add.php">Añadir imagen</a></li>
            <li><a href="filter.php"><strong>Filtrar imágenes</strong></a></li>
            <li><a href="logout.php">Cerrar sesión ($usuario)</a></li>
        </ul>
    END;
} ?>

<h2>Busca imágenes por filtro</h2>

<form method="get" action="#">
    <p>
        <label for="nombre">Busca por nombre</label>
        <input type="text" name="nombre" id="nombre" value="<?= $_GET && isset($_GET['nombre']) ? htmlspecialchars(trim($_GET['nombre'])) : "" ?>">
    </p>
    <p>
        <input type="submit" value="Buscar">
    </p>
</form>

<?php

if ($_GET) {
    foreach ($posts as $post) {
        $borrar = isset($usuario) ? "<a href='delete.php?id={$post['id']}'>Borrar</a>" : null;

        echo <<<END
        <figure>
            <div>{$post['nombre']} (subida por {$post['usuario']})</div>
            <img src="{$post['ruta']}" width="200px">
            $borrar
        </figure>
    END;
    }
}
?>