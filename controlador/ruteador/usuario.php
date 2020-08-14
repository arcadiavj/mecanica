Create Table CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(50) NOT NULL,
  `apellido_usuario` varchar(50) NOT NULL,
  `usuario_usuario` varchar(15) NOT NULL,
  `clave_usuario` varchar(50) NOT NULL,
  `tipoAcceso_usuario` int(1) NOT NULL,
  `id_area_usuario` int(11) NOT NULL,
  `fch_creacion` datetime NOT NULL,
  `fch_modificacion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fch_baja` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8
