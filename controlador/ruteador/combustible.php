Create Table CREATE TABLE `combustible` (
  `id_combustible` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_combustible` varchar(11) NOT NULL,
  `fch_creacion` datetime NOT NULL,
  `fch_modificacion` datetime NOT NULL,
  `fch_baja` datetime NOT NULL,
  PRIMARY KEY (`id_combustible`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8
