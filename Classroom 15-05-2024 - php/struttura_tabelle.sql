CREATE TABLE case_editrici (
    id int PRIMARY KEY AUTO_INCREMENT,
    nome varchar(30) NOT NULL,
    sito_web varchar(30) NOT NULL,
    data_inserimento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (nome),
    UNIQUE (sito_web)
);

CREATE TABLE autori (
    id int PRIMARY KEY AUTO_INCREMENT,
    nome varchar(30) NOT NULL,
    cognome varchar(30) NOT NULL,
    data_inserimento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE libri (
    id int PRIMARY KEY AUTO_INCREMENT,
    titolo varchar(50) NOT NULL,
    id_autore int NOT NULL,
    disponibile tinyint(1) NOT NULL DEFAULT '1',
    data_inserimento datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_casa_editrice int NOT NULL,
    FOREIGN KEY (id_autore) REFERENCES autori (id),
    FOREIGN KEY (id_casa_editrice) REFERENCES case_editrici (id)
);