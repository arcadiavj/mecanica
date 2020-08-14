Create Table CREATE TABLE `sitrack` (
  `Patente` varchar(12) CHARACTER SET latin1 NOT NULL,
  `Descripcion` varchar(5) CHARACTER SET latin1 DEFAULT NULL,
  `Fecha` date NOT NULL,
  `Kmrecorridos` double NOT NULL,
  `HrConduccion` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `HrDetenido` varchar(25) CHARACTER SET latin1 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci ROW_FORMAT=COMPACT
