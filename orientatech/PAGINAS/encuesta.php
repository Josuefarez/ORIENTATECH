<?php
// ============================================================
// ARCHIVO: PAGINAS/encuesta.php
// CAMBIOS:
//  1. Las preguntas aparecen en ORDEN ALEATORIO
//  2. Las OPCIONES dentro de cada pregunta también se mezclan
//     aleatoriamente -> la respuesta de electricidad no siempre
//     sera la A, ni la de ciencias siempre la misma letra
//  3. Se agrego opcion de CIENCIAS en cada pregunta (6 opciones)
//  4. Se guarda el TEXTO COMPLETO para que la IA analice de verdad
// ============================================================

$id_usuario = $_SESSION['id_usuario'];
$mensaje    = "";

$chk = mysqli_query($conexion,
    "SELECT id FROM resultados WHERE id_usuario = $id_usuario LIMIT 1");
if (mysqli_num_rows($chk) > 0) {
    header("Location: index.php?pagina=resultado");
    exit();
}

// ============================================================
// LAS 20 PREGUNTAS CON 6 OPCIONES CADA UNA
// La clave de cada opcion es la ESPECIALIDAD (no la letra A/B/C)
// Al mostrar, se mezclan y se les asigna letra dinamicamente
// ============================================================
$preguntas_base = [

    1 => [
        'texto' => '¿Que prefieres hacer en tu tiempo libre?',
        'IEME'        => 'Armar o reparar aparatos electricos y enchufes en casa',
        'MCM'         => 'Construir o fabricar objetos con metal u otros materiales',
        'EMA'         => 'Revisar motores, autos o mecanismos de vehiculos',
        'MECATRONICA' => 'Programar un robot o armar circuitos automaticos',
        'INFORMATICA' => 'Jugar, programar o aprender cosas nuevas en la computadora',
        'CIENCIAS'    => 'Leer, investigar o hacer experimentos sobre como funciona el mundo',
    ],

    2 => [
        'texto' => '¿Cual es tu materia favorita en el colegio?',
        'IEME'        => 'Fisica aplicada a circuitos y electricidad',
        'MCM'         => 'Dibujo tecnico o talleres de fabricacion metalica',
        'EMA'         => 'Fisica mecanica y funcionamiento de motores',
        'MECATRONICA' => 'Robotica, electronica y sistemas de automatizacion',
        'INFORMATICA' => 'Computacion, matematicas o logica',
        'CIENCIAS'    => 'Biologia, Quimica o Matematicas avanzadas',
    ],

    3 => [
        'texto' => '¿Como describes tu forma de trabajar?',
        'IEME'        => 'Metodico, me gusta seguir pasos y procedimientos electricos',
        'MCM'         => 'Manual, disfruto fabricar cosas con mis propias manos',
        'EMA'         => 'Practico, me gusta trabajar directamente con motores y vehiculos',
        'MECATRONICA' => 'Innovador, me gusta combinar mecanica con tecnologia',
        'INFORMATICA' => 'Analitico, prefiero resolver problemas usando la computadora',
        'CIENCIAS'    => 'Investigador, me gusta estudiar, analizar datos y sacar conclusiones',
    ],

    4 => [
        'texto' => '¿Que tipo de proyecto te gustaria desarrollar?',
        'IEME'        => 'Disenar e instalar un sistema electrico para una casa o fabrica',
        'MCM'         => 'Fabricar una pieza metalica precisa usando torno o fresadora',
        'EMA'         => 'Diagnosticar y reparar el sistema electronico de un auto',
        'MECATRONICA' => 'Programar un brazo robotico o un sistema automatizado',
        'INFORMATICA' => 'Crear una aplicacion web o un programa de computadora',
        'CIENCIAS'    => 'Hacer una investigacion cientifica y presentar los resultados',
    ],

    5 => [
        'texto' => '¿Cual de estas actividades harias por horas sin aburrirte?',
        'IEME'        => 'Revisar instalaciones electricas e identificar fallas',
        'MCM'         => 'Soldar piezas de metal y darles forma estructural',
        'EMA'         => 'Usar equipos de diagnostico automotriz para encontrar fallas',
        'MECATRONICA' => 'Disenar circuitos electronicos para sistemas automaticos',
        'INFORMATICA' => 'Escribir codigo y depurar errores en programas',
        'CIENCIAS'    => 'Leer articulos cientificos, hacer calculos o analizar resultados',
    ],

    6 => [
        'texto' => '¿Con que tipo de herramientas o recursos te sientes mas comodo?',
        'IEME'        => 'Multimetros, cables, tableros electricos y fusibles',
        'MCM'         => 'Tornos, fresadoras, taladros y soldadoras industriales',
        'EMA'         => 'Escaneres automotrices, llaves mecanicas y calibradores',
        'MECATRONICA' => 'Sensores, actuadores y microcontroladores como Arduino',
        'INFORMATICA' => 'Computadoras, software de programacion e internet',
        'CIENCIAS'    => 'Libros, laboratorios, microscopios y material de investigacion',
    ],

    7 => [
        'texto' => '¿Que palabras te describen mejor?',
        'IEME'        => 'Electrico, preciso, instalador de sistemas',
        'MCM'         => 'Constructor, resistente, detallista con materiales',
        'EMA'         => 'Mecanico, diagnosticador, apasionado de los autos',
        'MECATRONICA' => 'Automatizador, robotico, innovador tecnologico',
        'INFORMATICA' => 'Programador, logico, amante de la tecnologia digital',
        'CIENCIAS'    => 'Curioso, investigador, amante del conocimiento cientifico',
    ],

    8 => [
        'texto' => '¿Si un aparato o sistema deja de funcionar, que haces primero?',
        'IEME'        => 'Reviso el cableado y busco cortos o fallas electricas',
        'MCM'         => 'Lo desarmo mecanicamente y analizo las piezas danadas',
        'EMA'         => 'Conecto el escaner y leo los codigos de error del vehiculo',
        'MECATRONICA' => 'Analizo los sensores y el sistema de control automatico',
        'INFORMATICA' => 'Busco el error en el software o reinstalo el sistema',
        'CIENCIAS'    => 'Investigo la causa raiz del problema con metodo cientifico',
    ],

    9 => [
        'texto' => '¿Que tipo de problema te llama mas la atencion resolver?',
        'IEME'        => 'Un circuito electrico que no funciona correctamente',
        'MCM'         => 'Una pieza de metal que hay que fabricar con mucha exactitud',
        'EMA'         => 'Un motor con ruidos extranos o que no arranca bien',
        'MECATRONICA' => 'Un robot que no ejecuta el movimiento que fue programado',
        'INFORMATICA' => 'Un programa con errores de codigo o logica que hay que depurar',
        'CIENCIAS'    => 'Un fenomeno natural o social que necesita ser explicado con datos',
    ],

    10 => [
        'texto' => '¿Cual de estas profesiones te parece mas interesante?',
        'IEME'        => 'Tecnico electricista o instalador de sistemas electricos industriales',
        'MCM'         => 'Mecanico tornero, soldador o fabricante de estructuras metalicas',
        'EMA'         => 'Mecanico automotriz o tecnico en diagnostico de vehiculos',
        'MECATRONICA' => 'Tecnico en automatizacion industrial o programador de PLCs',
        'INFORMATICA' => 'Desarrollador de software o tecnico en soporte informatico',
        'CIENCIAS'    => 'Investigador, docente universitario o profesional en ciencias puras',
    ],

    11 => [
        'texto' => '¿En un trabajo en equipo, que rol prefieres tomar?',
        'IEME'        => 'El que revisa y asegura todas las conexiones electricas',
        'MCM'         => 'El que construye y da forma fisica al producto final',
        'EMA'         => 'El que ensambla y prueba los sistemas mecanicos',
        'MECATRONICA' => 'El que programa y controla el sistema automatizado',
        'INFORMATICA' => 'El que desarrolla el software o la interfaz del usuario',
        'CIENCIAS'    => 'El que investiga, analiza datos y redacta los informes',
    ],

    12 => [
        'texto' => '¿Como prefieres trabajar en el dia a dia?',
        'IEME'        => 'Con las manos, manipulando cables y componentes electricos',
        'MCM'         => 'Con las manos, fabricando piezas con maquinas industriales',
        'EMA'         => 'Con las manos, reparando vehiculos y diagnosticando motores',
        'MECATRONICA' => 'Combinando trabajo fisico con programacion de sistemas',
        'INFORMATICA' => 'Principalmente frente a la computadora resolviendo problemas digitales',
        'CIENCIAS'    => 'Leyendo, investigando, haciendo calculos y analizando informacion',
    ],

    13 => [
        'texto' => '¿Que sector de la industria o campo te atrae mas?',
        'IEME'        => 'Energia electrica, plantas industriales o telecomunicaciones',
        'MCM'         => 'Metalurgia, manufactura pesada o construccion metalica',
        'EMA'         => 'Industria automotriz, talleres mecanicos o transporte',
        'MECATRONICA' => 'Automatizacion industrial y manufactura de alta tecnologia',
        'INFORMATICA' => 'Tecnologia de la informacion, software o startups digitales',
        'CIENCIAS'    => 'Investigacion cientifica, salud, medio ambiente o educacion',
    ],

    14 => [
        'texto' => '¿Que logro personal te daria mas satisfaccion?',
        'IEME'        => 'Dejar funcionando perfectamente una instalacion electrica industrial',
        'MCM'         => 'Fabricar una pieza metalica exacta que nadie mas pudo hacer',
        'EMA'         => 'Reparar un vehiculo que ningun otro taller habia podido arreglar',
        'MECATRONICA' => 'Programar un sistema completamente automatico desde cero',
        'INFORMATICA' => 'Lanzar una aplicacion o programa que mucha gente comience a usar',
        'CIENCIAS'    => 'Publicar una investigacion o ganar una olimpiada de ciencias',
    ],

    15 => [
        'texto' => '¿Como prefieres aprender algo completamente nuevo?',
        'IEME'        => 'Practicando directamente con instalaciones reales y circuitos',
        'MCM'         => 'Fabricando piezas y viendo el resultado con mis propias manos',
        'EMA'         => 'Trabajando directamente en un motor o vehiculo real',
        'MECATRONICA' => 'Ensamblando sistemas fisicos y luego programandolos',
        'INFORMATICA' => 'Leyendo documentacion y escribiendo codigo de prueba',
        'CIENCIAS'    => 'Estudiando teoria, tomando notas y haciendo experimentos controlados',
    ],

    16 => [
        'texto' => '¿Cual de estos cursos tomarias por gusto propio?',
        'IEME'        => 'Instalaciones electricas residenciales e industriales',
        'MCM'         => 'Soldadura, torneado y mecanizado de piezas metalicas',
        'EMA'         => 'Diagnostico computarizado de vehiculos modernos',
        'MECATRONICA' => 'Programacion de robots y sistemas automaticos con Arduino',
        'INFORMATICA' => 'Desarrollo web, bases de datos o programacion en Python',
        'CIENCIAS'    => 'Matematicas avanzadas, Fisica universitaria o Biologia molecular',
    ],

    17 => [
        'texto' => '¿Como reaccionas cuando algo no te sale bien a la primera?',
        'IEME'        => 'Reviso paso a paso el diagrama electrico hasta encontrar el fallo',
        'MCM'         => 'Analizo las medidas y vuelvo a mecanizar la pieza con mas cuidado',
        'EMA'         => 'Leo los codigos de falla y verifico el sistema mecanico con calma',
        'MECATRONICA' => 'Depuro el codigo del controlador y reviso cada sensor del sistema',
        'INFORMATICA' => 'Busco el error en el codigo linea por linea hasta encontrarlo',
        'CIENCIAS'    => 'Reviso la metodologia, busco variables que no considere y repito',
    ],

    18 => [
        'texto' => '¿En que ambiente de trabajo te sentiras mas a gusto?',
        'IEME'        => 'Plantas electricas, subestaciones o talleres de instalaciones',
        'MCM'         => 'Talleres de mecanizado con tornos, fresadoras y prensas',
        'EMA'         => 'Talleres automotrices, concesionarios o servicios tecnicos de autos',
        'MECATRONICA' => 'Laboratorios de automatizacion con robots, sensores y actuadores',
        'INFORMATICA' => 'Oficinas modernas, empresas de tecnologia o trabajo remoto desde casa',
        'CIENCIAS'    => 'Universidades, centros de investigacion o laboratorios cientificos',
    ],

    19 => [
        'texto' => '¿Cual crees que es tu mayor habilidad natural?',
        'IEME'        => 'Entender como funciona un sistema electrico con facilidad',
        'MCM'         => 'Fabricar o construir objetos fisicos con mucha precision',
        'EMA'         => 'Identificar fallas en sistemas mecanicos o motores rapidamente',
        'MECATRONICA' => 'Integrar partes fisicas con programacion y electronica',
        'INFORMATICA' => 'Resolver problemas usando logica y programacion',
        'CIENCIAS'    => 'Analizar informacion, sacar conclusiones y explicar fenomenos',
    ],

    20 => [
        'texto' => '¿Como te imaginas trabajando dentro de 10 anos?',
        'IEME'        => 'Gestionando instalaciones electricas en una empresa industrial',
        'MCM'         => 'Dirigiendo un taller de mecanizado o fabricacion metalica',
        'EMA'         => 'Teniendo mi propio taller de mecanica automotriz especializada',
        'MECATRONICA' => 'Disenando sistemas automatizados para fabricas o empresas',
        'INFORMATICA' => 'Liderando proyectos de software o con mi propia empresa de tecnologia',
        'CIENCIAS'    => 'Trabajando como investigador, docente universitario o en el area de salud',
    ],
];

// ============================================================
// MEZCLAMOS el orden de las preguntas aleatoriamente
// ============================================================
$ids_preguntas = array_keys($preguntas_base);
shuffle($ids_preguntas);

// ============================================================
// Para cada pregunta, mezclamos también el orden de sus opciones
// Guardamos el orden mezclado en un array para usarlo al mostrar
// y al procesar el POST
// ============================================================
$orden_opciones = [];
$especialidades = ['IEME','MCM','EMA','MECATRONICA','INFORMATICA','CIENCIAS'];
foreach ($ids_preguntas as $idx => $id_pregunta) {
    $posicion = $idx + 1;
    $mezcla = $especialidades;
    shuffle($mezcla);
    $orden_opciones[$posicion] = $mezcla;
}

// ---- Si el estudiante envio el formulario ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $todas = true;
    for ($i = 1; $i <= 20; $i++) {
        if (!isset($_POST["p$i"]) || empty($_POST["p$i"])) {
            $todas = false;
            break;
        }
    }

    if (!$todas) {
        $mensaje = "error:Debes responder todas las preguntas antes de continuar.";
    } else {

        mysqli_query($conexion,
            "DELETE FROM respuestas_encuesta WHERE id_usuario = $id_usuario");

        for ($i = 1; $i <= 20; $i++) {
            // El valor del radio guardado es directamente la ESPECIALIDAD
            // (IEME, MCM, EMA, etc.) — no la letra A/B/C
            $especialidad_elegida = mysqli_real_escape_string($conexion, $_POST["p$i"]);
            $id_original          = (int)$_POST["id_original_p$i"];

            $texto_pregunta  = $preguntas_base[$id_original]['texto'] ?? '';
            $texto_respuesta = $preguntas_base[$id_original][$especialidad_elegida] ?? '';

            $tp = mysqli_real_escape_string($conexion, $texto_pregunta);
            $tr = mysqli_real_escape_string($conexion, $texto_respuesta);

            // Guardamos la especialidad como "respuesta" en lugar de una letra suelta
            mysqli_query($conexion,
                "INSERT INTO respuestas_encuesta
                    (id_usuario, numero_pregunta, respuesta, texto_pregunta, texto_respuesta)
                 VALUES ($id_usuario, $i, '$especialidad_elegida', '$tp', '$tr')");
        }

        header("Location: index.php?pagina=resultado");
        exit();
    }
}
?>

<!-- Titulo -->
<div class="page-header">
    <h2>📋 Encuesta Vocacional</h2>
    <p>Responde con honestidad. La IA analizara tu perfil completo para orientarte.</p>
</div>

<?php if ($mensaje !== ""): ?>
    <?php $pt = explode(":", $mensaje, 2); ?>
    <div class="alerta alerta-<?php echo $pt[0]; ?>">⚠️ <?php echo htmlspecialchars($pt[1]); ?></div>
<?php endif; ?>

<form method="POST" action="index.php?pagina=encuesta" id="form-encuesta">

    <?php
    $letras_visibles = ['A','B','C','D','E','F'];
    $posicion = 1;
    foreach ($ids_preguntas as $id_original):
        $pregunta       = $preguntas_base[$id_original];
        $opciones_orden = $orden_opciones[$posicion]; // especialidades en orden mezclado
    ?>
    <div class="pregunta-bloque">
        <span class="pregunta-num">PREGUNTA <?php echo $posicion; ?> / 20</span>
        <p><?php echo htmlspecialchars($pregunta['texto']); ?></p>

        <!-- ID original para recuperar el texto al procesar el POST -->
        <input type="hidden"
               name="id_original_p<?php echo $posicion; ?>"
               value="<?php echo $id_original; ?>">

        <div class="opciones-radio">
            <?php foreach ($opciones_orden as $idx_letra => $especialidad_clave): ?>
            <label class="opcion-radio">
                <input type="radio"
                       name="p<?php echo $posicion; ?>"
                       value="<?php echo $especialidad_clave; ?>"
                       <?php
                           // Mantener seleccion si hubo error de validacion
                           echo (isset($_POST["p$posicion"]) && $_POST["p$posicion"] === $especialidad_clave) ? 'checked' : '';
                       ?>>
                <span>
                    <strong style="color:var(--azul-brillante);">
                        <?php echo $letras_visibles[$idx_letra]; ?>.
                    </strong>
                    <?php echo htmlspecialchars($pregunta[$especialidad_clave]); ?>
                </span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php $posicion++; endforeach; ?>

    <div style="text-align:center; margin-top:28px;">
        <div class="alerta alerta-info" style="margin-bottom:18px; text-align:left;">
            🤖 Al enviar, la <strong>Inteligencia Artificial</strong> leera el contenido
            completo de tus respuestas y hara un análisis real de tu perfil vocacional.
            Esto puede tomar unos segundos. ¡No cierres la página!
        </div>
        <button type="submit" class="btn btn-verde"
                style="font-size:16px; padding:14px 40px;"
                onclick="document.getElementById('spinner').style.display='block';
                         this.style.display='none';">
            🚀 Enviar y Obtener mi Especialidad
        </button>
        <div class="cargando" id="spinner" style="padding:30px; display:none;">
            <div class="spinner"></div>
            <p>LA IA ESTÁ ANALIZANDO TU PERFIL...</p>
        </div>
    </div>

</form>
