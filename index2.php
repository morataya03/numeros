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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO info_pers (id_pers, carnet, direccion, telefono, celular, email, `work`, workplace, conversion, bautismo, `mod`, usr_mod) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['id_pers'], "int"),
                       GetSQLValueString($_POST['carnet'], "text"),
                       GetSQLValueString($_POST['direccion'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['work'], "text"),
                       GetSQLValueString($_POST['workplace'], "text"),
                       GetSQLValueString($_POST['conversion'], "text"),
                       GetSQLValueString($_POST['bautismo'], "text"),
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

$colname_name = "-1";
if (isset($_SESSION['carnet'])) {
  $colname_name = $_SESSION['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_name = sprintf("SELECT * FROM info_basic WHERE carnet = %s", GetSQLValueString($colname_name, "text"));
$name = mysql_query($query_name, $arkg) or die(mysql_error());
$row_name = mysql_fetch_assoc($name);
$totalRows_name = mysql_num_rows($name);
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
</head>

<body>
<div class="wrapper">
<div class="header">
<div class="auser">Usuario conectado: <?php echo $_SESSION['MM_Username']; ?> || <a href="<?php echo $logoutAction ?>">Cerrar Sesion</a></div>
</div>
<div id="frame">
<div id="destino" style="width:170px; display: none;"></div>
  <center>
  <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
    <p><strong>Nombre:</strong> [<?php echo $row_name['nombres'] . " " . $row_name['apellidos']; ?>] <strong>Carnet:</strong> [<?php echo $_SESSION['carnet']; ?>]</p>
      <table align="center">
        <tr valign="baseline">
          <th colspan="2" align="left" valign="top" nowrap="nowrap">Información Personal</th>
          </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">Direccion:</td>
          <td><textarea name="direccion" cols="50" rows="5"></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Telefono:</td>
          <td><input type="text" name="telefono" value="" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Celular:</td>
          <td><input type="text" name="celular" value="" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Email:</td>
          <td><input type="text" name="email" value="" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr valign="baseline">
          <th colspan="2" align="left" nowrap="nowrap">Información Laboral</th>
          </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Ocupación:</td>
          <td><input type="text" name="work" value="" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Lugar de Trabajo:</td>
          <td><input type="text" name="workplace" value="" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr valign="baseline">
          <th colspan="2" align="left" nowrap="nowrap">Información Religiosa</th>
          </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Año de Conversion:</td>
          <td><input name="conversion" type="text" value="" size="32" maxlength="4" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Año de Bautismo:</td>
          <td><input name="bautismo" type="text" value="" size="32" maxlength="4" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input name="Reset" type="reset" class="boton" value="Limpiar Formulario" />
            <label>
              <input name="button" type="submit" class="boton" id="button" value="Siguiente" />
            </label></td>
        </tr>
      </table>
      <input type="hidden" name="id_pers" value="" />
    <input type="hidden" name="carnet" value="<?php echo $_SESSION['carnet']; ?>" />
    <input name="usr_mod" type="hidden" id="usr_mod" value="<?php echo $_SESSION['MM_Username']; ?>" />
  <input type="hidden" name="mod" id="mod" value="<?php echo date('Y-m-d H:i:s'); ?>" />
    <input type="hidden" name="MM_insert" value="form2" />
  </form>
    <p>&nbsp;</p>
<img src="img/progress2.jpg" alt="Proceso de Inscripción" width="669" height="59" /><br />
  
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
mysql_free_result($name);
?>
