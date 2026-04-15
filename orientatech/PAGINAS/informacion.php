<?php
// PAGINAS/informacion.php
?>

<div class="page-header">
    <h2>ℹ️ Información del Sistema</h2>
    <p>Todo lo que necesitas saber sobre la Plataforma de OrientaTech.</p>
</div>

<div class="grid-2">
    <div class="tarjeta">
        <h3>🎯 ¿Qué es este sistema?</h3>
        <p style="color:#1a2438; font-size:14px; line-height:1.8;">
            Es una plataforma web de <strong style="color:var(--azul-brillante);">orientación vocacional con Inteligencia Artificial</strong>
            diseñada para ayudar a los estudiantes de Décimo Año a descubrir qué especialidad técnica
            se adapta mejor a sus intereses y habilidades.
        </p>
    </div>
    <div class="tarjeta">
        <h3>🤖 ¿Cómo usa la IA?</h3>
        <p style="color:#1a2438; font-size:14px; line-height:1.8;">
            El sistema usa la <strong style="color:var(--verde-neon);">API de OpenAI (GPT)</strong> para analizar tus 20 respuestas
            de forma inteligente, interpretando el patrón completo de tu perfil vocacional.
        </p>
    </div>
</div>

<div class="tarjeta">
    <h3>📋 ¿Cómo funciona el proceso?</h3>
    <div style="display:flex; gap:15px; flex-wrap:wrap; margin-top:15px;">
        <?php
        $pasos = [
            ['📝','PASO 1','Inicia sesion con tu correo y contrasena institucional.'],
            ['📋','PASO 2','Responde las 20 preguntas de la encuesta vocacional con honestidad.'],
            ['🤖','PASO 3','La Inteligencia Artificial analiza tu perfil y asigna tu especialidad.'],
            ['🎯','PASO 4','Recibes tu especialidad técnica recomendada con explicación personalizada.'],
        ];
        foreach ($pasos as $p): ?>
        <div style="flex:1; min-width:180px; text-align:center; padding:18px;">
            <div style="font-size:38px; margin-bottom:10px;"><?php echo $p[0]; ?></div>
            <h4 style="color:var(--azul-brillante); font-family:'Orbitron',sans-serif; font-size:11px; letter-spacing:1px; margin-bottom:8px;"><?php echo $p[1]; ?></h4>
            <p style="color:#1a2438; font-size:13px;"><?php echo $p[2]; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="grid-2">
    <div class="tarjeta">
        <h3>👥 Roles del Sistema</h3>
        <p style="color:#1a2438; font-size:14px; line-height:1.9; margin-top:8px;">
            <strong style="color:var(--verde-neon);">Estudiante:</strong> Accede a la encuesta, ve su resultado y contacta al DECE.<br>
            <strong style="color:var(--azul-brillante);">DECE:</strong> Visualiza resultados de todos los estudiantes y gestiona mensajes.<br>
            <strong style="color:#1a2438;">Rector:</strong> Acceso total a estadísticas generales del sistema.
        </p>
    </div>
    <div class="tarjeta">
        <h3>📚 Cursos Participantes</h3>
        <p style="color:#1a2438; font-size:14px; line-height:1.9; margin-top:8px;">
            La plataforma está disponible para todos los estudiantes de:<br><br>
            <span style="color:var(--azul-brillante); font-family:'Orbitron',sans-serif; font-size:13px;">
                Décimo "A" · "B" · "C" · "D" · "E" · "F" · "G" · "H"
            </span>
        </p>
    </div>
</div>
