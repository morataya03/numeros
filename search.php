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

$show = $_REQUEST['show'];

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

$colname_name = "-1";
if (isset($_SESSION['carnet'])) {
  $colname_name = $_SESSION['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_name = sprintf("SELECT * FROM info_basic WHERE carnet = %s", GetSQLValueString($colname_name, "text"));
$name = mysql_query($query_name, $arkg) or die(mysql_error());
$row_name = mysql_fetch_assoc($name);
$totalRows_name = mysql_num_rows($name);

$colname_s3ach = "-1";
if (isset($_POST['keyword'])) {
  $colname_s3ach = $_POST['keyword'];
}
mysql_select_db($database_arkg, $arkg);
$query_s3ach = sprintf("SELECT * FROM info_basic WHERE nombres LIKE %s OR apellidos LIKE %s", 
							GetSQLValueString("%" . $colname_s3ach . "%", "text"),
							GetSQLValueString("%" . $colname_s3ach . "%", "text"));
$s3ach = mysql_query($query_s3ach, $arkg) or die(mysql_error());
$row_s3ach = mysql_fetch_assoc($s3ach);
$totalRows_s3ach = mysql_num_rows($s3ach);
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
<div id="frame"><center>
      <form id="form1" name="form1" method="post" action="">
        <label>
          <input name="keyword" type="text" class="search_frm" id="keyword" />
        </label>
        <label>
          <input name="button" type="submit" class="search_btn" id="button" value="Buscar" />
        </label>
        <label>
        	<input name="back_btn" type="button" class="search_btn" id="back_btn" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Regresar" />
        </label>
      </form>
      <p>&nbsp;</p>
      <?php if ($totalRows_s3ach > 0) { ?>
      <?php do { ?>
          <div class="result">
      <div class="result_code"><?php echo $row_s3ach['carnet']; ?></div>
      <div class="result_name"><?php echo $row_s3ach['nombres']; ?> <?php echo $row_s3ach['apellidos']; ?></div>
      <div class="result_opt">
      <?php if($show == "profile"){ ?>
      <input name="view_btn" type="button" class="result_btn" id="view_btn" onclick="MM_goToURL('parent','profile.php?carnet=<?php echo $row_s3ach['carnet']; ?>');return document.MM_returnValue" value="Perfil" />
      <?php }; ?>
	  <?php if($show == "rights"){ ?>
      <input name="apply_btn" type="button" class="result_btn" id="apply_btn" onclick="MM_goToURL('parent','getRights.php?carnet=<?php echo $row_s3ach['carnet']; ?>');return document.MM_returnValue" value="Derechos" />
      <?php }; ?>
      </div>
      </div>
          <?php } while ($row_s3ach = mysql_fetch_assoc($s3ach)); ?>
      <?php }; ?>
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

mysql_free_result($s3ach);
?>