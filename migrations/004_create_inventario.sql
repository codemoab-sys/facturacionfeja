-- Migracion 004: Sistema de inventario
ALTER TABLE productos
    ADD COLUMN stock         DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER factor_icbper,
    ADD COLUMN stock_minimo  DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER stock,
    ADD COLUMN precio_compra DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER precio_unitario;

CREATE TABLE IF NOT EXISTS inventario_movimientos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    producto_id     INT NOT NULL,
    tipo            ENUM('entrada','salida','ajuste') NOT NULL,
    cantidad        DECIMAL(12,2) NOT NULL,
    stock_anterior  DECIMAL(12,2) NOT NULL,
    stock_nuevo     DECIMAL(12,2) NOT NULL,
    motivo          TEXT,
    referencia_tipo VARCHAR(50) DEFAULT NULL,
    referencia_id   INT DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto (producto_id),
    INDEX idx_tipo (tipo),
    INDEX idx_fecha (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS compras (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT NOT NULL,
    proveedor        VARCHAR(200) NOT NULL DEFAULT '',
    numero_documento VARCHAR(50) NOT NULL DEFAULT '',
    tipo_documento   VARCHAR(20) NOT NULL DEFAULT 'FACTURA',
    fecha_emision    DATE DEFAULT NULL,
    observaciones    TEXT,
    subtotal         DECIMAL(12,2) NOT NULL DEFAULT 0,
    igv              DECIMAL(12,2) NOT NULL DEFAULT 0,
    total            DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_proveedor (proveedor),
    INDEX idx_fecha (fecha_emision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS compra_detalles (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    compra_id       INT NOT NULL,
    producto_id     INT NOT NULL,
    cantidad        DECIMAL(12,2) NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal        DECIMAL(12,2) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_compra (compra_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
