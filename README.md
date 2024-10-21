# ChatOnline

## Descripción

**ChatOnline** es una página web que simula el funcionamiento y uso de WhatsApp Web. El sistema permite a los usuarios registrarse, iniciar sesión, buscar otros usuarios, enviar solicitudes de amistad y comunicarse a través de un chat en tiempo real. Este proyecto se desarrolla utilizando PHP de forma procedural, asegurando que se cumplan las mejores prácticas de seguridad para la protección de los datos de los usuarios.

## Funcionamiento

Al acceder a la aplicación, los usuarios se encontrarán con una pantalla de inicio donde tendrán la opción de registrarse o iniciar sesión.

### Funcionalidades para Usuarios Registrados

Una vez que un usuario registrado inicia sesión, tiene acceso a las siguientes funcionalidades:

1. **Búsqueda de Usuarios:**
   - Los usuarios pueden buscar otros usuarios ingresando un "username" o un "nombre real" en un formulario de búsqueda.
   - El sistema busca la cadena de caracteres en ambos campos y muestra los resultados correspondientes.
   - Si se encuentra un usuario, se puede enviar una solicitud de amistad, que el destinatario debe aceptar para establecer la relación.

2. **Solicitudes de Amistad:**
   - Los usuarios pueden ver un listado de las solicitudes de amistad recibidas.
   - Se proporciona la opción de aceptar o rechazar cada solicitud.

3. **Listado de Amigos:**
   - Los usuarios tienen acceso a un listado de amigos con los que han establecido una relación.

4. **Chat:**
   - Al seleccionar un amigo de la lista, se abre una pantalla de chat donde los usuarios pueden intercambiar mensajes.
   - El chat está dividido en dos secciones:
     - **Historial de Mensajes:** Se muestran todos los mensajes anteriores entre ambos usuarios en orden descendente (el más reciente en la parte superior), indicando el emisor de cada mensaje.
     - **Formulario de Envío de Mensajes:** Un formulario que permite enviar nuevos mensajes, con un límite de 250 caracteres.

## Requisitos de Implementación

Se valorarán especialmente los siguientes aspectos en la implementación del sistema:

- **Configuración de la Base de Datos:** La base de datos debe estar correctamente configurada para permitir la ejecución de todas las funcionalidades descritas anteriormente.

- **Validación y Saneamiento de Datos:** Todos los formularios deben incluir validación y saneamiento de datos en cada entrada de usuario.

- **Protección Contra Inyección SQL:** Todas las inserciones y consultas de datos deben estar correctamente protegidas contra ataques de inyección SQL.

- **Encriptación de Contraseñas:** El proceso de creación de usuario debe incluir la encriptación de contraseñas utilizando BCRYPT para garantizar la seguridad de las credenciales.

- **Emisor de Mensajes en el Chat:** Cada mensaje en el chat debe estar acompañado del nombre del emisor para que los usuarios puedan identificar fácilmente quién ha enviado cada mensaje.

## Importante

- Este proyecto debe ser desarrollado utilizando únicamente PHP procedimental, siguiendo las pautas y conceptos vistos en el módulo 7 con Alberto. No se permite el uso de PDO ni JavaScript en la implementación.

## Instalación

1. Clona este repositorio en tu máquina local.
2. Configura tu base de datos siguiendo el esquema proporcionado en la carpeta `database`.
3. Asegúrate de que el servidor web esté configurado para interpretar archivos PHP.
4. Accede a la aplicación a través de tu navegador.
