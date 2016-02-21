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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE info_pers SET carnet=%s, direccion=%s, telefono=%s, celular=%s, email=%s, `work`=%s, workplace=%s, conversion=%s, bautismo=%s, `mod`=%s, usr_mod=%s WHERE id_pers=%s",
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
                       GetSQLValueString($_POST['usr_mod'], "text"),
                       GetSQLValueString($_POST['id_pers'], "int"));

  mysql_select_db($database_arkg, $arkg);
  $Result1 = mysql_query($updateSQL, $arkg) or die(mysql_error());

  $updateGoTo = "profile.php?carnet=" . $row_personal['carnet'] . "";
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

$colname_personal = "-1";
if (isset($_GET['carnet'])) {
  $colname_personal = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_personal = sprintf("SELECT * FROM info_pers WHERE carnet = %s", GetSQLValueString($colname_personal, "text"));
$personal = mysql_query($query_personal, $arkg) or die(mysql_error());
$row_personal = mysql_fetch_assoc($personal);
$totalRows_personal = mysql_num_rows($personal);
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
  <form action="<?php echo $editFormAction; ?>" method="POST" name="form2" id="form2">
    <p><strong>Nombre:</strong> [<?php echo $row_name['nombres'] . " " . $row_name['apellidos']; ?>] <strong>Carnet:</strong> [<?php echo $_GET['carnet']; ?>]</p>
      <table align="center">
        <tr valign="baseline">
          <th colspan="2" align="left" valign="top" nowrap="nowrap">Información Personal</th>
          </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right" valign="top">Direccion:</td>
          <td><textarea name="direccion" cols="50" rows="5"><?php echo $row_personal['direccion']; ?></textarea></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Telefono:</td>
          <td><input type="text" name="telefono" value="<?php echo $row_personal['telefono']; ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Celular:</td>
          <td><input type="text" name="celular" value="<?php echo $row_personal['celular']; ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Email:</td>
          <td><input type="text" name="email" value="<?php echo $row_personal['email']; ?>" size="32" /></td>
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
          <td><input type="text" name="work" value="<?php echo $row_personal['work']; ?>" size="32" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Lugar:</td>
          <td><input type="text" name="workplace" value="<?php echo $row_personal['workplace']; ?>" size="32" /></td>
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
          <td><input name="conversion" type="text" value="<?php echo $row_personal['conversion']; ?>" size="32" maxlength="4" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">Año de Bautismo:</td>
          <td><input name="bautismo" type="text" value="<?php echo $row_personal['bautismo']; ?>" size="32" maxlength="4" /></td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr valign="baseline">
          <td nowrap="nowrap" align="right">&nbsp;</td>
          <td><input name="Reset" type="reset" class="boton" onclick="MM_goToURL('parent','profile.php?carnet=<?php echo $row_name['carnet']; ?>');return document.MM_returnValue" value="Regresar" />
            <label>
              <input name="button" type="submit" class="boton" id="button" value="Modificar" />
            </label></td>
        </tr>
      </table>
      <input type="hidden" name="id_pers" value="<?php echo $row_personal['id_pers']; ?>" />
    <input type="hidden" name="carnet" value="<?php echo $row_personal['carnet']; ?>" />
    <input name="usr_mod" type="hidden" id="usr_mod" value="<?php echo $_SESSION['MM_Username']; ?>" />
  <input type="hidden" name="mod" id="mod" value="<?php echo date('Y-m-d H:i:s'); ?>" />
  <input type="hidden" name="MM_update" value="form2" />
  </form>
    <p>&nbsp;</p>
    <br />
  
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

mysql_free_result($personal);
?>
