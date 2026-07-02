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

-- Seed: clientes demo (solo si la tabla est� vac�a)
INSERT INTO clientes (user_id, tipo_doc, num_doc, razon_social, direccion, email)
SELECT 1, '6', '20555666777', 'ACME CORPORATION SAC', 'AV. LARCO 1234 - MIRAFLORES', 'facturas@acme.com'
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE num_doc = '20555666777' AND user_id = 1);

INSERT INTO clientes (user_id, tipo_doc, num_doc, razon_social, direccion, email)
SELECT 1, '6', '20111222333', 'DISTRIBUIDORA LIMA EIRL', 'JR. COMERCIO 456 - LIMA', 'compras@distribuidoralima.pe'
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE num_doc = '20111222333' AND user_id = 1);

INSERT INTO clientes (user_id, tipo_doc, num_doc, razon_social, direccion, email)
SELECT 1, '6', '20444555666', 'TECNOLOGIA ANDINA SA', 'AV. PRINCIPAL 789 - SAN ISIDRO', 'facturacion@tecnologiaandina.pe'
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE num_doc = '20444555666' AND user_id = 1);

INSERT INTO clientes (user_id, tipo_doc, num_doc, razon_social, direccion, email)
SELECT 1, '6', '20333777888', 'CONSTRUCTORA NORTE SAC', 'AV. DEL NORTE 321 - TRUJILLO', 'admin@constructoranorte.pe'
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE num_doc = '20333777888' AND user_id = 1);

INSERT INTO clientes (user_id, tipo_doc, num_doc, razon_social, direccion, email)
SELECT 1, '6', '20666999000', 'LOGISTICA PERU EIRL', 'CALLE LOS OLIVOS 555 - CALLAO', 'logistica@logisticaperu.pe'
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE num_doc = '20666999000' AND user_id = 1);

INSERT INTO clientes (user_id, tipo_doc, num_doc, razon_social, direccion, email)
SELECT 1, '6', '20123456789', 'INVERSIONES DEL SUR SAC', 'AV. SOL 1000 - AREQUIPA', 'contacto@inversionessur.pe'
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE num_doc = '20123456789' AND user_id = 1);

INSERT INTO clientes (user_id, tipo_doc, num_doc, razon_social, direccion, email)
SELECT 1, '1', '12345678', 'JUAN PEREZ GARCIA', 'JR. UNION 200 - LIMA', 'jperez@email.com'
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE num_doc = '12345678' AND user_id = 1);

INSERT INTO clientes (user_id, tipo_doc, num_doc, razon_social, direccion, email)
SELECT 1, '1', '87654321', 'MARIA LOPEZ HUAMAN', 'AV. BOLIVAR 456 - CALLAO', 'mlopez@email.com'
WHERE NOT EXISTS (SELECT 1 FROM clientes WHERE num_doc = '87654321' AND user_id = 1);
