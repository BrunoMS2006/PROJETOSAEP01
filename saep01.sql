create database saep01;
use saep01;

create table usuarios(
						usu_cod int primary key auto_increment not null,
						usu_nome varchar (100),
						usu_email varchar(100)
); 

create table tarefas(
						 tarefa_cod int primary key auto_increment not null,
						 tarefa_setor varchar(50),
						 tarefa_prioridade varchar(25),
						 tarefa_descricao varchar(50),
						 tarefa_status varchar(50)
);

ALTER TABLE tarefas
ADD COLUMN usu_cod INT,
ADD CONSTRAINT fk_usu_cod FOREIGN KEY (usu_cod) REFERENCES usuarios(usu_cod);
