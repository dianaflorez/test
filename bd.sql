CREATE TABLE role(
	id 		  int(11) NOT NULL auto_increment PRIMARY KEY,
	nombre	varchar(15) not null
)ENGINE=InnoDB;

CREATE TABLE tipo_identificacion(
	id 		  int(11) NOT NULL auto_increment PRIMARY KEY,
	nombre	varchar(15) not null
)ENGINE=InnoDB;

CREATE TABLE usuario(
	id 		       int(11) NOT NULL auto_increment PRIMARY KEY,
	nombres		   varchar(25) not null,
	apellidos 	 varchar(25) not null,
  id_tipo_identificacion  int(11) not null,
	identificacion       varchar(12) not null unique,
	email 	 	           varchar(100) not null unique,
  username 	 	         varchar(100) not null unique,
	clave					varchar(70) not null,
	direccion 	   varchar (50) ,
	telefono 	     varchar(15),
 	id_role int(11) not null,
	FOREIGN KEY(id_tipo_identificacion) REFERENCES tipo_identificacion(id),
	FOREIGN KEY(id_role) REFERENCES role(id)
)ENGINE=InnoDB;

INSERT INTO `tipo_identificacion`(`id`, `nombre`) VALUES
(1,'Cedula'),(2,'Extranjeria'),(3, 'Tarjeta Identidad');

INSERT INTO `role`(`id`, `nombre`) VALUES
(1,'Admin'),(2,'Agent'),(3, 'Customer');

INSERT INTO `usuario`(id, nombres, apellidos, id_tipo_identificacion, identificacion, email, username, clave, direccion, telefono, id_role)
VALUES (1, 'Diana', 'Florez', 1, '1000888999', 'diana@gmail.com', 'diana@gmail.com','wiJvbX5pOMFRQ', 'Mz F Casa 7 Molinos Norte', '3007779999', 1)
