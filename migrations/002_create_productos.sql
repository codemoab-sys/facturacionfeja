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

-- Seed: productos demo (solo si la tabla est� vac�a)
INSERT INTO productos (user_id, codigo, cod_producto_sunat, descripcion, unidad, precio_unitario, tip_afe_igv, icbper, factor_icbper, categoria_id)
SELECT 1, 'P001', '43211503', 'LAPTOP HP PAVILION 15 i7 16GB 512GB SSD', 'NIU', 2950.00, '10', NULL, NULL, 1
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE codigo = 'P001' AND user_id = 1);

INSERT INTO productos (user_id, codigo, cod_producto_sunat, descripcion, unidad, precio_unitario, tip_afe_igv, icbper, factor_icbper, categoria_id)
SELECT 1, 'P002', '43211708', 'MOUSE LOGITECH M170 INALAMBRICO', 'NIU', 59.00, '10', NULL, NULL, 1
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE codigo = 'P002' AND user_id = 1);

INSERT INTO productos (user_id, codigo, cod_producto_sunat, descripcion, unidad, precio_unitario, tip_afe_igv, icbper, factor_icbper, categoria_id)
SELECT 1, 'P003', '43211706', 'TECLADO MECANICO REDRAGON K552 RGB', 'NIU', 189.00, '10', NULL, NULL, 1
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE codigo = 'P003' AND user_id = 1);

INSERT INTO productos (user_id, codigo, cod_producto_sunat, descripcion, unidad, precio_unitario, tip_afe_igv, icbper, factor_icbper, categoria_id)
SELECT 1, 'S001', '81111501', 'SERVICIO DE CONSULTORIA EN TI (HORA)', 'HUR', 150.00, '10', NULL, NULL, 2
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE codigo = 'S001' AND user_id = 1);

INSERT INTO productos (user_id, codigo, cod_producto_sunat, descripcion, unidad, precio_unitario, tip_afe_igv, icbper, factor_icbper, categoria_id)
SELECT 1, 'P005', '55101500', 'LIBRO "CLEAN CODE" -- ROBERT MARTIN', 'NIU', 89.00, '20', NULL, NULL, 3
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE codigo = 'P005' AND user_id = 1);

INSERT INTO productos (user_id, codigo, cod_producto_sunat, descripcion, unidad, precio_unitario, tip_afe_igv, icbper, factor_icbper, categoria_id)
SELECT 1, 'P006', '24112003', 'BOLSA PLASTICA BIODEGRADABLE', 'BG', 0.50, '10', 0.50, 0.50, 4
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE codigo = 'P006' AND user_id = 1);
