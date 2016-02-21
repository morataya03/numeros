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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO cat_minis (id_min, ministerio) VALUES (%s, %s)",
                       GetSQLValueString($_POST['id_min'], "int"),
                       GetSQLValueString($_POST['ministerio'], "text"));

  mysql_select_db($database_arkg, $arkg);
  $Result1 = mysql_query($insertSQL, $arkg) or die(mysql_error());

  $insertGoTo = "catalogo_areas.php?show=add";
  #if (isset($_SERVER['QUERY_STRING'])) {
  #  $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
  #  $insertGoTo .= $_SERVER['QUERY_STRING'];
  #}
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
  $updateSQL = sprintf("UPDATE cat_minis SET ministerio=%s WHERE id_min=%s",
                       GetSQLValueString($_POST['ministerio'], "text"),
                       GetSQLValueString($_POST['id_min'], "int"));

  mysql_select_db($database_arkg, $arkg);
  $Result1 = mysql_query($updateSQL, $arkg) or die(mysql_error());

  $updateGoTo = "catalogo_areas.php?show=add";
  #if (isset($_SERVER['QUERY_STRING'])) {
  #  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  #  $updateGoTo .= $_SERVER['QUERY_STRING'];
  #}
  header(sprintf("Location: %s", $updateGoTo));
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

mysql_select_db($database_arkg, $arkg);
$query_area = "SELECT * FROM cat_minis ORDER BY id_min ASC";
$area = mysql_query($query_area, $arkg) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

$colname_area_edit = "-1";
if (isset($_GET['id_min'])) {
  $colname_area_edit = $_GET['id_min'];
}
mysql_select_db($database_arkg, $arkg);
$query_area_edit = sprintf("SELECT * FROM cat_minis WHERE id_min = %s", GetSQLValueString($colname_area_edit, "int"));
$area_edit = mysql_query($query_area_edit, $arkg) or die(mysql_error());
$row_area_edit = mysql_fetch_assoc($area_edit);
$totalRows_area_edit = mysql_num_rows($area_edit);
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
<link href="css/estilo.css" rel="stylesheet" type="text/css" />
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
<center>
<div class="box_left">
	<?php if($show == "add") { ?>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table align="center">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Ministerio:</td>
            <td><input type="text" name="ministerio" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input name="btnFin4" type="button" class="search_btn" id="btnFin4" onclick="MM_goToURL('parent','catalogos.php');return document.MM_returnValue" value="Regresar" />              <input type="submit" class="search_btn" value="Agregar" /></td>
          </tr>
        </table>
        <input type="hidden" name="id_min" value="" />
        <input type="hidden" name="MM_insert" value="form1" />
      </form>
      <?php }; ?>
      <?php if($show == "edit") { ?>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
        <table align="center">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">ID:</td>
            <td><label>
              <input name="textfield" type="text" id="textfield" value="<?php echo $row_area_edit['id_min']; ?>" size="32" readonly="readonly"/>
            </label>
              </td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Ministerio:</td>
            <td><input type="text" name="ministerio" value="<?php echo htmlentities($row_area_edit['ministerio'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input name="btnFin" type="button" class="search_btn" id="btnFin" onclick="MM_goToURL('parent','catalogos.php');return document.MM_returnValue" value="Regresar" />              <input type="submit" class="search_btn" value="Actualizar" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_update" value="form2" />
        <input type="hidden" name="id_min" value="<?php echo $row_area_edit['id_min']; ?>" />
      </form>
      <?php }; ?>
</div>
<div class="box_right">
      <table width="387" border="0" cellpadding="3" cellspacing="3">
        <tr>
          <th width="55">ID</th>
          <th width="243" align="left">Area</th>
          <th width="21" align="left">&nbsp;</th>
        </tr>
        <?php do { ?>
          <tr>
            <td align="center"><?php echo $row_area['id_min']; ?></td>
            <td align="left"><?php echo $row_area['ministerio']; ?></td>
            <td align="left"><a href="catalogo_areas.php?show=edit&amp;id_min=<?php echo $row_area['id_min']; ?>"><img src="img/edit.png" alt="Editar" width="16" height="16" border="0" /></a></td>
          </tr>
          <?php } while ($row_area = mysql_fetch_assoc($area)); ?>
      </table>
</div>
<p>&nbsp;</p>
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

mysql_free_result($area);

mysql_free_result($area_edit);
?>