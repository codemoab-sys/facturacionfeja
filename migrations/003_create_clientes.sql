-- Migraci�n 003: Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    tipo_doc    VARCHAR(2) NOT NULL DEFAULT '6',
    num_doc     VARCHAR(15) NOT NULL,
    razon_social VARCHAR(200) NOT NULL,
    direccion   VARCHAR(300) DEFAULT '',
    email       VARCHAR(100) DEFAULT '',
    telefono    VARCHAR(20) DEFAULT '',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
