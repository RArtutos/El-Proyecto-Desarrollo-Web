-- Inicialización de la Base de Datos

CREATE DATABASE IF NOT EXIST monitor;


USE artutos1_monitor;

CREATE TABLE cuenta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(32) NOT NULL UNIQUE,
    contrasenia VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estaActivo TINYINT(1) NOT NULL DEFAULT 1
);

INSERT INTO cuenta (usuario, contrasena_hash, nombre, correo, rol, estaActivo)
VALUES ('eladmin', '$argon2i$v=19$m=16,t=2,p=1$c2FsdHNhbHQ$5BZ9A0Fus3owxwgNPn5znA', 'administrador', 'admin@eurotech.com', 'admin', 1);

CREATE TABLE servidor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alias VARCHAR(20) NOT NULL,
    ip VARCHAR(15) NOT NULL,
    dominio VARCHAR(50),
    estado ENUM('ENCENDIDO', 'APAGADO', 'INDETERMINADO') NOT NULL DEFAULT 'ENCENDIDO',
    token VARCHAR(255),
    ultima_senal DATETIME,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    dueno_id INT NOT NULL,
    CONSTRAINT fk_servidor_dueno
        FOREIGN KEY (dueno_id) REFERENCES cuenta(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);


CREATE TABLE usuarios_servidor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_servidor INT NOT NULL,
    id_usuario INT NOT NULL,
    rol ENUM('admin', 'user') NOT NULL DEFAULT 'admin',
    CONSTRAINT fk_us_servidor
        FOREIGN KEY (id_servidor) REFERENCES servidor(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_us_usuario
        FOREIGN KEY (id_usuario) REFERENCES cuenta(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


