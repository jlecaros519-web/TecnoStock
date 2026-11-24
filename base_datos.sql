CREATE DATABASE IF NOT EXISTS tecnostock
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE tecnostock;

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    estado TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    precio DECIMAL(10,2) NOT NULL,
    stock_actual INT NOT NULL DEFAULT 0,
    stock_minimo INT NOT NULL DEFAULT 0,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    id_categoria INT NOT NULL,
    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria),
    CONSTRAINT chk_precio CHECK (precio >= 0),
    CONSTRAINT chk_stock_actual CHECK (stock_actual >= 0),
    CONSTRAINT chk_stock_minimo CHECK (stock_minimo >= 0)
);

CREATE TABLE IF NOT EXISTS movimientos (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tipo ENUM('ENTRADA', 'SALIDA') NOT NULL,
    cantidad INT NOT NULL,
    observacion VARCHAR(255),
    id_producto INT NOT NULL,
    id_usuario INT NOT NULL,
    CONSTRAINT fk_movimiento_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto),
    CONSTRAINT fk_movimiento_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario),
    CONSTRAINT chk_cantidad CHECK (cantidad > 0)
);

INSERT IGNORE INTO usuarios (nombre, correo, clave, estado) VALUES
(
    'Administrador TecnoStock',
    'admin@tecnostock.cl',
    '$2y$12$t.fZVQa4H0876fU2Mofr7ug..goCwMv.GcvvZoh4JCU.yRErwC8y2',
    1
);

INSERT IGNORE INTO categorias (nombre, descripcion, estado) VALUES
('Periféricos', 'Dispositivos de entrada y accesorios para computador', 1),
('Cables y conectividad', 'Cables, adaptadores y accesorios de conexión', 1),
('Audio', 'Audífonos, parlantes y accesorios de sonido', 1),
('Almacenamiento', 'Dispositivos para almacenamiento de datos', 1);
