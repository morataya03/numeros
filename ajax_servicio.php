<?php require_once('Connections/arkg.php'); ?>
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

$colname_servicio = "-1";
if (isset($_POST['min_id'])) {
  $colname_servicio = $_POST['min_id'];
}
mysql_select_db($database_arkg, $arkg);
$query_servicio = sprintf("SELECT * FROM cat_serv WHERE min_id = %s ORDER BY id_ser ASC", GetSQLValueString($colname_servicio, "int"));
$servicio = mysql_query($query_servicio, $arkg) or die(mysql_error());
$row_servicio = mysql_fetch_assoc($servicio);
$totalRows_servicio = mysql_num_rows($servicio);
?>

<?php do {
    
      echo '<option value="'.$row_servicio['id_ser'].'">'.$row_servicio['servicio'].'</option>';
       
} while ($row_servicio = mysql_fetch_assoc($servicio)); ?>

<?php
mysql_free_result($servicio);
?>
