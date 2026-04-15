<?php
// ============================================================
// ARCHIVO: cerrar_sesion.php
// QUE HACE: Destruye todos los datos de la sesion del usuario
//           (como si "olvidara" quien estaba conectado)
//           y lo manda de regreso a la página de login.
// ============================================================

// Iniciamos la sesion para poder destruirla
session_start();

// Borramos todos los datos guardados en la sesion
session_destroy();

// Mandamos al usuario de regreso al login
header("Location: login.php");
exit();
?>
