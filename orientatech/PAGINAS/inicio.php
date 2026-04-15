<?php
// ============================================================
// ARCHIVO: PAGINAS/inicio.php
// QUE HACE: Es el dashboard (panel principal) del sistema.
//           Muestra contenido diferente segun el rol:
//           - Estudiante: ve su progreso y resultado de la IA
//           - DECE y Rector: ven estadisticas generales
// ============================================================

// Leemos los datos del usuario desde la sesion
$id_usuario = $_SESSION['id_usuario'];
$nombre     = $_SESSION['nombre'];
$rol        = $_SESSION['rol'];
$curso      = $_SESSION['curso'];

// Variables que usaremos segun el rol
$tiene_resultado       = false;
$resultado_ia          = null;
$preguntas_respondidas = 0;

// ---- Si es ESTUDIANTE ----
if ($rol === 'estudiante') {

    // Buscamos si ya tiene un resultado guardado en la tabla "resultados"
    $sql_res = "SELECT especialidad, justificacion, fecha_resultado
                FROM resultados
                WHERE id_usuario = $id_usuario
                ORDER BY fecha_resultado DESC
                LIMIT 1";
    $res_q = mysqli_query($conexion, $sql_res);

    if (mysqli_num_rows($res_q) > 0) {
        // Si encontramos resultado, lo guardamos para mostrarlo
        $tiene_resultado = true;
        $resultado_ia    = mysqli_fetch_assoc($res_q);
    }

    // Contamos cuantas preguntas ha respondido hasta ahora
    $sql_p = "SELECT COUNT(DISTINCT numero_pregunta) as total
              FROM respuestas_encuesta
              WHERE id_usuario = $id_usuario";
    $res_p = mysqli_query($conexion, $sql_p);
    $preguntas_respondidas = mysqli_fetch_assoc($res_p)['total'];
}

// ---- Si es DECE o RECTOR ----
if ($rol === 'dece' || $rol === 'rector') {

    // Contamos cuantos estudiantes hay en el sistema
    $res_te = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT COUNT(*) as t FROM usuarios WHERE rol = 'estudiante'"));
    $total_estudiantes = $res_te['t'];

    // Contamos cuantos ya completaron la encuesta y tienen resultado
    $res_tr = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT COUNT(DISTINCT id_usuario) as t FROM resultados"));
    $total_resultados = $res_tr['t'];

    // Contamos cuantos mensajes nuevos hay sin leer
    $res_tm = mysqli_fetch_assoc(mysqli_query($conexion,
        "SELECT COUNT(*) as t FROM mensajes_contacto WHERE leido = 0"));
    $mensajes_pendientes = $res_tm['t'];
}
?>

<!-- Titulo de la pagina -->
<div class="page-header">
    <h2>👋 Bienvenido, <?php echo htmlspecialchars(explode(' ', $nombre)[0]); ?></h2>
    <p>Panel de inicio - <?php echo ucfirst($rol); ?>
       <?php if ($curso): ?>- Curso: <?php echo htmlspecialchars($curso); ?><?php endif; ?>
    </p>
</div>

<!-- ===== CONTENIDO PARA ESTUDIANTE ===== -->
<?php if ($rol === 'estudiante'): ?>

    <?php if ($tiene_resultado): ?>
        <!-- El estudiante YA tiene su resultado de la IA -->
        <div class="tarjeta resultado-ia">
            <div class="icono-res">🎯</div>
            <h2>TU ESPECIALIDAD TECNICA</h2>
            <div class="esp-nombre"><?php echo htmlspecialchars($resultado_ia['especialidad']); ?></div>
            <p><?php echo htmlspecialchars($resultado_ia['justificacion']); ?></p>
            <br>
            <small style="color:#2d3748;">
                Resultado obtenido el <?php echo $resultado_ia['fecha_resultado']; ?>
            </small>
            <br><br>
            <a href="index.php?pagina=especialidades" class="btn btn-primario">
                🎓 Ver detalles de mi especialidad
            </a>
        </div>

    <?php else: ?>
        <!-- El estudiante AUN NO tiene resultado -->
        <div class="grid-2">

            <!-- Tarjeta con el progreso de la encuesta -->
            <div class="tarjeta">
                <h3>📋 Estado de tu Encuesta</h3>
                <p style="color:#1a2438; margin-bottom:14px;">
                    Preguntas respondidas:
                    <strong style="color:var(--verde-neon);"><?php echo $preguntas_respondidas; ?>/20</strong>
                </p>
                <!-- Barra de progreso: calcula el porcentaje completado -->
                <div class="barra-progreso">
                    <div class="barra-relleno"
                         style="width:<?php echo ($preguntas_respondidas / 20) * 100; ?>%">
                    </div>
                </div>
                <!-- Boton que cambia segun si ya empezo o no -->
                <?php if ($preguntas_respondidas < 20): ?>
                    <a href="index.php?pagina=encuesta" class="btn btn-verde">
                        📝 <?php echo $preguntas_respondidas > 0 ? 'Continuar Encuesta' : 'Iniciar Encuesta'; ?>
                    </a>
                <?php else: ?>
                    <a href="index.php?pagina=resultado" class="btn btn-primario">
                        🤖 Ver mi Resultado
                    </a>
                <?php endif; ?>
            </div>

            <!-- Tarjeta con instrucciones de como funciona -->
            <div class="tarjeta">
                <h3>ℹ️ Como funciona?</h3>
                <p style="color:#1a2438; font-size:14px; line-height:1.8;">
                    1. Responde las <strong style="color:var(--azul-brillante);">20 preguntas</strong>.<br>
                    2. La <strong style="color:var(--verde-neon);">IA analiza</strong> tu perfil completo.<br>
                    3. Recibes tu <strong style="color:var(--azul-brillante);">especialidad ideal</strong>.<br>
                    4. Consulta al <strong style="color:var(--verde-neon);">DECE</strong> si tienes dudas.
                </p>
            </div>

        </div>
    <?php endif; ?>

<?php endif; ?>

<!-- ===== CONTENIDO PARA DECE Y RECTOR ===== -->
<?php if ($rol === 'dece' || $rol === 'rector'): ?>

    <!-- Tres tarjetas con numeros importantes -->
    <div class="grid-3">

        <div class="tarjeta" style="text-align:center;">
            <h3>👥 Estudiantes</h3>
            <p class="metrica-num" style="color:var(--azul-brillante);"><?php echo $total_estudiantes; ?></p>
            <p style="color:#2d3748; font-size:13px;">registrados en el sistema</p>
        </div>

        <div class="tarjeta" style="text-align:center;">
            <h3>✅ Con Resultado IA</h3>
            <p class="metrica-num" style="color:var(--verde-neon);"><?php echo $total_resultados; ?></p>
            <?php
            // Calculamos el porcentaje de avance
            $pct = $total_estudiantes > 0 ? round(($total_resultados / $total_estudiantes) * 100) : 0;
            ?>
            <div class="barra-progreso">
                <div class="barra-relleno" style="width:<?php echo $pct; ?>%"></div>
            </div>
            <p style="color:#2d3748; font-size:13px;"><?php echo $pct; ?>% completado</p>
        </div>

        <div class="tarjeta" style="text-align:center;">
            <h3>✉️ Mensajes Nuevos</h3>
            <p class="metrica-num" style="color:#e67e22;"><?php echo $mensajes_pendientes; ?></p>
            <a href="index.php?pagina=contacto" class="btn btn-borde" style="font-size:12px; padding:6px 14px;">
                Ver mensajes
            </a>
        </div>

    </div>

    <!-- Botones de acceso rapido segun el rol -->
    <div class="tarjeta">
        <h3>🚀 Acceso Rápido</h3>
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:10px;">

            <!-- El DECE y el Rector pueden ver el panel DECE -->
            <a href="index.php?pagina=dece" class="btn btn-primario">📊 Panel DECE</a>

            <!-- Solo el Rector puede ver su propio panel -->
            <?php if ($rol === 'rector'): ?>
                <a href="index.php?pagina=rector" class="btn btn-verde">👑 Panel Rector</a>
            <?php endif; ?>

            <a href="index.php?pagina=especialidades" class="btn btn-borde">🎓 Especialidades</a>
            <a href="index.php?pagina=contacto"       class="btn btn-borde">✉️ Mensajes</a>
        </div>
    </div>

<?php endif; ?>
