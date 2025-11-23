## 🚀 Prerrequisitos

Asegúrate de tener instalado el siguiente software en tu sistema antes de comenzar:

- **PHP** 8.1+
- **Composer**
- **MySQL** 8.0+
- **Node.js** 16+

## ⚙️ Instalación y Configuración

Sigue estos pasos para poner en marcha el proyecto en tu entorno local.

### 1. Clonar el Repositorio

Abre tu terminal y ejecuta el siguiente comando:

git clone https://github.com/fmelgarejoc/emprendimiento.git
cd emprendimiento

### 2. Instalar Dependencias PHP
hacer el comando dentro de la carpeta clonada:
composer install
### 3. Configurar Base de Datos
primero debes crear una BD vacia
### modificar el archivo
 .env
el nombre de la BD que creaste en mysql y si tiene contraseña

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aqui_el nombrede tuBD
DB_USERNAME=root
DB_PASSWORD=tu_password_si le pusiste

### 4. Ejecutar Migraciones (Crear tablas en la base de datos)
php artisan migrate

### Crear usuario administrador (te preguntara nombre, email y contraseña para enrolarte al sistema)
php artisan make:filament-user

### Poblar con datos de prueba (opcional )
php artisan db:seed

### 5. Iniciar el Servidor

php artisan serve

### 6. Generar Alertas del Sistema (si ya tienes datos o realizaste los seeders)

php artisan sistema:deteccion-desviaciones