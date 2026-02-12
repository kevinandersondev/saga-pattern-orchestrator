# 🎼 Laravel Saga Pattern Orchestrator

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Pattern](https://img.shields.io/badge/Pattern-Saga_Orchestration-purple?style=for-the-badge)

Este repositório é uma implementação de **Engenharia de Software** demonstrando como lidar com **Transações Distribuídas** e consistência de dados em sistemas complexos onde o ACID tradicional do banco de dados não é suficiente (ou em arquiteturas de microserviços).

## 🧠 O Problema e a Solução

Em sistemas distribuídos, uma operação de negócio (ex: Compra) envolve múltiplos serviços (Pagamento, Estoque, Nota Fiscal). Se o passo 3 falhar, como desfazemos o passo 1 que já foi commitado no banco?

Este projeto implementa o **Saga Pattern (Orquestrado)**. Um "Orquestrador" central coordena os passos e, em caso de falha, executa transações de compensação (rollback lógico) na ordem inversa.

### Fluxo da Aplicação (Happy Path vs. Failure Path)

```mermaid
sequenceDiagram
    participant User
    participant Orchestrator
    participant Payment
    participant Inventory
    
    User->>Orchestrator: Iniciar Compra
    Orchestrator->>Payment: 1. Cobrar Cartão
    Payment-->>Orchestrator: Sucesso (R$ 100)
    
    Orchestrator->>Inventory: 2. Baixar Estoque
    alt Sucesso
        Inventory-->>Orchestrator: OK
        Orchestrator-->>User: Pedido Confirmado
    else Falha (Estoque Insuficiente)
        Inventory--xOrchestrator: Erro!
        Note right of Orchestrator: Iniciar Rollback (Saga)
        Orchestrator->>Payment: 3. Estornar (Compensate)
        Payment-->>Orchestrator: Estorno Realizado
        Orchestrator-->>User: Pedido Cancelado (Erro tratado)
    end


🚀 Tecnologias e Conceitos Aplicados
Laravel 11: Framework Backend.

Vue.js 3 + Inertia: Frontend reativo para visualização dos logs em tempo real.

Design Patterns:

Saga Pattern: Gerenciamento de transações longas.

Command Pattern: Cada passo da saga é uma classe encapsulada.

Interface Segregation: Contrato estrito (execute / compensate) para todos os passos.

Docker & Sail: Ambiente de desenvolvimento containerizado.

📂 Estrutura do Core (Onde a mágica acontece)
A lógica complexa não está nos Controllers, mas isolada no domínio da aplicação:

app/Saga/SagaOrchestrator.php: O motor que gerencia a execução e o rollback automático.

app/Saga/Interfaces/SagaStep.php: O contrato que obriga a implementação do método compensate.

app/Saga/Steps/*: Implementações isoladas de cada serviço (Pagamento, Estoque).

🛠️ Instalação e Execução
Pré-requisitos: Docker e WSL2 (se estiver no Windows).

Clone o repositório:

Bash
git clone [https://github.com/seu-usuario/saga-pattern-laravel.git](https://github.com/seu-usuario/saga-pattern-laravel.git)
cd saga-pattern-laravel
Suba os containers (Laravel Sail):

Bash
./vendor/bin/sail up -d
Instale dependências e migre o banco:

Bash
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail artisan migrate
Inicie o Frontend:

Bash
./vendor/bin/sail npm run dev
🧪 Como Testar a Saga (Prova de Conceito)
Acesse http://localhost/visualizar-saga.

A aplicação foi desenhada para simular falhas baseadas na quantidade de itens:

Cenário de Sucesso:

Insira Quantidade: 1.

Resultado: Pagamento OK -> Estoque OK -> Pedido Confirmado.

Cenário de Falha (Rollback Automático):

Insira Quantidade: 10.

O que acontece:

O Pagamento é aprovado.

O Estoque falha (simulação de falta de produto).

O Orquestrador detecta o erro.

O Orquestrador aciona o Estorno do Pagamento automaticamente.

O Pedido é cancelado.

Autor
Desenvolvido por Kevin Anderson.
