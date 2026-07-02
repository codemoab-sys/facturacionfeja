-- Migraci�n 002: Tablas de productos y categor�as
CREATE TABLE IF NOT EXISTS categorias (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS productos (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    user_id           INT NOT NULL,
    codigo            VARCHAR(20) NOT NULL,
    cod_producto_sunat VARCHAR(20) DEFAULT '',
    descripcion       VARCHAR(500) NOT NULL,
    unidad            VARCHAR(10) NOT NULL DEFAULT 'NIU',
    precio_unitario   DECIMAL(12,2) NOT NULL DEFAULT 0,
    tip_afe_igv       VARCHAR(2) NOT NULL DEFAULT '10',
    icbper            DECIMAL(12,2) DEFAULT NULL,
    factor_icbper     DECIMAL(12,2) DEFAULT NULL,
    categoria_id      INT DEFAULT NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed: categor�as por defecto
INSERT IGNORE INTO categorias (id, nombre) VALUES
(1, 'Tecnolog�a'),
(2, 'Servicios'),
(3, 'Libros (exonerado)'),
(4, 'Empaque'),
(5, 'Oficina'),
(6, 'Alimentaci�n'),
(7, 'Vestimenta'),
(8, 'Otros');
