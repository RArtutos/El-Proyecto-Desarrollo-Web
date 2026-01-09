# El-Proyecto-Desarrollo-Web

## Descripción del Proyecto

Este es un **sistema de monitoreo de servidores** desarrollado en PHP que permite a los usuarios supervisar el estado de múltiples servidores de forma centralizada. La aplicación proporciona una plataforma web intuitiva para gestionar y monitorear la disponibilidad de servidores en tiempo real.

### Características Principales

- **Autenticación de Usuarios**: Sistema de login y registro con roles diferenciados (administrador y usuario)
- **Gestión de Servidores**: Agregar, configurar y administrar servidores para monitoreo
- **Monitoreo en Tiempo Real**: Seguimiento del estado de los servidores (encendido, apagado, indeterminado)
- **API de Comunicación**: Endpoints para que los servidores envíen señales de vida (keepalive) y notificaciones de apagado
- **Sistema de Tokens**: Autenticación segura mediante tokens para la comunicación servidor-aplicación
- **Panel de Control (Dashboard)**: Interfaz web para visualizar el estado de todos los servidores monitoreados
- **Control de Acceso**: Sistema de permisos para gestionar qué usuarios pueden administrar qué servidores

### Arquitectura Técnica

El proyecto sigue el patrón **Modelo-Vista-Controlador (MVC)** con la siguiente estructura:

- **Modelos** (`/app/modelos`): Gestión de datos y lógica de negocio
- **Vistas** (`/app/vistas`): Interfaces de usuario (autenticación y dashboard)
- **Controladores** (`/app/controladores`): Lógica de control de flujo y manejo de peticiones
- **Base de Datos**: MySQL con tablas para cuentas de usuario, servidores y relaciones usuario-servidor

### Seguridad

- Sesiones configuradas con cookies seguras y HttpOnly
- Contraseñas hasheadas con Argon2ID
- Tokens de autenticación para comunicación entre servidores
- Validación de permisos en todas las rutas protegidas