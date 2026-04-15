<?php
// ============================================================
// ARCHIVO: PAGINAS/rector.php
// QUE HACE: Panel exclusivo del Rector.
//           Muestra estadisticas generales del sistema:
//           total de estudiantes, cuantos completaron,
//           mensajes pendientes, y avance por curso.
//           IMPORTANTE: Solo el Rector puede ver esta página.
//           El DECE NO tiene acceso aqui (controlado en index.php).
// ============================================================

// Contamos el total de estudiantes registrados
$res_te = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) as t FROM usuarios WHERE rol = 'estudiante'"));
$total_estudiantes = $res_te['t'];

// Contamos cuantos ya tienen resultado de la IA
$res_tr = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(DISTINCT id_usuario) as t FROM resultados"));
$total_resultados = $res_tr['t'];

// Contamos mensajes sin leer en el sistema
$res_tm = mysqli_fetch_assoc(mysqli_query($conexion,
    "SELECT COUNT(*) as t FROM mensajes_contacto WHERE leido = 0"));
$mensajes_pendientes = $res_tm['t'];

// Calculamos el porcentaje global de avance
$pct_global = $total_estudiantes > 0
    ? round(($total_resultados / $total_estudiantes) * 100)
    : 0;

// Traemos cuantos estudiantes hay por cada especialidad
$res_esp = mysqli_query($conexion,
    "SELECT especialidad, COUNT(*) as total
     FROM resultados
     GROUP BY especialidad
     ORDER BY total DESC");
$por_esp = [];
while ($f = mysqli_fetch_assoc($res_esp)) {
    $por_esp[] = $f;
}

// Traemos el avance por cada curso
// COUNT(DISTINCT u.id) = total de estudiantes en ese curso
// COUNT(DISTINCT r.id_usuario) = cuantos de ese curso ya completaron
$res_cur = mysqli_query($conexion,
    "SELECT u.curso,
            COUNT(DISTINCT u.id) as total_est,
            COUNT(DISTINCT r.id_usuario) as con_res
     FROM usuarios u
     LEFT JOIN resultados r ON u.id = r.id_usuario
     WHERE u.rol = 'estudiante'
     GROUP BY u.curso
     ORDER BY u.curso ASC");
$por_curso = [];
while ($f = mysqli_fetch_assoc($res_cur)) {
    $por_curso[] = $f;
}
?>

<!-- Titulo de la pagina -->
<div class="page-header">
    <h2>👑 Panel Rector</h2>
    <p>Reporte general del sistema de orientacion vocacional.</p>
</div>

<!-- Tres tarjetas con los numeros principales -->
<div class="grid-3">

    <div class="tarjeta" style="text-align:center;">
        <h3>👥 Estudiantes Totales</h3>
        <p class="metrica-num" style="color:var(--azul-brillante);"><?php echo $total_estudiantes; ?></p>
        <p style="color:#2d3748; font-size:13px;">registrados en el sistema</p>
    </div>

    <div class="tarjeta" style="text-align:center;">
        <h3>✅ Con Resultado IA</h3>
        <p class="metrica-num" style="color:var(--verde-neon);"><?php echo $total_resultados; ?></p>
        <!-- Barra de progreso global -->
        <div class="barra-progreso">
            <div class="barra-relleno" style="width:<?php echo $pct_global; ?>%"></div>
        </div>
        <p style="color:#2d3748; font-size:13px;"><?php echo $pct_global; ?>% de avance global</p>
    </div>

    <div class="tarjeta" style="text-align:center;">
        <h3>✉️ Mensajes Pendientes</h3>
        <p class="metrica-num" style="color:#e67e22;"><?php echo $mensajes_pendientes; ?></p>
        <a href="index.php?pagina=contacto" class="btn btn-borde" style="font-size:12px; padding:6px 14px;">
            Ver mensajes
        </a>
    </div>

</div>

<!-- Tabla de resultados por especialidad -->
<div class="tarjeta">
    <h3>🎓 Resultados por Especialidad</h3>

    <?php if (empty($por_esp)): ?>
        <p style="color:#2d3748; font-size:14px; margin-top:10px;">Sin resultados aun.</p>
    <?php else: ?>
    <div class="tabla-wrapper" style="margin-top:14px;">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Especialidad</th>
                    <th>Estudiantes</th>
                    <th>Porcentaje del total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($por_esp as $e):
                    // Calculamos el porcentaje que representa cada especialidad
                    $p = $total_resultados > 0
                        ? round(($e['total'] / $total_resultados) * 100)
                        : 0;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($e['especialidad']); ?></td>
                    <td><span class="badge badge-verde"><?php echo $e['total']; ?></span></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="barra-progreso" style="flex:1; height:8px; margin:0;">
                                <div class="barra-relleno" style="width:<?php echo $p; ?>%"></div>
                            </div>
                            <span style="font-size:13px; color:#2d3748; width:35px;">
                                <?php echo $p; ?>%
                            </span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Tabla de avance por curso -->
<div class="tarjeta">
    <h3>📚 Avance por Curso</h3>
    <div class="tabla-wrapper" style="margin-top:14px;">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Estudiantes</th>
                    <th>Completados</th>
                    <th>Pendientes</th>
                    <th>Avance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($por_curso as $c):
                    // Calculamos cuantos faltan y el porcentaje del curso
                    $pend = $c['total_est'] - $c['con_res'];
                    $pc   = $c['total_est'] > 0
                        ? round(($c['con_res'] / $c['total_est']) * 100)
                        : 0;
                ?>
                <tr>
                    <td><strong>Decimo "<?php echo substr($c['curso'], 2); ?>"</strong></td>
                    <td><?php echo $c['total_est']; ?></td>
                    <td><span class="badge badge-verde"><?php echo $c['con_res']; ?></span></td>
                    <td>
                        <?php if ($pend > 0): ?>
                            <span class="badge badge-gris"><?php echo $pend; ?></span>
                        <?php else: ?>
                            <!-- Si no hay pendientes, todos completaron -->
                            <span class="badge badge-verde">✓ Todos</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div class="barra-progreso" style="flex:1; height:6px; margin:0;">
                                <div class="barra-relleno" style="width:<?php echo $pc; ?>%"></div>
                            </div>
                            <span style="font-size:12px; color:#2d3748; width:30px;">
                                <?php echo $pc; ?>%
                            </span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Boton para ir al panel DECE completo -->
<div style="text-align:center; margin-top:5px;">
    <a href="index.php?pagina=dece" class="btn btn-primario">📊 Ver Panel DECE completo</a>
</div>
