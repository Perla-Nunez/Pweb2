USE REDSOCIAL;
INSERT INTO users(Nombre,email,Contraseña) VALUES ('Izaak', 'cesarisaac2004@gmail.com', '1442');
CREATE DATABASE REDSOCIAL;
CREATE TABLE users (
    idUsuario INT PRIMARY KEY IDENTITY(1,1),
    Nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    fechaNacimiento DATE,
    genero VARCHAR(20),
    ciudad VARCHAR(100),
    pais VARCHAR(100),
    fotoPerfil VARCHAR(255),
    biografia TEXT,
    fechaRegistro DATETIME DEFAULT GETDATE()
);


CREATE TABLE publicaciones (
    idPublicacion INT PRIMARY KEY IDENTITY(1,1),
    idUsuario INT FOREIGN KEY REFERENCES users(idUsuario),
    texto VARCHAR(500),
    tipoContenido VARCHAR(10), -- 'imagen', 'video', o NULL
    rutamulti VARCHAR(255), -- ruta al archivo si tiene contenido multimedia
    postdate DATETIME DEFAULT GETDATE()
);
INSERT INTO publicaciones (idUsuario, texto, tipoContenido, rutamulti)
VALUES (3, 'Esta es una publicación de prueba desde SQL Server.', 'imagen', 'recurso/imagen/fongo.jpg');




SELECT * FROM users

SELECT TOP 1 * FROM users;
DELETE FROM users WHERE idUsuario = 2;

DROP TABLE users