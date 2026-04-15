<?php
// ============================================================
// ARCHIVO: GENERAL/conexion.php
// QUÉ HACE: Conecta con la base de datos MySQL de XAMPP.
// PUERTOS DE TU XAMPP:
//   Apache → 80, 443
//   MySQL  → 33066 (puerto personalizado)
// ============================================================

$servidor      = "localhost";
$puerto        = 33066;          // Tu puerto MySQL personalizado
$usuario_db    = "root";
$contrasena_db = "";             // En XAMPP local no tiene contraseña
$base_datos    = "vocacional_uets";

// Conectamos incluyendo el puerto personalizado
$conexion = mysqli_connect($servidor, $usuario_db, $contrasena_db, $base_datos, $puerto);

if (!$conexion) {
    die("
    <div style='font-family:sans-serif;padding:30px;background:#fff3f3;border:2px solid #e74c3c;border-radius:8px;margin:20px;'>
        <h3 style='color:#e74c3c;'>❌ Error de conexión con la base de datos</h3>
        <p><strong>Error:</strong> " . mysqli_connect_error() . "</p>
        <p>Verifica que:</p>
        <ul>
            <li>MySQL esté corriendo en XAMPP (botón verde)</li>
            <li>La base de datos <strong>vocacional_uets</strong> exista en phpMyAdmin</li>
            <li>El puerto MySQL sea <strong>33066</strong></li>
        </ul>
    </div>");
}

// Forzamos UTF-8 para que tildes y ñ funcionen correctamente
mysqli_set_charset($conexion, "utf8mb4");
mysqli_query($conexion, "SET NAMES 'utf8mb4'");
mysqli_query($conexion, "SET CHARACTER SET utf8mb4");
?>
