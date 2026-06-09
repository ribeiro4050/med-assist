# 🏥 MedAssist - Sistema de Apoio à Decisão Clínica (SAD)

![Status](https://img.shields.io/badge/Status-Concluído-success)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)
![Python](https://img.shields.io/badge/Python-3.9+-3776AB?logo=python&logoColor=white)
![FastAPI](https://img.shields.io/badge/FastAPI-009688?logo=fastapi&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)

## 📖 Sobre o Projeto
O **MedAssist** é um Sistema de Gestão Hospitalar e Prontuário Eletrónico integrado a um **Sistema de Auxílio ao Diagnóstico (SAD)**. Desenvolvido como Trabalho de Conclusão de Curso (TCC) em Análise e Desenvolvimento de Sistemas.

O grande diferencial tecnológico deste projeto é a sua **Arquitetura Híbrida**: um núcleo monolítico para a gestão hospitalar (desenvolvido no padrão MVC com Service Pattern) integrado a um **microsserviço de Inteligência Artificial**, que utiliza Machine Learning para prever o risco de doenças cardíacas com base em exames e sinais vitais.

---

## ✨ Funcionalidades Principais

### 👨‍⚕️ Módulo Médico (Prontuário e SAD)
* **Hub de Atendimento:** Dashboard unificado com sinais vitais, cálculo automático de IMC e alertas visuais de risco clínico.
* **Inteligência Artificial (SAD):** Análise preditiva de risco cardíaco (probabilidade em %) com travas de validação no formulário.
* **Prescrição Eletrônica:** Geração de Receitas, Guias de Exames e Laudos com integração CID-10.
* **Assinatura Digital:** Tokens de autenticidade (SHA-256) gerados e validados via QR Code.

### 👩‍⚕️ Módulo de Enfermagem
* **Triagem Inteligente:** Registo de queixa principal e sinais vitais.

### 🛡️ Segurança
* **Controlo de Acesso (RBAC):** Perfis isolados para Médicos, Enfermeiros e Pacientes.
* **Proteção IDOR:** Validação rigorosa no Backend (`Controllers`) para garantir a privacidade dos dados médicos.

---

## 🧠 Domínio Clínico e Arquitetura do Sistema

O MedAssist foi projetado unindo as melhores práticas de Engenharia de Software com rigorosos protocolos do Ministério da Saúde e do CFM (Conselho Federal de Medicina).

### 1. Arquitetura de Microsserviços e API RESTful
Para garantir alta disponibilidade e performance, o sistema utiliza uma arquitetura híbrida focada em **desacoplamento**:
* A gestão hospitalar (banco de dados, controle de usuários, emissão de laudos) fica no núcleo PHP. 
* O processamento matemático complexo da Inteligência Artificial fica isolado em um **microsserviço Python**. 
* **API RESTful:** A comunicação entre o PHP e o Python é feita via requisições assíncronas. O Front-end (JavaScript) envia um pacote de dados clínicos no formato JSON para a API (FastAPI) e recebe a predição instantaneamente.

### 2. SAD (Sistema de Auxílio ao Diagnóstico / CDSS)
O coração inovador do MedAssist é a sua atuação como um SAD (Sistema de Auxílio ao Diagnóstico) para **previsão de risco cardíaco**. Ele não substitui o médico, mas atua como um "copiloto clínico" baseado em dados. O sistema cruza **13 *features*** (sinais vitais, exames laboratoriais como colesterol e glicemia, e resultados de ECG) com um histórico de milhares de casos para retornar um alerta de probabilidade estatística de risco cardíaco, mitigando erros humanos em cenários de alta pressão.

### 3. Protocolos Médicos Oficiais Implementados

#### 🚑 Tela de Triagem: Protocolo de Manchester
A triagem utiliza a lógica do Sistema de Triagem de Manchester para classificar a gravidade e definir a prioridade na fila de espera do médico. As cores refletem o nível de urgência:
* 🔴 **Vermelho:** Emergência (Atendimento imediato / Risco de morte).
* 🟠 **Laranja:** Muito Urgente (Atendimento quase imediato).
* 🟡 **Amarelo:** Urgente (Avaliação rápida, paciente estável).
* 🟢 **Verde:** Pouco Urgente (Casos menos graves).
* 🔵 **Azul:** Não Urgente (Casos simples e ambulatoriais).

#### 💊 Tela de Prescrição: Portaria 344/98 (Controle Especial)
O módulo de prescrição categoriza a emissão de laudos e receitas com base na periculosidade e controle do medicamento, gerando um **Hash Criptográfico SHA-256** único (QR Code) para evitar falsificações de medicamentos controlados em farmácias:
* 📄 **Receita Branca (Simples):** Analgésicos e anti-inflamatórios comuns.
* 📄 **Receita Branca de Controle Especial:** Antibióticos e imunossupressores (evita a automedicação e superbactérias).
* 🟦 **Receita Azul (Notificação B):** Medicamentos psicotrópicos que agem no sistema nervoso central (ex: tranquilizantes).
* 🟨 **Receita Amarela (Notificação A):** Nível máximo de controle. Entorpecentes e psicotrópicos de uso rigoroso.

---
### 4. Inteligência Artificial e Modelo Preditivo
O módulo de IA atua com um modelo treinado de **Machine Learning** focado na predição de doenças cardiovasculares.

**Tipo de Algoritmo:** Trata-se de um modelo de **Classificação Binária Supervisionada**, utilizando o algoritmo **Random Forest Classifier** (desenvolvido com a biblioteca Scikit-Learn em Python). O modelo cria um *ensemble* (conjunto) de 100 árvores de decisão simultâneas que "votam" para chegar a um consenso altamente preciso, evitando o *overfitting*. Ele foi treinado utilizando o renomado **Heart Disease Dataset (Cleveland Database)**, originário do *UCI Machine Learning Repository*. 
Trata-se de uma base de dados clínica real coletada pelo Dr. Robert Detrano (Cleveland Clinic Foundation), amplamente validada no meio acadêmico global para a criação de sistemas de predição cardiológica. O modelo processa o subconjunto clássico de 14 atributos (13 variáveis independentes e 1 alvo) para estabelecer os padrões de risco.
 
* **Saída (Output):** A API retorna um JSON contendo a predição binária (`1` para presença de risco cardíaco, `0` para ausência) e a **probabilidade exata** (ex: `87.50%`), permitindo ao médico avaliar o nível de confiança da predição.
* **Dados Consumidos (13 Features):** Para realizar o cálculo em tempo real, o modelo recebe através do *Front-end* um array com 13 variáveis independentes estruturadas, abrangendo demografia, sinais vitais e exames laboratoriais/imagem:
  1. `age`: Idade do paciente.
  2. `sex`: Sexo biológico (1 = Masc; 0 = Fem).
  3. `chest_pain_type`: Tipo de dor no peito (Angina típica, atípica, dor não anginosa ou assintomático).
  4. `resting_blood_pressure`: Pressão arterial em repouso (mmHg).
  5. `cholesterol`: Colesterol sérico (mg/dl).
  6. `fasting_blood_sugar`: Glicemia em jejum (1 = > 120 mg/dl; 0 = < 120 mg/dl).
  7. `resting_electrocardiogram`: Resultados do Eletrocardiograma (ECG) em repouso.
  8. `max_heart_rate_achieved`: Frequência cardíaca máxima alcançada.
  9. `exercise_induced_angina`: Angina induzida por exercício (1 = Sim; 0 = Não).
  10. `st_depression`: Depressão do segmento ST (*Oldpeak*) induzida por exercício em relação ao repouso.
  11. `st_slope`: Inclinação do segmento ST no pico do exercício (Ascendente, Plano, Descendente).
  12. `num_major_vessels`: Número de vasos principais (0-3) coloridos por fluoroscopia.
  13. `thalassemia`: Diagnóstico de Talassemia (Normal, Defeito Fixo ou Defeito Reversível).
