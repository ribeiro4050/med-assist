<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Predição de Doença Cardiovascular</title>
</head>
<body>
  <h2>Verificar Risco Cardiovascular</h2>
  <form action="processa.php" method="post">
    Idade: <input type="number" name="age" required><br>
    Sexo (1=Masculino, 0=Feminino): <input type="number" name="sex" required><br>
    Pressão em Repouso (trestbps): <input type="number" name="trestbps" required><br>
    Colesterol (chol): <input type="number" name="chol" required><br>
    Frequência Máxima (thalach): <input type="number" name="thalach" required><br>
    Dor no Peito (cp: 0-3): <input type="number" name="cp" required><br>
    Açúcar no Sangue >120mg/dl (fbs: 1 ou 0): <input type="number" name="fbs" required><br>
    ECG em Repouso (restecg: 0-2): <input type="number" name="restecg" required><br>
    Exercício Induz Angina? (exang: 1 ou 0): <input type="number" name="exang" required><br>
    Depressão ST (oldpeak): <input type="number" step="0.1" name="oldpeak" required><br>
    Inclinação ST (slope: 0-2): <input type="number" name="slope" required><br>
    N° de Vasos Coloridos (ca: 0-4): <input type="number" name="ca" required><br>
    Talassemia (thal: 0-3): <input type="number" name="thal" required><br>
    <button type="submit">Verificar</button>
  </form>
</body>
</html>
