<?php
// ============================================================
// ARCHIVO: PAGINAS/admin.php
// ROL: admin — gestiona todos los usuarios del sistema
// ============================================================

$msg = "";

$especialidades = ['IEME', 'MCM', 'EMA', 'Mecatrónica', 'Informática', 'Ciencias'];
$roles_gestionables = ['dece', 'rector', 'coordinador'];

// ── Crear usuario ────────────────────────────────────────────
if (isset($_POST['accion']) && $_POST['accion'] === 'crear_usuario') {
    $nombre   = trim($_POST['nuevo_nombre']);
    $apellido = trim($_POST['nuevo_apellido']);
    $correo   = trim($_POST['nuevo_correo']);
    $clave    = trim($_POST['nuevo_clave']);
    $rol_nuevo = trim($_POST['nuevo_rol']);
    $esp       = trim($_POST['nuevo_esp'] ?? '');

    if (empty($nombre) || empty($apellido) || empty($correo) || empty($clave) || empty($rol_nuevo)) {
        $msg = "error:Completa todos los campos obligatorios.";
    } elseif (!str_ends_with(strtolower($correo), '@uets.edu.ec')) {
        $msg = "error:El correo debe terminar en @uets.edu.ec.";
    } elseif (strlen($clave) < 6) {
        $msg = "error:La contrasena debe tener al menos 6 caracteres.";
    } elseif ($rol_nuevo === 'coordinador' && empty($esp)) {
        $msg = "error:Selecciona la especialidad del coordinador.";
    } elseif (!in_array($rol_nuevo, $roles_gestionables)) {
        $msg = "error:Rol no valido.";
    } else {
        $n_s  = mysqli_real_escape_string($conexion, $nombre);
        $a_s  = mysqli_real_escape_string($conexion, $apellido);
        $c_s  = mysqli_real_escape_string($conexion, $correo);
        $p_s  = mysqli_real_escape_string($conexion, $clave);
        $r_s  = mysqli_real_escape_string($conexion, $rol_nuevo);
        $e_s  = mysqli_real_escape_string($conexion, $esp);
        $chk  = mysqli_query($conexion, "SELECT id FROM usuarios WHERE correo='$c_s'");
        if (mysqli_num_rows($chk) > 0) {
            $msg = "error:Ya existe un usuario con ese correo.";
        } else {
            mysqli_query($conexion, "INSERT INTO usuarios (nombre,apellido,correo,contrasena,rol,especialidad_coord)
                                     VALUES ('$n_s','$a_s','$c_s','$p_s','$r_s','$e_s')");
            $msg = "exito:Usuario $rol_nuevo creado correctamente.";
        }
    }
}

// ── Editar usuario ───────────────────────────────────────────
if (isset($_POST['accion']) && $_POST['accion'] === 'editar_usuario') {
    $id_u     = intval($_POST['id_u']);
    $nombre   = trim($_POST['edit_nombre']);
    $apellido = trim($_POST['edit_apellido']);
    $correo   = trim($_POST['edit_correo']);
    $clave    = trim($_POST['edit_clave']);
    $esp      = trim($_POST['edit_esp'] ?? '');

    if (empty($nombre) || empty($apellido) || empty($correo)) {
        $msg = "error:Nombre, apellido y correo son obligatorios.";
    } elseif (!str_ends_with(strtolower($correo), '@uets.edu.ec')) {
        $msg = "error:El correo debe terminar en @uets.edu.ec.";
    } else {
        $n_s = mysqli_real_escape_string($conexion, $nombre);
        $a_s = mysqli_real_escape_string($conexion, $apellido);
        $c_s = mysqli_real_escape_string($conexion, $correo);
        $e_s = mysqli_real_escape_string($conexion, $esp);
        if (!empty($clave)) {
            $p_s = mysqli_real_escape_string($conexion, $clave);
            mysqli_query($conexion, "UPDATE usuarios SET nombre='$n_s',apellido='$a_s',
                correo='$c_s',contrasena='$p_s',especialidad_coord='$e_s' WHERE id=$id_u");
        } else {
            mysqli_query($conexion, "UPDATE usuarios SET nombre='$n_s',apellido='$a_s',
                correo='$c_s',especialidad_coord='$e_s' WHERE id=$id_u");
        }
        $msg = "exito:Usuario actualizado correctamente.";
    }
}

// ── Desactivar/Activar usuario ───────────────────────────────
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id_t = intval($_GET['toggle']);
    mysqli_query($conexion, "UPDATE usuarios SET activo = IF(activo=1,0,1) WHERE id=$id_t AND rol != 'admin'");
    header("Location: index.php?pagina=admin");
    exit();
}

// ── Cargar todos los usuarios gestionables ───────────────────
$usuarios = [];
$res_u = mysqli_query($conexion, "SELECT id, nombre, apellido, correo, rol, especialidad_coord, activo
                                   FROM usuarios
                                   WHERE rol IN ('dece','rector','coordinador')
                                   ORDER BY rol ASC, apellido ASC");
while ($f = mysqli_fetch_assoc($res_u)) { $usuarios[] = $f; }

// ── Usuario a editar ─────────────────────────────────────────
$editar_id  = isset($_GET['editar']) ? intval($_GET['editar']) : 0;
$u_editar   = null;
if ($editar_id > 0) {
    $re = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id=$editar_id AND rol != 'admin' AND rol != 'estudiante'");
    $u_editar = mysqli_fetch_assoc($re);
}

// Iconos por rol
$iconos_rol = ['dece' => '📊', 'rector' => '👑', 'coordinador' => '📐'];
$colores_rol = ['dece' => '#1a4080', 'rector' => '#7c3aed', 'coordinador' => '#065f46'];
?>

<div class="page-header">
    <h2>⚙️ Panel Admin</h2>
    <p>Gestiona todos los usuarios del sistema.</p>
</div>

<?php if ($msg !== ""): $pt = explode(":", $msg, 2); ?>
<div class="alerta alerta-<?php echo $pt[0]; ?>" style="margin-bottom:16px;">
    <?php echo $pt[0]==='exito'?'✅':'⚠️'; ?> <?php echo htmlspecialchars($pt[1]); ?>
</div>
<?php endif; ?>

<!-- ── Crear usuario ── -->
<div class="tarjeta">
    <h3>➕ Crear Nuevo Usuario</h3>
    <form method="POST" action="index.php?pagina=admin" style="margin-top:14px;">
        <input type="hidden" name="accion" value="crear_usuario">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="campo" style="margin:0;"><label>Nombre</label>
                <input type="text" name="nuevo_nombre" placeholder="Nombre"></div>
            <div class="campo" style="margin:0;"><label>Apellido</label>
                <input type="text" name="nuevo_apellido" placeholder="Apellido"></div>
            <div class="campo" style="margin:0;"><label>Correo (@uets.edu.ec)</label>
                <input type="email" name="nuevo_correo" placeholder="usuario@uets.edu.ec"
                       pattern=".+@uets\.edu\.ec"></div>
            <div class="campo" style="margin:0;"><label>Contrasena</label>
                <input type="password" name="nuevo_clave" placeholder="Min. 6 caracteres"></div>
            <div class="campo" style="margin:0;"><label>Rol</label>
                <select name="nuevo_rol" onchange="toggleEsp(this.value)">
                    <option value="">Selecciona un rol</option>
                    <option value="dece">DECE</option>
                    <option value="rector">Rector</option>
                    <option value="coordinador">Coordinador de Area</option>
                </select>
            </div>
            <div class="campo" style="margin:0;" id="campo-esp" style="display:none;">
                <label>Especialidad (solo coordinador)</label>
                <select name="nuevo_esp">
                    <option value="">Selecciona especialidad</option>
                    <?php foreach ($especialidades as $e): ?>
                    <option value="<?php echo $e; ?>"><?php echo $e; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-verde" style="margin-top:14px;">➕ Crear Usuario</button>
    </form>
</div>

<!-- ── Editar usuario ── -->
<?php if ($u_editar): ?>
<div class="tarjeta" style="border:2px solid var(--amarillo);">
    <h3>✏️ Editando: <?php echo htmlspecialchars($u_editar['nombre'].' '.$u_editar['apellido']); ?>
        <span style="font-size:12px; color:#8a9bb5; margin-left:8px;">(<?php echo $u_editar['rol']; ?>)</span>
    </h3>
    <form method="POST" action="index.php?pagina=admin" style="margin-top:14px;">
        <input type="hidden" name="accion" value="editar_usuario">
        <input type="hidden" name="id_u" value="<?php echo $u_editar['id']; ?>">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="campo" style="margin:0;"><label>Nombre</label>
                <input type="text" name="edit_nombre" value="<?php echo htmlspecialchars($u_editar['nombre']); ?>"></div>
            <div class="campo" style="margin:0;"><label>Apellido</label>
                <input type="text" name="edit_apellido" value="<?php echo htmlspecialchars($u_editar['apellido']); ?>"></div>
            <div class="campo" style="margin:0;"><label>Correo</label>
                <input type="email" name="edit_correo" value="<?php echo htmlspecialchars($u_editar['correo']); ?>"
                       pattern=".+@uets\.edu\.ec"></div>
            <div class="campo" style="margin:0;"><label>Nueva contrasena <small style="color:#8a9bb5;">(opcional)</small></label>
                <input type="password" name="edit_clave" placeholder="Dejar vacio para no cambiar"></div>
            <?php if ($u_editar['rol'] === 'coordinador'): ?>
            <div class="campo" style="margin:0; grid-column:1/-1;"><label>Especialidad</label>
                <select name="edit_esp">
                    <?php foreach ($especialidades as $e): ?>
                    <option value="<?php echo $e; ?>" <?php echo $u_editar['especialidad_coord']===$e?'selected':''; ?>>
                        <?php echo $e; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
                <input type="hidden" name="edit_esp" value="">
            <?php endif; ?>
        </div>
        <div style="display:flex; gap:10px; margin-top:14px;">
            <button type="submit" class="btn btn-verde">💾 Guardar Cambios</button>
            <a href="index.php?pagina=admin" class="btn btn-borde">✖ Cancelar</a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- ── Tabla de usuarios ── -->
<div class="tarjeta">
    <h3>👥 Usuarios del Sistema (<?php echo count($usuarios); ?>)</h3>
    <div class="tabla-wrapper" style="margin-top:14px;">
        <table class="tabla" style="table-layout:fixed; width:100%;">
            <thead>
                <tr>
                    <th style="width:20%">Nombre</th>
                    <th style="width:25%">Correo</th>
                    <th style="width:12%">Rol</th>
                    <th style="width:15%">Especialidad</th>
                    <th style="width:10%">Estado</th>
                    <th style="width:18%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr style="<?php echo !$u['activo'] ? 'opacity:0.5;' : ''; ?>">
                    <td><?php echo htmlspecialchars($u['nombre'].' '.$u['apellido']); ?></td>
                    <td style="font-size:11px; word-break:break-all;"><?php echo htmlspecialchars($u['correo']); ?></td>
                    <td>
                        <span style="background:<?php echo $colores_rol[$u['rol']] ?? '#374151'; ?>;
                                     color:#fff; font-size:10px; padding:3px 8px; border-radius:20px;">
                            <?php echo $iconos_rol[$u['rol']] ?? ''; ?>
                            <?php echo $u['rol'] === 'coordinador' ? 'C. Area' : ucfirst($u['rol']); ?>
                        </span>
                    </td>
                    <td style="font-size:12px;">
                        <?php echo $u['especialidad_coord'] ? htmlspecialchars($u['especialidad_coord']) : '—'; ?>
                    </td>
                    <td>
                        <?php if ($u['activo']): ?>
                            <span class="badge badge-verde">Activo</span>
                        <?php else: ?>
                            <span class="badge badge-gris">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td style="display:flex; gap:6px; flex-wrap:wrap;">
                        <a href="index.php?pagina=admin&editar=<?php echo $u['id']; ?>"
                           class="btn btn-borde" style="font-size:11px; padding:4px 8px;">✏️</a>
                        <a href="index.php?pagina=admin&toggle=<?php echo $u['id']; ?>"
                           class="btn <?php echo $u['activo']?'btn-borde':'btn-verde'; ?>"
                           style="font-size:11px; padding:4px 8px;"
                           onclick="return confirm('<?php echo $u['activo']?'Desactivar':'Activar'; ?> este usuario?')">
                            <?php echo $u['activo'] ? '🔒' : '🔓'; ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleEsp(rol) {
    var campo = document.getElementById('campo-esp');
    campo.style.display = rol === 'coordinador' ? 'block' : 'none';
}
</script>
