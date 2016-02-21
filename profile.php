<?php require_once('Connections/arkg.php'); ?>
<?php require('leerCatalogos.php'); ?>
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

$colname_name = "-1";
if (isset($_SESSION['carnet'])) {
  $colname_name = $_SESSION['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_name = sprintf("SELECT * FROM info_basic WHERE carnet = %s", GetSQLValueString($colname_name, "text"));
$name = mysql_query($query_name, $arkg) or die(mysql_error());
$row_name = mysql_fetch_assoc($name);
$totalRows_name = mysql_num_rows($name);

$colname_basic = "-1";
if (isset($_GET['carnet'])) {
  $colname_basic = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_basic = sprintf("SELECT * FROM info_basic WHERE carnet = %s", GetSQLValueString($colname_basic, "text"));
$basic = mysql_query($query_basic, $arkg) or die(mysql_error());
$row_basic = mysql_fetch_assoc($basic);
$totalRows_basic = mysql_num_rows($basic);

$colname_personal = "-1";
if (isset($_GET['carnet'])) {
  $colname_personal = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_personal = sprintf("SELECT * FROM info_pers WHERE carnet = %s", GetSQLValueString($colname_personal, "text"));
$personal = mysql_query($query_personal, $arkg) or die(mysql_error());
$row_personal = mysql_fetch_assoc($personal);
$totalRows_personal = mysql_num_rows($personal);

$colname_servicio = "-1";
if (isset($_GET['carnet'])) {
  $colname_servicio = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_servicio = sprintf("SELECT * FROM info_serv WHERE carnet = %s", GetSQLValueString($colname_servicio, "text"));
$servicio = mysql_query($query_servicio, $arkg) or die(mysql_error());
$row_servicio = mysql_fetch_assoc($servicio);
$totalRows_servicio = mysql_num_rows($servicio);

$colname_foto = "-1";
if (isset($_GET['carnet'])) {
  $colname_foto = $_GET['carnet'];
}
mysql_select_db($database_arkg, $arkg);
$query_foto = sprintf("SELECT * FROM info_pic WHERE carnet = %s", GetSQLValueString($colname_foto, "text"));
$foto = mysql_query($query_foto, $arkg) or die(mysql_error());
$row_foto = mysql_fetch_assoc($foto);
$totalRows_foto = mysql_num_rows($foto);
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
      <table width="800" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="320" align="left" valign="top"><table width="323" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><img src="fotos/<?php echo $row_foto['file']; ?>" alt="<?php echo $row_foto['carnet']; ?>" name="ProfilePicture" width="320" height="240" border="1" id="ProfilePicture" style="background-color: #333333" /></td>
              </tr>
              <tr>
                <td><a href="#">[Cambiar Foto]</a></td>
              </tr>
          </table></td>
          <td width="459" valign="top"><table width="463" border="0" cellspacing="3" cellpadding="3">
            <tr>
              <th colspan="2" align="left"><table width="463" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="231">Información Básica</td>
                    <td width="232" align="right">&nbsp;</td>
                  </tr>
                </table></th>
            </tr>
            <tr>
              <td width="158" align="right">Nombres:</td>
              <td width="276"><?php echo $row_basic['nombres']; ?></td>
            </tr>
            <tr>
              <td align="right">Apellidos:</td>
              <td><?php echo $row_basic['apellidos']; ?></td>
            </tr>
            <tr>
              <td align="right">Fecha de Nacimiento:</td>
              <td><?php echo $row_basic['birthday']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">Codigo de Servidor:</td>
              <td valign="top"><?php echo $row_basic['carnet']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">&nbsp;</td>
              <td valign="top">&nbsp;</td>
            </tr>
            <tr>
              <th height="24" colspan="2" align="left" valign="top"><table width="463" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="231">Información Personal</td>
                  <td width="232" align="right"><a href="index2_edit.php?carnet=<?php echo $row_basic['carnet']; ?>">[Editar]</a></td>
                </tr>
              </table></th>
              </tr>
            <tr>
              <td height="24" align="right" valign="top">Correo Electronico:</td>
              <td valign="top"><?php echo $row_personal['email']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">Teléfono:</td>
              <td valign="top"><?php echo $row_personal['telefono']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">Celular:</td>
              <td valign="top"><?php echo $row_personal['celular']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">Dirección:</td>
              <td valign="top"><?php echo $row_personal['direccion']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">&nbsp;</td>
              <td valign="top">&nbsp;</td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">Ocupación:</td>
              <td valign="top"><?php echo $row_personal['work']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">Lugar de desempeño:</td>
              <td valign="top"><?php echo $row_personal['workplace']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">&nbsp;</td>
              <td valign="top">&nbsp;</td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">Profesión de Fe:</td>
              <td valign="top"><?php echo $row_personal['conversion']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">Bautismo:</td>
              <td valign="top"><?php echo $row_personal['bautismo']; ?></td>
            </tr>
            <tr>
              <td height="24" align="right" valign="top">&nbsp;</td>
              <td valign="top">&nbsp;</td>
            </tr>
            <tr>
              <th height="24" colspan="2" align="left" valign="top"><table width="463" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="231">Información Ministerial</td>
                  <td width="232" align="right"><a href="index3_edit.php?show=add&amp;carnet=<?php echo $row_basic['carnet']; ?>">[Editar]</a></td>
                </tr>
              </table></th>
              </tr>
            <tr>
              <td height="103" colspan="2" align="left" valign="top"><?php do { ?>
                <div style="padding-bottom:5px; display:block;"><strong><?php echo getServicio($row_servicio['servicio']); ?></strong> <br />
                  <?php echo getCargo($row_servicio['cargo']); ?> - <?php echo getCulto($row_servicio['horario']); ?> </div>
                <?php } while ($row_servicio = mysql_fetch_assoc($servicio)); ?></td>
            </tr>
            <tr>
              <td colspan="2" align="right"><input type="submit" class="boton" onclick="MM_goToURL('parent','search.php?show=profile');return document.MM_returnValue" value="Regresar" /></td>
              </tr>
          </table></td>
        </tr>
      </table>
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

mysql_free_result($basic);

mysql_free_result($personal);

mysql_free_result($servicio);

mysql_free_result($foto);
?>