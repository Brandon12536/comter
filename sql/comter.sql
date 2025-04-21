CREATE TABLE usuarios (
    id_usuarios INT AUTO_INCREMENT PRIMARY KEY,
    compania VARCHAR(100) NOT NULL,
    business_unit VARCHAR(100) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    codigo_verificacion CHAR(4) NOT NULL,
    role ENUM('Cliente', 'Proveedor') NOT NULL DEFAULT 'Cliente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    password VARCHAR(255) NOT NULL
);

ALTER TABLE usuarios ADD COLUMN verificado TINYINT(1) NOT NULL DEFAULT 0;




CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    compania VARCHAR(100) NOT NULL,
    business_unit VARCHAR(100) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    codigo_verificacion CHAR(4) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'Proveedor',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
ALTER TABLE proveedores
    ADD COLUMN departamento VARCHAR(50),
    ADD COLUMN puesto VARCHAR(50),
    ADD COLUMN id_turno INT,
    ADD FOREIGN KEY (id_turno) REFERENCES turnos(id_turno);

ALTER TABLE proveedores 
    ADD COLUMN verificado TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE proveedores 
    ADD COLUMN password VARCHAR(255) NOT NULL AFTER correo;

CREATE TABLE turnos (
    id_turno INT AUTO_INCREMENT PRIMARY KEY,
    nombre_turno ENUM('1er.', '2do.', '3er.') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    turno_completo VARCHAR(50) GENERATED ALWAYS AS 
        (CONCAT(nombre_turno, ' T de ', 
                hora_inicio, ' a ',
                hora_fin)) STORED
);

CREATE TABLE PCBA (
    id_pcba INT AUTO_INCREMENT PRIMARY KEY,      
    inspection_date DATE NOT NULL,               
    description VARCHAR(255) NOT NULL,           
    shift TINYINT NOT NULL,                      
    operators VARCHAR(255) NOT NULL,            
    goods INT NOT NULL,                          
    fails_dedos_oro INT DEFAULT 0,              
    fails_mal_corte INT DEFAULT 0,              
    fails_contaminacion INT DEFAULT 0,          
    pd INT DEFAULT 0,                            
    fails_desplazados INT DEFAULT 0,            
    fails_insuficiencias INT DEFAULT 0,         
    fails_despanelizados INT DEFAULT 0,         
    fails_desprendidos INT DEFAULT 0,           
    total_fails INT NOT NULL,                    
    total INT NOT NULL,                          
    yield VARCHAR(10) NOT NULL,                  
    comments TEXT,                               
    user_id INT NOT NULL, 
    FOREIGN KEY (user_id) REFERENCES proveedores(id_proveedor) 
);


ALTER TABLE PCBA CHANGE shift shift VARCHAR(50);


INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (1,'2024-12-05','INSPECCION PCB  1064280107',1,'ABIGAIL, LUPITA',32,0,0,0,0,0,0,0,0,0,32,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (2,'2024-12-12','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',71,0,0,0,0,0,0,0,0,0,71,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (3,'2024-12-12','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',78,0,0,0,0,0,0,0,0,0,78,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (4,'2024-12-13','INSPECCION PCB  1064280187',1,'RICARDO',92,0,0,0,0,0,0,0,0,0,92,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (5,'2024-12-18','INSPECCION PCB  1064280184',2,'PAULINA',45,0,0,0,0,0,0,0,0,0,45,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (6,'2024-12-18','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',1057,2,13,2,0,0,0,0,1,18,1075,'98%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (7,'2024-12-19','INSPECCION PCB  1064280187',1,'ABIGAIL, LUPITA',278,0,0,16,0,0,0,0,4,20,298,'93%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (8,'2024-12-19','INSPECCION PCB  1064280187',1,'CAROLINA',374,2,0,6,0,0,0,0,0,8,382,'98%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (9,'2024-12-19','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',677,0,0,0,0,0,0,0,0,0,677,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (10,'2024-12-19','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',818,2,0,0,0,0,0,0,0,2,820,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (11,'2024-12-19','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',792,0,4,0,0,0,0,0,0,4,796,'99%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (12,'2024-12-21','INSPECCION PCB  1064280187',2,'CAROLINA',145,0,0,0,0,0,0,0,0,0,145,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (13,'2024-12-30','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',86,0,0,0,4,0,0,0,0,4,90,'96%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (14,'2024-12-05','INSPECCION PCB  1064281191',1,'ABIGAIL, LUPITA',12,0,0,0,0,0,0,0,0,0,12,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (15,'2024-12-05','INSPECCION PCB  1064281191',2,'MIGUEL, CAROLINA, PAULINA',261,0,0,0,0,0,0,0,0,0,261,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (16,'2024-12-05','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',376,0,0,0,0,0,0,0,0,0,376,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (17,'2024-12-05','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',373,0,0,0,0,0,0,0,0,0,373,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (18,'2024-12-06','INSPECCION PCB  1064281191',1,'RICARDO',92,0,0,0,0,0,0,0,0,0,92,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (19,'2024-12-06','INSPECCION PCB  1064280187',1,'RICARDO',92,0,0,0,0,0,0,0,0,0,92,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (20,'2024-12-10','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',310,0,0,0,0,0,0,0,0,0,310,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (21,'2024-12-10','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',350,0,0,0,0,0,0,0,0,0,350,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (22,'2024-12-10','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',383,0,0,0,0,0,0,0,0,0,383,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (23,'2024-12-10','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',398,0,0,0,0,0,0,0,0,0,398,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (24,'2024-12-11','INSPECCION PCB  1064281191',1,'ABIGAIL, LUPITA',826,0,0,0,0,0,0,0,0,0,826,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (25,'2024-12-11','INSPECCION PCB  1064281191',2,'PAULINA',82,0,0,0,0,0,0,0,0,0,82,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (26,'2024-12-11','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',68,0,0,0,0,0,0,0,0,0,68,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (27,'2024-12-11','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',80,0,0,0,0,0,0,0,0,0,80,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (28,'2024-12-11','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',72,0,0,0,0,0,0,0,0,0,72,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (29,'2024-12-11','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',78,0,0,0,0,0,0,0,0,0,78,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (30,'2024-12-11','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',75,0,0,0,0,0,0,0,0,0,75,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (31,'2024-12-12','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',75,0,0,0,0,0,0,0,0,0,75,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (32,'2024-12-12','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',72,0,0,0,0,0,0,0,0,0,72,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (33,'2024-12-12','INSPECCION PCB  1064281191',3,'JOSEFINA Y LUZ',71,0,0,0,0,0,0,0,0,0,71,'100%','',1);
INSERT INTO PCBA (`id_pcba`,`inspection_date`,`description`,`shift`,`operators`,`goods`,`fails_dedos_oro`,`fails_mal_corte`,`fails_contaminacion`,`pd`,`fails_desplazados`,`fails_insuficiencias`,`fails_despanelizados`,`fails_desprendidos`,`total_fails`,`total`,`yield`,`comments`,`user_id`) VALUES (34,'2024-12-12','INSPECCION PCB  1064280187',3,'JOSEFINA Y LUZ',72,0,0,0,0,0,0,0,0,0,72,'100%','',1);


CREATE TABLE materiales (
    id_material INT AUTO_INCREMENT PRIMARY KEY,   
    inspection_date DATE,                          
    descripcion VARCHAR(255),                      
    shift VARCHAR(50),                            
    operators INT,                               
    goods INT,                                    
    dedos_de_oro_contaminados INT,                
    faltante INT,                                 
    desplazados INT,                              
    insuficiencias INT,                           
    despanelizados INT,                           
    desprendidos INT,                             
    total INT,                                    
    yield DECIMAL(5,2),                           
    total_final INT,                              
    comments TEXT,                                
    id_proveedor INT,                             
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor) 
);
ALTER TABLE materiales
ADD COLUMN descripcion_image LONGBLOB,  
ADD COLUMN comments_image LONGBLOB;     
ALTER TABLE materiales MODIFY operators VARCHAR(255);


ALTER TABLE materiales CHANGE shift shift VARCHAR(50);



INSERT INTO materiales (`id_material`,`inspection_date`,`descripcion`,`shift`,`operators`,`goods`,`dedos_de_oro_contaminados`,`faltante`,`desplazados`,`insuficiencias`,`despanelizados`,`desprendidos`,`total`,`yield`,`total_final`,`comments`,`id_proveedor`,`descripcion_image`,`comments_image`) VALUES (4,'2024-12-19','INSPECCION PCB  1064280227','1','CARLA, SANDRA Y LUZ',548,20,0,0,0,0,0,20,96.00,20,'',1,NULL,NULL);
INSERT INTO materiales (`id_material`,`inspection_date`,`descripcion`,`shift`,`operators`,`goods`,`dedos_de_oro_contaminados`,`faltante`,`desplazados`,`insuficiencias`,`despanelizados`,`desprendidos`,`total`,`yield`,`total_final`,`comments`,`id_proveedor`,`descripcion_image`,`comments_image`) VALUES (5,'2024-07-19','INSPECCION PCB  1064280227','2','PAULINA, VICTOR Y MARY',373,0,0,0,0,0,0,0,100.00,0,'',1,NULL,NULL);
INSERT INTO materiales (`id_material`,`inspection_date`,`descripcion`,`shift`,`operators`,`goods`,`dedos_de_oro_contaminados`,`faltante`,`desplazados`,`insuficiencias`,`despanelizados`,`desprendidos`,`total`,`yield`,`total_final`,`comments`,`id_proveedor`,`descripcion_image`,`comments_image`) VALUES (6,'2024-07-29','INSPECCION PCB  1064280227','3','Luz y Josefina',564,0,0,0,0,0,0,0,100.00,0,'',1,NULL,NULL);
INSERT INTO materiales (`id_material`,`inspection_date`,`descripcion`,`shift`,`operators`,`goods`,`dedos_de_oro_contaminados`,`faltante`,`desplazados`,`insuficiencias`,`despanelizados`,`desprendidos`,`total`,`yield`,`total_final`,`comments`,`id_proveedor`,`descripcion_image`,`comments_image`) VALUES (7,'2024-07-19','INSPECCION PCB  1064280227','2','VICTOR, MARY Y PAULINA',689,17,0,0,0,0,0,17,98.00,17,'',1,NULL,NULL);
INSERT INTO materiales (`id_material`,`inspection_date`,`descripcion`,`shift`,`operators`,`goods`,`dedos_de_oro_contaminados`,`faltante`,`desplazados`,`insuficiencias`,`despanelizados`,`desprendidos`,`total`,`yield`,`total_final`,`comments`,`id_proveedor`,`descripcion_image`,`comments_image`) VALUES (8,'2024-07-18','','3','LIDIA Y JOSEFINA',265,2,0,0,0,0,0,2,99.00,2,'',1,NULL,NULL);
INSERT INTO materiales (`id_material`,`inspection_date`,`descripcion`,`shift`,`operators`,`goods`,`dedos_de_oro_contaminados`,`faltante`,`desplazados`,`insuficiencias`,`despanelizados`,`desprendidos`,`total`,`yield`,`total_final`,`comments`,`id_proveedor`,`descripcion_image`,`comments_image`) VALUES (9,'2024-07-18','','1','FERNANDO, SANDRA Y LUZ',0,0,0,0,0,0,0,0,0.00,0,'',1,NULL,NULL);

















CREATE TABLE versiones_inspeccion (
    id_version INT AUTO_INCREMENT PRIMARY KEY,     
    nombre_version VARCHAR(50) NOT NULL UNIQUE    
                      
);




INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (1,'sem42');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (2,'sem43');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (3,'sem44');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (4,'sem45');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (5,'sem46');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (6,'sem47');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (7,'sem48');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (8,'sem49');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (9,'sem50');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (10,'sem51');
INSERT INTO versiones_inspeccion (`id_version`,`nombre_version`) VALUES (11,'sem52');





CREATE TABLE inspecciones (
    id_inspeccion INT AUTO_INCREMENT PRIMARY KEY,    
    id_version INT,                                  
    id_proveedor INT,                                 
    inspection_date DATE,                             
    operators VARCHAR(255),                                    
    descripcion VARCHAR(255),                         
    primer_t INT,                            
    segundo_t INT,                           
    tercer_t INT,                           
    goods INT,                                        
    coupler INT,                                      
    dano_end_face INT,                                
    golpe_top INT,                                   
    rebaba INT,                                       
    dano_en_lente INT,                                
    fuera_de_spc INT,                                 
    dano_fisico INT,                                  
    coupler_danado INT,                              
    hundimiento INT,                                 
    fisura INT,                                      
    silicon_contaminacion INT,                        
    contaminacion_end_face INT,                       
    total INT,                                        
    total_final INT,                                  
    comments TEXT,                                    
    FOREIGN KEY (id_version) REFERENCES versiones_inspeccion(id_version), 
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)
);





INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (1,1,1,'2024-10-14',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','PULL 1064280107',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (2,1,1,'2024-10-14',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064281154',736,0,0,736,0,0,0,0,0,0,638,0,0,0,0,0,638,1374,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (5,2,1,'2024-10-21',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','PULL 1064280107',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (6,3,1,'2024-10-28',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','PULL 1064280107',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (7,3,1,'2024-10-28',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280199',1404,736,724,2864,0,0,0,0,0,56,44,0,0,0,0,0,100,2964,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (9,4,1,'2024-11-04',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280087',0,372,584,956,0,0,0,0,0,0,22,0,0,0,0,0,22,978,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (10,4,1,'2024-11-04','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280199',0,564,260,824,0,0,0,0,0,0,0,0,0,0,0,0,0,824,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (11,5,1,'2024-11-13',' REBECA, LUPITA, ABIGAIL, \r\nCARO, SONIA, PAULINA, LUZ, \r\nHECTOR, ELENA, JOSEFINA','HOUSING 1061271790',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (12,5,1,'2024-11-13',' REBECA, LUPITA, ABIGAIL, \r\nCARO, SONIA, PAULINA, LUZ, \r\nHECTOR, ELENA, JOSEFINA','HOUSING 1064280287',171,0,0,171,0,0,0,0,0,74,0,0,0,0,0,0,74,245,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (13,6,1,'2024-11-18',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1061271590',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (14,6,1,'2024-11-18','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','HOUSING 1061151110',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (15,6,1,'2024-11-18','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280562',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (16,6,1,'2024-11-18','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 1837009010',0,1437,960,2397,0,0,0,0,1,0,0,2,0,0,0,0,3,2400,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (17,6,1,'2024-11-18','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 1837009035',864,864,0,1728,0,0,0,0,0,0,0,0,0,0,0,0,0,1728,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (18,6,1,'2024-11-19','','HOUSING 1061271790',200,0,0,200,0,0,0,0,0,0,0,0,0,0,0,0,0,200,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (19,6,1,'2024-11-19','','HOUSING 1061151110',200,0,0,200,0,0,0,0,0,25,1,0,0,0,0,0,26,226,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (20,6,1,'2024-11-19','','HOUSING 1064280087',0,780,0,780,0,0,0,0,0,107,0,0,0,0,0,0,107,887,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (22,6,1,'2024-11-19','','FIBRA 1064280562',0,1031,2555,3586,0,0,0,0,0,0,74,0,0,0,0,0,74,3660,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (23,6,1,'2024-11-19','','FIBRA 1064280558',397,1529,795,2721,0,3,0,0,0,0,31,0,0,0,0,0,34,2755,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (24,6,1,'2024-11-19','','SOE 1837009035',0,1296,1293,2589,0,0,0,0,3,0,0,0,0,0,0,0,3,2592,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (25,7,1,'2024-11-25',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280199',181,0,0,181,0,0,0,0,0,0,0,0,0,0,0,0,0,181,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (26,7,1,'2024-11-25',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280087',82,0,0,82,0,0,0,0,0,0,0,0,0,0,0,0,0,82,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (27,7,1,'2024-11-25','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','PULL  1064280071',0,580,0,580,0,0,1,0,0,0,0,0,0,0,0,0,1,581,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (29,1,1,'2024-10-14',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','SOE 1064280190',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (30,1,1,'2024-10-14','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280561',1347,912,1198,3457,0,0,0,0,0,0,0,0,0,0,143,0,143,3600,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (31,1,1,'2024-10-14',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','SOE 1837009010',960,955,988,2903,0,0,0,0,4,0,0,2,0,0,0,1,7,2910,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (32,1,1,'2024-10-14',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','SOE 1837009013',0,678,0,678,0,0,0,0,0,0,0,2,0,0,0,0,2,680,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (33,1,1,'2024-10-14','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ',' CONDIUIT 1069411827',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (34,1,1,'2024-10-14',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064280558',0,0,800,800,0,0,0,0,0,0,0,0,0,0,0,0,0,800,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (35,1,1,'2024-10-14','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','SOE 183700950',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (36,1,1,'2024-10-14','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','SOE 1837009035',432,1284,432,2148,0,0,0,0,6,0,0,1,0,0,0,0,7,2155,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (37,1,1,'2024-10-15',' REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','PULL 1064280107',0,0,236,236,0,0,0,0,0,32,0,0,0,0,0,0,32,268,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (38,1,1,'2024-10-15','REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','PCB 1064281140',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (39,1,1,'2024-10-15',' REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','SOE 1837009010',680,0,0,680,0,0,0,0,0,0,0,0,0,0,0,0,0,680,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (40,1,1,'2024-10-15',' REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','FIBRA 1064280561',1173,320,840,2333,0,2,0,0,0,0,0,0,0,0,145,0,147,2480,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (41,1,1,'2024-10-15',' REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','FIBRA 1064280558',0,1523,1198,2721,0,65,0,0,0,0,0,0,0,0,14,0,79,2800,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (42,1,1,'2024-10-15','REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','FIBRA 1064280205',400,0,0,400,0,0,0,0,0,0,0,0,0,0,0,0,0,400,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (43,1,1,'2024-10-15','REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','SOE 1837009013',120,0,0,120,0,0,0,0,0,0,0,0,0,0,0,0,0,120,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (44,1,1,'2024-10-15','REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','FIBRA 1064281154',505,360,0,865,0,0,0,0,0,0,0,0,0,0,0,0,0,865,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (45,1,1,'2024-10-15',' REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','SOE 1837009035',1188,0,0,1188,0,0,0,0,0,0,0,0,0,0,0,0,0,1188,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (46,2,1,'2024-10-21','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064281154',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (47,2,1,'2024-10-21',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','SOE 1064280190',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (48,2,1,'2024-10-21',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064280561',899,465,2024,3388,0,16,0,0,0,0,13,0,0,0,0,0,29,3417,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (49,2,1,'2024-10-21','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','SOE 1837009010',240,958,480,1678,0,0,0,0,2,0,0,0,0,0,0,0,2,1680,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (50,2,1,'2024-10-21','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','SOE 1837009013',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (51,2,1,'2024-10-21',' REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','CONDIUIT 1069411827',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (52,2,1,'2024-10-21','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064280558',0,1499,0,1499,0,0,0,0,0,0,13,0,0,0,4,0,17,1516,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (53,2,1,'2024-10-21','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 183700950',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (54,2,1,'2024-10-21','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 1837009035',1637,1454,1495,4586,0,0,0,0,35,0,26,2,0,0,0,1,64,4650,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (55,2,1,'2024-10-22',' REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','PULL 1064280107',645,0,0,645,0,0,0,0,0,87,0,0,0,0,0,0,87,732,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (56,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280287',1534,1174,0,2708,0,0,0,0,0,0,126,0,0,0,0,0,126,2834,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (57,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064280561',539,0,0,539,0,1,0,0,0,0,0,0,0,0,0,0,1,540,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (58,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280562',0,1047,1495,2542,0,0,0,0,0,0,27,0,0,0,12,0,39,2581,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (59,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 1837009010',0,958,480,1438,0,0,0,0,2,0,0,0,0,0,0,0,2,1440,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (60,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 1837009013',480,0,0,480,0,0,0,0,0,0,0,0,0,0,0,0,0,480,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (61,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280205',0,0,500,500,0,0,0,0,0,0,0,0,0,0,0,0,0,500,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (62,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280558',280,1095,458,1833,0,2,0,0,0,0,25,0,0,0,0,0,27,1860,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (63,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 183700950',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (64,3,1,'2024-10-28','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 1837009035',1149,1292,432,2873,0,0,0,0,3,0,2,0,0,0,0,0,5,2878,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (65,3,1,'2024-10-29','REBECA, RICARDO, VICTOR, MARY\r\nPAULINA, JOSEFINA, ELENA Y LUZ','HOUSING 1064280199',780,0,448,1228,0,0,0,0,0,72,0,0,0,0,0,0,72,1300,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (66,4,1,'2024-11-04','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280287',0,650,779,1429,0,0,0,0,0,0,1,0,0,0,0,0,1,1430,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (67,4,1,'2024-11-04','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','HOUSING 1061271790',200,0,0,200,0,0,0,0,0,1,0,0,0,0,0,0,1,201,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (68,4,1,'2024-11-04','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','HOUSING 1061271990',225,0,0,225,0,0,0,0,0,7,0,0,0,0,0,0,7,232,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (69,4,1,'2024-11-04','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280561',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (70,4,1,'2024-11-04','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280562',0,601,1119,1720,0,21,0,0,0,0,59,0,0,0,0,0,80,1800,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (71,5,1,'2024-11-11','REBECA, LUPITA, ABIGAIL, \r\nCARO, SONIA, PAULINA, LUZ, \r\nHECTOR, ELENA, JOSEFINA','HOUSING 1061271590',75,0,0,75,0,0,0,0,0,0,0,0,0,0,0,0,0,75,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (72,5,1,'2024-11-11','REBECA, LUPITA, ABIGAIL, \r\nCARO, SONIA, PAULINA, LUZ, \r\nHECTOR, ELENA, JOSEFINA','HOUSING 1061151110',475,0,0,475,0,0,0,0,0,0,0,0,0,0,0,0,0,475,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (73,5,1,'2024-11-11','REBECA, LUPITA, ABIGAIL, CARO, SONIA, PAULINA, LUZ, HECTOR, ELENA, JOSEFINA','FIBRA 1064270072',0,50,0,50,0,0,0,0,0,0,0,0,0,0,0,0,0,50,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (74,6,1,'2024-11-25','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280562',0,0,2356,2356,0,0,0,0,0,0,0,0,0,0,0,0,0,2356,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (75,7,1,'2024-11-25','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','FIBRA 1064280562',0,0,2356,2356,0,0,0,0,0,0,0,0,0,0,0,0,0,2356,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (76,7,1,'2024-11-25','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 1837009010',480,959,960,2399,0,0,0,0,0,0,0,1,0,0,0,0,1,2400,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (77,7,1,'2024-11-25','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064280205',0,0,540,540,0,0,0,0,0,0,0,0,0,0,0,0,0,540,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (78,7,1,'2024-11-25','REBECA, LUPITA, RICARDO, MARY\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064280558',497,807,1517,2821,0,18,18,0,0,0,0,0,0,0,3,0,39,2860,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (79,7,1,'2024-11-25','REBECA, LUPITA, RICARDO, MARY JOSEFINA, ELENA Y LUZ','SOE 1837009035',864,1724,864,3452,0,0,0,0,0,0,0,0,0,0,0,0,0,3452,'');
INSERT INTO inspecciones (`id_inspeccion`,`id_version`,`id_proveedor`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`coupler_danado`,`hundimiento`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`) VALUES (80,7,1,'2024-11-26','REBECA, LUPITA, RICARDO, \r\nVICTOR, MARY\r\nPAULINA, JOSEFINA, \r\nELENA Y LUZ','HOUSING 1061271990',328,0,0,328,0,0,0,0,0,1,1,0,0,0,0,0,2,330,'');






CREATE TABLE molex (
    id_molex INT AUTO_INCREMENT PRIMARY KEY,    
    inspection_date DATE,                             
    operators VARCHAR(255),                                       
    descripcion VARCHAR(255),                        
    primer_t INT,                            
    segundo_t INT,                           
    tercer_t INT,                           
    goods INT,                                        
    coupler INT,                                       
    dano_end_face INT,                              
    golpe_top INT,                                     
    rebaba INT,                                        
    dano_en_lente INT,                               
    fuera_de_spc INT,                                 
    dano_fisico INT,                                 
    wirebond_corto INT,                              
    wirebond_chueco INT,                              
    fisura INT,                                       
    silicon_contaminacion INT,                         
    contaminacion_end_face INT,                       
    total INT,                                         
    total_final INT,                                   
    comments TEXT,
     id_version INT,  
    id_proveedor INT,
    FOREIGN KEY (id_version) REFERENCES versiones_inspeccion(id_version),
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)                                     
    
);

INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (1,'2024-12-02','  ABIGAIL, LUPITA, RICARDO,  CAROLINA, MIGUEL, PAULINA, HECTOR\r\nJOSEFINA, ELENA Y LU','HOUSING 1064280199',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',8,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (2,'2024-12-02','  ABIGAIL, LUPITA, RICARDO,  CAROLINA, MIGUEL, PAULINA, HECTOR\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280087',347,0,260,607,0,0,0,0,0,0,0,0,0,0,0,0,0,607,'',8,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (3,'2024-12-02','  ABIGAIL, LUPITA, RICARDO,  CAROLINA, MIGUEL, PAULINA, HECTOR\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064280562',1602,854,655,3111,0,69,0,0,0,0,0,0,0,0,0,0,69,3180,'',8,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (4,'2024-12-02','ABIGAIL, LUPITA, RICARDO,  CAROLINA, MIGUEL, PAULINA, HECTOR\r\nJOSEFINA, ELENA Y LUZ','SOE 1837009010',0,2152,1648,3800,0,0,0,0,41,0,0,0,39,0,0,0,80,3880,'',8,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (5,'2024-12-02','  ABIGAIL, LUPITA, RICARDO,  CAROLINA, MIGUEL, PAULINA, HECTOR\r\nJOSEFINA, ELENA Y LUZ','FIBRA 1064280558',0,358,2856,3214,0,10,0,0,0,0,0,0,0,0,0,0,10,3224,'',8,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (6,'2024-12-02',' ABIGAIL, LUPITA, RICARDO,  CAROLINA, MIGUEL, PAULINA, HECTOR\r\nJOSEFINA, ELENA Y LUZ','SOE 1837009035',108,1920,1279,3307,0,0,0,0,17,0,0,0,24,0,0,0,41,3348,'',8,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (7,'2024-12-03',' REBECA,  ABIGAIL, LUPITA, RICARDO,  CAROLINA, MIGUEL, PAULINA, HECTOR\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280199',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',8,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (8,'2024-12-03','REBECA,  ABIGAIL, LUPITA, RICARDO,  CAROLINA, MIGUEL, PAULINA, HECTOR\r\nJOSEFINA, ELENA Y LUZ','HOUSING 1064280087',0,0,130,130,0,0,0,0,0,0,0,0,0,0,0,0,0,130,'',8,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (9,'2024-12-09','RICARDO, ABIGAIL, LUPITA, CAROLINA, MIGUEL, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','CONDUIT   1069411491',39,0,0,39,0,0,0,0,0,0,0,0,0,0,0,0,0,39,'',9,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (10,'2024-12-09',' RICARDO, ABIGAIL, LUPITA, CAROLINA, MIGUEL, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','FIBRA 1064280562',1142,1110,179,2431,0,54,0,0,0,0,0,0,0,0,0,24,78,2509,'',9,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (11,'2024-12-09',' RICARDO, ABIGAIL, LUPITA, CAROLINA, MIGUEL, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','SOE 1837009010',960,959,480,2399,0,0,0,0,1,0,0,0,0,0,0,0,1,2400,'',9,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (12,'2024-12-09','RICARDO, ABIGAIL, LUPITA, CAROLINA, MIGUEL, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','FIBRA 1064280558',394,800,1998,3192,0,5,0,0,0,0,0,0,0,0,0,3,8,3200,'',9,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (13,'2024-12-09','RICARDO, ABIGAIL, LUPITA, CAROLINA, MIGUEL, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','SOE 1837009035',864,2160,432,3456,0,0,0,0,0,0,0,0,0,0,0,0,0,3456,'',9,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (14,'2024-12-10','REBECA, RICARDO, ABIGAIL, LUPITA, CAROLINA, MIGUEL, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','HOUSING 1064280199',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',9,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (15,'2024-12-16','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','CONDUIT   1069411491',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',10,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (16,'2024-12-16','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','FIBRA 1064280562',1770,2399,831,5000,0,0,0,0,9,0,26,0,0,0,5,0,40,5040,'',10,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (17,'2024-12-16','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','SOE 1837009010',840,1434,959,3233,2,0,0,0,4,0,0,0,0,0,0,0,6,3239,'',10,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (18,'2024-12-16','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','FIBRA 1064280558',1594,794,793,3181,0,0,0,0,7,0,12,0,0,0,0,0,19,3200,'',10,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (19,'2024-12-16','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','SOE 1837009035',432,1185,864,2481,0,0,0,0,0,0,0,0,0,0,0,0,0,2481,'',10,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (20,'2024-12-17','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','HOUSING 1061154101',350,0,0,350,0,0,0,8,0,0,0,0,0,0,0,0,8,358,'',10,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (21,'2024-12-17','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','SOE 1837009010',1152,957,1440,3549,0,0,0,0,3,0,8,0,0,0,0,0,11,3560,'',10,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (22,'2024-12-23','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','CONDUIT   1069411491',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'',11,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (23,'2024-12-23','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','FIBRA 1064280561',359,900,1193,2452,0,7,0,0,0,0,1,0,0,0,0,0,8,2460,'',11,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (24,'2024-12-23','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','SOE 1837009010',1463,0,1916,3379,0,0,0,0,21,0,0,0,0,0,0,0,21,3400,'',11,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (25,'2024-12-23','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','FIBRA 1064280558',394,1200,0,1594,0,0,0,0,0,0,6,0,0,0,0,0,6,1600,'',11,1);
INSERT INTO molex (`id_molex`,`inspection_date`,`operators`,`descripcion`,`primer_t`,`segundo_t`,`tercer_t`,`goods`,`coupler`,`dano_end_face`,`golpe_top`,`rebaba`,`dano_en_lente`,`fuera_de_spc`,`dano_fisico`,`wirebond_corto`,`wirebond_chueco`,`fisura`,`silicon_contaminacion`,`contaminacion_end_face`,`total`,`total_final`,`comments`,`id_version`,`id_proveedor`) VALUES (26,'2024-12-23','ABIGAIL, LUPITA, REBECA, CAROLINA, PAULINA, ELENA, JOSEFINA, LUZ Y HECTOR','SOE 1837009035',432,432,664,1528,0,0,0,0,0,0,0,0,0,0,0,0,0,1528,'',11,1);

CREATE TABLE administrador (
    id_administrador INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    compania VARCHAR(100) NOT NULL,
    business_unit VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    codigo_verificacion CHAR(4),
    role VARCHAR(50) DEFAULT 'Administrador',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE administrador
ADD COLUMN verificado TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN password VARCHAR(255) NOT NULL;



CREATE TABLE roles_permisos (
    id_rol_permiso INT AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT,
    permiso_ver BOOLEAN NOT NULL DEFAULT FALSE,
    permiso_editar BOOLEAN NOT NULL DEFAULT FALSE,
    permiso_capturar BOOLEAN NOT NULL DEFAULT FALSE,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    asignado_por INT NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor)
);



CREATE TABLE detalles_respaldo (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,        
    id_respaldo INT,                                  
    tabla_respaldo ENUM('usuarios', 'proveedores', 'turnos', 'inspecciones', 'materiales', 'PCBA', 'molex') NOT NULL, 
    id_registro INT,                                 
    descripcion TEXT,                               
    tipo_respaldo ENUM('individual', 'completo') NOT NULL DEFAULT 'individual', 
    FOREIGN KEY (id_respaldo) REFERENCES respaldos(id_respaldo) 
);


ALTER TABLE respaldos ADD COLUMN id_administrador INT NOT NULL;
ALTER TABLE respaldos MODIFY ruta_archivo VARCHAR(255) NULL;
ALTER TABLE detalles_respaldo ADD COLUMN tabla VARCHAR(255);



CREATE TABLE respaldos (
    id_respaldo INT AUTO_INCREMENT PRIMARY KEY,
    nombre_archivo VARCHAR(255) NOT NULL,
    fecha_respaldo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('Completado', 'Fallido') NOT NULL DEFAULT 'Completado',
    ruta_archivo BLOB NOT NULL, 
    tamano_archivo BIGINT,     
    descripcion TEXT,
    usuario_id INT,
    tipo_respaldo ENUM('individual', 'completo') NOT NULL DEFAULT 'completo',
    respaldo_automatico BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES administrador(id_administrador)
);








CREATE TABLE roles_permisos_usuarios (
    id_rol_permiso_usuarios INT AUTO_INCREMENT PRIMARY KEY,
    id_usuarios INT,
    permiso_ver BOOLEAN NOT NULL DEFAULT FALSE,
    permiso_editar BOOLEAN NOT NULL DEFAULT FALSE,
    permiso_capturar BOOLEAN NOT NULL DEFAULT FALSE,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    asignado_por INT NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (id_usuarios) REFERENCES usuarios(id_usuarios)
);





ALTER TABLE roles_permisos_usuarios 
ADD CONSTRAINT fk_roles_permisos_usuarios 
FOREIGN KEY (id_usuarios) 
REFERENCES usuarios(id_usuarios)
ON DELETE CASCADE;


ALTER TABLE respaldos MODIFY COLUMN ruta_archivo LONGTEXT;

ALTER TABLE inspecciones
ADD COLUMN id_administrador INT,
ADD FOREIGN KEY (id_administrador) REFERENCES administrador(id_administrador);

ALTER TABLE molex
ADD COLUMN id_administrador INT,
ADD FOREIGN KEY (id_administrador) REFERENCES administrador(id_administrador);