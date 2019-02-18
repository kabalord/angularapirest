CREATE DATABASE IF NOT EXISTS angularapirest;
USE angularapirest;

CREATE TABLE productos(
id 			int(255) auto_increment not null,
nombre 		varchar(255),
descripcion text, 
precio		varchar(255),
imagen		varchar(255),
CONSTRAINT pk_productos PRIMARY KEY(id)
)ENGINE=InnoDb;;
