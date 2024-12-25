CREATE TABLE usuarios (
    id_usuarios INT AUTO_INCREMENT PRIMARY KEY,  -- ID único para cada usuario (renombrado como id_usuarios)
    firstname VARCHAR(100) NOT NULL,             -- Nombre del usuario
    lastname VARCHAR(100) NOT NULL,              -- Apellido del usuario
    email VARCHAR(255) NOT NULL UNIQUE,          -- Correo electrónico único
    password VARCHAR(255) NOT NULL,              -- Contraseña del usuario
    photo LONGBLOB,                              -- Foto del usuario (BLOB)
    role ENUM('Cliente', 'Proveedor') NOT NULL DEFAULT 'Cliente', -- Campo de rol (Cliente o Proveedor)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Fecha de creación (opcional)
);

ALTER TABLE usuarios
ADD COLUMN active TINYINT(1) NULL DEFAULT 0,
ADD COLUMN activation_token VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN reset_token VARCHAR(255) NULL DEFAULT NULL;


CREATE TABLE inspection_data (
    id_inspection_data INT AUTO_INCREMENT PRIMARY KEY, -- ID único para cada reporte
    inspection_date DATE NOT NULL,                     -- Fecha de inspección
    operators VARCHAR(255) NOT NULL,                   -- Operadores
    descripcion VARCHAR(255) NOT NULL,                 -- Descripción de la inspección
    goods INT DEFAULT 0,                               -- Goods (nuevo campo)
    primer_t INT DEFAULT 0,                            -- 1er T (nuevo campo)
    segundo_t INT DEFAULT 0,                           -- 2do T (nuevo campo)
    tercer_t INT DEFAULT 0,                            -- 3er T (nuevo campo)
    coupler INT DEFAULT 0,                             -- Coupler
    dano_end_face INT DEFAULT 0,                       -- Daño End Face
    golpe_top INT DEFAULT 0,                           -- Golpe Top
    rebaba INT DEFAULT 0,                              -- Rebaba
    dano_en_lente INT DEFAULT 0,                       -- Daño en Lente
    fuera_de_spc INT DEFAULT 0,                        -- Fuera de SPC
    dano_fisico INT DEFAULT 0,                         -- Daño Físico
    coupler_dano INT DEFAULT 0,                        -- Coupler Dañado
    hundimiento INT DEFAULT 0,                         -- Hundimiento
    fisura INT DEFAULT 0,                              -- Fisura
    silicon INT DEFAULT 0,                             -- Silicon
    contaminacion INT DEFAULT 0,                       -- Contaminación
    total DECIMAL(10, 2) NOT NULL,                     -- Total (decimales permitidos)
    comments TEXT DEFAULT NULL,                        -- Comentarios (nuevo campo)
    id_usuarios INT NOT NULL,                          -- Clave foránea que apunta a usuarios
    CONSTRAINT fk_inspection_usuarios 
        FOREIGN KEY (id_usuarios) REFERENCES usuarios(id_usuarios)
        ON DELETE CASCADE ON UPDATE CASCADE           
);


CREATE TABLE report_fails (
    id_report_fails INT AUTO_INCREMENT PRIMARY KEY,    -- ID único para cada reporte
    inspection_date DATE NOT NULL,                      -- Fecha de inspección
    operators VARCHAR(255) NOT NULL,                    -- Operadores
    descripcion VARCHAR(255) NOT NULL,                  -- Descripción de la inspección
    primer_t INT DEFAULT 0,                             -- 1er T
    segundo_t INT DEFAULT 0,                            -- 2do T
    tercer_t INT DEFAULT 0,                             -- 3er T
    comments TEXT DEFAULT NULL,                         -- Comentarios
    burr INT DEFAULT 0,                                 -- Burr (nuevo campo)
    blockend_hole INT DEFAULT 0,                        -- Blockend Hole (nuevo campo)
    non_flat_edge INT DEFAULT 0,                        -- Non Flat Edge (nuevo campo)
    id_usuarios INT NOT NULL,                           -- Clave foránea que apunta a usuarios
    CONSTRAINT fk_report_fails FOREIGN KEY (id_usuarios) 
        REFERENCES usuarios(id_usuarios)
        ON DELETE CASCADE ON UPDATE CASCADE           
);

-- Tabla para almacenar las imágenes asociadas a cada reporte
CREATE TABLE report_images (
    id_image INT AUTO_INCREMENT PRIMARY KEY,         -- ID único para cada imagen
    id_report_fails INT NOT NULL,                     -- Clave foránea que apunta a report_fails
    image LONGBLOB NOT NULL,                          -- Imagen en formato binario
    CONSTRAINT fk_report_images FOREIGN KEY (id_report_fails)
        REFERENCES report_fails(id_report_fails)
        ON DELETE CASCADE ON UPDATE CASCADE         
);



CREATE TABLE reporte (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    folio_captura VARCHAR(50) NOT NULL,
    folio_requisicion VARCHAR(50) NOT NULL,
    cliente_fabricante VARCHAR(100) NOT NULL,
    fecha_reporte DATE NOT NULL,
    caja VARCHAR(50) NOT NULL,
    po_skid VARCHAR(50) NOT NULL,
    num_parte VARCHAR(50) NOT NULL,
    date_code VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    nombre_operador VARCHAR(100) NOT NULL,
    horario VARCHAR(50) NOT NULL,
    productividad_a INT NOT NULL,
    productividad_b INT NOT NULL,
    total_inspeccionadas INT NOT NULL,
    defectos_y_descripcion TEXT NOT NULL,
    total_defectos INT NOT NULL,
    buenas INT NOT NULL,
    comentarios_defecto TEXT,
    id_usuario INT NOT NULL, 
    CONSTRAINT fk_reporte_usuarios FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuarios)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
ALTER TABLE reporte
ADD COLUMN total_inspeccionadas_c INT NOT NULL,  
ADD COLUMN comentarios_descripcion_sorteo TEXT NOT NULL; 


CREATE TABLE wire_failures (
    id_wire_failures INT AUTO_INCREMENT PRIMARY KEY, -- ID único de cada registro
    id_usuario INT NOT NULL,                        -- Relación con la tabla usuarios
    box VARCHAR(10) NOT NULL,                       -- BOX/BAG (e.g., BOX 1, BOX 2, BAG 1)
    a INT DEFAULT 0,                                -- Fallas en posición A
    b INT DEFAULT 0,                                -- Fallas en posición B
    c INT DEFAULT 0,                                -- Fallas en posición C
    a_and_b INT DEFAULT 0,                          -- Fallas en posición A & B
    goods INT DEFAULT 0,                            -- Número de goods
    total INT DEFAULT 0,                            -- Total
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuarios)
);
