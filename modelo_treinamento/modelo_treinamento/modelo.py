import sys
import joblib
import numpy as np
import pandas as pd

# Carrega o modelo
modelo = joblib.load("modelo.pkl")

# Lê os argumentos vindos do PHP
args = sys.argv[1:]
args = [float(a) for a in args]

# Define as colunas na mesma ordem do treinamento
cols = ["age", "sex", "cp", "trestbps", "chol", "fbs", "restecg",
        "thalach", "exang", "oldpeak", "slope", "ca", "thal"]

# Cria o DataFrame com uma linha
entrada = pd.DataFrame([args], columns=cols)

# Recria as colunas dummies (igual ao treino)
entrada = pd.get_dummies(entrada, columns=["cp", "slope", "thal", "restecg"], drop_first=True)

# Adiciona colunas faltantes (para garantir mesmo formato do treino)
# Isso é importante caso o modelo espere colunas que não existam aqui
colunas_modelo = modelo.feature_names_in_
for c in colunas_modelo:
    if c not in entrada.columns:
        entrada[c] = 0
entrada = entrada[colunas_modelo]

# Faz a previsão
pred = modelo.predict(entrada)

# Retorna o resultado (0 ou 1)
print(int(pred[0]))
