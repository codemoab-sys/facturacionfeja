-- Migración 001: Crear tablas iniciales
-- 1. Crear la base de datos (ajusta el nombre si es diferente)
--    CREATE DATABASE facturacionfeja CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Importar: mysql -u tu_usuario -p facturacionfeja < migrations/001_create_tables.sql

CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario     VARCHAR(50) UNIQUE NOT NULL,
    password    VARCHAR(255) NOT NULL,
    nombre      VARCHAR(100) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_configs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    base_url    VARCHAR(255) NOT NULL DEFAULT '',
    api_key     VARCHAR(255) NOT NULL DEFAULT '',
    api_secret  VARCHAR(255) NOT NULL DEFAULT '',
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS documents (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    tipo            VARCHAR(30) NOT NULL,
    remote_id       INT DEFAULT NULL,
    numero          VARCHAR(30) DEFAULT NULL,
    serie           VARCHAR(10) DEFAULT NULL,
    correlativo     VARCHAR(10) DEFAULT NULL,
    cliente_num_doc  VARCHAR(15) DEFAULT NULL,
    cliente_razon   VARCHAR(200) DEFAULT NULL,
    total           DECIMAL(12,2) DEFAULT NULL,
    moneda          VARCHAR(3) DEFAULT NULL,
    estado          VARCHAR(30) DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed: usuario demo (password: demo123)
INSERT INTO users (usuario, password, nombre) VALUES ('demo', '$2y$10$wQ4y.Hvp6utV59Wk9L.4BenXx8UA33BLsqxqieifz4RVJI4GnejYi', 'Usuario Demo');

-- Seed: API keys demo (vacío — configurar desde la interfaz)
INSERT INTO user_configs (user_id, base_url, api_key, api_secret) VALUES (1, '', '', '');
