Create Table CREATE TABLE `tiposervicio` (
  `id_tiposervicio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_tiposervicio` varchar(255) NOT NULL,
  `descripcion_tiposervicio` blob NOT NULL,
  `fch_creacion` datetime NOT NULL,
  `fch_modificacion` datetime NOT NULL,
  `fch_baja` datetime NOT NULL,
  KEY `id_tiposervicio` (`id_tiposervicio`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1
