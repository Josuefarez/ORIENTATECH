<?php
// ============================================================
// ARCHIVO: olvide_contrasena.php
// QUÉ HACE: Recuperación de contraseña por preguntas de seguridad.
//   PASO 1: El estudiante ingresa su correo.
//   PASO 2: Responde las 3 preguntas de seguridad.
//   PASO 3: Si son correctas, elige una nueva contraseña.
// ============================================================
session_start();
if (isset($_SESSION['id_usuario'])) { header("Location: index.php"); exit(); }
include('GENERAL/conexion.php');

$paso    = isset($_POST['paso']) ? intval($_POST['paso']) : 1;
$error   = "";
$usuario = null;

// Preguntas de seguridad (las mismas del registro)
$preguntas_seg = [
    1 => '¿Cuál es tu comida favorita?',
    2 => '¿Cuál es el nombre de tu mascota (o tu animal favorito)?',
    3 => '¿En qué ciudad naciste?',
];

// ── PASO 1: Verificar correo ─────────────────────────────────
if ($paso === 1 && isset($_POST['correo'])) {
    $correo_s = mysqli_real_escape_string($conexion, trim($_POST['correo']));
    $res = mysqli_query($conexion,
        "SELECT id, nombre, seg_resp1, seg_resp2, seg_resp3
         FROM usuarios WHERE correo='$correo_s' AND activo=1 LIMIT 1");
    if (mysqli_num_rows($res) === 0) {
        $error = "No existe una cuenta con ese correo.";
        $paso  = 1;
    } else {
        $usuario = mysqli_fetch_assoc($res);
        // Verificamos que tenga preguntas de seguridad configuradas
        if (empty($usuario['seg_resp1'])) {
            $error = "Esta cuenta no tiene preguntas de seguridad configuradas. Contacta al DECE.";
            $paso  = 1;
        } else {
            $paso = 2; // Avanzamos a las preguntas
        }
    }
}

// ── PASO 2: Verificar respuestas de seguridad ────────────────
elseif ($paso === 2 && isset($_POST['correo_hidden'])) {
    $correo_s = mysqli_real_escape_string($conexion, trim($_POST['correo_hidden']));
    $res = mysqli_query($conexion,
        "SELECT id, nombre, seg_resp1, seg_resp2, seg_resp3
         FROM usuarios WHERE correo='$correo_s' AND activo=1 LIMIT 1");
    $usuario = mysqli_fetch_assoc($res);

    $r1 = strtolower(trim($_POST['seg_resp1'] ?? ''));
    $r2 = strtolower(trim($_POST['seg_resp2'] ?? ''));
    $r3 = strtolower(trim($_POST['seg_resp3'] ?? ''));

    if ($r1 !== $usuario['seg_resp1'] || $r2 !== $usuario['seg_resp2'] || $r3 !== $usuario['seg_resp3']) {
        $error = "Una o más respuestas son incorrectas. Intenta de nuevo.";
        $paso  = 2;
    } else {
        $paso = 3; // Respuestas correctas → nueva contraseña
    }
}

// ── PASO 3: Guardar nueva contraseña ─────────────────────────
elseif ($paso === 3 && isset($_POST['correo_hidden'], $_POST['nueva_contrasena'])) {
    $correo_s = mysqli_real_escape_string($conexion, trim($_POST['correo_hidden']));
    $nueva    = $_POST['nueva_contrasena'];
    $confirmar = $_POST['confirmar_contrasena'];

    $res = mysqli_query($conexion,
        "SELECT id, nombre FROM usuarios WHERE correo='$correo_s' AND activo=1 LIMIT 1");
    $usuario = mysqli_fetch_assoc($res);

    if (strlen($nueva) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
        $paso  = 3;
    } elseif ($nueva !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
        $paso  = 3;
    } else {
        $id_u     = $usuario['id'];
        $nueva_s = mysqli_real_escape_string($conexion, $nueva);
        mysqli_query($conexion, "UPDATE usuarios SET contrasena='$nueva_s' WHERE id=$id_u");
        $paso = 4; // ¡Listo!
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrientaTech · Recuperar Contraseña</title>
    <link rel="stylesheet" href="GENERAL/estilos.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box" style="max-width:440px;">

        <!-- Cabecera -->
        <div class="login-box-header">
            <div class="icono">
                <?php if ($paso===1) echo '🔑';
                elseif ($paso===2) echo '🛡️';
                elseif ($paso===3) echo '🔒';
                else echo '✅'; ?>
            </div>
            <h1>
                <?php if ($paso===1) echo 'RECUPERAR CONTRASEÑA';
                elseif ($paso===2) echo 'PREGUNTAS DE SEGURIDAD';
                elseif ($paso===3) echo 'NUEVA CONTRASEÑA';
                else echo '¡LISTO!'; ?>
            </h1>
            <p>
                <?php if ($paso===1) echo 'Ingresa tu correo para continuar';
                elseif ($paso===2) echo 'Responde tus 3 preguntas de seguridad';
                elseif ($paso===3) echo 'Elige una nueva contraseña segura';
                else echo 'Tu contraseña fue actualizada correctamente'; ?>
            </p>
        </div>

        <div class="login-box-body">

            <!-- Indicador de pasos -->
            <div class="pasos-recuperacion">
                <div class="paso-item <?php echo $paso>=1?'activo':''; ?> <?php echo $paso>1?'completado':''; ?>">
                    <div class="paso-num"><?php echo $paso>1?'✓':'1'; ?></div>
                    <span>Correo</span>
                </div>
                <div class="paso-linea <?php echo $paso>1?'completada':''; ?>"></div>
                <div class="paso-item <?php echo $paso>=2?'activo':''; ?> <?php echo $paso>2?'completado':''; ?>">
                    <div class="paso-num"><?php echo $paso>2?'✓':'2'; ?></div>
                    <span>Seguridad</span>
                </div>
                <div class="paso-linea <?php echo $paso>2?'completada':''; ?>"></div>
                <div class="paso-item <?php echo $paso>=3?'activo':''; ?> <?php echo $paso>3?'completado':''; ?>">
                    <div class="paso-num"><?php echo $paso>3?'✓':'3'; ?></div>
                    <span>Nueva clave</span>
                </div>
            </div>

            <?php if ($error): ?>
            <div class="alerta alerta-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- ── PASO 1: Correo ── -->
            <?php if ($paso===1): ?>
            <form method="POST">
                <input type="hidden" name="paso" value="1">
                <div class="campo">
                    <label>Correo Electrónico</label>
                    <input type="email" name="correo" placeholder="tucorreo@uets.edu.ec" autofocus
                           value="<?php echo isset($_POST['correo'])?htmlspecialchars($_POST['correo']):''; ?>">
                </div>
                <button type="submit" class="btn btn-verde btn-completo">Continuar →</button>
            </form>

            <!-- ── PASO 2: Preguntas de seguridad ── -->
            <?php elseif ($paso===2): ?>
            <form method="POST">
                <input type="hidden" name="paso" value="2">
                <input type="hidden" name="correo_hidden" value="<?php echo htmlspecialchars($_POST['correo'] ?? $_POST['correo_hidden'] ?? ''); ?>">

                <div style="background:var(--amarillo-fondo);border:1.5px solid #f0c96a;border-radius:8px;padding:14px 16px;margin-bottom:16px;">
                    <p style="font-size:12px;font-weight:700;color:var(--azul-oscuro);margin-bottom:4px;">
                        🛡️ Verificando identidad de:
                        <strong><?php echo htmlspecialchars($usuario['nombre']); ?></strong>
                    </p>
                    <p style="font-size:12px;color:#5a6a82;">
                        Responde exactamente igual a como las escribiste al registrarte.
                    </p>
                </div>

                <?php foreach ($preguntas_seg as $num => $preg): ?>
                <div class="campo">
                    <label><?php echo $num; ?>. <?php echo $preg; ?></label>
                    <input type="text" name="seg_resp<?php echo $num; ?>"
                           placeholder="Tu respuesta"
                           value="<?php echo isset($_POST["seg_resp$num"])?htmlspecialchars($_POST["seg_resp$num"]):''; ?>">
                </div>
                <?php endforeach; ?>

                <button type="submit" class="btn btn-verde btn-completo">Verificar Respuestas →</button>
            </form>

            <!-- ── PASO 3: Nueva contraseña ── -->
            <?php elseif ($paso===3): ?>
            <form method="POST">
                <input type="hidden" name="paso" value="3">
                <input type="hidden" name="correo_hidden" value="<?php echo htmlspecialchars($_POST['correo_hidden'] ?? ''); ?>">

                <div class="alerta alerta-exito" style="margin-bottom:16px;">
                    ✅ Identidad verificada. Ahora elige tu nueva contraseña.
                </div>

                <div class="campo">
                    <label>Nueva Contraseña</label>
                    <div class="campo-pass">
                        <input type="password" name="nueva_contrasena" id="pass-nueva" placeholder="Mínimo 6 caracteres">
                        <button type="button" class="btn-ojo" id="ojo-nueva" onclick="togglePass('pass-nueva','ojo-nueva')">
                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line class="linea-tajar" x1="3" y1="3" x2="21" y2="21"/></svg>
                        </button>
                    </div>
                </div>
                <div class="campo">
                    <label>Confirmar Contraseña</label>
                    <div class="campo-pass">
                        <input type="password" name="confirmar_contrasena" id="pass-conf" placeholder="Repite tu contraseña">
                        <button type="button" class="btn-ojo" id="ojo-conf" onclick="togglePass('pass-conf','ojo-conf')">
                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line class="linea-tajar" x1="3" y1="3" x2="21" y2="21"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-verde btn-completo">Guardar Nueva Contraseña →</button>
            </form>

            <!-- ── PASO 4: Éxito ── -->
            <?php else: ?>
            <div style="text-align:center;padding:10px 0 20px;">
                <div style="font-size:64px;margin-bottom:12px;">🎉</div>
                <p style="color:#1a2438;font-size:15px;margin-bottom:20px;">
                    ¡Contraseña actualizada! Ya puedes iniciar sesión con tu nueva clave.
                </p>
                <a href="login.php" class="btn btn-verde btn-completo">Ir al inicio de sesión →</a>
            </div>
            <?php endif; ?>

            <?php if ($paso < 4): ?>
            <p style="text-align:center;margin-top:18px;font-size:13px;">
                <a href="login.php" style="color:var(--azul-oscuro);font-weight:600;text-decoration:none;">
                    ← Volver al inicio de sesión
                </a>
            </p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
function togglePass(inputId, botonId) {
    var input = document.getElementById(inputId);
    var boton = document.getElementById(botonId);
    boton.classList.add('animar');
    boton.addEventListener('animationend', function() { boton.classList.remove('animar'); }, {once:true});
    if (input.type==='password') { input.type='text'; boton.classList.add('activo'); }
    else { input.type='password'; boton.classList.remove('activo'); }
}
</script>
</body>
</html>
