<?php require_once('Connections/arkg.php'); ?>
<?php require("leerCatalogos.php"); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "Alfa,Delta,Sierra";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO info_serv (id_serv, carnet, ministerio, servicio, cargo, horario, `mod`, usr_mod) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id_serv'], "int"),
                       GetSQLValueString($_POST['carnet'], "text"),
                       GetSQLValueString($_POST['ministerio'], "int"),
                       GetSQLValueString($_POST['servicio'], "int"),
                       GetSQLValueString($_POST['cargo'], "int"),
                       GetSQLValueString($_POST['culto'], "int"),
                       GetSQLValueString($_POST['mod'], "date"),
                       GetSQLValueString($_POST['usr_mod'], "text"));

  mysql_select_db($database_arkg, $arkg);
  $Result1 = mysql_query($insertSQL, $arkg) or die(mysql_error());

  $insertGoTo = "index3.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_servicio = "-1";
if (isset($_SESSION['carnet'])) {
  $colname_servicio = $_SESSION['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_servicio = sprintf("SELECT * FROM info_serv WHERE carnet = %s ORDER BY ministerio ASC", GetSQLValueString($colname_servicio, "text"));
$servicio = mysql_query($query_servicio, $arkg) or die(mysql_error());
$row_servicio = mysql_fetch_assoc($servicio);
$totalRows_servicio = mysql_num_rows($servicio);

$colname_name = "-1";
if (isset($_SESSION['carnet'])) {
  $colname_name = $_SESSION['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_name = sprintf("SELECT * FROM info_basic WHERE carnet = %s", GetSQLValueString($colname_name, "text"));
$name = mysql_query($query_name, $arkg) or die(mysql_error());
$row_name = mysql_fetch_assoc($name);
$totalRows_name = mysql_num_rows($name);

mysql_select_db($database_arkg, $arkg);
$query_cargo = "SELECT * FROM cat_carg ORDER BY id_carg ASC";
$cargo = mysql_query($query_cargo, $arkg) or die(mysql_error());
$row_cargo = mysql_fetch_assoc($cargo);
$totalRows_cargo = mysql_num_rows($cargo);

mysql_select_db($database_arkg, $arkg);
$query_horario = "SELECT * FROM cat_culto ORDER BY id_culto ASC";
$horario = mysql_query($query_horario, $arkg) or die(mysql_error());
$row_horario = mysql_fetch_assoc($horario);
$totalRows_horario = mysql_num_rows($horario);

mysql_select_db($database_arkg, $arkg);
$query_ministerio = "SELECT * FROM cat_minis ORDER BY id_min ASC";
$ministerio = mysql_query($query_ministerio, $arkg) or die(mysql_error());
$row_ministerio = mysql_fetch_assoc($ministerio);
$totalRows_ministerio = mysql_num_rows($ministerio);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!--
ARCA DE NOE v2.0 Gopher
Departamento de Comunicaciones de Escuela Biblica
Luis Morataya (morataya03@gmail.com)
23/09/2012
1a Corintios 15:58
 -->
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Arca de Noe :: Ghoper</title>
<link href="css/estilo.css" rel="stylesheet" type="text/css" />
<script src="lib/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>
</head>

<body>
<div class="wrapper">
<div class="header">
<div class="auser">Usuario conectado: <?php echo $_SESSION['MM_Username']; ?> || <a href="<?php echo $logoutAction ?>">Cerrar Sesion</a></div>
</div>
<div id="frame">
<div id="destino" style="width:170px; display: none;"></div>
  <center>
      <p><strong>Nombre:</strong> [<?php echo $row_name['nombres'] . " " . $row_name['apellidos']; ?>] <strong>Carnet:</strong> [<?php echo $_SESSION['carnet']; ?>]</p>
  <div class="box_left">
  <script type="text/javascript">
	$(document).ready(function(){
			   $(".ministerio").change(function(){
							 var id=$(this).val();
							 var dataString = 'min_id='+ id;
							 $.ajax({
								type: "POST",
								url: "ajax_servicio.php",
								data: dataString,
								cache: false,
								success: function(html){
									$(".servicio").html(html);
									}
								});
						 });
	 });
  </script>
    <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
      <table width="200" border="0" cellspacing="3" cellpadding="3">
        <tr>
          <th colspan="2" align="left" nowrap="nowrap">Información Ministerial</th>
          </tr>
        <tr>
          <td align="right">Area:</td>
          <td>
          <select name="ministerio" class="ministerio" id="ministerio">
            <option selected="selected" value="-1">-- Seleccione el Area de Servicio --</option>
            <?php
do {  
?>
            <option value="<?php echo $row_ministerio['id_min']?>"><?php echo $row_ministerio['ministerio']?></option>
            <?php
} while ($row_ministerio = mysql_fetch_assoc($ministerio));
  $rows = mysql_num_rows($ministerio);
  if($rows > 0) {
      mysql_data_seek($ministerio, 0);
	  $row_ministerio = mysql_fetch_assoc($ministerio);
  }
?>
          </select>
          </td>
          </tr>
        <tr>
          <td align="right">Ministerio:</td>
          <td>
          <select name="servicio" class="servicio" id="servicio">
			<option selected="selected">-- Seleccione un Ministerio --</option>
		  </select>
		  </td>
          </tr>
        <tr>
          <td align="right">Cargo:</td>
          <td>
          <label>
            <select name="cargo" id="cargo">
              <option value="-1">-- Seleccione un Cargo --</option>
              <?php
do {  
?>
              <option value="<?php echo $row_cargo['id_carg']?>"><?php echo $row_cargo['cargo']?></option>
              <?php
} while ($row_cargo = mysql_fetch_assoc($cargo));
  $rows = mysql_num_rows($cargo);
  if($rows > 0) {
      mysql_data_seek($cargo, 0);
	  $row_cargo = mysql_fetch_assoc($cargo);
  }
?>
            </select>
          </label></td>
        </tr>
        <tr>
          <td align="right">Horario:</td>
          <td><label>
            <select name="culto" id="culto">
              <option value="-1">-- Seleccione un Horario --</option>
              <?php
do {  
?>
              <option value="<?php echo $row_horario['id_culto']?>"><?php echo $row_horario['culto']?></option>
              <?php
} while ($row_horario = mysql_fetch_assoc($horario));
  $rows = mysql_num_rows($horario);
  if($rows > 0) {
      mysql_data_seek($horario, 0);
	  $row_horario = mysql_fetch_assoc($horario);
  }
?>
            </select>
          </label></td>
          </tr>
      </table>
      <input name="id_serv" type="hidden" id="id_serv" value="" />
    <input type="hidden" name="carnet" value="<?php echo $_SESSION['carnet']; ?>" />
    <input name="usr_mod" type="hidden" id="usr_mod" value="<?php echo $_SESSION['MM_Username']; ?>" />
  <input type="hidden" name="mod" id="mod" value="<?php echo date('Y-m-d H:i:s'); ?>" />
      <br>
      <p>
        <label>
          <input name="button2" type="reset" class="boton" id="button2" value="Limpiar Formulario" />
          <input name="button" type="submit" class="boton" id="button" value="Agregar" />
        </label>
        <input name="button3" type="button" class="boton" id="button3" onclick="MM_goToURL('parent','foto.php');return document.MM_returnValue" value="Siguiente" />
      </p>
      <input type="hidden" name="MM_insert" value="form1" />
    </form>
  </div>
  <div class="box_right">
    <?php do { ?>
      <div class="servicio_detalle">
        <div class="servicio_detalle_Serv"><?php echo getServicio($row_servicio['servicio']); ?></div>
        <div class="servicio_detalle_Det"><?php echo getCargo($row_servicio['cargo']); ?> | <?php echo getCulto($row_servicio['horario']); ?></div>
        </div>
      <?php } while ($row_servicio = mysql_fetch_assoc($servicio)); ?>
    
  </div>
<p>&nbsp;</p>
    <p style="display:block; margin-top:20px;"><img src="img/progress3.jpg" alt="Proceso de Inscripción" width="669" height="59" /><br />
    </p>
  </center>
  

</div>
</div>
<div class="footer">
Numeros. Recursos Humanos y Estadisticas Ministeriales<br />
MSB Technology Solutions &copy; Todos los Derechos Reservados <?php echo date('Y'); ?>
</div>
</body>
</html>
<?php
mysql_free_result($servicio);

mysql_free_result($name);

mysql_free_result($cargo);

mysql_free_result($horario);

mysql_free_result($ministerio);
?>
