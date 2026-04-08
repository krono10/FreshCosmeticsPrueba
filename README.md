# Documentación Técnica: Gestor de Banners para Freshly Cosmetics

Este proyecto es una propuesta para modernizar la gestión de banners, pasando de un sistema acoplado a un módulo de PrestaShop a una solución independiente basada en Symfony.

## Instalación y Configuración

Para poner en marcha el proyecto en un entorno local, sigue estos pasos:

1. **Clonar el repositorio.**
2. **Instalar las dependencias de PHP**:
   - Ejecuta `composer install`.
   - *Nota importante:* Si tras este paso, al intentar ejecutar comandos de Symfony, recibes un error indicando que falta el **"Symfony Runtime"**, es probable que la instalación se haya interrumpido por problemas de certificados SSL o bloqueos de archivos en Windows. Para solucionarlo, fuerza la instalación del componente con:  
     `composer require symfony/runtime`
3. **Configurar el archivo `.env.dev`**: Ajusta la variable `DATABASE_URL` con tus credenciales locales.
4. **Preparar la base de datos**:
   - Ejecuta `php bin/console doctrine:database:create` para crear la base de datos.
   - Ejecuta `php bin/console doctrine:migrations:migrate` para crear la estructura de tablas automáticamente.
---

## Decisiones de Arquitectura

### Enfoque de API Independiente
He decidido que Symfony funcione como un servicio de backend puro (API REST). En lugar de renderizar el contenido directamente en el servidor de PrestaShop, la lógica reside en esta API. Esto permite que los mismos banners puedan ser consumidos por la web actual, una futura aplicación móvil o cualquier otro servicio, ganando una flexibilidad total.

### Gestión de Idiomas mediante JSON
Para los campos de contenido traducibles, he utilizado el tipo de dato JSON en la base de datos. Las estructuras tradicionales de traducción suelen requerir tablas extra que complican el mantenimiento. Con JSON, si el equipo de marketing necesita añadir un nuevo idioma mañana, el sistema lo soporta sin necesidad de modificar el código ni el esquema de la base de datos.

### Panel de Administración Ligero
Para el frontend del administrador, he optado por JavaScript moderno (Vanilla JS) y Tailwind CSS. El objetivo era demostrar que se puede ofrecer una experiencia de usuario ágil y fluida (sin recargas de página) sin necesidad de introducir la complejidad de frameworks como React o Vue, manteniendo el proyecto fácil de leer e integrar.

---

## Proceso de Desarrollo y Buenas Prácticas

- Validaciones: Se ha implementado lógica para asegurar la integridad de los datos, como verificar que la fecha de inicio siempre sea anterior a la de fin.
- Organización del Código: La lógica de filtrado (decidir qué banners están activos según la fecha actual) se ha ubicado en el Repository de Doctrine. Esto mantiene los controladores limpios y facilita la reutilización de código.
- Experiencia de Usuario: Se han incluido avisos de confirmación para acciones críticas (como borrar un banner) y notificaciones temporales para confirmar que los cambios se han guardado correctamente.

---

## Integración con PrestaShop

Aunque el sistema es independiente, la integración para el usuario administrador sería transparente:

1. Ubicación: Se propone crear una nueva sección en el panel de PrestaShop (dentro de "Diseño" o "Promociones").
2. Método: El panel de Symfony se cargaría dentro de esa sección mediante un acceso directo o un componente integrado.
3. Seguridad: La API de Symfony validaría la sesión del usuario de PrestaShop para asegurar que solo personal autorizado pueda realizar cambios.

---

## Responsabilidad y Valor Diferencial

Sobre la responsabilidad del Full Stack en Freshly:
Considero que la tarea principal no es solo escribir código, sino ser el puente entre lo que el negocio necesita y una solución técnica que sea sostenible. Un desarrollador en Freshly debe velar por que la plataforma pueda crecer rápido sin generar problemas de mantenimiento a futuro.

Mi valor diferencial:
No me limito a que una funcionalidad "funcione", sino que me aseguro de que sea intuitiva para quien la usa y eficiente por dentro. Mi capacidad para diseñar sistemas desacoplados como este garantiza que la empresa pueda evolucionar tecnológicamente sin bloqueos ni dependencias obsoletas.
