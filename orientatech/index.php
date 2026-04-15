<?php
// ============================================================
// ARCHIVO: index.php
// QUE HACE: Es la puerta de entrada principal del sistema.
//           Siempre muestra la barra lateral y carga la página
//           que el usuario pidio con ?página=nombre.
//           También controla que cada usuario solo vea
//           lo que le corresponde segun su rol.
// ============================================================

// Iniciamos la sesion para poder leer los datos del usuario
session_start();

// Si el usuario NO ha iniciado sesion, lo mandamos al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Conectamos a la base de datos
include('GENERAL/conexion.php');

// Leemos el rol del usuario (estudiante, dece o rector)
$rol = $_SESSION['rol'];

// Leemos que página quiere ver. Si no pide ninguna, mostramos "inicio"
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'inicio';

// ---- Páginas que puede ver cada rol ----

// Páginas que TODOS pueden ver sin importar el rol
$paginas_comunes = ['inicio', 'informacion', 'especialidades', 'contacto'];

// Páginas solo para el estudiante
$paginas_estudiante = ['encuesta', 'resultado'];

// Páginas solo para el DECE (el DECE NO puede ver el panel del Rector)
$paginas_dece = ['dece'];

// Páginas solo para el Rector
$paginas_rector = ['rector', 'dece'];

// Páginas para el Admin (puede ver todo)
$paginas_admin = ['admin', 'rector', 'dece'];

// Páginas para el Coordinador
$paginas_coordinador = ['coordinador'];

// ---- Verificamos si el usuario tiene permiso ----

$pagina_valida = false;

if (in_array($pagina, $paginas_comunes)) {
    $pagina_valida = true;

} elseif ($rol === 'estudiante' && in_array($pagina, $paginas_estudiante)) {
    $pagina_valida = true;

} elseif ($rol === 'dece' && in_array($pagina, $paginas_dece)) {
    $pagina_valida = true;

} elseif ($rol === 'rector' && in_array($pagina, $paginas_rector)) {
    $pagina_valida = true;

} elseif ($rol === 'admin' && in_array($pagina, $paginas_admin)) {
    $pagina_valida = true;

} elseif ($rol === 'coordinador' && in_array($pagina, $paginas_coordinador)) {
    $pagina_valida = true;
}

// Si no tiene permiso, lo enviamos al inicio sin explicacion
if (!$pagina_valida) {
    header("Location: index.php?pagina=inicio");
    exit();
}

// Armamos la ruta al archivo PHP de la página que se quiere cargar
$archivo_pagina = 'PAGINAS/' . $pagina . '.php';

// Si el archivo físicamente no existe, lo marcamos como nulo
if (!file_exists($archivo_pagina)) {
    $archivo_pagina = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrientaTech - UETS</title>
    <!-- Cargamos los estilos globales desde GENERAL/estilos.css -->
    <link rel="stylesheet" href="GENERAL/estilos.css">
</head>
<body>

<div class="layout">

    <!-- La barra lateral SIEMPRE se muestra en todas las paginas -->
    <?php include('GENERAL/sidebar.php'); ?>

    <!-- Area principal: aqui cargamos el contenido de cada pagina -->
    <main class="contenido">
        <?php
        if ($archivo_pagina) {
            // Cargamos el archivo de la página solicitada
            include($archivo_pagina);
        } else {
            // Si la página no existe, mostramos un mensaje de error
            echo '<div class="alerta alerta-error">Página no encontrada.</div>';
        }
        ?>
    </main>

</div>

</body>
</html>
