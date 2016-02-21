<?php require_once('Connections/arkg.php'); ?>
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
$aaaa = $_POST['dateAnio'];
$mm = $_POST['dateMes'];
$dd = $_POST['dateDia'];
$birthday_sql = $aaaa."-".$mm."-".$dd;

#Almacenando el numero de carnet en una variable de sesion
  $_SESSION['carnet'] = $_POST['carVal']; 

#echo $_POST['carVal'];
#echo "<br>";
#echo $_POST['nombre'];
#echo "<br>";
#echo $_POST['apellidos'];
#echo "<br>";
#echo $birthday_sql;
#echo "<br>";
#echo $_POST['mod'];
#echo "<br>";
#echo $_POST['usr_mod'];

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO info_basic (id_basic, carnet, nombres, apellidos, birthday, `mod`, usr_mod) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id_basic'], "int"),
                       GetSQLValueString($_POST['carVal'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['apellidos'], "text"),
                       GetSQLValueString($birthday_sql, "date"),
                       GetSQLValueString($_POST['mod'], "date"),
                       GetSQLValueString($_POST['usr_mod'], "text"));

  mysql_select_db($database_arkg, $arkg);
  $Result1 = mysql_query($insertSQL, $arkg) or die(mysql_error());

  $insertGoTo = "index2.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
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
<script>
$(document).ready(function(){
   $("#generarCarnet").click(function(evento){
		var n = document.getElementById('nombre');
		var a = document.getElementById('apellidos');
		var day = document.getElementById('dateDia');
		var month = document.getElementById('dateMes');
		var year = document.getElementById('dateAnio');
      evento.preventDefault();
   $("#destino").load("lib/jqueryCarnet.php", {
						 nombre: n.value, 
						 apellidos: a.value, 
						 anio: year.value, 
						 mes: month.value, 
						 dia: day.value
						 }, 
						 function () {
      						var text = $(this).text();
      						$("input#carVal").val(text);
							 alert("El Codigo de Carnet ha sido generado.");			
      });
   });
});
</script>
</head>
<body>
<div class="wrapper">
<div class="header">
<div class="auser">Usuario conectado: <?php echo $_SESSION['MM_Username']; ?> || <a href="<?php echo $logoutAction ?>">Cerrar Sesion</a></div>
</div>
<div id="frame">

<div id="destino" style="width:170px; display:none;"></div>

  <center>
    <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
      <p>
        <label>Nombres:
          <input name="nombre" type="text" id="nombre" />
        </label>
      </p>
      <p>
        <label>Apellidos:
          <input name="apellidos" type="text" id="apellidos" />
        </label>
      </p>
      <p>Fecha de Nacimiento:
        <input name="dateDia" type="text" id="dateDia" size="6" maxlength="2" />
        /
        <label>
          <select name="dateMes" id="dateMes">
            <option value="-1" selected="selected">- Mes -</option>
            <option value="01">Enero</option>
            <option value="02">Febrero</option>
            <option value="03">Marzo</option>
            <option value="04">Abril</option>
            <option value="05">Mayo</option>
            <option value="06">Junio</option>
            <option value="07">Julio</option>
            <option value="08">Agosto</option>
            <option value="09">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
          </select>
        </label>
        <label>
          /
          <input name="dateAnio" type="text" id="dateAnio" size="12" maxlength="4" />
        </label>
      </p>
  		<p>
        Carnet: 
        <label>
          <input name="carVal" type="text" id="carVal" /> 
        </label>
        </p>
<p>&nbsp;</p>
<p>
  <input type="hidden" name="id_basic" id="id_basic" />
  <input name="usr_mod" type="hidden" id="usr_mod" value="<?php echo $_SESSION['MM_Username']; ?>" />
  <input type="hidden" name="mod" id="mod" value="<?php echo date('Y-m-d H:i:s'); ?>" />
  <a href="#" id="generarCarnet" class="boton">Generar Carnet</a>
<label>
  <input name="btnClear" type="reset" class="boton" id="button" value="Limpiar Formulario" />
</label>
<label>
  <input name="btnNext" type="submit" class="boton" id="button" value="Siguiente" />
</label>
</p>
<input type="hidden" name="MM_insert" value="form1" />
    </form>
  
  <img src="img/progress1.jpg" alt="Proceso de Inscripción" width="669" height="59" /><br />
  
  </center>
  

</div>
</div>
<div class="footer">
Numeros. Recursos Humanos y Estadisticas Ministeriales<br />
MSB Technology Solutions &copy; Todos los Derechos Reservados <?php echo date('Y'); ?>
</div>
</body>
</html>