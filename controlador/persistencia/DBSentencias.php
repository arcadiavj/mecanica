<?php


interface DBSentencias {
    
//SENTENCIAS PARA BLOQUEO Y DESBLOQUEO (CONCURRENCIA)
    const BLOQUEAR_TABLAS = "LOCK TABLES";
    const DESBLOQUEAR_TABLAS = "UNLOCK TABLES";
    
//USUARIOS
    const BUSCAR_NOMBRE_USUARIO = "SELECT * FROM usuario WHERE fch_baja = '0000-00-00 00:00:00' AND id_usuario = ? LOCK IN SHARE MODE";
    //const CHECK_USER = "SELECT * FROM usuario INNER JOIN consultorio ON usuario.id_consultorio = consultorio.id_consultorio WHERE usuario_usuario = ? LOCK IN SHARE MODE";
    
    const CHECK_USER = "SELECT * FROM usuario WHERE usuario_usuario = ?";
    const BUSCAR_USUARIOS="SELECT usuario.id_usuario,usuario.nombre_usuario, usuario.apellido_usuario,usuario.usuario_usuario, usuario.clave_usuario,
                            usuario.tipoAcceso_usuario, usuario.fch_creacion, usuario.fch_modificacion, usuario.id_especialidad, especialidad.descripcion_especialidad  
                            FROM usuario INNER JOIN especialidad ON usuario.id_especialidad = especialidad.id_especialidad 
                            WHERE usuario.fch_baja= '0000-00-00 00:00:00' LOCK IN SHARE MODE";
    const ELIMINAR_USUARIO="UPDATE usuario SET fch_baja = ? WHERE id_usuario=?";
    //const INSERTAR_USUARIO="INSERT INTO usuario(nombre_usuario, apellido_usuario,usuario_usuario,clave_usuario,tipoAcceso_usuario,fch_creacion,fch_modificacion,fch_baja) VALUES (?,?,?,?,?,?,?,?)";
    const BUSCAR_USUARIO_ID="SELECT * FROM usuario WHERE id_usuario = ?";
    
    const BUSCAR_ULTIMO_USUARIO="SELECT MAX(id_usuario) FROM usuario";
    const MODIFICAR_USUARIO="UPDATE usuario SET nombre_usuario=?, apellido_usuario=?, usuario_usuario=?,tipoAcceso_usuario=?, fch_modificacion=?,fch_baja=?, id_especialidad = ? WHERE id_usuario=?";
    const MODIFICAR_USUARIO_CLAVE="UPDATE usuario SET clave_usuario = ?, fch_modificacion = ? WHERE id_usuario=? ";
    //UPDATE usuario SET id_consultorio = ? WHERE id_usuario=?
    
    
    const ACTUALIZAR_CONSULTORIO_DE_USER = "UPDATE usuario SET usuario.id_consultorio = ?, estado_usuario = 1 WHERE usuario.id_usuario = ?";
//    const ACTUALIZAR_CONSULTORIO_DE_USER = "UPDATE usuario INNER JOIN consultorio ON usuario.id_consultorio = consultorio.id_consultorio 
//        SET usuario.id_consultorio = ?, usuario.estado_usuario = 1, consultorio.estado_consultorio = 1   
//        WHERE usuario.id_usuario = ? AND consultorio.id_consultorio = usuario.id_consultorio";
    const ACTUALIZAR_CONSULTORIO_DE_USER2 = "UPDATE consultorio SET consultorio.estado_consultorio = 1 WHERE id_consultorio = ?";
    
    const TRAER_DESCRIPCION_CONSULTORIO ="SELECT descripcion_consultorio FROM consultorio WHERE id_consultorio = ? LOCK IN SHARE MODE";

    //NUEVAS SENTENCIAS PARA CREO
    const BUSCAR_ESPECIALIDADES = "SELECT * FROM especialidad WHERE especialidad.fch_baja = '0000-00-00' LOCK IN SHARE MODE";
    const ELIMINAR_ESPECIALIDAD = "UPDATE especialidad SET fch_baja = ? WHERE id_especialidad = ?";
    const INSERTAR_ESPECIALIDAD = "INSERT INTO especialidad(descripcion_especialidad, fch_creacion) VALUES(?, ?)";
    const ACTUALIZAR_ESPECIALIDAD = "UPDATE especialidad SET descripcion_especialidad = ?, fch_modificacion = ? WHERE id_especialidad = ?";
    const ULTIMA_ESPECIALIDAD = "SELECT MAX(id_especialidad) FROM especialidad WHERE fch_baja = '0000-00-00' LOCK IN SHARE MODE";
    const BUSCAR_UNA_ESPECIALIDAD = "SELECT * FROM especialidad WHERE especialidad.fch_baja = '0000-00-00' AND id_especialidad = ? LOCK IN SHARE MODE";
    
    
    const BUSCAR_CONSULTORIO = "SELECT * FROM consultorio WHERE consultorio.fch_baja = '0000-00-00' AND id_consultorio != 11 ORDER BY descripcion_consultorio LOCK IN SHARE MODE";
    const BUSCAR_CONSULTORIO_LOGIN = "SELECT * FROM consultorio WHERE id_consultorio != 11 AND (estado_consultorio = 0 OR id_consultorio = 14) ORDER BY descripcion_consultorio LOCK IN SHARE MODE";
    
    const ELIMINAR_CONSULTORIO = "UPDATE consultorio SET fch_baja = ? WHERE id_consultorio = ?";
    const INSERTAR_CONSULTORIO = "INSERT INTO consultorio(descripcion_consultorio, fch_creacion) VALUES(?, ?)";
    const ACTUALIZAR_CONSULTORIO = "UPDATE consultorio SET descripcion_consultorio = ?, fch_modificacion = ? WHERE id_consultorio = ?";
    const ULTIMO_CONSULTORIO = "SELECT MAX(id_consultorio) FROM consultorio WHERE fch_baja = '0000-00-00' LOCK IN SHARE MODE";
    const BUSCAR_UN_CONSULTORIO = "SELECT * FROM consultorio WHERE consultorio.fch_baja = '0000-00-00' AND id_consultorio = ? LOCK IN SHARE MODE";
    
    const MOSTRAR_TURNOS = "SELECT id_consultorio, MAX(fch_llegada_turno)FROM turno GROUP BY id_consultorio";
    const ULTIMO_TURNO = "SELECT * FROM turno WHERE fch_llegada_turno = (SELECT MAX(fch_llegada_turno) FROM turno) LOCK IN SHARE MODE";
    
    //actuales son en curso
    const BUSCAR_TURNOS_ACTUALES = "SELECT * FROM turno INNER JOIN usuario ON turno.id_usuario = usuario.id_usuario WHERE turno.id_usuario = ? AND ((estado_turno = 0 OR estado_turno = 1) AND fch_llegada_turno LIKE ?) ORDER BY fch_llegada_turno"; //INNER JOIN consultorio ON turno.id_consultorio = consultorio.id_consultorio
    //terminados son los ya atendidos
    const BUSCAR_TURNOS_TERMINADOS = "SELECT * FROM turno INNER JOIN usuario ON turno.id_usuario = usuario.id_usuario WHERE turno.id_usuario = ? AND (estado_turno = 2 AND fch_llegada_turno LIKE ?) ORDER BY fch_llegada_turno";
    const CANCELAR_TURNO ="UPDATE turno SET estado_turno = '0',fch_inicio_turno = ?, id_usuario = ? WHERE turno.id_turno=?";
	const ULTIMO_TURNO2 = "SELECT fch_llegada_turno FROM turno ORDER BY fch_llegada_turno DESC LIMIT 1";
    const ULTIMOS_TURNOS = "SELECT * FROM turno WHERE estado_turno = '1' ORDER BY fch_llegada_turno DESC";
    //const BUSCAR_USUARIOS_LOGEADOS = "SELECT * FROM usuario WHERE usuario.fch_baja = '0000-00-00' AND usuario.estado_turno = 1";
    
    //"id_usuario"=>$idUser, "id_consultorio"=>$idCons, "id_turno" => $id
    const LLAMAR_TURNO ="UPDATE turno SET estado_turno = '1',fch_inicio_turno = ?, id_usuario = ? WHERE turno.id_turno=?";
    const VOLVER_LLAMAR_TURNO ="UPDATE turno SET estado_turno = '0',`fch_inicio_turno`= '0000-00-00 00:00:00',`fch_fin_turno`='0000-00-00 00:00:00' WHERE turno.id_turno=?";
    const TERMINAR_TURNO ="UPDATE `turno` SET `estado_turno` = '2',`fch_fin_turno` = ? WHERE `turno`.`id_turno`=?";

    const INSERTAR_TURNO="INSERT INTO turno (id_consultorio, id_usuario, historia_clinica_turno, estado_turno, fch_llegada_turno, fch_inicio_turno,fch_fin_turno) 
                            VALUE (?,?,?,?,?,?,?)";
    const ULTIMO_TURNO_CARGA= "SELECT MAX(id_turno) FROM turno LOCK IN SHARE MODE";
    const BUSCAR_UN_TURNO = "SELECT * FROM turno INNER JOIN usuario ON turno.id_usuario = usuario.id_usuario WHERE id_turno = ? LOCK IN SHARE MODE";
    const BUSCAR_TODOS_TURNOS = "SELECT * FROM turno INNER JOIN usuario ON turno.id_usuario = usuario.id_usuario WHERE turno.fch_llegada_turno >= ? AND turno.fch_llegada_turno < ?";//"SELECT * FROM turno INNER JOIN usuario ON turno.id_usuario = usuario.id_usuario WHERE estado_turno != 9";
    const ELIMINAR_TURNO="UPDATE turno SET estado_turno = 9 WHERE id_turno = ?";
    const ACTUALIZAR_TURNO="UPDATE turno SET id_usuario = ?, historia_clinica_turno = ? WHERE id_turno = ?";
    const VERIFICAR_TURNO = "SELECT * FROM turno WHERE id_usuario = ? AND id_turno = ? LOCK IN SHARE MODE";
    
    //REPORTES
    
    const REPORTE_DIARIO = "SELECT * FROM usuario INNER JOIN turno ON usuario.id_usuario = turno.id_usuario WHERE turno.id_usuario = ? AND (turno.fch_llegada_turno LIKE ? AND turno.estado_turno = 2) ORDER BY turno.fch_llegada_turno";
    const REPORTE_DIARIO_TODOS = "SELECT * FROM usuario INNER JOIN turno ON usuario.id_usuario = turno.id_usuario WHERE turno.fch_llegada_turno LIKE ? AND turno.estado_turno = 2 ORDER BY turno.fch_llegada_turno";
    const REPORTE_PERIODO = "SELECT * FROM usuario INNER JOIN turno ON usuario.id_usuario = turno.id_usuario WHERE turno.id_usuario = ? AND (turno.fch_llegada_turno > ? AND (fch_llegada_turno < ? AND turno.estado_turno = 2)) ORDER BY turno.fch_llegada_turno";
    const BUSCAR_USUARIOS_REPORTE = "SELECT DISTINCT usuario.id_usuario, usuario.apellido_usuario, usuario.nombre_usuario FROM usuario INNER JOIN turno ON usuario.id_usuario = turno.id_usuario WHERE usuario.tipoAcceso_usuario != 0 AND (usuario.fch_baja = '0000-00-00' AND turno.fch_llegada_turno LIKE ?) LOCK IN SHARE MODE";
    const BUSCAR_USUARIOS_REPORTE_PERIODO = "SELECT DISTINCT usuario.id_usuario, usuario.apellido_usuario, usuario.nombre_usuario FROM usuario INNER JOIN turno ON usuario.id_usuario = turno.id_usuario WHERE usuario.tipoAcceso_usuario != 0 AND (usuario.fch_baja = '0000-00-00' AND (turno.fch_llegada_turno >= ? AND turno.fch_llegada_turno <= ? )) LOCK IN SHARE MODE";

    const VERIFICAR_EXISTENCIA_USUARIO ="SELECT COUNT(*) FROM usuario WHERE usuario_usuario = ? LOCK IN SHARE MODE";
    const ULTIMOS_TURNOS_PEPE="SELECT usuario.id_consultorio, turno.estado_turno, turno.historia_clinica_turno, usuario.nombre_usuario, usuario.apellido_usuario, usuario.estado_usuario ,consultorio.descripcion_consultorio 
                                FROM turno INNER JOIN usuario ON turno.id_usuario=usuario.id_usuario 
                                INNER JOIN consultorio ON usuario.id_consultorio=consultorio.id_consultorio 
                                ORDER BY fch_inicio_turno DESC LIMIT 1 LOCK IN SHARE MODE"; //WHERE estado_turno = '1' OR estado_turno = '2'
     
//    const CERRAR_SESION= "UPDATE usuario SET estado_usuario = 0, id_consultorio = 11 WHERE id_usuario = ?";
    const CERRAR_SESION = "UPDATE usuario SET usuario.id_consultorio = 11, usuario.estado_usuario = 0 WHERE usuario.id_usuario = ?";
    const CERRAR_SESION2 = "UPDATE consultorio SET consultorio.estado_consultorio = 0 WHERE id_consultorio = ?";
    const BUSCAR_ID_CONS_DEL_USER = "SELECT id_consultorio FROM usuario WHERE id_usuario = ? LOCK IN SHARE MODE";
    
    const BUSCAR_UN_USUARIO = "SELECT * FROM usuario WHERE id_usuario = ? LOCK IN SHARE MODE";
    const BUSCAR_DOCTORES="SELECT usuario.id_usuario, usuario.nombre_usuario, usuario.apellido_usuario, usuario.estado_usuario, usuario.id_consultorio
                            FROM usuario"; 
                            //INNER JOIN turno 
                            //ON usuario.id_usuario = turno.id_usuario";
    const BUSCAR_PACIENTE_ATENDIDO="SELECT id_turno FROM turno WHERE estado_turno=1 AND id_usuario=?";
    const TERMINAR_PACIENTE_ATENDIDO="UPDATE turno SET estado_turno=2 WHERE id_turno=?";
   
    //SENTENCIAS VIDEOS
    
    const BUSCAR_VIDEO = "SELECT * FROM video WHERE fch_baja = '0000-00-00'";
    
    
    
}   
