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

#Limpiando la variable de sesion carnet
  unset($_SESSION['carnet']); 

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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE security SET carnet=%s, contrasena=%s, acceso=%s WHERE id_usr=%s",
                       GetSQLValueString($_POST['carnet'], "text"),
                       GetSQLValueString($_POST['contrasena'], "text"),
                       GetSQLValueString($_POST['acceso'], "text"),
                       GetSQLValueString($_POST['id_usr'], "int"));

  mysql_select_db($database_arkg, $arkg);
  $Result1 = mysql_query($updateSQL, $arkg) or die(mysql_error());

  $updateGoTo = "search.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_name = "-1";
if (isset($_GET['carnet'])) {
  $colname_name = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_name = sprintf("SELECT * FROM info_basic WHERE carnet = %s", GetSQLValueString($colname_name, "text"));
$name = mysql_query($query_name, $arkg) or die(mysql_error());
$row_name = mysql_fetch_assoc($name);
$totalRows_name = mysql_num_rows($name);

$colname_pers = "-1";
if (isset($_GET['carnet'])) {
  $colname_pers = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_pers = sprintf("SELECT * FROM info_pers WHERE carnet = %s", GetSQLValueString($colname_pers, "text"));
$pers = mysql_query($query_pers, $arkg) or die(mysql_error());
$row_pers = mysql_fetch_assoc($pers);
$totalRows_pers = mysql_num_rows($pers);

$colname_servi = "-1";
if (isset($_GET['carnet'])) {
  $colname_servi = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_servi = sprintf("SELECT * FROM info_serv WHERE carnet = %s", GetSQLValueString($colname_servi, "text"));
$servi = mysql_query($query_servi, $arkg) or die(mysql_error());
$row_servi = mysql_fetch_assoc($servi);
$totalRows_servi = mysql_num_rows($servi);

$colname_RightsCheck = "-1";
if (isset($_GET['carnet'])) {
  $colname_RightsCheck = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_RightsCheck = sprintf("SELECT * FROM security WHERE carnet = %s", GetSQLValueString($colname_RightsCheck, "text"));
$RightsCheck = mysql_query($query_RightsCheck, $arkg) or die(mysql_error());
$row_RightsCheck = mysql_fetch_assoc($RightsCheck);
$totalRows_RightsCheck = mysql_num_rows($RightsCheck);
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
 --><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Arca de Noe :: Ghoper</title>
<script src="swfobject.js" language="javascript"></script>
<script src="SpryAssets/SpryValidationPassword.js" type="text/javascript"></script>
<link href="css/estilo.css" rel="stylesheet" type="text/css" />
<link href="SpryAssets/SpryValidationPassword.css" rel="stylesheet" type="text/css" />
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
<div id="frame"><center>
<table width="424" border="0" cellspacing="3" cellpadding="3">
  <tr>
    <th colspan="2" align="left">Resumen del Usuario</th>
    </tr>
  <tr>
    <td width="158" align="right">Nombres:</td>
    <td width="245"><?php echo $row_name['nombres']; ?></td>
  </tr>
  <tr>
    <td align="right">Apellidos:</td>
    <td><?php echo $row_name['apellidos']; ?></td>
  </tr>
  <tr>
    <td align="right">Fecha de Nacimiento:</td>
    <td><?php echo $row_name['birthday']; ?></td>
  </tr>
  <tr>
    <td align="right">Correo Electronico:</td>
    <td><?php echo $row_pers['email']; ?></td>
  </tr>
  <tr>
    <td height="103" align="right" valign="top">Servicio:</td>
    <td valign="top"><?php do { ?>
			<div style="padding-bottom:5px; display:block;"><strong><?php echo getServicio($row_servi['servicio']); ?></strong> <br /><?php echo getCargo($row_servi['cargo']); ?> - <?php echo getCulto($row_servi['horario']); ?>
            </div>
        <?php } while ($row_servi = mysql_fetch_assoc($servi)); ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
    <table border="0" align="center" cellpadding="3" cellspacing="3">
          <tr valign="baseline">
            <th colspan="2" align="left" nowrap="nowrap">Definir Derechos de Usuario</th>
          </tr>
          <tr valign="baseline">
            <td width="159" align="right" nowrap="nowrap">Carnet:</td>
            <td width="244"><input type="text" name="carnet" value="<?php echo $_GET['carnet']; ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Contraseña:</td>
            <td><span id="sprypassword1">
            <label>
              <input name="contrasena" type="password" id="contrasena" value="<?php echo $row_RightsCheck['contrasena']; ?>" size="32" />
            </label>
            <span class="passwordRequiredMsg">Debe escribir una contraseña.</span><span class="passwordMinCharsMsg">Debe de tener un minimo de 6 caracteres.</span><span class="passwordMaxCharsMsg">Debe de tener un maximo de 10 caracteres.</span></span></td>
          </tr>
          <tr valign="baseline">
            <td align="right" valign="top" nowrap="nowrap">Nivel de Acceso:</td>
            <td valign="baseline"><table>
              <tr>
                <td><input <?php if (!(strcmp($row_RightsCheck['acceso'],"Alfa"))) {echo "checked=\"checked\"";} ?> type="radio" name="acceso" value="Alfa" />
                  Alfa</td>
              </tr>
              <tr>
                <td><input <?php if (!(strcmp($row_RightsCheck['acceso'],"Charlie"))) {echo "checked=\"checked\"";} ?> type="radio" name="acceso" value="Charlie" />
                  Charlie</td>
              </tr>
              <tr>
                <td><input <?php if (!(strcmp($row_RightsCheck['acceso'],"Delta"))) {echo "checked=\"checked\"";} ?> name="acceso" type="radio" value="Delta" />
                  Delta</td>
              </tr>
              <tr>
                <td><input <?php if (!(strcmp($row_RightsCheck['acceso'],"Sierra"))) {echo "checked=\"checked\"";} ?> type="radio" name="acceso" value="Sierra" />
                  Sierra</td>
              </tr>
            </table></td>
          </tr>
          <tr valign="baseline">
            <td colspan="2" align="right" nowrap="nowrap"><input type="submit" class="boton" onclick="MM_goToURL('parent','search.php');return document.MM_returnValue" value="Cancelar" />              <input type="submit" class="boton" value="Actualizar Derechos" /></td>
          </tr>
        </table>
        <input type="hidden" name="id_usr" value="<?php echo $row_RightsCheck['id_usr']; ?>" />
        <input type="hidden" name="MM_update" value="form1" />
</form>
      <p>&nbsp;</p></center></div>
</div>
<div class="footer">
Numeros. Recursos Humanos y Estadisticas Ministeriales<br />
MSB Technology Solutions &copy; Todos los Derechos Reservados <?php echo date('Y'); ?>
</div>
<script type="text/javascript">
<!--
var sprypassword1 = new Spry.Widget.ValidationPassword("sprypassword1", {validateOn:["blur"], minChars:6, maxChars:10});
//-->
</script>
</body>
</html>
<?php
mysql_free_result($name);

mysql_free_result($pers);

mysql_free_result($servi);

mysql_free_result($RightsCheck);
?>