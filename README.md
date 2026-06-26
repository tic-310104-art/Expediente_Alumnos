# 🎓 Sistema de Gestión de Expedientes Digitales - UT Nayarit

Este proyecto surge como una solución integral para la *digitalización y centralización de expedientes académicos* en la Universidad Tecnológica de Nayarit. El sistema transforma el archivo físico tradicional en un repositorio digital inteligente, permitiendo una gestión trazable, segura y eficiente de la información estudiantil y el seguimiento de tutorías.

---

## 🚀 Propuesta Técnica
El sistema está desarrollado íntegramente con el framework *Laravel, implementando una arquitectura **Modelo-Vista-Controlador (MVC)* que garantiza un desarrollo modular y profesional.

* *Seguridad:* Implementación de almacenamiento cifrado en directorios protegidos y validación robusta de datos.
* *Eficiencia:* Uso de *Eloquent ORM* para una vinculación única entre documentos y matrículas.
* *Escalabilidad:* Arquitectura preparada para integraciones mediante APIs con el Sistema de Información Estudiantil (SIS).

---

## 🛠️ Stack Tecnológico

* *Fullstack Framework:* ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
* *Lenguaje:* ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
* *Base de Datos:* ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
* *Frontend Engine:* ![Blade](https://img.shields.io/badge/Blade_Templates-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

---

## 🏗️ Arquitectura: MVC
Para demostrar orden técnico y facilitar el mantenimiento, el sistema se rige bajo el patrón *Modelo-Vista-Controlador*:
1.  *Modelos:* Gestión de la base de datos y relaciones lógicas mediante Eloquent.
2.  *Vistas:* Interfaces de usuario dinámicas creadas con el motor de plantillas *Blade de Laravel*.
3.  *Controladores:* Intermediarios que procesan las peticiones y ejecutan la lógica de negocio.

---

## 📋 Requerimientos del Proyecto

### Funcionales
* *Gestión de Usuarios:* Registro, autenticación y login seguro para tutores y alumnos.
* *Control de Expedientes:* CRUD completo de historial académico y de tutorías.
* *Agenda de Tutorías:* Registro de sesiones con fechas, notas y seguimiento del tutor.
* *Captura de Datos:* Formularios estandarizados con validaciones obligatorias.
* *Reportes:* Generación de reportes personalizados por alumno y tutor.
* *Control de Acceso:* Sistema de roles y permisos definidos (RBAC).

### No Funcionales
* *Seguridad:* Encriptación de tránsito y protección contra ataques externos.
* *Rendimiento:* Respuesta rápida y soporte para múltiples usuarios simultáneos.
* *Escalabilidad:* Uso de APIs para crecimiento e integración futura.
* *Usabilidad:* Interfaz intuitiva y acceso desde dispositivos móviles.

---

## 👥 Roles y Permisos
| Rol | Funciones Principales |
| :--- | :--- |
| *Servicios Escolares* | Control total, gestión de roles y auditoría del sistema| Digitalización y     edición de expedientes académicos. |
| *Tutor* | Seguimiento de alumnos, registro de sesiones y observaciones. |
| *Alumno* | Consulta de expediente propio y agenda de tutorías. |

---

## 💻 Guía de Instalación

### Requisitos Previos
* *PHP* (Versión 8.1 o superior)
* *Composer*
* *Node.js & NPM*
* *MySQL*

### Pasos para la configuración
1. *Clonar el proyecto:*
   ```bash
   git clone [https://github.com/tu-usuario/nombre-del-proyecto.git](https://github.com/tu-usuario/nombre-del-proyecto.git)

*Equipo de Desarrollo*
🧑‍💻 Araujo Robledo Alain Javier

👩‍💻 Cisneros Macias Alondra Guadalupe

🧑‍💻 Mendoza Salas Gilberto Alonso

🧑‍💻 Flores Ochoa Kervin Geovanni

🧑‍💻 Ramos Rivera Yoel Guadalupe

*Universidad Tecnológica de Nayarit - 2026*