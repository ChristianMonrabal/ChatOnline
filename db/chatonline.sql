CREATE DATABASE chat_online;

USE chat_online;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    pwd VARCHAR(255) NOT NULL,
    nombre_real VARCHAR(100) NOT NULL, -- Nuevo campo para el nombre real
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Amistades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    amigo_id INT NOT NULL,
    estado ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id),
    FOREIGN KEY (amigo_id) REFERENCES Usuarios(id)
);

CREATE TABLE Mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emisor_id INT NOT NULL,
    receptor_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emisor_id) REFERENCES Usuarios(id),
    FOREIGN KEY (receptor_id) REFERENCES Usuarios(id)
);

SELECT * FROM usuarios;
SHOW TABLES;