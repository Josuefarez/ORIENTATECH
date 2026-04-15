<?php
// ============================================================
// ARCHIVO: PAGINAS/coordinador.php
// ROL: coordinador — ve estudiantes de su especialidad
// ============================================================

$id_usuario = $_SESSION['id_usuario'];

// Obtener especialidad del coordinador
$res_coord = mysqli_query($conexion, "SELECT especialidad_coord FROM usuarios WHERE id=$id_usuario");
$coord     = mysqli_fetch_assoc($res_coord);
$mi_esp    = $coord['especialidad_coord'] ?? '';

// Estudiantes con esa especialidad
$esp_s      = mysqli_real_escape_string($conexion, $mi_esp);
$sql        = "SELECT u.nombre, u.apellido, u.correo, u.curso, r.fecha_resultado
               FROM resultados r
               INNER JOIN usuarios u ON r.id_usuario = u.id
               WHERE r.especialidad = '$esp_s'
               ORDER BY u.curso ASC, u.apellido ASC";
$res        = mysqli_query($conexion, $sql);
$estudiantes = [];
while ($f = mysqli_fetch_assoc($res)) { $estudiantes[] = $f; }

// Total general de esa especialidad
$total = count($estudiantes);

// Por curso
$por_curso = [];
foreach ($estudiantes as $e) {
    $c = $e['curso'] ?? 'Sin curso';
    $por_curso[$c] = ($por_curso[$c] ?? 0) + 1;
}
?>

<div class="page-header">
    <h2>📐 Mi Especialidad</h2>
    <p>Coordinacion de <strong><?php echo htmlspecialchars($mi_esp); ?></strong></p>
</div>

<!-- Resumen -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
    <div class="tarjeta" style="text-align:center;">
        <p style="font-size:13px; color:#8a9bb5; margin-bottom:8px;">Total estudiantes asignados</p>
        <p style="font-size:48px; font-weight:700; color:#0d2b5e; line-height:1;"><?php echo $total; ?></p>
        <p style="font-size:12px; color:#8a9bb5; margin-top:6px;">
            con especialidad <?php echo htmlspecialchars($mi_esp); ?>
        </p>
    </div>
    <div class="tarjeta">
        <h3 style="margin-bottom:12px;">📊 Por Curso</h3>
        <?php if (empty($por_curso)): ?>
            <p style="color:#8a9bb5; font-size:13px;">Aun no hay estudiantes asignados.</p>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <?php foreach ($por_curso as $curso => $cant): ?>
                <div style="display:flex; justify-content:space-between; align-items:center;
                            padding:6px 10px; background:#f4f6fa; border-radius:8px;">
                    <span style="font-size:13px; color:#1a2438; font-weight:600;">
                        Decimo <?php echo htmlspecialchars(substr($curso, 2)); ?>
                    </span>
                    <span style="background:#0d2b5e; color:#fff; font-size:12px;
                                 padding:2px 10px; border-radius:20px;">
                        <?php echo $cant; ?> estudiantes
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tabla de estudiantes -->
<div class="tarjeta">
    <h3>👥 Estudiantes en <?php echo htmlspecialchars($mi_esp); ?> (<?php echo $total; ?>)</h3>
    <?php if (empty($estudiantes)): ?>
        <p style="color:#8a9bb5; font-size:13px; margin-top:12px;">
            Aun no hay estudiantes asignados a esta especialidad.
        </p>
    <?php else: ?>
    <div class="tabla-wrapper" style="margin-top:14px;">
        <table class="tabla" style="table-layout:fixed; width:100%;">
            <thead>
                <tr>
                    <th style="width:30%">Nombre</th>
                    <th style="width:35%">Correo</th>
                    <th style="width:15%">Curso</th>
                    <th style="width:20%">Fecha Resultado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $est): ?>
                <tr>
                    <td><?php echo htmlspecialchars($est['nombre'].' '.$est['apellido']); ?></td>
                    <td style="font-size:12px; word-break:break-all;">
                        <?php echo htmlspecialchars($est['correo']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($est['curso'] ?? '—'); ?></td>
                    <td style="font-size:12px; color:#2d3748;">
                        <?php echo $est['fecha_resultado'] ?? '—'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
