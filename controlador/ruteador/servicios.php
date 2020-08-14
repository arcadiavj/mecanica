Create Table CREATE TABLE `servicios` (
  `id_servicios` int(11) NOT NULL AUTO_INCREMENT,
  `id_tiposervicio` int(11) NOT NULL,
  `id_movilesu` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_areas` int(11) NOT NULL,
  `Observaciones` blob NOT NULL,
  `aprobado` tinyint(1) DEFAULT NULL,
  `km` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_servicios`),
  KEY `id_area` (`id_areas`),
  CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`id_areas`) REFERENCES `areas` (`id_areas`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1
