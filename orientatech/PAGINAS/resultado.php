<?php
// ============================================================
// ARCHIVO: PAGINAS/resultado.php
// Actualizado para trabajar con el nuevo sistema de encuesta
// donde las respuestas guardadas son el nombre de la especialidad
// (IEME, MCM, EMA, MECATRONICA, INFORMATICA, CIENCIAS)
// en lugar de letras sueltas (A, B, C, D, E)
// ============================================================

$id_usuario = $_SESSION['id_usuario'];
$nombre     = $_SESSION['nombre'];

// ---- Verificamos si ya tiene un resultado guardado ----
$sql_existe = "SELECT especialidad, justificacion, fecha_resultado
               FROM resultados
               WHERE id_usuario = $id_usuario
               ORDER BY fecha_resultado DESC
               LIMIT 1";
$res_existe = mysqli_query($conexion, $sql_existe);

if (mysqli_num_rows($res_existe) > 0) {
    $datos         = mysqli_fetch_assoc($res_existe);
    $especialidad  = $datos['especialidad'];
    $justificacion = $datos['justificacion'];

} else {

    // Verificamos que tenga las 20 respuestas
    $sql_resp = "SELECT numero_pregunta, respuesta, texto_pregunta, texto_respuesta
                 FROM respuestas_encuesta
                 WHERE id_usuario = $id_usuario
                 ORDER BY número_pregunta ASC";
    $res_resp = mysqli_query($conexion, $sql_resp);

    if (mysqli_num_rows($res_resp) < 20) {
        header("Location: index.php?pagina=encuesta");
        exit();
    }

    // ============================================================
    // PLAN A: Contamos cual especialidad se repitio mas en las respuestas
    // ============================================================
    $resumen_ia = "";
    $respuestas_arr = [];
    while ($f = mysqli_fetch_assoc($res_resp)) {
        $tp = !empty($f['texto_pregunta'])  ? $f['texto_pregunta']  : '(pregunta)';
        $tr = !empty($f['texto_respuesta']) ? $f['texto_respuesta'] : $f['respuesta'];
        $resumen_ia   .= "- Pregunta: {$tp}\n  Respuesta: {$tr}\n\n";
        $respuestas_arr[] = strtoupper(trim($f['respuesta']));
    }

    $mapa_esp = [
        'IEME'        => 'IEME (Instalaciones, Equipos y Maquinas Electricas)',
        'MCM'         => 'MCM (Mecanizado y Construcciones Metalicas)',
        'EMA'         => 'EMA (Electromecanica Automotriz)',
        'MECATRONICA' => 'Mecatronica',
        'INFORMATICA' => 'Informatica',
        'CIENCIAS'    => 'Ciencias',
        'A' => 'IEME (Instalaciones, Equipos y Maquinas Electricas)',
        'B' => 'MCM (Mecanizado y Construcciones Metalicas)',
        'C' => 'EMA (Electromecanica Automotriz)',
        'D' => 'Mecatronica',
        'E' => 'Informatica',
    ];

    // Contar frecuencia de cada especialidad
    $conteo = [];
    foreach ($respuestas_arr as $r) {
        if (isset($mapa_esp[$r])) {
            $esp_nombre = $mapa_esp[$r];
            $conteo[$esp_nombre] = ($conteo[$esp_nombre] ?? 0) + 1;
        }
    }

    $especialidad  = "";
    $justificacion = "";

    if (!empty($conteo)) {
        arsort($conteo);
        $especialidad  = array_key_first($conteo);
        $justificacion = "Segun el analisis de tus respuestas, tu perfil vocacional se orienta hacia $especialidad. Esta especialidad te permitira desarrollar tus habilidades tecnicas y construir una carrera solida en el area que mas se alinea con tus intereses.";
    }

    // ============================================================
    // PLAN B: Si el conteo no dio resultado, llamamos a la IA
    // ============================================================
    if (empty($especialidad)) {

        $api_key = "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";

        $prompt = "Eres un orientador vocacional experto para estudiantes de décimo año de una institucion técnica en Ecuador. Tu tarea es analizar las respuestas completas de un estudiante y determinar cual especialidad técnica se adapta mejor a su perfil personal.

Las especialidades disponibles son exactamente estas 6:
1. IEME (Instalaciones, Equipos y Maquinas Eléctricas)
2. MCM (Mecanizado y Construcciones Metálicas)
3. EMA (Electromecánica Automotriz)
4. Mecatronica
5. Informatica
6. Ciencias

Respuestas del estudiante:
$resumen_ia

Responde UNICAMENTE con este formato:
ESPECIALIDAD: [nombre exacto]
JUSTIFICACION: [3 oraciones en segunda persona explicando por que esa especialidad encaja con su perfil.]";

        $datos_api = json_encode([
            "model"       => "gpt-3.5-turbo",
            "messages"    => [
                ["role" => "system", "content" => "Eres un orientador vocacional experto. Responde en espanol con el formato exacto solicitado."],
                ["role" => "user",   "content" => $prompt]
            ],
            "max_tokens"  => 400,
            "temperature" => 0.4
        ]);

        $opciones_http = [
            "http" => [
                "method"  => "POST",
                "header"  => "Content-Type: application/json\r\n" .
                             "Authorization: Bearer " . $api_key . "\r\n",
                "content" => $datos_api,
                "timeout" => 45,
                "ignore_errors" => true,
            ],
            "ssl" => [
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ]
        ];

        $contexto      = stream_context_create($opciones_http);
        $respuesta_api = @file_get_contents("https://api.openai.com/v1/chat/completions", false, $contexto);

        if ($respuesta_api) {
            $json = json_decode($respuesta_api, true);
            if (isset($json['choices'][0]['message']['content'])) {
                $texto = trim($json['choices'][0]['message']['content']);
                foreach (explode("\n", $texto) as $linea) {
                    $linea = trim($linea);
                    if (strpos($linea, "ESPECIALIDAD:") === 0)
                        $especialidad = trim(str_replace("ESPECIALIDAD:", "", $linea));
                    if (strpos($linea, "JUSTIFICACION:") === 0)
                        $justificacion = trim(str_replace("JUSTIFICACION:", "", $linea));
                }
            }
        }

        // Validar que la especialidad sea una de las 6
        $validas = ['IEME','MCM','EMA','Mecatronica','Informatica','Ciencias'];
        $encontrada = false;
        foreach ($validas as $v) {
            if (stripos($especialidad, $v) !== false) {
                $especialidad = $mapa_esp[strtoupper($v)] ?? $v;
                $encontrada   = true;
                break;
            }
        }

        // Si la IA tampoco dio resultado, ponemos Informatica por defecto
        if (!$encontrada || empty($especialidad)) {
            $especialidad  = 'Informatica';
            $justificacion = "No fue posible determinar tu especialidad automaticamente. Te recomendamos hablar con el DECE para una orientacion personalizada.";
        }
    }

    // ---- Guardamos el resultado ----
    $esp_s  = mysqli_real_escape_string($conexion, $especialidad);
    $just_s = mysqli_real_escape_string($conexion, $justificacion);

    mysqli_query($conexion,
        "INSERT INTO resultados (id_usuario, especialidad, justificacion)
         VALUES ($id_usuario, '$esp_s', '$just_s')");
}

// Iconos por especialidad
$iconos = [
    "IEME"        => "⚡",
    "MCM"         => "🔩",
    "EMA"         => "🚗",
    "Mecatronica" => "🤖",
    "Informatica" => "💻",
    "Ciencias"    => "🔬",
];

$icono = "🎯";
foreach ($iconos as $clave => $ico) {
    if (stripos($especialidad, $clave) !== false) {
        $icono = $ico;
        break;
    }
}
?>

<div class="page-header">
    <h2>🤖 Resultado del Análisis de IA</h2>
    <p>Análisis personalizado completado para <?php echo htmlspecialchars($nombre); ?></p>
</div>

<div class="tarjeta resultado-ia">
    <div class="icono-res"><?php echo $icono; ?></div>
    <h2>TU ESPECIALIDAD TÉCNICA IDEAL ES:</h2>
    <div class="linea-dorada"></div>
    <div class="esp-nombre"><?php echo htmlspecialchars($especialidad); ?></div>
    <p><?php echo htmlspecialchars($justificacion); ?></p>
</div>

<div style="display:flex; gap:14px; justify-content:center; flex-wrap:wrap; margin-top:5px;">
    <a href="index.php?pagina=especialidades" class="btn btn-primario">🎓 Ver detalles de mi especialidad</a>
    <a href="index.php?pagina=contacto"       class="btn btn-borde">✉️ Contactar al DECE</a>
    <a href="index.php?pagina=inicio"         class="btn btn-borde">🏠 Volver al inicio</a>
</div>

<div class="alerta alerta-info" style="margin-top:22px;">
    ℹ️ Este resultado es una <strong>orientación</strong> basada en el análisis de la IA
    de tus 20 respuestas completas. Puedes conversarlo con el equipo del DECE.
</div>
