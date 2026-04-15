# 📘 GUÍA DE INSTALACIÓN — PLATAFORMA VOCACIONAL UETS v2
## Una sola página · Sidebar siempre visible · PHP + MySQL + OpenAI

---

## 📁 ESTRUCTURA

```
vocacional/
│
├── index.php              ← PUNTO ÚNICO DE ENTRADA (sidebar siempre visible)
├── login.php              ← Login (sin sidebar)
├── cerrar_sesion.php      ← Destruye la sesión
│
├── GENERAL/
│   ├── estilos.css        ← TODOS los estilos aquí
│   ├── conexion.php       ← Conexión MySQL
│   ├── sidebar.php        ← Barra lateral
│   └── base_datos.sql     ← Script de la BD
│
└── PAGINAS/
    ├── inicio.php         ← Dashboard (cambia según rol)
    ├── informacion.php    ← Info del sistema
    ├── especialidades.php ← Las 6 especialidades
    ├── encuesta.php       ← 20 preguntas (solo estudiantes)
    ├── resultado.php      ← Resultado IA (solo estudiantes)
    ├── contacto.php       ← Mensajes (envío y bandeja)
    ├── dece.php           ← Panel DECE
    └── rector.php         ← Panel Rector
```

**¿Cómo funciona la navegación?**
Todas las páginas se cargan en `index.php` usando `?pagina=nombre`.
Ejemplo: `index.php?pagina=encuesta`, `index.php?pagina=dece`, etc.
El sidebar siempre está visible con los enlaces ya construidos así.

---

## 🔧 PASOS DE INSTALACIÓN

### PASO 1 — Copiar la carpeta
Copia la carpeta `vocacional/` dentro de:
```
C:\xampp\htdocs\vocacional
```

### PASO 2 — Crear la base de datos
1. Abre `http://localhost/phpmyadmin`
2. Clic en **"Nueva"** → Nombre: `vocacional_uets` → **Crear**
3. Clic en la pestaña **"SQL"**
4. Copia el contenido de `GENERAL/base_datos.sql` y pégalo
5. Clic en **"Continuar"**

### PASO 3 — Configurar tu API Key de OpenAI
Abre `PAGINAS/resultado.php` y en la línea:
```php
$api_key = "TU_API_KEY_AQUI";
```
Reemplaza con tu API Key real de: https://platform.openai.com/api-keys

### PASO 4 — Acceder al sistema
```
http://localhost/vocacional/login.php
```

---

## 👤 USUARIOS DE PRUEBA

| Rol        | Correo               | Contraseña |
|------------|----------------------|------------|
| Rector     | rector@uets.edu.ec   | Admin123   |
| DECE       | dece@uets.edu.ec     | Admin123   |
| Estudiante | carlos@uets.edu.ec   | Admin123   |
| Estudiante | maria@uets.edu.ec    | Admin123   |

---

## ⚠️ ERRORES COMUNES

| Error | Solución |
|-------|----------|
| "Unknown database vocacional_uets" | Ejecuta el SQL del PASO 2 |
| Página en blanco | Asegúrate que Apache y MySQL están activos en XAMPP |
| IA no responde | Verifica que tu API Key sea correcta y tenga saldo |
| "Access denied for root" | Revisa contraseña en `GENERAL/conexion.php` |

---

*Plataforma desarrollada para la UETS · Año lectivo 2025-2026*
