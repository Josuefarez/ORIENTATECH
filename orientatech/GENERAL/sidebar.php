<?php
// ============================================================
// ARCHIVO: GENERAL/sidebar.php
// QUE HACE: Genera la barra lateral de navegacion.
//           Muestra el logo, el menu segun el rol del usuario,
//           y los datos del usuario conectado.
//           Se incluye en index.php para mostrarse en todas
//           las páginas del sistema.
// ============================================================

// Leemos los datos del usuario desde la sesion
$nombre_sb = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$rol_sb    = isset($_SESSION['rol'])    ? $_SESSION['rol']    : 'estudiante';

// Leemos que página esta activa para resaltarla en el menu
$pagina_sb = isset($_GET['pagina']) ? $_GET['pagina'] : 'inicio';

// ---- Menu que ven TODOS los roles ----
$menu_comun = [
    ['pagina' => 'inicio',         'icono' => '🏠', 'texto' => 'Inicio'],
    ['pagina' => 'informacion',    'icono' => 'ℹ️',  'texto' => 'Informacion'],
    ['pagina' => 'especialidades', 'icono' => '🎓', 'texto' => 'Especialidades'],
    ['pagina' => 'contacto',       'icono' => '✉️',  'texto' => 'Contacto'],
];

// ---- Menu especifico segun el rol ----
$menu_rol = [];

if ($rol_sb === 'estudiante') {
    // El estudiante solo ve: Mi Encuesta
    $menu_rol[] = ['pagina' => 'encuesta', 'icono' => '📋', 'texto' => 'Mi Encuesta'];

} elseif ($rol_sb === 'dece') {
    // El DECE solo ve: Panel DECE (NO ve el panel del Rector)
    $menu_rol[] = ['pagina' => 'dece', 'icono' => '📊', 'texto' => 'Panel DECE'];

} elseif ($rol_sb === 'rector') {
    $menu_rol[] = ['pagina' => 'rector', 'icono' => '👑', 'texto' => 'Panel Rector'];
    $menu_rol[] = ['pagina' => 'dece',   'icono' => '📊', 'texto' => 'Ver DECE'];

} elseif ($rol_sb === 'admin') {
    $menu_rol[] = ['pagina' => 'admin',  'icono' => '⚙️',  'texto' => 'Panel Admin'];
    $menu_rol[] = ['pagina' => 'rector', 'icono' => '👑', 'texto' => 'Panel Rector'];
    $menu_rol[] = ['pagina' => 'dece',   'icono' => '📊', 'texto' => 'Panel DECE'];

} elseif ($rol_sb === 'coordinador') {
    $menu_rol[] = ['pagina' => 'coordinador', 'icono' => '📐', 'texto' => 'Mi Especialidad'];
}
?>

<aside class="sidebar">

    <!-- LOGO DEL SISTEMA -->
    <div class="sidebar-logo">
        <img src="IMAGENES/LOGO.png" alt="OrientaTech Logo" class="logo-img">
        <h1>OrientaTech</h1>
        <small>UETS · 2025-2026</small>
    </div>

    <!-- MENU DE NAVEGACION -->
    <nav class="sidebar-nav">
        <ul>
            <!-- Mostramos las opciones comunes para todos -->
            <?php foreach ($menu_comun as $item): ?>
            <li>
                <a href="index.php?pagina=<?php echo $item['pagina']; ?>"
                   class="<?php echo ($pagina_sb === $item['pagina']) ? 'activo' : ''; ?>">
                    <span class="nav-icono"><?php echo $item['icono']; ?></span>
                    <span><?php echo $item['texto']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>

            <!-- Linea separadora -->
            <li><div class="sidebar-separador"></div></li>

            <!-- Mostramos las opciones especificas del rol -->
            <?php foreach ($menu_rol as $item): ?>
            <li>
                <a href="index.php?pagina=<?php echo $item['pagina']; ?>"
                   class="<?php echo ($pagina_sb === $item['pagina']) ? 'activo' : ''; ?>">
                    <span class="nav-icono"><?php echo $item['icono']; ?></span>
                    <span><?php echo $item['texto']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>

            <!-- Linea separadora -->
            <li><div class="sidebar-separador"></div></li>

            <!-- Opcion para cerrar sesion -->
            <li>
                <a href="cerrar_sesion.php">
                    <span class="nav-icono">🚪</span>
                    <span>Cerrar Sesion</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- DATOS DEL USUARIO CONECTADO (parte inferior del sidebar) -->
    <div class="sidebar-usuario">
        <div class="avatar">👤</div>
        <div class="info">
            <!-- Mostramos solo el primer nombre -->
            <p><?php echo htmlspecialchars(explode(' ', $nombre_sb)[0]); ?></p>
            <!-- Mostramos el rol con la primera letra en mayuscula -->
            <small><?php echo ucfirst($rol_sb); ?></small>
        </div>
    </div>

</aside>
