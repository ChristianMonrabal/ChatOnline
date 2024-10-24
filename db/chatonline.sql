CREATE DATABASE chat_online;

USE chat_online;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE solicitudes_amistad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    remitente_id INT NOT NULL,
    receptor_id INT NOT NULL,
    estado ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (receptor_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE amigos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id1 INT NOT NULL,
    usuario_id2 INT NOT NULL,
    fecha_amistad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id1) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id2) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE (usuario_id1, usuario_id2)
);

CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emisor_id INT NOT NULL,
    receptor_id INT NOT NULL,
    mensaje TEXT NOT NULL CHECK (CHAR_LENGTH(mensaje) <= 250),
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emisor_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (receptor_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

SELECT * FROM usuarios;
SHOW TABLES;