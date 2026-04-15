<?php
session_start();
if (isset($_SESSION['id_usuario'])) { header("Location: index.php"); exit(); }
include('GENERAL/conexion.php');
$error      = "";
$msg_dece   = "";

// ── Formulario de contacto al DECE ────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contacto_dece'])) {
    $cn = trim($_POST['ct_nombre']);
    $cc = trim($_POST['ct_correo']);
    $ca = trim($_POST['ct_asunto']);
    $cp = trim($_POST['ct_problema']);
    if (empty($cn) || empty($cc) || empty($ca) || empty($cp)) {
        $msg_dece = "error:Completa todos los campos.";
    } elseif (!str_ends_with(strtolower($cc), '@uets.edu.ec')) {
        $msg_dece = "error:El correo debe terminar en @uets.edu.ec.";
    } else {
        $cn_s = mysqli_real_escape_string($conexion, $cn);
        $cc_s = mysqli_real_escape_string($conexion, $cc);
        $ca_s = mysqli_real_escape_string($conexion, $ca);
        $cp_s = mysqli_real_escape_string($conexion, $cp);
        $mens = "Nombre: $cn_s | Correo: $cc_s | Asunto: $ca_s | Problema: $cp_s";
        // Guardamos como mensaje sin id_usuario (0 = no registrado)
        // Buscamos si existe algun usuario DECE para asignarle el mensaje
        $sql_dece = "SELECT id FROM usuarios WHERE rol='dece' LIMIT 1";
        $res_dece = mysqli_query($conexion, $sql_dece);
        if ($res_dece && mysqli_num_rows($res_dece) > 0) {
            $fila_dece = mysqli_fetch_assoc($res_dece);
            $id_dece   = $fila_dece['id'];
            mysqli_query($conexion, "INSERT INTO mensajes_contacto (id_usuario, asunto, mensaje)
                                     VALUES ($id_dece, '$ca_s', '$mens')");
        }
        $msg_dece = "exito:Tu mensaje fue enviado al DECE. Te responderan pronto.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['contacto_dece'])) {
    $correo     = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    if (empty($correo) || empty($contrasena)) {
        $error = "Por favor completa todos los campos.";
    } else {
        $correo_s    = mysqli_real_escape_string($conexion, $correo);
        $contrasena_s = mysqli_real_escape_string($conexion, $contrasena);
        
        $sql = "SELECT id, nombre, apellido, rol, curso FROM usuarios
                WHERE correo = '$correo_s' AND contrasena = '$contrasena_s' AND activo = 1";
        $resultado = mysqli_query($conexion, $sql);
        if (mysqli_num_rows($resultado) === 1) {
            $usuario = mysqli_fetch_assoc($resultado);
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nombre']     = $usuario['nombre'] . ' ' . $usuario['apellido'];
            $_SESSION['rol']        = $usuario['rol'];
            $_SESSION['curso']      = $usuario['curso'];
            header("Location: index.php?pagina=inicio");
            exit();
        } else {
            $error = "Correo o contrasena incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion - UETS</title>
    <link rel="stylesheet" href="GENERAL/estilos.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box" style="max-width:420px;">

        <div class="login-box-header">
            <img src="IMAGENES/LOGO.png" alt="OrientaTech" class="logo-login">
            <h1>OrientaTech</h1>
            <p>Unidad Educativa Técnico Salesiaño &middot; 2025-2026</p>
        </div>

        <div class="login-box-body">

            <?php if ($error !== ""): ?>
                <div class="alerta alerta-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="campo">
                    <label>Correo Electronico</label>
                    <input type="email" name="correo" placeholder="tucorreo@uets.edu.ec"
                           value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>">
                </div>

                <!-- Campo contrasena con ojo ver/ocultar -->
                <div class="campo">
                    <label>Contrasena</label>
                    <div class="campo-pass">
                        <input type="password" name="contrasena" id="pass-login" placeholder="••••••••">
                        <button type="button" class="btn-ojo" id="ojo-login" onclick="togglePass('pass-login','ojo-login')" title="Ver / Ocultar contrasena">
                            <!-- Icono SVG del ojo -->
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <!-- Contorno del ojo -->
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <!-- Pupila -->
                                <circle cx="12" cy="12" r="3"/>
                                <!-- Linea diagonal que "taja" el ojo cuando esta oculto -->
                                <line class="linea-tajar" x1="3" y1="3" x2="21" y2="21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div style="text-align:right; margin-bottom:10px; margin-top:-4px;">
                    <a href="olvide_contrasena.php"
                       style="font-size:12px; color:var(--azul-claro); text-decoration:none;">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>
                <button type="submit" class="btn btn-verde btn-completo" style="margin-top:8px;">
                    Ingresar al Sistema &rarr;
                </button>

            </form>

            <p style="text-align:center; margin-top:22px; color:#4a5568; font-size:13px;">
                No tienes cuenta?
                <a href="registro.php" style="color:var(--azul-oscuro); font-weight:600; text-decoration:none;">
                    Registrate aqui
                </a>
            </p>
            <p style="text-align:center; margin-top:8px; color:#5a6a82; font-size:12px;">
                Problemas para ingresar?
                <a href="#" onclick="document.getElementById('modal-dece').style.display='flex'; return false;"
                   style="color:#1a4080; font-weight:600; text-decoration:none;">
                    Habla con el DECE
                </a>
            </p>

<!-- Modal contacto DECE -->
<div id="modal-dece" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:28px; width:90%; max-width:440px;
                box-shadow:0 8px 32px rgba(0,0,0,0.18); position:relative;">

        <!-- Cerrar -->
        <button onclick="document.getElementById('modal-dece').style.display='none'"
                style="position:absolute; top:12px; right:16px; background:none; border:none;
                       font-size:20px; cursor:pointer; color:#8a9bb5;">✕</button>

        <h3 style="color:#0d2b5e; font-size:16px; margin-bottom:6px;">📬 Contactar al DECE</h3>
        <p style="font-size:12px; color:#8a9bb5; margin-bottom:16px;">
            Cuéntanos tu problema y el DECE te ayudará.
        </p>

        <?php if ($msg_dece !== ""): $pt = explode(":", $msg_dece, 2); ?>
        <div style="padding:10px 14px; border-radius:8px; margin-bottom:14px; font-size:13px;
             background:<?php echo $pt[0]==='exito'?'#d1fae5':'#fee2e2'; ?>;
             color:<?php echo $pt[0]==='exito'?'#065f46':'#991b1b'; ?>;">
            <?php echo $pt[0]==='exito'?'✅':'⚠️'; ?> <?php echo htmlspecialchars($pt[1]); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="contacto_dece" value="1">
            <div style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:4px;">
                    Nombre completo
                </label>
                <input type="text" name="ct_nombre" placeholder="Tu nombre"
                       style="width:100%; padding:9px 12px; border:1px solid #d0dcf0; border-radius:8px;
                              font-size:13px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:4px;">
                    Correo electrónico
                </label>
                <input type="email" name="ct_correo" placeholder="tucorreo@uets.edu.ec"
                       pattern=".+@uets\.edu\.ec" title="El correo debe terminar en @uets.edu.ec"
                       style="width:100%; padding:9px 12px; border:1px solid #d0dcf0; border-radius:8px;
                              font-size:13px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:4px;">
                    Asunto
                </label>
                <input type="text" name="ct_asunto" placeholder="Ej: No puedo ingresar a mi cuenta"
                       style="width:100%; padding:9px 12px; border:1px solid #d0dcf0; border-radius:8px;
                              font-size:13px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:4px;">
                    Describe tu problema
                </label>
                <textarea name="ct_problema" placeholder="Explica qué problema tienes al intentar ingresar..." rows="3"
                          style="width:100%; padding:9px 12px; border:1px solid #d0dcf0; border-radius:8px;
                                 font-size:13px; resize:vertical; box-sizing:border-box; font-family:inherit;"></textarea>
            </div>
            <button type="submit"
                    style="width:100%; padding:11px; background:#0d2b5e; color:#fff; border:none;
                           border-radius:10px; font-size:14px; font-weight:600; cursor:pointer;">
                📤 Enviar al DECE
            </button>
        </form>
    </div>
</div>

        </div>
    </div>
</div>

<script>
// Funcion que muestra u oculta la contrasena al hacer clic en el ojo
function togglePass(inputId, botonId) {
    var input  = document.getElementById(inputId);
    var boton  = document.getElementById(botonId);

    // Agregamos la animacion de parpadeo
    boton.classList.add('animar');
    boton.addEventListener('animationend', function() {
        boton.classList.remove('animar');
    }, { once: true });

    // Alternamos entre mostrar y ocultar
    if (input.type === 'password') {
        input.type = 'text';          // Mostramos el texto
        boton.classList.add('activo'); // El ojo se pone dorado
    } else {
        input.type = 'password';        // Ocultamos con puntos
        boton.classList.remove('activo'); // El ojo vuelve a gris
    }
}
</script>

</body>
</html>
