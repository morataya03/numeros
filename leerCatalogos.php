<?php

function getMinisterio($idm)
{
	$mysqli = new mysqli("localhost", "gkudzjlv_gopherU", "g4xQgC4k5zP+il", "gkudzjlv_gopher");
	$resultado = $mysqli->query("SELECT * FROM `cat_minis` WHERE `id_min` = $idm");
	$fila = $resultado->fetch_assoc();
	return htmlentities($fila['ministerio']);

}

function getServicio($ids)
{
	$mysqli = new mysqli("localhost", "gkudzjlv_gopherU", "g4xQgC4k5zP+il", "gkudzjlv_gopher");
	$resultado = $mysqli->query("SELECT * FROM `cat_serv` WHERE `id_ser` = $ids");
	$fila = $resultado->fetch_assoc();
	return htmlentities($fila['servicio']);
}

function getCargo($idc)
{
	$mysqli = new mysqli("localhost", "gkudzjlv_gopherU", "g4xQgC4k5zP+il", "gkudzjlv_gopher");
	$resultado = $mysqli->query("SELECT * FROM `cat_carg` WHERE `id_carg` = $idc");
	$fila = $resultado->fetch_assoc();
	return htmlentities($fila['cargo']);
}

function getCulto($idh)
{
	$mysqli = new mysqli("localhost", "gkudzjlv_gopherU", "g4xQgC4k5zP+il", "gkudzjlv_gopher");
	$resultado = $mysqli->query("SELECT * FROM `cat_culto` WHERE `id_culto` = $idh");
	$fila = $resultado->fetch_assoc();
	return htmlentities($fila['culto']);
}
?>
