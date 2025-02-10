# 🚗 **Gestão de Multas**

## 🎯 **1. Seleção de Unidade**

✅ Permitir ao usuário selecionar a unidade responsável pela gestão da multa.

---

## 🔄 **2. Controle de Status**

O sistema deve gerenciar o status de cada multa, com as seguintes etapas principais:

### 📌 **2.1 Ciência (Prazo: 2 dias)**

🗓 **Informações registradas:**

-   **📅 Data de Ciência:** Data em que a multa foi lançada no sistema.
-   **⚠️ Data da Multa:** Data da infração.
-   **⏳ Data Limite:** Calculada automaticamente como **40 dias após a data da infração**.
-   **👤 Responsável:** Pessoa responsável pela gestão da multa.
-   **🚗 Propriedade:** Identificação do dono e local do veículo (campo de seleção).
-   **📄 Número do Auto de Infração:** Código da infração.
-   **📝 Usuário:** Quem realizou o registro no sistema.

---

### 📌 **2.2 Identificação Interna (Prazo: 7 dias)**

🗓 **Informações registradas:**

-   **👤 Nome do Condutor:** Seleção a partir de um cadastro pré-existente.
-   **📅 Data de Identificação:** Data em que o condutor foi identificado internamente.

---

### 📌 **2.3 Identificação no DETRAN (Prazo: 2 dias)**

🗓 **Informações registradas:**

-   **👤 Nome do Condutor:** Informado automaticamente com base no registro interno.
-   **📅 Data de Identificação no DETRAN:** Data em que a identificação foi realizada no sistema do DETRAN.

---

### 📌 **2.4 Status Final (Prazo: 4 dias)**

🛠 Definir a resolução da multa com base na identificação e no desconto aplicável. As possibilidades incluem:

✔️ **Identificado e Descontado:**  
🔹 Vale será enviado por meio do sistema **Triare** para solicitar o desconto em folha.

❌ **Identificado e Não Descontado:**  
🔹 Justificar por que o desconto não foi aplicado.

❌ **Não Identificado e Descontado:**  
🔹 Justificar a situação e identificar quem será responsabilizado pelo desconto.

❌ **Não Identificado e Não Descontado:**  
🔹 Justificar o motivo pelo qual o desconto não foi aplicado e quem deve ser responsabilizado.

---

✅ **Essa estrutura garante clareza no fluxo de trabalho e facilita o gerenciamento eficiente das multas.** 🚀
