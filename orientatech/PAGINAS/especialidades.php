<?php
// ============================================================
// ARCHIVO: PAGINAS/especialidades.php
// QUÉ HACE: Muestra las 6 especialidades con descripción
//           y enlace directo a la página oficial de la UETS.
// ============================================================

$rol = $_SESSION['rol'];

$especialidades = [
    [
        'sigla'       => 'IEME',
        'nombre'      => 'Instalaciones, Equipos y Máquinas Eléctricas',
        'icono'       => '⚡',
        'descripcion' => 'Aprenderás sobre circuitos eléctricos, instalaciones industriales y domiciliarias, motores, generadores y sistemas de automatización eléctrica.',
        'url'         => 'https://uets.edu.ec/ieme/',
    ],
    [
        'sigla'       => 'MCM',
        'nombre'      => 'Mecanizado y Construcciones Metálicas',
        'icono'       => '🔩',
        'descripcion' => 'Trabajarás con tornos, fresadoras, soldadura y fabricación de piezas metálicas con precisión, interpretando planos y usando maquinaria especializada.',
        'url'         => 'https://uets.edu.ec/mcm/',
    ],
    [
        'sigla'       => 'EMA',
        'nombre'      => 'Electromecánica Automotriz',
        'icono'       => '🚗',
        'descripcion' => 'Te especializarás en los sistemas eléctricos y mecánicos de vehículos: frenos ABS, dirección, climatización, inyección electrónica y diagnóstico automotriz.',
        'url'         => 'https://uets.edu.ec/ema/',
    ],
    [
        'sigla'       => 'Mecatrónica',
        'nombre'      => 'Mecatrónica',
        'icono'       => '🤖',
        'descripcion' => 'Combina mecánica, electrónica y programación para diseñar y mantener sistemas automatizados, robots industriales y máquinas inteligentes de alta precisión.',
        'url'         => 'https://uets.edu.ec/mecatronica/',
    ],
    [
        'sigla'       => 'Informática',
        'nombre'      => 'Informática',
        'icono'       => '💻',
        'descripcion' => 'Desarrollarás habilidades en programación, bases de datos, redes, soporte técnico y desarrollo de software para resolver problemas con tecnología.',
        'url'         => 'https://uets.edu.ec/informatica/',
    ],
    [
        'sigla'       => 'Ciencias',
        'nombre'      => 'Bachillerato en Ciencias',
        'icono'       => '🔬',
        'descripcion' => 'Profundizarás en matemáticas, física, química y biología, preparándote para carreras universitarias científicas, de salud o ingeniería.',
        'url'         => 'https://uets.edu.ec/ciencias/',
    ],
];
?>

<div class="page-header">
    <h2>🎓 Especialidades Técnicas</h2>
    <p>Conoce las seis especialidades del Bachillerato Técnico de la institución.</p>
</div>

<div class="grid-3">
    <?php foreach ($especialidades as $esp): ?>
    <div class="esp-card">
        <span class="esp-icono"><?php echo $esp['icono']; ?></span>
        <h3><?php echo htmlspecialchars($esp['sigla']); ?></h3>
        <p style="color:#1a2438; margin-bottom:16px;">
            <strong style="color:var(--azul-oscuro);">
                <?php echo htmlspecialchars($esp['nombre']); ?>
            </strong><br><br>
            <?php echo htmlspecialchars($esp['descripcion']); ?>
        </p>

        <!-- Enlace a la página oficial del colegio -->
        <div style="text-align:center; margin-top:auto; padding-top:10px;">
            <a href="<?php echo $esp['url']; ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="enlace-especialidad">
                Ver en la página del colegio
                <span class="enlace-flecha">↗</span>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="alerta alerta-info" style="margin-top:5px;">
    🤖 La <strong>Inteligencia Artificial</strong> analizará tus respuestas y te recomendará
    la especialidad que mejor se adapta a tu perfil, habilidades e intereses.
</div>

<?php if ($rol === 'estudiante'): ?>
<div style="text-align:center; margin-top:20px;">
    <a href="index.php?pagina=encuesta" class="btn btn-verde">📋 Ir a la Encuesta</a>
</div>
<?php endif; ?>
