<?php
$dados = [
  $_POST['age'],
  $_POST['sex'],
  $_POST['cp'],
  $_POST['trestbps'],
  $_POST['chol'],
  $_POST['fbs'],
  $_POST['restecg'],
  $_POST['thalach'],
  $_POST['exang'],
  $_POST['oldpeak'],
  $_POST['slope'],
  $_POST['ca'],
  $_POST['thal']
];

$comando = "python modelo.py " . implode(" ", $dados);
$resultado = shell_exec($comando);

if (trim($resultado) == "1") {
  echo "<h3>O paciente tem risco de doença cardiovascular.</h3>";
} else {
  echo "<h3>O paciente NÃO tem risco de doença cardiovascular.</h3>";
}
?>
