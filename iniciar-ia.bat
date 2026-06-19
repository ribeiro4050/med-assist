@echo off
color 0A
echo ===================================================
echo     Iniciando a Inteligencia Artificial (SAD)
echo               MedAssist - IA
echo ===================================================
echo.
cd modelo_treinamento
uvicorn api:app --reload --port 8000
pause