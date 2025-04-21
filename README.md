# Comter

Sistema web para la gestión de usuarios, administradores y proveedores, con módulos de registro, inicio de sesión, verificación y administración. El proyecto está estructurado en varias carpetas para separar el backend, frontend, recursos y configuraciones.

## 🗂️ Estructura principal de carpetas

```
comter/
├── 🗄️ admin/          # Panel y utilidades para la administración avanzada del sistema
├── 📦 assets/         # Recursos estáticos (imágenes, estilos, JS)
├── 🖥️ backend/        # Scripts PHP para lógica de negocio y autenticación
├── ⚙️ config/         # Archivos de configuración
├── 🎨 css/            # Hojas de estilo CSS
├── 🖼️ frontend/       # Vistas y scripts para usuarios, admins y proveedores
├── 🧩 ico/            # Íconos de la aplicación
├── 🖼️ img/            # Imágenes de la aplicación
├── 📜 js/             # Scripts JavaScript adicionales
├── ✉️ phpmailer/      # Librería para envío de correos
├── 🗃️ sql/            # Scripts SQL para la base de datos
├── 🪟 Ui/             # Interfaces de usuario y vistas adicionales
├── 📁 vendor/         # Dependencias externas (Composer)
├── alter_respaldos.sql
├── composer.json
├── composer.lock
└── index.php
```

- **admin/**: 🗄️ Panel y utilidades para la administración avanzada del sistema.
- **assets/**: 📦 Recursos estáticos como imágenes, hojas de estilo y scripts JS.
- **backend/**: 🖥️ Scripts PHP para lógica de negocio, autenticación y manejo de usuarios (registro, login, verificación, etc.).
- **config/**: ⚙️ Archivos de configuración del sistema.
- **css/**: 🎨 Hojas de estilo CSS para el sistema.
- **frontend/**: 🖼️ Vistas y scripts para la interacción de usuarios, administradores y proveedores.
- **ico/**: 🧩 Íconos utilizados en la aplicación.
- **img/**: 🖼️ Imágenes utilizadas en la aplicación.
- **js/**: 📜 Scripts JavaScript adicionales.
- **phpmailer/**: ✉️ Librería para envío de correos electrónicos.
- **sql/**: 🗃️ Scripts SQL para la base de datos.
- **Ui/**: 🪟 Interfaces de usuario y vistas adicionales.
- **vendor/**: 📁 Dependencias externas gestionadas por Composer.

## 📄 Archivos principales

- `index.php`: Punto de entrada principal del sistema.
- `composer.json` y `composer.lock`: Definen las dependencias PHP del proyecto.
- `alter_respaldos.sql`: Script para alteraciones y respaldos de la base de datos.

## ⚙️ Instalación y despliegue

1. Clona el repositorio o copia los archivos al servidor local (XAMPP recomendado).
2. Instala las dependencias PHP con Composer:
   ```bash
   composer install
   ```
3. Configura la base de datos usando los scripts en la carpeta `sql/`.
4. Ajusta los archivos de configuración en `config/` según tus credenciales de base de datos.
5. Inicia el servidor local y accede a `index.php` desde tu navegador.

## 🚀 Funcionalidades principales

- Registro y login de administradores y proveedores.
- Gestión y actualización de usuarios.
- Verificación de cuentas por correo electrónico.
- Paneles de administración y usuario.

## 🛠️ Requisitos

- PHP 7.4+
- Servidor web local (XAMPP, WAMP, etc.)
- Composer para gestión de dependencias
- MySQL/MariaDB

---

Creado por **Brandon Pérez**

Licencia: [MIT](https://opensource.org/licenses/MIT)
