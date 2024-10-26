CREATE DATABASE chatPro;
USE chatPro;

CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    nombre_real VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

SELECT * FROM Usuarios;

CREATE TABLE Amistades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    amigo_id INT NOT NULL,
    estado ENUM('pendiente', 'aceptada', 'rechazada') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id),
    FOREIGN KEY (amigo_id) REFERENCES Usuarios(id)
);

SELECT * FROM Amistades;

CREATE TABLE Mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emisor_id INT NOT NULL,
    receptor_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emisor_id) REFERENCES Usuarios(id),
    FOREIGN KEY (receptor_id) REFERENCES Usuarios(id)
);

SELECT * FROM Mensajes;