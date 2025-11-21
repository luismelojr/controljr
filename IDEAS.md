# Análise do Sistema e Propostas de Funcionalidades

## Visão Geral do Sistema Atual
**ControlJr** é uma aplicação de gestão financeira pessoal construída com Laravel 12, React 19 e Inertia.js. Segue uma arquitetura de Domain-Driven Design (DDD).

**Funcionalidades Existentes:**
- **Autenticação:** Email/Senha e Google OAuth.
- **Dashboard:** Gráficos de fluxo de caixa, resumos de despesas/receitas, transações futuras.
- **Entidades Principais:** Carteiras, Contas (Recorrentes), Categorias, Transações, Receitas.
- **Relatórios:** Construtor com várias visualizações (Pizza, Barra, Linha) e exportações (PDF/Excel).
- **Reconciliação:** Importação de arquivo OFX e correspondência de transações.
- **Alertas:** Sistema de notificações.

---

## 1. Funcionalidades Principais Ausentes
Estas são funcionalidades padrão para aplicações de finanças pessoais que estão atualmente ausentes ou incompletas.

### 1.1 Sistema de Orçamentos (Alta Prioridade)
**Status Atual:** Não implementado.
**Proposta:** Implementar um sistema de orçamentos para permitir que os usuários definam limites de gastos por categoria ou globalmente.
- **Backend:** Criar tabela `budgets` (user_id, category_id, amount, period_start, period_end).
- **Frontend:** Barras de progresso visuais mostrando gasto vs. limite.
- **Alertas:** Notificar o usuário ao se aproximar ou exceder o orçamento.

### 1.2 Metas de Economia (Alta Prioridade)
**Status Atual:** Componente `savings.tsx` existe mas sem integração com backend.
**Proposta:** Criar um rastreador de metas dedicado.
- **Backend:** Criar tabela `goals` (name, target_amount, current_amount, deadline, color).
- **Frontend:** Conectar `savings.tsx` a dados reais. Permitir contribuições manuais ou alocação automática de receitas.

### 1.3 Sistema de Tags
**Status Atual:** Não implementado.
**Proposta:** Adicionar um sistema de tags para categorização flexível (ex: #ferias, #trabalho).
- **Backend:** Criar tabelas `tags` e `taggables` (polimórfica).
- **Frontend:** Seleção múltipla de tags nos formulários de transação.

### 1.4 Perfil de Usuário e Configurações
**Status Atual:** Sem interface para alterar detalhes do usuário (nome, avatar, senha) dentro do app.
**Proposta:** Criar uma página de configurações.
- **Funcionalidades:** Atualização de perfil, alteração de senha, preferências de notificação, exportação de dados (LGPD).


### 1.5 Relatórios Avançados (Business Intelligence)
**Status Atual:** Básico (Pizza/Barras simples).
**Proposta:** Implementar relatórios analíticos para tomada de decisão.
- **Fluxo de Caixa Projetado:** Projeção de saldo para os próximos 6-12 meses considerando recorrentes.
- **Evolução Patrimonial:** Gráfico de crescimento do patrimônio líquido (Ativos - Passivos).
- **Análise de Pareto (80/20):** Identificar os 20% de gastos que consomem 80% da renda.
- **Comparativo de Períodos (YoY/MoM):** Comparar desempenho financeiro entre meses ou anos equivalentes.
- **Taxa de Poupança (Savings Rate):** Indicador de saúde financeira (% da renda poupada).
- **Mapa de Calor (Heatmap):** Visualização de dias com maior concentração de gastos.

---

## 2. Melhorias Recomendadas
Funcionalidades que elevariam a experiência do usuário e o fator "uau".

### 2.1 Suporte a Múltiplas Moedas
**Status Atual:** Assume moeda única (BRL).
**Proposta:** Suporte a múltiplas moedas para carteiras e transações.
- **Implementação:** Adicionar `currency_code` às carteiras. Usar uma API de taxa de câmbio para relatórios consolidados.

### 2.2 Categorização com IA
**Status Atual:** Categorização manual.
**Proposta:** Usar um LLM (Gemini/OpenAI) para sugerir categorias com base na descrição da transação durante a importação ou criação.
- **Fluxo:** Usuário digita "Uber" -> Sistema sugere "Transporte".

### 2.3 Calendário de Transações Recorrentes
**Status Atual:** Apenas visualizações em lista.
**Proposta:** Uma visualização de calendário mostrando contas e receitas futuras.
- **UI:** FullCalendar ou grid personalizado mostrando totais diários e datas de vencimento.

### 2.4 Reconciliação Avançada
**Status Atual:** Importação básica de OFX.
**Proposta:** Integração bancária via Open Finance (ex: Pluggy/Belvo) ou regras de correspondência mais inteligentes (regras regex para auto-categorização).

### 2.5 PWA Mobile
**Status Atual:** Design web responsivo.
**Proposta:** Melhorar o manifesto e service workers para torná-lo um PWA totalmente instalável com suporte offline para visualização de dados em cache.

---

## 3. Melhorias Técnicas
- **Refatoração:** `DashboardService` está crescendo muito. Considerar quebrá-lo em serviços de modelo de leitura menores (ex: `CashflowService`, `NotificationService`).
- **Testes:** Garantir que caminhos críticos (Reconciliação, Relatórios) tenham alta cobertura de testes.
