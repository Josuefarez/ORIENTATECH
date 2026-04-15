<?php
// ============================================================
// ARCHIVO: PAGINAS/contacto.php
// QUE HACE: 
//   - ESTUDIANTE: envia mensajes al DECE y ve las respuestas.
//   - DECE/RECTOR: ve todos los mensajes y puede responder.
// ============================================================

$id_usuario = $_SESSION['id_usuario'];
$rol        = $_SESSION['rol'];
$msg_estado = "";

// ── Estudiante envia mensaje ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rol === 'estudiante') {
    $asunto  = trim($_POST['asunto']);
    $mensaje = trim($_POST['mensaje']);
    if (empty($asunto) || empty($mensaje)) {
        $msg_estado = "error:Completa todos los campos.";
    } else {
        $as = mysqli_real_escape_string($conexion, $asunto);
        $ms = mysqli_real_escape_string($conexion, $mensaje);
        mysqli_query($conexion, "INSERT INTO mensajes_contacto (id_usuario, asunto, mensaje)
                                  VALUES ($id_usuario, '$as', '$ms')");
        $msg_estado = "exito:Tu mensaje fue enviado. El DECE lo revisara pronto.";
    }
}

// ── DECE/Rector responde un mensaje ──────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($rol === 'dece' || $rol === 'rector')) {
    $id_msg    = intval($_POST['id_mensaje']);
    $respuesta = trim($_POST['respuesta']);
    if (!empty($respuesta)) {
        $rs = mysqli_real_escape_string($conexion, $respuesta);
        mysqli_query($conexion, "UPDATE mensajes_contacto 
                                  SET respuesta='$rs', leido=1, fecha_respuesta=NOW() 
                                  WHERE id=$id_msg");
        $msg_estado = "exito:Respuesta enviada correctamente.";
    } else {
        $msg_estado = "error:Escribe una respuesta antes de enviar.";
    }
}

// ── Marcar como leido sin responder ──────────────────────────
if (isset($_GET['leer']) && ($rol === 'dece' || $rol === 'rector')) {
    $id_m = intval($_GET['leer']);
    mysqli_query($conexion, "UPDATE mensajes_contacto SET leido = 1 WHERE id = $id_m");
    header("Location: index.php?pagina=contacto");
    exit();
}

// ── Cargar mensajes segun rol ─────────────────────────────────
$mensajes = [];
if ($rol === 'dece' || $rol === 'rector') {
    $sql_m = "SELECT m.id, m.asunto, m.mensaje, m.leido, m.respuesta, m.fecha_envio, m.fecha_respuesta,
                     u.nombre, u.apellido, u.curso
              FROM mensajes_contacto m
              INNER JOIN usuarios u ON m.id_usuario = u.id
              ORDER BY m.leido ASC, m.fecha_envio DESC";
    $res_m = mysqli_query($conexion, $sql_m);
    while ($f = mysqli_fetch_assoc($res_m)) {
        $mensajes[] = $f;
    }
} elseif ($rol === 'estudiante') {
    // El estudiante ve SUS propios mensajes y las respuestas
    $sql_e = "SELECT id, asunto, mensaje, leido, respuesta, fecha_envio, fecha_respuesta
              FROM mensajes_contacto
              WHERE id_usuario = $id_usuario
              ORDER BY fecha_envio DESC";
    $res_e = mysqli_query($conexion, $sql_e);
    while ($f = mysqli_fetch_assoc($res_e)) {
        $mensajes[] = $f;
    }
}
?>

<div class="page-header">
    <h2>✉️ Contacto</h2>
    <p>
        <?php if ($rol === 'estudiante'): ?>
            Envia un mensaje al equipo del DECE.
        <?php else: ?>
            Bandeja de mensajes de estudiantes.
        <?php endif; ?>
    </p>
</div>

<?php if ($msg_estado !== ""): ?>
    <?php $pt = explode(":", $msg_estado, 2); ?>
    <div class="alerta alerta-<?php echo $pt[0]; ?>" style="margin-bottom:16px;">
        <?php echo $pt[0] === 'exito' ? '✅' : '⚠️'; ?>
        <?php echo htmlspecialchars($pt[1]); ?>
    </div>
<?php endif; ?>

<!-- ===== ESTUDIANTE: formulario + historial ===== -->
<?php if ($rol === 'estudiante'): ?>

<div class="grid-2">
    <div class="tarjeta">
        <h3>📬 Enviar Mensaje al DECE</h3>
        <form method="POST" action="index.php?pagina=contacto" style="margin-top:14px;">
            <div class="campo">
                <label>Asunto</label>
                <input type="text" name="asunto" placeholder="Ej: Duda sobre mi especialidad">
            </div>
            <div class="campo">
                <label>Mensaje</label>
                <textarea name="mensaje" placeholder="Escribe tu consulta aqui..."></textarea>
            </div>
            <button type="submit" class="btn btn-verde">📤 Enviar Mensaje</button>
        </form>
    </div>
    <div class="tarjeta">
        <h3>📍 Donde encontrarnos?</h3>
        <p style="color:#1a2438; font-size:14px; line-height:2; margin-top:10px;">
            🏫 <strong>Departamento DECE</strong><br>
            📍 Bloque administrativo, planta baja<br>
            🕐 Lunes a viernes - 07:30 a 14:00<br>
            📧 dece@uets.edu.ec
        </p>
    </div>
</div>

<!-- Historial de mensajes del estudiante -->
<?php if (!empty($mensajes)): ?>
<div class="tarjeta" style="margin-top:20px;">
    <h3>📩 Mis Mensajes</h3>
    <div style="margin-top:14px; display:flex; flex-direction:column; gap:14px;">
        <?php foreach ($mensajes as $m): ?>
        <div style="border:1px solid #d0dcf0; border-radius:10px; overflow:hidden;">
            <!-- Cabecera del mensaje -->
            <div style="background:#f4f6fa; padding:10px 14px; display:flex; justify-content:space-between; align-items:center;">
                <strong style="color:#0d2b5e;"><?php echo htmlspecialchars($m['asunto']); ?></strong>
                <span style="font-size:11px; color:#8a9bb5;"><?php echo $m['fecha_envio']; ?></span>
            </div>
            <!-- Mensaje del estudiante -->
            <div style="padding:12px 14px; font-size:13px; color:#374151; background:#fff;">
                <?php echo nl2br(htmlspecialchars($m['mensaje'])); ?>
            </div>
            <!-- Respuesta del DECE si existe -->
            <?php if ($m['respuesta']): ?>
            <div style="padding:12px 14px; font-size:13px; color:#1a4080; background:#ebf0fb; border-top:1px solid #d0dcf0;">
                <strong>💬 Respuesta del DECE</strong>
                <span style="font-size:11px; color:#8a9bb5; margin-left:8px;"><?php echo $m['fecha_respuesta']; ?></span><br>
                <span style="margin-top:6px; display:block;"><?php echo nl2br(htmlspecialchars($m['respuesta'])); ?></span>
            </div>
            <?php else: ?>
            <div style="padding:8px 14px; font-size:12px; color:#8a9bb5; background:#fff; border-top:1px solid #f0f0f0;">
                ⏳ Pendiente de respuesta
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<!-- ===== DECE / RECTOR: bandeja con respuestas ===== -->
<?php if ($rol === 'dece' || $rol === 'rector'): ?>

<div style="display:flex; flex-direction:column; gap:16px;">
    <?php if (empty($mensajes)): ?>
        <div class="tarjeta"><p style="color:#2d3748; font-size:14px;">No hay mensajes aun.</p></div>
    <?php else: ?>
        <?php foreach ($mensajes as $m): ?>
        <div class="tarjeta" style="border-left:4px solid <?php echo $m['leido'] ? '#94a3b8' : '#f5c518'; ?>; padding:0; overflow:hidden;">
            <!-- Cabecera -->
            <div style="padding:12px 16px; background:#f4f6fa; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <div>
                    <strong style="color:#0d2b5e;"><?php echo htmlspecialchars($m['nombre'].' '.$m['apellido']); ?></strong>
                    <?php if ($m['curso']): ?>
                        <span style="background:#0d2b5e; color:#fff; font-size:10px; padding:2px 8px; border-radius:20px; margin-left:8px;">
                            <?php echo htmlspecialchars($m['curso']); ?>
                        </span>
                    <?php endif; ?>
                    <div style="font-size:12px; color:#8a9bb5; margin-top:3px;"><?php echo $m['fecha_envio']; ?></div>
                </div>
                <?php if ($m['leido']): ?>
                    <span class="badge badge-gris">Leido</span>
                <?php else: ?>
                    <span class="badge badge-azul">Nuevo ✉️</span>
                <?php endif; ?>
            </div>

            <!-- Asunto y mensaje -->
            <div style="padding:12px 16px;">
                <div style="font-weight:600; color:#1a2438; margin-bottom:6px;">
                    📌 <?php echo htmlspecialchars($m['asunto']); ?>
                </div>
                <div style="font-size:13px; color:#374151; line-height:1.6;">
                    <?php echo nl2br(htmlspecialchars($m['mensaje'])); ?>
                </div>
            </div>

            <!-- Respuesta existente -->
            <?php if ($m['respuesta']): ?>
            <div style="padding:12px 16px; background:#ebf0fb; border-top:1px solid #d0dcf0;">
                <strong style="color:#1a4080; font-size:13px;">💬 Tu respuesta</strong>
                <span style="font-size:11px; color:#8a9bb5; margin-left:8px;"><?php echo $m['fecha_respuesta']; ?></span>
                <div style="font-size:13px; color:#374151; margin-top:6px;">
                    <?php echo nl2br(htmlspecialchars($m['respuesta'])); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Formulario de respuesta -->
            <div style="padding:12px 16px; border-top:1px solid #e2e8f0;">
                <form method="POST" action="index.php?pagina=contacto" style="display:flex; gap:10px; align-items:flex-end;">
                    <input type="hidden" name="id_mensaje" value="<?php echo $m['id']; ?>">
                    <div style="flex:1;">
                        <label style="font-size:12px; color:#8a9bb5; display:block; margin-bottom:4px;">
                            <?php echo $m['respuesta'] ? '✏️ Editar respuesta' : '💬 Escribir respuesta'; ?>
                        </label>
                        <textarea name="respuesta" rows="2"
                            style="width:100%; padding:8px 10px; border:1px solid #d0dcf0; border-radius:8px; font-size:13px; resize:vertical; font-family:inherit;"
                            placeholder="Escribe tu respuesta al estudiante..."><?php echo $m['respuesta'] ? htmlspecialchars($m['respuesta']) : ''; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-verde" style="white-space:nowrap; height:38px; padding:0 16px;">
                        📤 Responder
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php endif; ?>
