import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, classification_report
import joblib

# 1. Carregar o dataset
df = pd.read_csv("heart.csv")

# 2. Verificar colunas principais (exemplo de 14 usados) 
# Conforme a documentação do dataset: age, sex, cp, trestbps, chol, fbs, restecg, thalach, exang, oldpeak, slope, ca, thal, target. :contentReference[oaicite:1]{index=1}

# 3. Selecionar as colunas que iremos usar
cols = ["age", "sex", "cp", "trestbps", "chol", "fbs", "restecg", "thalach", "exang", "oldpeak", "slope", "ca", "thal"]
X = df[cols]

# 4. Processar a variável alvo
# Em muitos casos, “target” ou “num” representa presença (≥1) ou ausência (0). Vamos converter para binário: presença = 1, ausência = 0.
df["target_binary"] = np.where(df["target"] > 0, 1, 0)
y = df["target_binary"]

# 5. Tratar valores faltantes ou categóricos (se houver)
# Verificar se há valores faltantes:
print("Valores faltantes por coluna:")
print(X.isnull().sum())

# Para simplificação, removemos linhas com valores faltantes:
X = X.dropna()
y = y.loc[X.index]

# Categóricas: “thal” e “cp” e “slope” e “restecg” provavelmente são categóricas — podemos transformá-las via dummy/one-hot encoding:
X = pd.get_dummies(X, columns=["cp", "slope", "thal", "restecg"], drop_first=True)

# 6. Dividir em treino e teste
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)

# 7. Treinar o modelo
modelo = RandomForestClassifier(n_estimators=100, random_state=42)
modelo.fit(X_train, y_train)

# 8. Avaliar o modelo
y_pred = modelo.predict(X_test)
acuracia = accuracy_score(y_test, y_pred)
print(f"Acurácia no teste: {acuracia*100:.2f}%")
print("Relatório de classificação:")
print(classification_report(y_test, y_pred))

# 9. Salvar o modelo treinado
joblib.dump(modelo, "modelo.pkl")
print("Modelo salvo como arquivo ‘modelo.pkl’")
