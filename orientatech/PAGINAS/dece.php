<?php
$rol        = $_SESSION['rol'];
$id_usuario = $_SESSION['id_usuario'];
$msg        = "";

// Creacion de usuarios solo disponible para el Admin

// ── Editar datos de un estudiante ────────────────────────────
if (isset($_POST['accion']) && $_POST['accion'] === 'editar_estudiante') {
    $id_est   = intval($_POST['id_est']);
    $nombre   = trim($_POST['edit_nombre']);
    $apellido = trim($_POST['edit_apellido']);
    $correo   = trim($_POST['edit_correo']);
    $curso    = trim($_POST['edit_curso']);
    $clave    = trim($_POST['edit_clave']);
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($curso)) {
        $msg = "error:Nombre, apellido, correo y curso son obligatorios.";
    } elseif (!str_ends_with(strtolower($correo), '@uets.edu.ec')) {
        $msg = "error:El correo debe terminar en @uets.edu.ec.";
    } else {
        $n_s  = mysqli_real_escape_string($conexion, $nombre);
        $a_s  = mysqli_real_escape_string($conexion, $apellido);
        $c_s  = mysqli_real_escape_string($conexion, $correo);
        $cu_s = mysqli_real_escape_string($conexion, $curso);
        if (!empty($clave)) {
            $p_s = mysqli_real_escape_string($conexion, $clave);
            mysqli_query($conexion, "UPDATE usuarios SET nombre='$n_s',apellido='$a_s',
                correo='$c_s',curso='$cu_s',contrasena='$p_s' WHERE id=$id_est AND rol='estudiante'");
        } else {
            mysqli_query($conexion, "UPDATE usuarios SET nombre='$n_s',apellido='$a_s',
                correo='$c_s',curso='$cu_s' WHERE id=$id_est AND rol='estudiante'");
        }
        $msg = "exito:Datos del estudiante actualizados.";
    }
}

// ── Filtro por curso ─────────────────────────────────────────
$filtro_curso = "";
$where        = "";
if (isset($_GET['curso']) && !empty($_GET['curso'])) {
    $filtro_curso = mysqli_real_escape_string($conexion, $_GET['curso']);
    $where = "AND u.curso = '$filtro_curso'";
}

// ── Estudiantes ──────────────────────────────────────────────
$sql = "SELECT u.id, u.nombre, u.apellido, u.correo, u.curso,
               r.especialidad, r.fecha_resultado
        FROM usuarios u
        LEFT JOIN resultados r ON u.id = r.id_usuario
        WHERE u.rol = 'estudiante' $where
        ORDER BY u.curso ASC, u.apellido ASC";
$res = mysqli_query($conexion, $sql);
$estudiantes = [];
while ($f = mysqli_fetch_assoc($res)) { $estudiantes[] = $f; }

// ── Especialidades ───────────────────────────────────────────
$res_esp = mysqli_query($conexion, "SELECT especialidad, COUNT(*) as total
                                     FROM resultados GROUP BY especialidad ORDER BY total DESC");
$por_esp = [];
while ($f = mysqli_fetch_assoc($res_esp)) { $por_esp[] = $f; }

$cursos = ['10A','10B','10C','10D','10E','10F','10G','10H','10I','10J'];

// ── Estudiante a editar ──────────────────────────────────────
$editar_id  = isset($_GET['editar']) ? intval($_GET['editar']) : 0;
$est_editar = null;
if ($editar_id > 0) {
    $re = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id=$editar_id AND rol='estudiante'");
    $est_editar = mysqli_fetch_assoc($re);
}
?>

<div class="page-header">
    <h2>📊 Panel DECE</h2>
    <p>Gestiona estudiantes, crea usuarios y revisa resultados.</p>
</div>

<?php if ($msg !== ""): $pt = explode(":", $msg, 2); ?>
<div class="alerta alerta-<?php echo $pt[0]; ?>" style="margin-bottom:16px;">
    <?php echo $pt[0]==='exito'?'✅':'⚠️'; ?> <?php echo htmlspecialchars($pt[1]); ?>
</div>
<?php endif; ?>

<!-- Distribucion -->
<div class="tarjeta">
    <h3>📈 Distribucion por Especialidad</h3>
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;">
        <?php if (empty($por_esp)): ?>
            <p style="color:#2d3748;">Aun no hay resultados.</p>
        <?php else: foreach ($por_esp as $e): ?>
            <div style="background:#ebf0fb;border:1px solid #d0dcf0;border-radius:10px;
                        padding:12px 18px;text-align:center;min-width:130px;">
                <p style="color:#0d2b5e;font-size:28px;font-weight:700;margin-bottom:4px;">
                    <?php echo $e['total']; ?></p>
                <p style="color:#1a2438;font-size:12px;"><?php echo htmlspecialchars($e['especialidad']); ?></p>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>



<!-- Editar estudiante -->
<?php if ($est_editar): ?>
<div class="tarjeta" style="border:2px solid var(--amarillo);">
    <h3>✏️ Editando: <?php echo htmlspecialchars($est_editar['nombre'].' '.$est_editar['apellido']); ?></h3>
    <form method="POST" action="index.php?pagina=dece" style="margin-top:14px;">
        <input type="hidden" name="accion" value="editar_estudiante">
        <input type="hidden" name="id_est" value="<?php echo $est_editar['id']; ?>">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div class="campo" style="margin:0;"><label>Nombre</label>
                <input type="text" name="edit_nombre" value="<?php echo htmlspecialchars($est_editar['nombre']); ?>"></div>
            <div class="campo" style="margin:0;"><label>Apellido</label>
                <input type="text" name="edit_apellido" value="<?php echo htmlspecialchars($est_editar['apellido']); ?>"></div>
            <div class="campo" style="margin:0;"><label>Correo</label>
                <input type="email" name="edit_correo" value="<?php echo htmlspecialchars($est_editar['correo']); ?>"
                       pattern=".+@uets\.edu\.ec"></div>
            <div class="campo" style="margin:0;"><label>Curso</label>
                <select name="edit_curso">
                    <?php foreach ($cursos as $c): ?>
                    <option value="<?php echo $c; ?>" <?php echo $est_editar['curso']===$c?'selected':''; ?>>
                        Decimo "<?php echo substr($c,2); ?>"
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo" style="margin:0;grid-column:1/-1;">
                <label>Nueva contrasena <small style="color:#8a9bb5;font-weight:400;">(dejar vacio para no cambiar)</small></label>
                <input type="password" name="edit_clave" placeholder="Nueva contrasena (opcional)">
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:14px;">
            <button type="submit" class="btn btn-verde">💾 Guardar Cambios</button>
            <a href="index.php?pagina=dece" class="btn btn-borde">✖ Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Filtro -->
<div class="tarjeta">
    <h3>🔍 Filtrar por Curso</h3>
    <form method="GET" action="index.php"
          style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;margin-top:14px;">
        <input type="hidden" name="pagina" value="dece">
        <div class="campo" style="margin:0;min-width:180px;"><label>Curso</label>
            <select name="curso">
                <option value="">Todos los cursos</option>
                <?php foreach ($cursos as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo $filtro_curso===$c?'selected':''; ?>>
                    Decimo "<?php echo substr($c,2); ?>"
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primario">🔍 Filtrar</button>
        <a href="index.php?pagina=dece" class="btn btn-borde">✖ Limpiar</a>
    </form>
</div>

<!-- Tabla estudiantes -->
<div class="tarjeta">
    <h3>👥 Estudiantes (<?php echo count($estudiantes); ?>)</h3>
    <div class="tabla-wrapper" style="margin-top:14px;">
        <table class="tabla" style="table-layout:fixed;width:100%;">
            <thead>
                <tr>
                    <th style="width:18%">Nombre</th>
                    <th style="width:22%">Correo</th>
                    <th style="width:7%">Curso</th>
                    <th style="width:20%">Especialidad</th>
                    <th style="width:15%">Fecha</th>
                    <th style="width:10%">Accion</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $est): ?>
                <tr>
                    <td><?php echo htmlspecialchars($est['nombre'].' '.$est['apellido']); ?></td>
                    <td style="font-size:11px;word-break:break-all;"><?php echo htmlspecialchars($est['correo']); ?></td>
                    <td><?php echo htmlspecialchars($est['curso']); ?></td>
                    <td>
                        <?php if ($est['especialidad']): ?>
                            <span class="badge badge-verde"><?php echo htmlspecialchars($est['especialidad']); ?></span>
                        <?php else: ?>
                            <span class="badge badge-gris">Sin completar</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:12px;color:#2d3748;"><?php echo $est['fecha_resultado']??'—'; ?></td>
                    <td>
                        <a href="index.php?pagina=dece&editar=<?php echo $est['id']; ?>"
                           class="btn btn-borde" style="font-size:11px;padding:4px 10px;">
                            ✏️ Editar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
