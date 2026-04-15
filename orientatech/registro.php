<?php
// ============================================================
// ARCHIVO: registro.php
// QUÉ HACE: Permite crear una cuenta de estudiante.
//           Pide 3 preguntas de seguridad que se usarán
//           para recuperar la contraseña si la olvida.
// ============================================================
session_start();
if (isset($_SESSION['id_usuario'])) { header("Location: index.php"); exit(); }
include('GENERAL/conexion.php');

$error  = "";
$exito  = "";
$cursos = ['10A','10B','10C','10D','10E','10F','10G','10H','10I','10J'];

// Las 3 preguntas de seguridad fijas
$preguntas_seg = [
    1 => '¿Cuál es tu comida favorita?',
    2 => '¿Cuál es el nombre de tu mascota (o tu animal favorito)?',
    3 => '¿En qué ciudad naciste?',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre']);
    $apellido  = trim($_POST['apellido']);
    $correo    = trim($_POST['correo']);
    $curso     = trim($_POST['curso']);
    $contrasena = $_POST['contrasena'];
    $confirmar  = $_POST['confirmar'];
    $resp1      = strtolower(trim($_POST['seg_resp1']));
    $resp2      = strtolower(trim($_POST['seg_resp2']));
    $resp3      = strtolower(trim($_POST['seg_resp3']));

    if (empty($nombre) || empty($apellido) || empty($correo) || empty($curso) || empty($contrasena)) {
        $error = "Por favor completa todos los campos obligatorios.";
    } elseif (!in_array($curso, $cursos)) {
        $error = "Selecciona un curso válido.";
    } elseif (!str_ends_with(strtolower($correo), '@uets.edu.ec')) {
        $error = "El correo debe pertenecer a la institución (@uets.edu.ec).";
    } elseif (strlen($contrasena) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($contrasena !== $confirmar) {
        $error = "Las contraseñas no coinciden.";
    } elseif (empty($resp1) || empty($resp2) || empty($resp3)) {
        $error = "Debes responder las 3 preguntas de seguridad.";
    } else {
        $correo_s = mysqli_real_escape_string($conexion, $correo);
        $verificar = mysqli_query($conexion, "SELECT id FROM usuarios WHERE correo = '$correo_s'");
        if (mysqli_num_rows($verificar) > 0) {
            $error = "Ya existe una cuenta con ese correo electrónico.";
        } else {
            $nombre_s     = mysqli_real_escape_string($conexion, $nombre);
            $contrasena_s = mysqli_real_escape_string($conexion, $contrasena);
            $apellido_s = mysqli_real_escape_string($conexion, $apellido);
            $curso_s    = mysqli_real_escape_string($conexion, $curso);
            
            $r1_s       = mysqli_real_escape_string($conexion, $resp1);
            $r2_s       = mysqli_real_escape_string($conexion, $resp2);
            $r3_s       = mysqli_real_escape_string($conexion, $resp3);

            $sql = "INSERT INTO usuarios
                    (nombre, apellido, correo, contrasena, rol, curso, seg_resp1, seg_resp2, seg_resp3)
                    VALUES
                    ('$nombre_s','$apellido_s','$correo_s','$contrasena_s','estudiante','$curso_s','$r1_s','$r2_s','$r3_s')";

            if (mysqli_query($conexion, $sql)) {
                $exito = "¡Cuenta creada exitosamente! Ya puedes iniciar sesión.";
            } else {
                $error = "Ocurrió un error al crear la cuenta. Intenta de nuevo.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrientaTech · Registro</title>
    <link rel="stylesheet" href="GENERAL/estilos.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box" style="max-width:520px;">

        <div class="login-box-header">
            <div class="icono">📝</div>
            <h1>CREAR CUENTA</h1>
            <p>Registro para estudiantes de Décimo Año · OrientaTech UETS</p>
        </div>

        <div class="login-box-body">

            <?php if ($exito !== ""): ?>
                <div class="alerta alerta-exito">✅ <?php echo htmlspecialchars($exito); ?></div>
                <a href="login.php" class="btn btn-primario btn-completo">Ir al inicio de sesión →</a>
            <?php else: ?>

                <?php if ($error !== ""): ?>
                    <div class="alerta alerta-error">⚠️ <?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="">

                    <!-- Datos personales -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="campo">
                            <label>Nombres</label>
                            <input type="text" name="nombre" placeholder="Ej: Carlos"
                                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                        </div>
                        <div class="campo">
                            <label>Apellidos</label>
                            <input type="text" name="apellido" placeholder="Ej: Pérez"
                                   value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>">
                        </div>
                    </div>

                    <div class="campo">
                        <label>Correo Electrónico</label>
                        <input type="email" name="correo" placeholder="tucorreo@uets.edu.ec"
                               pattern=".+@uets\.edu\.ec" title="El correo debe terminar en @uets.edu.ec"
                               value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>">
                    </div>

                    <div class="campo">
                        <label>Curso</label>
                        <select name="curso">
                            <option value="">-- Selecciona tu curso --</option>
                            <?php foreach ($cursos as $c): ?>
                            <option value="<?php echo $c; ?>" <?php echo (isset($_POST['curso']) && $_POST['curso']===$c)?'selected':''; ?>>
                                Décimo "<?php echo substr($c,2); ?>"
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="campo">
                            <label>Contraseña</label>
                            <div class="campo-pass">
                                <input type="password" name="contrasena" id="pass1" placeholder="Mín. 6 caracteres">
                                <button type="button" class="btn-ojo" id="ojo1" onclick="togglePass('pass1','ojo1')">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line class="linea-tajar" x1="3" y1="3" x2="21" y2="21"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="campo">
                            <label>Confirmar Contraseña</label>
                            <div class="campo-pass">
                                <input type="password" name="confirmar" id="pass2" placeholder="Repite tu contraseña">
                                <button type="button" class="btn-ojo" id="ojo2" onclick="togglePass('pass2','ojo2')">
                                    <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/><line class="linea-tajar" x1="3" y1="3" x2="21" y2="21"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Preguntas de seguridad -->
                    <div style="background:var(--amarillo-fondo);border:1.5px solid #f0c96a;border-radius:8px;padding:16px 18px;margin:16px 0 4px;">
                        <p style="font-size:12px;font-weight:700;color:var(--azul-oscuro);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:12px;">
                            🔐 Preguntas de Seguridad
                        </p>
                        <p style="font-size:12px;color:#5a6a82;margin-bottom:14px;">
                            Si olvidas tu contraseña, deberás responder estas 3 preguntas para recuperarla.
                            Escribe respuestas que no olvides fácilmente.
                        </p>
                        <?php foreach ($preguntas_seg as $num => $preg): ?>
                        <div class="campo">
                            <label><?php echo $num; ?>. <?php echo $preg; ?></label>
                            <input type="text" name="seg_resp<?php echo $num; ?>"
                                   placeholder="Tu respuesta (no importan mayúsculas)"
                                   value="<?php echo isset($_POST["seg_resp$num"]) ? htmlspecialchars($_POST["seg_resp$num"]) : ''; ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="btn btn-verde btn-completo" style="margin-top:10px;">
                        Crear mi Cuenta →
                    </button>
                </form>

            <?php endif; ?>

            <p style="text-align:center;margin-top:18px;color:#5a6a82;font-size:13px;">
                ¿Ya tienes cuenta?
                <a href="login.php" style="color:var(--azul-oscuro);font-weight:600;text-decoration:none;">
                    Iniciar sesión
                </a>
            </p>
        </div>
    </div>
</div>
<script>
function togglePass(inputId, botonId) {
    var input = document.getElementById(inputId);
    var boton = document.getElementById(botonId);
    boton.classList.add('animar');
    boton.addEventListener('animationend', function() { boton.classList.remove('animar'); }, {once:true});
    if (input.type === 'password') { input.type='text'; boton.classList.add('activo'); }
    else { input.type='password'; boton.classList.remove('activo'); }
}
</script>
</body>
</html>
