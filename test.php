<?php require('lib/getCarnet.php'); ?>
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
<title>Documento sin título</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="test.php">
  <p>Nombres
    <label>
      <input type="text" name="nombres" id="nombres" />
    </label>
  </p>
  <p>
    Apellidos
    <label>
      <input type="text" name="apellidos" id="apellidos" />
    </label>
  </p>
  <p>
    Fecha de Nacimiento
    <label>
      <input type="text" name="fechanac" id="fechanac" />
    </label>
  </p>
  <p>
    <label>
      <input type="submit" name="button" id="button" value="Enviar" />
    </label>
  </p>
</form>
<?php

echo getCarnet($_POST['nombres'],$_POST['apellidos'],$_POST['fechanac']);

?>
</body>
</html>