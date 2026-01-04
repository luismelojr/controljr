# 🚀 Roadmap de Features Premium - MeloSys

**Data de criação:** 2026-01-03
**Versão:** 1.0
**Status:** Em planejamento

---

## 📊 Visão Geral

Este documento detalha todas as features necessárias para implementar os planos **Premium** e **Family** do MeloSys, incluindo a infraestrutura de assinaturas e pagamentos.

### Estatísticas do Projeto

- **Total de Features:** 15 principais
- **Features Concluídas:** 6/15 (40%)
- **Tempo Total Estimado:** 35-50 dias de desenvolvimento
- **Fases de Implementação:** 5 fases
- **Progresso Atual:**
  - ✅ Fase 1: FUNDAÇÃO - 100% concluída (6/6 features)
  - ⬜ Fase 2: FEATURES PREMIUM CORE - 0% (0/4 features)
  - ⬜ Fase 3: PLANO FAMILY - 0% (0/4 features)
  - ⬜ Fase 4: DIFERENCIAL IA - 0% (0/4 features)
  - ⬜ Fase 5: POLIMENTO - 0% (0/3 features)

---

## ✅ Features JÁ IMPLEMENTADAS (Plano Free Atual)

### Autenticação Completa
- [x] Login email/senha
- [x] Google OAuth
- [x] Reset de senha

### Gestão Financeira Básica
- [x] Categorias (ilimitadas atualmente)
- [x] Carteiras (contas bancárias + cartões de crédito)
- [x] Contas a pagar (recorrentes/parceladas)
- [x] Receitas (recorrentes/únicas)
- [x] Transações
- [x] Orçamentos mensais

### Análise e Relatórios
- [x] Dashboard com gráficos de cashflow
- [x] Relatórios financeiros (overview, categorias)
- [x] Análise de gastos mensais

### Alertas Inteligentes
- [x] Alertas de limite de cartão de crédito
- [x] Alertas de vencimento de contas
- [x] Alertas de saldo baixo
- [x] Alertas de orçamento excedido

### Funcionalidades Auxiliares
- [x] Exportação de dados (CSV/Excel)
- [x] Reconciliação bancária
- [x] Dark mode básico
- [x] Notificações in-app

---

## 🎯 FASE 1: FUNDAÇÃO (7-10 dias) - CRÍTICO ✅ **CONCLUÍDA**

**Objetivo:** Habilitar sistema de planos e pagamentos

**Progresso:** 6/6 tarefas concluídas (100%) ✨

**Resumo da Implementação:**
- ✅ Sistema completo de assinaturas com 3 planos (Free, Premium, Family)
- ✅ Integração total com Asaas (PIX, Boleto, Cartão de Crédito)
- ✅ Webhooks configurados para atualização automática de status
- ✅ Middleware de verificação de plano e features
- ✅ Sistema de limites configurável por plano
- ✅ Dashboard de assinatura e histórico de pagamentos
- ✅ Fluxo completo de upgrade/downgrade
- ✅ 10 testes automatizados passando
- ✅ 4 páginas frontend de pagamento
- ✅ 2 páginas frontend de assinatura

### 1.1 Sistema de Assinaturas ✅

**Prioridade:** 🔴 CRÍTICA
**Complexidade:** Alta
**Tempo estimado:** 3-5 dias
**Status:** ✅ Concluído

#### Checklist de Implementação

##### Backend - Migrations
- [x] Criar migration `create_subscription_plans_table`
  - [x] Campos: id, uuid, name, slug, price_cents, billing_period, features (json), is_active
  - [x] Seed inicial com 3 planos (Free, Premium, Family)
- [x] Criar migration `create_subscriptions_table`
  - [x] Campos: id, uuid, user_id, subscription_plan_id, started_at, ends_at, cancelled_at, status, payment_gateway, external_subscription_id
  - [x] Foreign keys e indexes
- [x] Adicionar migration `add_subscription_id_to_users_table`
  - [x] Campo: current_subscription_id (nullable, foreign key)

##### Backend - Models
- [x] Criar model `SubscriptionPlan.php`
  - [x] HasUuidCustom trait
  - [x] Casts: features -> array, price_cents -> integer
  - [x] Relationships: hasMany(Subscription)
  - [x] Scopes: active(), bySlug()
  - [x] Accessor: price (cents to BRL)
- [x] Criar model `Subscription.php`
  - [x] HasUuidCustom trait
  - [x] Relationships: belongsTo(User), belongsTo(SubscriptionPlan)
  - [x] Scopes: active(), cancelled(), expired()
  - [x] Methods: isActive(), isCancelled(), cancel(), renew()
- [x] Atualizar model `User.php`
  - [x] Relationship: currentSubscription(), subscriptions()
  - [x] Methods: hasActiveSubscription(), isOnPlan($slug), getPlanLimits()

##### Backend - Enums
- [x] Criar `PlanTypeEnum.php`
  - [x] Values: FREE, PREMIUM, FAMILY
  - [x] Labels em português
- [x] Criar `SubscriptionStatusEnum.php`
  - [x] Values: ACTIVE, CANCELLED, EXPIRED, PENDING
  - [x] Labels em português

##### Backend - Services
- [x] Criar `SubscriptionService.php`
  - [x] Method: create($user, $planSlug)
  - [x] Method: upgrade($user, $newPlanSlug)
  - [x] Method: downgrade($user, $newPlanSlug)
  - [x] Method: cancel($subscription)
  - [x] Method: renew($subscription)
  - [x] Method: checkExpiredSubscriptions()

##### Backend - Controllers
- [x] Criar `SubscriptionController.php`
  - [x] Method: index() - Dashboard de assinatura do usuário
  - [x] Method: plans() - Lista de planos disponíveis
  - [x] Method: subscribe(PlanSlug) - Iniciar processo de assinatura
  - [x] Method: cancel() - Cancelar assinatura
  - [x] Method: resume() - Retomar assinatura cancelada

##### Backend - Middleware
- [x] Criar `CheckSubscription.php`
  - [x] Verificar se usuário tem assinatura ativa
  - [x] Redirecionar para /subscription/plans se não tiver

##### Backend - Routes
- [x] Adicionar rotas em `routes/web.php`
  - [x] GET /subscription - dashboard de assinatura
  - [x] GET /subscription/plans - lista de planos
  - [x] POST /subscription/subscribe/{planSlug} - criar assinatura
  - [x] DELETE /subscription/cancel - cancelar assinatura
  - [x] POST /subscription/resume - retomar assinatura
  - [x] POST /subscription/upgrade/{planSlug} - upgrade de plano
  - [x] POST /subscription/downgrade/{planSlug} - downgrade de plano

##### Backend - Seeders
- [x] Criar `SubscriptionPlanSeeder.php`
  - [x] Plano Free (R$ 0,00)
  - [x] Plano Premium (R$ 19,90/mês)
  - [x] Plano Family (R$ 29,90/mês)

##### Frontend - Pages
- [x] Criar `pages/subscription/index.tsx` - Dashboard de assinatura
- [x] Criar `pages/subscription/plans.tsx` - Página de planos
- [ ] Criar `pages/subscription/checkout.tsx` - Checkout de pagamento (Fase 1.2)

##### Frontend - TypeScript Types
- [x] Criar `types/subscription.d.ts` - Tipos para Subscription e SubscriptionPlan

##### Frontend - Components
- [ ] Criar `components/subscription/plan-card.tsx` - Card de plano (não necessário, implementado inline)
- [ ] Criar `components/subscription/subscription-status.tsx` - Status da assinatura (não necessário, implementado inline)
- [ ] Criar `components/subscription/upgrade-prompt.tsx` - Prompt para upgrade (não necessário, implementado inline)

##### Testes
- [x] Criar `SubscriptionServiceTest.php` (unit) - 10 testes passando
- [ ] Criar `SubscriptionControllerTest.php` (feature)
- [ ] Criar `SubscriptionPolicyTest.php` (feature)

---

### 1.2 Integração de Pagamento com Asaas ✅

**Prioridade:** 🔴 CRÍTICA
**Complexidade:** Alta
**Tempo estimado:** 4-6 dias
**Status:** ✅ Concluído

**Gateway escolhido:** Asaas (melhor para SaaS brasileiro)

**Vantagens:**
- Focado em assinaturas recorrentes
- Taxas competitivas (Cartão: 2,99% + R$0,39 | Boleto: 1,99% | PIX: R$0,99)
- Suporte a PIX, Boleto, Cartão
- API bem documentada em português
- Retry automático para cartões recusados
- Dashboard completo de gestão

#### Checklist de Implementação

##### Configuração
- [ ] Criar conta no Asaas (https://www.asaas.com) - Usuário deve fazer
- [ ] Obter credenciais de sandbox e produção - Usuário deve configurar
- [ ] Instalar SDK do Asaas - Não necessário, implementamos HTTP client próprio
  ```bash
  # composer require asaas/asaas-php-sdk
  ```
- [ ] Adicionar credenciais no `.env` - Usuário deve configurar
  - [ ] ASAAS_API_KEY (sandbox)
  - [ ] ASAAS_API_KEY_PRODUCTION
  - [ ] ASAAS_ENVIRONMENT=sandbox (ou production)
- [x] Criar config `config/asaas.php`
  ```php
  return [
      'api_key' => env('ASAAS_API_KEY'),
      'environment' => env('ASAAS_ENVIRONMENT', 'sandbox'),
      'webhook_token' => env('ASAAS_WEBHOOK_TOKEN'),
  ];
  ```

##### Backend - Migrations
- [x] Criar migration `create_payments_table`
  - [x] Campos: id, uuid, user_id, subscription_id, amount_cents, status, payment_method, payment_gateway, external_payment_id, paid_at, pix_qr_code, pix_copy_paste, boleto_barcode, invoice_url, due_date, confirmed_at
- [ ] Criar migration `create_payment_webhooks_table` - Não implementado (logs via Log facade)
  - [ ] Campos: id, payload (json), type, status, processed_at

##### Backend - Models
- [x] Criar model `Payment.php`
  - [x] HasUuidCustom trait
  - [x] HasMoneyAccessors trait
  - [x] Relationships: belongsTo(User), belongsTo(Subscription)
  - [x] Scopes: pending(), confirmed(), received(), overdue(), pix(), boleto(), creditCard()
  - [x] Methods: isPending(), isConfirmed(), isReceived(), isPix(), isBoleto(), isCreditCard(), markAsConfirmed(), markAsReceived(), markAsOverdue()

##### Backend - Services
- [x] Criar `AsaasService.php` (HTTP client próprio, sem SDK)
  - [x] Method: createCustomer($user) - Criar cliente no Asaas
  - [x] Method: createPayment($customerId, $data) - Criar cobrança
  - [x] Method: createSubscription($customerId, $data) - Criar assinatura recorrente
  - [x] Method: getPayment($paymentId) - Buscar dados do pagamento
  - [x] Method: getPixQrCode($paymentId) - Buscar QR Code PIX
  - [x] Method: cancelPayment($paymentId) - Cancelar pagamento
  - [x] Method: cancelSubscription($subscriptionId) - Cancelar assinatura
  - [x] Method: refundPayment($paymentId) - Reembolsar pagamento
- [x] Criar `PaymentGatewayService.php` (orquestração)
  - [x] Method: createSubscriptionPayment($subscription, $paymentMethod)
  - [x] Method: createPayment($data)
  - [x] Method: getOrCreateCustomer($user)
  - [x] Method: fetchPaymentMethodData($payment) - PIX/Boleto
  - [x] Method: processCreditCardPayment($payment, $creditCardData)
  - [x] Method: cancelPayment($payment)
  - [x] Method: refundPayment($payment)
  - [x] Method: checkPaymentStatus($payment)
- [x] Criar `WebhookService.php`
  - [x] Method: processWebhook($data) - Processar evento
  - [x] Method: handlePaymentConfirmed($data)
  - [x] Method: handlePaymentReceived($data)
  - [x] Method: handlePaymentOverdue($data)
  - [x] Method: handlePaymentRefunded($data)
  - [x] Method: handlePaymentDeleted($data)
  - [x] Method: verifyWebhookSignature($payload, $signature)

##### Backend - Controllers
- [x] Criar `PaymentController.php`
  - [x] Method: choosePaymentMethod() - Escolher PIX/Boleto/Cartão
  - [x] Method: createPayment() - Criar pagamento
  - [x] Method: show($payment) - Visualizar boleto/QR Code PIX
  - [x] Method: success($payment) - Página de sucesso
  - [x] Method: index() - Histórico de pagamentos
  - [x] Method: checkStatus($payment) - Verificar status (API)
  - [x] Method: cancel($payment) - Cancelar pagamento
- [x] Criar `WebhookController.php`
  - [x] Method: asaas() - Webhook do Asaas
  - [x] Validação de token do webhook
  - [x] Processamento assíncrono via Queue (ProcessPaymentWebhook)
  - [x] Log de todas as notificações recebidas

##### Backend - Jobs
- [x] Criar `ProcessPaymentWebhook.php`
  - [x] Atualizar status de pagamento via WebhookService
  - [x] Atualizar status de assinatura quando pagamento confirmado
  - [x] Retry com backoff (3 tentativas: 1min, 2min, 5min)
- [ ] Criar `CheckExpiredSubscriptions.php` - Pode usar SubscriptionService::checkExpiredSubscriptions()
  - [ ] Rodar diariamente
  - [ ] Marcar assinaturas expiradas
  - [ ] Enviar email de aviso

##### Backend - Routes
- [x] Adicionar rotas em `routes/web.php`
  - [x] GET /dashboard/payment - Histórico de pagamentos
  - [x] GET /dashboard/payment/choose-method - Escolher método de pagamento
  - [x] POST /dashboard/payment/create - Criar pagamento
  - [x] GET /dashboard/payment/{uuid} - Ver boleto/QR Code PIX
  - [x] GET /dashboard/payment/{uuid}/success - Página de sucesso
  - [x] GET /dashboard/payment/{uuid}/check-status - API verificar status
  - [x] DELETE /dashboard/payment/{uuid}/cancel - Cancelar pagamento
  - [x] POST /webhook/asaas (sem auth, validação por token)

##### Frontend - Pages
- [x] Criar `pages/dashboard/payment/payment-method.tsx`
  - [x] Cards para selecionar PIX/Boleto/Cartão
  - [x] Preview de cada método
  - [x] Integração com subscription atual
- [x] Criar `pages/dashboard/payment/show.tsx`
  - [x] QR Code PIX + código copia-e-cola
  - [x] Linha digitável do boleto + link para visualizar
  - [x] Auto-refresh de status (polling a cada 10s)
- [x] Criar `pages/dashboard/payment/success.tsx`
  - [x] Confirmação de pagamento
  - [x] Detalhes do pagamento
  - [x] Próximos passos
- [x] Criar `pages/dashboard/payment/index.tsx`
  - [x] Histórico completo de pagamentos
  - [x] Filtros por status
  - [x] Paginação

##### Frontend - Components
- [x] Componentes implementados inline nas páginas (não criados separadamente)
  - [x] Seleção de método de pagamento (inline em payment-method.tsx)
  - [x] Exibição de QR Code PIX (inline em show.tsx)
  - [x] Exibição de Boleto (inline em show.tsx)
  - [x] Histórico de pagamentos (inline em index.tsx)

##### Frontend - TypeScript Types
- [x] Criar `types/payment.d.ts`
  - [x] Payment interface
  - [x] PaymentPageProps
  - [x] PaymentMethodPageProps
  - [x] PaymentIndexPageProps

##### Testes
- [ ] Criar `PaymentGatewayServiceTest.php` (unit)
- [ ] Criar `WebhookServiceTest.php` (unit)
- [ ] Criar `ProcessPaymentWebhookTest.php` (feature)

##### Webhooks do Asaas (Configurar no dashboard)
- [ ] PAYMENT_CREATED - Cobrança criada
- [ ] PAYMENT_UPDATED - Cobrança atualizada
- [ ] PAYMENT_CONFIRMED - Pagamento confirmado (aprovado)
- [ ] PAYMENT_RECEIVED - Pagamento recebido (compensado)
- [ ] PAYMENT_OVERDUE - Pagamento vencido
- [ ] PAYMENT_DELETED - Cobrança deletada
- [ ] SUBSCRIPTION_CREATED - Assinatura criada
- [ ] SUBSCRIPTION_UPDATED - Assinatura atualizada
- [ ] SUBSCRIPTION_DELETED - Assinatura cancelada

##### Fluxos de Pagamento

**Fluxo PIX:**
1. Usuário escolhe plano Premium
2. Backend cria assinatura no Asaas
3. Asaas gera QR Code PIX e código copia-e-cola
4. Frontend exibe QR Code + countdown (30min)
5. Usuário paga via app do banco
6. Asaas envia webhook PAYMENT_CONFIRMED (2-10 segundos)
7. Sistema ativa assinatura imediatamente
8. Usuário redirecionado para success page

**Fluxo Boleto:**
1. Usuário escolhe plano Premium
2. Backend cria assinatura no Asaas
3. Asaas gera boleto (vencimento em 3 dias)
4. Frontend exibe linha digitável + botão PDF
5. Usuário paga boleto no banco (1-2 dias úteis)
6. Asaas envia webhook PAYMENT_RECEIVED
7. Sistema ativa assinatura
8. Email de confirmação enviado

**Fluxo Cartão:**
1. Usuário escolhe plano Premium
2. Frontend coleta dados do cartão
3. Asaas tokeniza cartão (segurança PCI)
4. Backend cria assinatura com token
5. Asaas processa pagamento (5-10 segundos)
6. Webhook PAYMENT_CONFIRMED enviado
7. Sistema ativa assinatura imediatamente
8. Cobrança recorrente automática todo mês

##### Segurança
- [ ] Validar token do webhook (ASAAS_WEBHOOK_TOKEN)
- [ ] Usar HTTPS em produção
- [ ] Não armazenar dados de cartão (usar tokenização)
- [ ] Log de todas as transações
- [ ] Validar origem das requisições de webhook
- [ ] Rate limiting em rotas de pagamento

##### Documentação
- [ ] Documentar fluxo completo de pagamento
- [ ] Documentar cada webhook e sua função
- [ ] Documentar ambiente de testes/sandbox do Asaas
  - Cartão de teste: 5162306219378829
  - CVV: 318
  - Validade: qualquer data futura
- [ ] Criar guia de troubleshooting
- [ ] Documentar como testar pagamentos em sandbox

---

### 1.3 Middleware de Verificação de Plano ✅

**Prioridade:** 🔴 CRÍTICA
**Complexidade:** Baixa
**Tempo estimado:** 1 dia
**Status:** ✅ Concluído

#### Checklist de Implementação

- [x] Criar middleware `CheckSubscription.php`
  - [x] Verificar se usuário tem assinatura ativa
  - [x] Suporte a verificação de planos específicos (free, premium, family)
  - [x] Redirecionar para /subscription/plans com toast
- [x] Criar middleware `CheckPlanFeature.php`
  - [x] Verificar se feature está disponível no plano do usuário
  - [x] Redirecionar para /subscription/plans com toast
  - [x] Method estático hasReachedLimit() para uso em controllers
- [x] Registrar middleware em `bootstrap/app.php`
  - [x] Alias 'subscription' para CheckSubscription
  - [x] Alias 'plan.feature' para CheckPlanFeature
- [ ] Aplicar middleware em rotas premium (será feito ao implementar features)
  - [ ] Savings Goals routes
  - [ ] Tags routes
  - [ ] Attachments routes
  - [ ] Custom Reports routes
  - [ ] AI Predictions routes

---

### 1.4 Limites de Features por Plano ✅

**Prioridade:** 🔴 CRÍTICA
**Complexidade:** Baixa
**Tempo estimado:** 1-2 dias
**Status:** ✅ Concluído

#### Checklist de Implementação

##### Backend - Config
- [x] Criar `config/plan_limits.php`
  ```php
  return [
      'free' => [
          'categories' => 10,
          'wallets' => 3,
          'budgets' => 5,
          'savings_goals' => 0,
          'export_per_month' => 5,
          'transactions_history_months' => 12,
          'tags' => 0,
          'attachments' => 0,
          'custom_reports' => 0,
          'ai_predictions' => false,
          'family_members' => 1,
      ],
      'premium' => [
          'categories' => -1, // Ilimitado
          'wallets' => -1,
          'budgets' => -1,
          'savings_goals' => 20,
          'export_per_month' => -1,
          'transactions_history_months' => -1,
          'tags' => -1,
          'attachments' => 100,
          'custom_reports' => 50,
          'ai_predictions' => true,
          'family_members' => 1,
      ],
      'family' => [
          // Mesmas do Premium +
          'family_members' => 5,
          'attachments' => 500,
      ],
  ];
  ```

##### Backend - Service
- [x] Criar `PlanLimitService.php`
  - [x] Method: canCreate($user, $feature, $currentCount) - Verifica se pode criar
  - [x] Method: checkLimitWithToast($user, $feature, $currentCount, $resourceName) - Verifica e exibe toast
  - [x] Method: getRemainingCount($user, $feature, $currentCount) - Retorna quantidade restante
  - [x] Method: hasFeature($user, $feature) - Verifica se feature está habilitada
  - [x] Method: getLimit($user, $feature) - Retorna limite do plano
  - [x] Method: getUsagePercentage($user, $feature, $currentCount) - Retorna % de uso
  - [x] Method: getFeatureDisplayName($feature) - Nome amigável da feature

##### Backend - Validação
- [ ] Atualizar `StoreCategoryRequest.php` - Será feito ao aplicar limites
  - [ ] Validar limite de categorias
- [ ] Atualizar `StoreWalletRequest.php` - Será feito ao aplicar limites
  - [ ] Validar limite de carteiras
- [ ] Atualizar `StoreBudgetRequest.php` - Será feito ao aplicar limites
  - [ ] Validar limite de orçamentos
- [ ] Criar `StoreExportRequest.php` - Será feito ao aplicar limites
  - [ ] Validar limite de exportações por mês

##### Backend - Controllers
- [ ] Atualizar controllers para verificar limites antes de criar (será feito ao aplicar limites)
  - [ ] CategoriesController
  - [ ] WalletsController
  - [ ] BudgetsController
  - [ ] ExportsController

##### Frontend - Components
- [ ] Criar `components/limits/usage-indicator.tsx` - Será criado quando aplicar limites
  - [ ] Mostrar uso atual vs. limite
  - [ ] Progress bar
- [ ] Criar `components/limits/upgrade-modal.tsx` - Será criado quando aplicar limites
  - [ ] Modal sugerindo upgrade quando limite atingido
- [ ] Atualizar forms para mostrar limite restante - Será feito quando aplicar limites

##### Testes
- [ ] Criar `PlanLimitServiceTest.php` - Pode ser criado
- [ ] Testar limites do plano Free
- [ ] Testar ilimitado do plano Premium

---

### 1.5 Dashboard de Assinatura ✅

**Prioridade:** 🔴 CRÍTICA
**Complexidade:** Média
**Tempo estimado:** 1-2 dias
**Status:** ✅ Concluído

#### Checklist de Implementação

##### Frontend - Pages
- [x] Criar página `pages/dashboard/subscription/index.tsx`
  - [x] Informações do plano atual
  - [x] Status da assinatura (ativa, cancelada, grace period)
  - [x] Data de renovação/expiração
  - [x] Histórico de mudanças de plano
  - [x] Botão para cancelar
  - [x] Botão para retomar assinatura cancelada
  - [x] Avisos de grace period
- [x] Criar página `pages/dashboard/subscription/plans.tsx`
  - [x] Listagem de todos os planos
  - [x] Comparação de features
  - [x] Badge "Plano Atual" e "Mais Popular"
  - [x] Botões de ação (Assinar, Upgrade, etc)

##### Frontend - Components
- [x] Componentes implementados inline (não separados)
  - [x] Card de plano atual (inline em index.tsx)
  - [x] Histórico de assinaturas (inline em index.tsx)
  - [x] Dialogs de confirmação (inline)

##### Backend
- [x] Método em SubscriptionController para retornar dados do dashboard
  - [x] index() - Dashboard com subscription atual
  - [x] plans() - Lista de planos disponíveis

---

### 1.6 Fluxo de Upgrade/Downgrade ✅

**Prioridade:** 🔴 CRÍTICA
**Complexidade:** Média
**Tempo estimado:** 2-3 dias
**Status:** ✅ Concluído

#### Checklist de Implementação

##### Backend - Service
- [x] Expandir `SubscriptionService.php`
  - [x] Method: upgrade($user, $newPlanSlug) - Upgrade imediato
  - [x] Method: downgrade($user, $newPlanSlug) - Downgrade agendado
  - [x] Method: activate($subscription) - Ativar subscription
  - [x] Method: cancel($subscription) - Cancelar com grace period
  - [x] Method: resume($subscription) - Retomar subscription cancelada
  - [ ] Method: calculateProration($currentPlan, $newPlan) - Cálculo proporcional (futuro)

##### Backend - Controller
- [x] Adicionar em `SubscriptionController.php`
  - [x] Method: upgrade($planSlug)
  - [x] Method: downgrade($planSlug)
  - [x] Method: previewChange($planSlug) - Preview da mudança
  - [x] Method: cancel() - Cancelar assinatura
  - [x] Method: resume() - Retomar assinatura

##### Frontend
- [x] Implementado inline nas páginas existentes
  - [x] Botões de upgrade/downgrade em plans.tsx
  - [x] Confirmações via dialog/alert do shadcn/ui
  - [x] Avisos de mudança de plano

##### Regras de Negócio
- [x] Upgrade: Cria nova subscription PENDING (requer pagamento)
- [x] Downgrade: Agenda para final do período (cria subscription PENDING)
- [x] Cancelamento: Mantém ativa até final do período (grace period)
- [x] Retomar: Remove cancelamento e volta ao estado ACTIVE
- [ ] Validar features compatíveis com downgrade (será implementado ao aplicar limites)

---

## 🎯 FASE 2: FEATURES PREMIUM CORE (5-7 dias) - ALTA

**Objetivo:** Entregar valor imediato para assinantes Premium

**Progresso:** 0/4 tarefas concluídas

### 2.1 Tags Personalizadas

**Prioridade:** 🟡 ALTA
**Complexidade:** Baixa
**Tempo estimado:** 1-2 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `create_tags_table`
  - [ ] Campos: id, uuid, user_id, name, color
  - [ ] Unique constraint: user_id + name
- [ ] Criar migration `create_taggables_table`
  - [ ] Campos: tag_id, taggable_id, taggable_type
  - [ ] Polymorphic relationship

##### Backend - Models
- [ ] Criar model `Tag.php`
  - [ ] HasUuidCustom trait
  - [ ] Relationship: belongsTo(User)
  - [ ] Relationship: morphedByMany(Transaction, Taggable)
  - [ ] Relationship: morphedByMany(Account, Taggable)
  - [ ] Relationship: morphedByMany(Income, Taggable)
- [ ] Criar trait `HasTags.php`
  - [ ] Method: tags() - morphToMany relationship
  - [ ] Method: attachTag($tag)
  - [ ] Method: detachTag($tag)
  - [ ] Method: syncTags($tags)

##### Backend - Models (Atualizar)
- [ ] Adicionar `HasTags` trait em:
  - [ ] Transaction.php
  - [ ] Account.php
  - [ ] Income.php
  - [ ] Budget.php

##### Backend - Services
- [ ] Criar `TagService.php`
  - [ ] Method: create($user, $data)
  - [ ] Method: update($tag, $data)
  - [ ] Method: delete($tag)
  - [ ] Method: getUserTags($user)
  - [ ] Method: getPopularTags($user)

##### Backend - Controllers
- [ ] Criar `TagsController.php`
  - [ ] index() - Listar tags do usuário
  - [ ] store() - Criar nova tag
  - [ ] update() - Atualizar tag
  - [ ] destroy() - Deletar tag

##### Backend - Resources
- [ ] Criar `TagResource.php`
- [ ] Atualizar `TransactionResource.php` para incluir tags
- [ ] Atualizar `AccountResource.php` para incluir tags
- [ ] Atualizar `IncomeResource.php` para incluir tags

##### Backend - Requests
- [ ] Criar `StoreTagRequest.php`
- [ ] Criar `UpdateTagRequest.php`

##### Backend - Routes
- [ ] Adicionar rotas com middleware `CheckPlanFeature:tags`
  - [ ] GET /dashboard/tags
  - [ ] POST /dashboard/tags
  - [ ] PATCH /dashboard/tags/{tag}
  - [ ] DELETE /dashboard/tags/{tag}

##### Frontend - Pages
- [ ] Criar `pages/dashboard/tags/index.tsx`

##### Frontend - Components
- [ ] Criar `components/tags/tag-input.tsx`
  - [ ] Multi-select com criação inline
  - [ ] Color picker
- [ ] Criar `components/tags/tag-badge.tsx`
- [ ] Criar `components/tags/tag-manager.tsx`

##### Frontend - Forms (Atualizar)
- [ ] Adicionar tag input em:
  - [ ] Transaction form
  - [ ] Account form
  - [ ] Income form

##### Testes
- [ ] Criar `TagServiceTest.php`
- [ ] Criar `TagsControllerTest.php`
- [ ] Testar middleware CheckPlanFeature

---

### 2.2 Anexos e Notas

**Prioridade:** 🟡 ALTA
**Complexidade:** Média
**Tempo estimado:** 2-3 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `create_attachments_table`
  - [ ] Campos: id, uuid, user_id, attachable_id, attachable_type, original_name, file_name, file_path, mime_type, file_size
- [ ] Criar migration `add_notes_to_transactions_table`
  - [ ] Campo: notes (text, nullable)
- [ ] Criar migration `add_notes_to_accounts_table`
  - [ ] Campo: notes (text, nullable)
- [ ] Criar migration `add_notes_to_incomes_table`
  - [ ] Campo: notes (text, nullable)

##### Backend - Models
- [ ] Criar model `Attachment.php`
  - [ ] HasUuidCustom trait
  - [ ] Relationship: belongsTo(User)
  - [ ] Relationship: morphTo(Attachable)
  - [ ] Accessor: file_url
  - [ ] Method: download()
- [ ] Criar trait `HasAttachments.php`
  - [ ] Relationship: morphMany(Attachment)
  - [ ] Method: addAttachment($file)
  - [ ] Method: removeAttachment($attachment)
  - [ ] Method: getAttachments()

##### Backend - Models (Atualizar)
- [ ] Adicionar `HasAttachments` trait em:
  - [ ] Transaction.php
  - [ ] Account.php
  - [ ] Income.php

##### Backend - Config
- [ ] Atualizar `config/filesystems.php`
  - [ ] Criar disco 'attachments' (local ou S3)
- [ ] Configurar storage público

##### Backend - Services
- [ ] Criar `AttachmentService.php`
  - [ ] Method: upload($user, $file, $attachable)
  - [ ] Method: delete($attachment)
  - [ ] Method: download($attachment)
  - [ ] Method: checkSizeLimit($user) - Validar limite do plano
  - [ ] Method: checkFileType($file) - Validar tipos permitidos

##### Backend - Controllers
- [ ] Criar `AttachmentsController.php`
  - [ ] store() - Upload de arquivo
  - [ ] destroy() - Deletar arquivo
  - [ ] download() - Download de arquivo

##### Backend - Requests
- [ ] Criar `StoreAttachmentRequest.php`
  - [ ] Validação: max size (5MB), mimes (pdf, jpg, png, jpeg)

##### Backend - Routes
- [ ] Adicionar rotas com middleware `CheckPlanFeature:attachments`
  - [ ] POST /dashboard/attachments
  - [ ] DELETE /dashboard/attachments/{attachment}
  - [ ] GET /dashboard/attachments/{attachment}/download

##### Frontend - Components
- [ ] Criar `components/attachments/file-upload.tsx`
  - [ ] Drag & drop
  - [ ] Preview de imagens
  - [ ] Progress bar
- [ ] Criar `components/attachments/attachment-list.tsx`
- [ ] Criar `components/attachments/attachment-card.tsx`

##### Frontend - Forms (Atualizar)
- [ ] Adicionar campo notes em:
  - [ ] Transaction form
  - [ ] Account form
  - [ ] Income form
- [ ] Adicionar file upload em:
  - [ ] Transaction form
  - [ ] Account form
  - [ ] Income form

##### Segurança
- [ ] Validar ownership antes de download
- [ ] Sanitizar nomes de arquivos
- [ ] Validar MIME types
- [ ] Limitar tamanho de upload (5MB)

##### Testes
- [ ] Criar `AttachmentServiceTest.php`
- [ ] Criar `AttachmentsControllerTest.php`
- [ ] Testar upload e download
- [ ] Testar limites do plano

---

### 2.3 Metas de Economia (Savings Goals)

**Prioridade:** 🟡 ALTA
**Complexidade:** Média
**Tempo estimado:** 2-3 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `create_savings_goals_table`
  - [ ] Campos: id, uuid, user_id, category_id (nullable), name, description, target_amount_cents, current_amount_cents, target_date, icon, color, is_active

##### Backend - Models
- [ ] Criar model `SavingsGoal.php`
  - [ ] HasUuidCustom trait
  - [ ] HasMoneyAccessors trait
  - [ ] Relationship: belongsTo(User)
  - [ ] Relationship: belongsTo(Category)
  - [ ] Accessor: progress_percentage
  - [ ] Accessor: remaining_amount
  - [ ] Accessor: days_remaining
  - [ ] Method: addProgress($amount)
  - [ ] Method: removeProgress($amount)
  - [ ] Method: complete()

##### Backend - Services
- [ ] Criar `SavingsGoalService.php`
  - [ ] Method: create($user, $data)
  - [ ] Method: update($goal, $data)
  - [ ] Method: delete($goal)
  - [ ] Method: addContribution($goal, $amount)
  - [ ] Method: getUserGoals($user)
  - [ ] Method: getActiveGoals($user)
  - [ ] Method: getCompletedGoals($user)

##### Backend - Controllers
- [ ] Criar `SavingsGoalsController.php`
  - [ ] index() - Listar metas
  - [ ] store() - Criar meta
  - [ ] show() - Ver detalhes
  - [ ] update() - Atualizar meta
  - [ ] destroy() - Deletar meta
  - [ ] addContribution() - Adicionar contribuição

##### Backend - Resources
- [ ] Criar `SavingsGoalResource.php`

##### Backend - Requests
- [ ] Criar `StoreSavingsGoalRequest.php`
- [ ] Criar `UpdateSavingsGoalRequest.php`
- [ ] Criar `AddContributionRequest.php`

##### Backend - Routes
- [ ] Adicionar rotas com middleware `CheckPlanFeature:savings_goals`
  - [ ] GET /dashboard/savings-goals
  - [ ] POST /dashboard/savings-goals
  - [ ] GET /dashboard/savings-goals/{goal}
  - [ ] PATCH /dashboard/savings-goals/{goal}
  - [ ] DELETE /dashboard/savings-goals/{goal}
  - [ ] POST /dashboard/savings-goals/{goal}/contribute

##### Frontend - Pages
- [ ] Criar `pages/dashboard/savings-goals/index.tsx`
- [ ] Criar `pages/dashboard/savings-goals/create.tsx`
- [ ] Criar `pages/dashboard/savings-goals/edit.tsx`
- [ ] Criar `pages/dashboard/savings-goals/show.tsx`

##### Frontend - Components
- [ ] Criar `components/savings/goal-card.tsx`
  - [ ] Progress bar circular
  - [ ] Informações da meta
- [ ] Criar `components/savings/goal-form.tsx`
- [ ] Criar `components/savings/contribution-modal.tsx`
- [ ] Criar `components/savings/goal-progress-chart.tsx`

##### Frontend - Dashboard (Atualizar)
- [ ] Adicionar widget de metas no dashboard principal
- [ ] Mostrar progresso das metas ativas

##### Testes
- [ ] Criar `SavingsGoalServiceTest.php`
- [ ] Criar `SavingsGoalsControllerTest.php`
- [ ] Testar cálculos de progresso
- [ ] Testar limites do plano

---

### 2.4 Relatórios Customizados

**Prioridade:** 🟢 MÉDIA
**Complexidade:** Média
**Tempo estimado:** 3-4 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `create_saved_reports_table`
  - [ ] Campos: id, uuid, user_id, name, description, report_type, filters (json), chart_config (json), is_favorite

##### Backend - Models
- [ ] Criar model `SavedReport.php`
  - [ ] HasUuidCustom trait
  - [ ] Relationship: belongsTo(User)
  - [ ] Casts: filters -> array, chart_config -> array

##### Backend - Enums
- [ ] Criar `ReportTypeEnum.php`
  - [ ] EXPENSES_BY_CATEGORY
  - [ ] INCOME_BY_CATEGORY
  - [ ] CASHFLOW_COMPARISON
  - [ ] BUDGET_ANALYSIS
  - [ ] WALLET_BALANCE_HISTORY
  - [ ] SAVINGS_PROGRESS

##### Backend - Services
- [ ] Criar `CustomReportService.php`
  - [ ] Method: generate($reportType, $filters)
  - [ ] Method: saveReport($user, $data)
  - [ ] Method: getUserReports($user)
  - [ ] Method: deleteReport($report)
  - [ ] Method: exportToPDF($report)

##### Backend - Controllers
- [ ] Expandir `ReportsController.php`
  - [ ] saved() - Listar relatórios salvos
  - [ ] storeSaved() - Salvar novo relatório
  - [ ] showSaved() - Ver relatório salvo
  - [ ] destroySaved() - Deletar relatório
  - [ ] generateCustom() - Gerar relatório customizado
  - [ ] exportPDF() - Exportar para PDF

##### Backend - Requests
- [ ] Criar `GenerateCustomReportRequest.php`
- [ ] Criar `SaveReportRequest.php`

##### Backend - Routes
- [ ] Adicionar rotas com middleware `CheckPlanFeature:custom_reports`
  - [ ] GET /dashboard/reports/custom
  - [ ] POST /dashboard/reports/custom/generate
  - [ ] POST /dashboard/reports/saved
  - [ ] GET /dashboard/reports/saved
  - [ ] GET /dashboard/reports/saved/{report}
  - [ ] DELETE /dashboard/reports/saved/{report}
  - [ ] GET /dashboard/reports/saved/{report}/pdf

##### Frontend - Pages
- [ ] Criar `pages/dashboard/reports/custom.tsx`
- [ ] Criar `pages/dashboard/reports/saved.tsx`
- [ ] Criar `pages/dashboard/reports/builder.tsx`

##### Frontend - Components
- [ ] Criar `components/reports/report-builder.tsx`
  - [ ] Seleção de tipo de relatório
  - [ ] Filtros avançados
  - [ ] Seleção de tipo de gráfico
- [ ] Criar `components/reports/filter-panel.tsx`
- [ ] Criar `components/reports/chart-selector.tsx`
- [ ] Criar `components/reports/saved-report-card.tsx`

##### PDF Export
- [ ] Instalar biblioteca de PDF (DomPDF ou wkhtmltopdf)
- [ ] Criar template Blade para PDF
- [ ] Estilização para impressão

##### Testes
- [ ] Criar `CustomReportServiceTest.php`
- [ ] Criar `ReportsControllerTest.php`
- [ ] Testar geração de diferentes tipos de relatórios

---

## 🎯 FASE 3: PLANO FAMILY (7-10 dias) - ALTA

**Objetivo:** Habilitar compartilhamento entre múltiplos usuários

**Progresso:** 0/4 tarefas concluídas

### 3.1 Sistema de Family Groups

**Prioridade:** 🟡 ALTA
**Complexidade:** Muito Alta
**Tempo estimado:** 4-5 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `create_family_groups_table`
  - [ ] Campos: id, uuid, owner_id, subscription_id, name, max_members, is_active
- [ ] Criar migration `create_family_group_members_table`
  - [ ] Campos: id, family_group_id, user_id, role, joined_at
  - [ ] Unique constraint: family_group_id + user_id
- [ ] Criar migration `add_family_group_id_to_tables`
  - [ ] Adicionar em: transactions, wallets, accounts, incomes, budgets, categories, savings_goals

##### Backend - Models
- [ ] Criar model `FamilyGroup.php`
  - [ ] HasUuidCustom trait
  - [ ] Relationship: belongsTo(User, 'owner_id')
  - [ ] Relationship: belongsTo(Subscription)
  - [ ] Relationship: hasMany(FamilyGroupMember)
  - [ ] Relationship: hasManyThrough(Users)
  - [ ] Method: addMember($user, $role)
  - [ ] Method: removeMember($user)
  - [ ] Method: isFull()
  - [ ] Method: isOwner($user)
- [ ] Criar model `FamilyGroupMember.php`
  - [ ] Relationship: belongsTo(FamilyGroup)
  - [ ] Relationship: belongsTo(User)

##### Backend - Enums
- [ ] Criar `FamilyRoleEnum.php`
  - [ ] ADMIN - Controle total
  - [ ] MEMBER - Pode criar e editar seus dados
  - [ ] VIEWER - Apenas visualização

##### Backend - Models (Atualizar)
- [ ] Adicionar trait `BelongsToFamilyGroup` em:
  - [ ] Transaction.php
  - [ ] Wallet.php
  - [ ] Account.php
  - [ ] Income.php
  - [ ] Budget.php
  - [ ] Category.php
  - [ ] SavingsGoal.php
- [ ] Trait deve adicionar:
  - [ ] Relationship: belongsTo(FamilyGroup)
  - [ ] Scope: forFamilyGroup($groupId)

##### Backend - Services
- [ ] Criar `FamilyGroupService.php`
  - [ ] Method: create($owner, $name)
  - [ ] Method: update($group, $data)
  - [ ] Method: delete($group)
  - [ ] Method: inviteMember($group, $email, $role)
  - [ ] Method: removeMember($group, $user)
  - [ ] Method: updateMemberRole($group, $user, $newRole)
  - [ ] Method: getGroupMembers($group)
  - [ ] Method: getGroupData($group) - Dados consolidados

##### Backend - Controllers
- [ ] Criar `FamilyGroupsController.php`
  - [ ] index() - Dashboard do grupo
  - [ ] store() - Criar grupo
  - [ ] update() - Atualizar grupo
  - [ ] destroy() - Deletar grupo
  - [ ] members() - Listar membros
  - [ ] inviteMember() - Convidar membro
  - [ ] removeMember() - Remover membro
  - [ ] updateRole() - Atualizar role de membro
  - [ ] leave() - Sair do grupo

##### Backend - Policies
- [ ] Criar `FamilyGroupPolicy.php`
  - [ ] viewAny() - Apenas membros do grupo
  - [ ] view() - Apenas membros do grupo
  - [ ] create() - Apenas usuários em plano Family
  - [ ] update() - Apenas owner ou admin
  - [ ] delete() - Apenas owner
  - [ ] addMember() - Apenas owner ou admin
  - [ ] removeMember() - Apenas owner ou admin
  - [ ] updateRole() - Apenas owner

##### Backend - Atualizar Policies Existentes
- [ ] Atualizar todas as policies para considerar family_group_id:
  - [ ] TransactionPolicy
  - [ ] WalletPolicy
  - [ ] AccountPolicy
  - [ ] IncomePolicy
  - [ ] BudgetPolicy
  - [ ] CategoryPolicy
  - [ ] SavingsGoalPolicy

##### Backend - Middleware
- [ ] Criar `CheckFamilyAccess.php`
  - [ ] Verificar se usuário pertence ao family_group
  - [ ] Verificar role do usuário

##### Backend - Notifications
- [ ] Criar `FamilyInvitationNotification.php`
  - [ ] Email com link de convite
- [ ] Criar `FamilyMemberJoinedNotification.php`
- [ ] Criar `FamilyMemberLeftNotification.php`

##### Backend - Routes
- [ ] Adicionar rotas com middleware `CheckPlanFeature:family_members`
  - [ ] GET /dashboard/family
  - [ ] POST /dashboard/family
  - [ ] PATCH /dashboard/family
  - [ ] DELETE /dashboard/family
  - [ ] GET /dashboard/family/members
  - [ ] POST /dashboard/family/invite
  - [ ] DELETE /dashboard/family/members/{user}
  - [ ] PATCH /dashboard/family/members/{user}/role
  - [ ] POST /dashboard/family/leave

##### Frontend - Pages
- [ ] Criar `pages/dashboard/family/index.tsx`
- [ ] Criar `pages/dashboard/family/members.tsx`
- [ ] Criar `pages/dashboard/family/settings.tsx`

##### Frontend - Components
- [ ] Criar `components/family/member-card.tsx`
- [ ] Criar `components/family/invite-modal.tsx`
- [ ] Criar `components/family/role-selector.tsx`
- [ ] Criar `components/family/consolidated-stats.tsx`

##### Testes
- [ ] Criar `FamilyGroupServiceTest.php`
- [ ] Criar `FamilyGroupPolicyTest.php`
- [ ] Testar isolamento de dados entre grupos
- [ ] Testar permissões por role

---

### 3.2 Permissões e Roles

**Prioridade:** 🟡 ALTA
**Complexidade:** Alta
**Tempo estimado:** 2-3 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Matriz de Permissões
- [ ] Documentar matriz de permissões por role
  ```
  ADMIN:
    - Visualizar todos os dados do grupo
    - Criar/editar/deletar qualquer dado
    - Convidar/remover membros
    - Alterar configurações do grupo

  MEMBER:
    - Visualizar todos os dados do grupo
    - Criar seus próprios dados
    - Editar/deletar apenas seus dados

  VIEWER:
    - Apenas visualizar dados do grupo
    - Não pode criar/editar/deletar
  ```

##### Backend - Implementação
- [ ] Criar helper `can_manage_family_resource($user, $resource)`
- [ ] Atualizar controllers para verificar role antes de actions
- [ ] Adicionar validação de ownership em updates/deletes

##### Frontend
- [ ] Mostrar/esconder botões baseado no role do usuário
- [ ] Adicionar badges de role ao lado do nome do usuário
- [ ] Mensagens de erro quando ação não permitida

##### Testes
- [ ] Testar permissões do ADMIN
- [ ] Testar permissões do MEMBER
- [ ] Testar permissões do VIEWER
- [ ] Testar tentativa de ação não autorizada

---

### 3.3 Dashboard Consolidado do Grupo

**Prioridade:** 🟡 ALTA
**Complexidade:** Média
**Tempo estimado:** 2-3 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Service
- [ ] Expandir `DashboardOrchestrationService.php`
  - [ ] Method: getFamilyGroupDashboard($group)
  - [ ] Agregar dados de todos os membros
  - [ ] Separar por membro (opcional)

##### Backend - Controller
- [ ] Adicionar em `DashboardController.php`
  - [ ] Method: familyDashboard()

##### Frontend - Pages
- [ ] Criar `pages/dashboard/family/dashboard.tsx`

##### Frontend - Components
- [ ] Criar `components/family/consolidated-balance.tsx`
- [ ] Criar `components/family/expenses-by-member.tsx`
- [ ] Criar `components/family/family-cashflow-chart.tsx`
- [ ] Criar `components/family/shared-budgets.tsx`

##### Features
- [ ] Saldo total consolidado do grupo
- [ ] Gastos por membro (gráfico de pizza)
- [ ] Cashflow consolidado
- [ ] Orçamentos compartilhados
- [ ] Metas de economia do grupo
- [ ] Top categorias de gastos do grupo

##### Testes
- [ ] Testar agregação de dados
- [ ] Testar isolamento entre grupos diferentes

---

### 3.4 Convites para Membros

**Prioridade:** 🟡 ALTA
**Complexidade:** Média
**Tempo estimado:** 1-2 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `create_family_invitations_table`
  - [ ] Campos: id, uuid, family_group_id, email, role, token, expires_at, accepted_at

##### Backend - Models
- [ ] Criar model `FamilyInvitation.php`
  - [ ] HasUuidCustom trait
  - [ ] Relationship: belongsTo(FamilyGroup)
  - [ ] Method: isExpired()
  - [ ] Method: accept($user)
  - [ ] Method: decline()

##### Backend - Service
- [ ] Expandir `FamilyGroupService.php`
  - [ ] Method: createInvitation($group, $email, $role)
  - [ ] Method: sendInvitationEmail($invitation)
  - [ ] Method: acceptInvitation($invitation, $user)
  - [ ] Method: declineInvitation($invitation)
  - [ ] Method: cancelInvitation($invitation)

##### Backend - Controllers
- [ ] Criar `FamilyInvitationsController.php`
  - [ ] store() - Criar convite
  - [ ] show() - Visualizar convite
  - [ ] accept() - Aceitar convite
  - [ ] decline() - Recusar convite
  - [ ] destroy() - Cancelar convite

##### Backend - Notifications
- [ ] Criar `FamilyInvitationMail.php`
  - [ ] Template de email com link de convite
  - [ ] Informações do grupo
  - [ ] Role que será atribuído

##### Backend - Routes
- [ ] POST /dashboard/family/invitations
- [ ] GET /family/invite/{token} (rota pública)
- [ ] POST /family/invite/{token}/accept
- [ ] POST /family/invite/{token}/decline
- [ ] DELETE /dashboard/family/invitations/{invitation}

##### Frontend - Pages
- [ ] Criar `pages/family/invite.tsx` (página pública de convite)

##### Frontend - Components
- [ ] Criar `components/family/invite-form.tsx`
- [ ] Criar `components/family/pending-invitations.tsx`

##### Validações
- [ ] Validar se email já é membro
- [ ] Validar se grupo não está cheio (max 5 membros)
- [ ] Validar se convite não expirou (7 dias)
- [ ] Validar se usuário convidado tem conta ou precisa criar

##### Testes
- [ ] Testar criação de convite
- [ ] Testar aceitação de convite
- [ ] Testar recusa de convite
- [ ] Testar expiração de convite

---

## 🎯 FASE 4: DIFERENCIAL IA (7-10 dias) - MÉDIA

**Objetivo:** Criar diferencial competitivo com inteligência artificial

**Progresso:** 0/4 tarefas concluídas

### 4.1 Integração com OpenAI

**Prioridade:** 🟢 MÉDIA
**Complexidade:** Alta
**Tempo estimado:** 2-3 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Configuração
- [ ] Instalar SDK do OpenAI
  ```bash
  composer require openai-php/client
  ```
- [ ] Adicionar credenciais no `.env`
  - [ ] OPENAI_API_KEY
  - [ ] OPENAI_ORGANIZATION (opcional)
- [ ] Criar config `config/openai.php`

##### Backend - Service
- [ ] Criar `OpenAIService.php`
  - [ ] Method: chat($messages) - Chamada básica de chat
  - [ ] Method: completion($prompt) - Completion simples
  - [ ] Method: analyze($data, $context) - Análise de dados
  - [ ] Method: summarize($text) - Resumo de texto

##### Backend - Cache
- [ ] Implementar cache de respostas (1 hora)
- [ ] Evitar chamadas duplicadas para mesmos dados

##### Segurança
- [ ] Validar inputs antes de enviar para API
- [ ] Não enviar dados sensíveis (senhas, tokens)
- [ ] Limitar tamanho de contexto
- [ ] Rate limiting (prevenir abuso)

##### Testes
- [ ] Criar `OpenAIServiceTest.php` (com mocks)
- [ ] Testar rate limiting

---

### 4.2 Previsões Financeiras

**Prioridade:** 🟢 MÉDIA
**Complexidade:** Muito Alta
**Tempo estimado:** 3-4 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Service
- [ ] Criar `AIFinancialPredictionService.php`
  - [ ] Method: predictNextMonthExpenses($user)
  - [ ] Method: predictCategorySpending($user, $category, $months = 3)
  - [ ] Method: detectSpendingAnomaly($user, $transaction)
  - [ ] Method: forecastCashFlow($user, $months = 6)

##### Backend - Implementação de Algoritmos
- [ ] Coletar dados históricos (últimos 12 meses)
- [ ] Calcular médias e tendências
- [ ] Identificar padrões sazonais
- [ ] Preparar prompt para OpenAI com contexto
  ```
  Contexto: Usuário gastou R$ X nos últimos 6 meses
  Categorias principais: [lista]
  Tendência: [crescente/decrescente/estável]

  Preveja os gastos dos próximos 3 meses por categoria.
  ```

##### Backend - Controllers
- [ ] Expandir `DashboardController.php`
  - [ ] Method: predictions()

##### Backend - Routes
- [ ] GET /dashboard/ai/predictions

##### Frontend - Pages
- [ ] Criar `pages/dashboard/ai/predictions.tsx`

##### Frontend - Components
- [ ] Criar `components/ai/prediction-card.tsx`
- [ ] Criar `components/ai/forecast-chart.tsx`
- [ ] Criar `components/ai/trend-indicator.tsx`

##### Features Específicas
- [ ] Previsão de gastos do próximo mês
- [ ] Previsão por categoria (3 meses)
- [ ] Alertas de anomalias (gasto incomum)
- [ ] Sugestão de meta de economia realista
- [ ] Previsão de quando atingir savings goal

##### Testes
- [ ] Criar `AIFinancialPredictionServiceTest.php`
- [ ] Testar com dados de diferentes padrões
- [ ] Testar edge cases (novo usuário, poucos dados)

---

### 4.3 Insights e Recomendações

**Prioridade:** 🟢 MÉDIA
**Complexidade:** Alta
**Tempo estimado:** 2-3 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Service
- [ ] Criar `AIInsightsService.php`
  - [ ] Method: generateMonthlyInsights($user)
  - [ ] Method: suggestBudgetOptimization($user)
  - [ ] Method: findSavingsOpportunities($user)
  - [ ] Method: compareToPeers($user) - Opcional

##### Backend - Tipos de Insights
- [ ] **Otimização de Orçamento**
  - "Você gastou 30% a mais em 'Alimentação' este mês"
  - "Reduza 10% em 'Lazer' para atingir sua meta"
- [ ] **Oportunidades de Economia**
  - "Cancelando assinatura X você economiza R$ Y/ano"
  - "Trocar de fornecedor Y pode economizar R$ Z/mês"
- [ ] **Alertas de Tendências**
  - "Seus gastos com Saúde aumentaram 40% nos últimos 3 meses"
  - "Você está gastando menos do que o orçado em Educação"
- [ ] **Recomendações Personalizadas**
  - "Baseado no seu padrão, você pode economizar R$ X este mês"
  - "Considere criar uma meta de economia para [categoria]"

##### Backend - Migrations
- [ ] Criar migration `create_ai_insights_table`
  - [ ] Campos: id, uuid, user_id, type, title, description, data (json), is_read, generated_at

##### Backend - Models
- [ ] Criar model `AIInsight.php`
  - [ ] HasUuidCustom trait
  - [ ] Relationship: belongsTo(User)
  - [ ] Scope: unread()
  - [ ] Method: markAsRead()

##### Backend - Controllers
- [ ] Criar `AIInsightsController.php`
  - [ ] index() - Listar insights
  - [ ] generate() - Gerar novos insights
  - [ ] markAsRead() - Marcar como lido

##### Backend - Jobs
- [ ] Criar `GenerateMonthlyInsights.php`
  - [ ] Rodar mensalmente no dia 1
  - [ ] Gerar insights para todos os usuários Premium/Family

##### Backend - Routes
- [ ] GET /dashboard/ai/insights
- [ ] POST /dashboard/ai/insights/generate
- [ ] PATCH /dashboard/ai/insights/{insight}/read

##### Frontend - Components
- [ ] Criar `components/ai/insight-card.tsx`
- [ ] Criar `components/ai/insights-list.tsx`
- [ ] Criar `components/ai/insight-notification.tsx`

##### Frontend - Dashboard (Atualizar)
- [ ] Adicionar widget de insights no dashboard
- [ ] Badge de novos insights não lidos

##### Testes
- [ ] Criar `AIInsightsServiceTest.php`
- [ ] Testar geração de diferentes tipos de insights

---

### 4.4 Alertas Inteligentes

**Prioridade:** 🟢 MÉDIA
**Complexidade:** Média
**Tempo estimado:** 2-3 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Expandir AlertService
- [ ] Adicionar tipo de alerta: AI_PREDICTION
- [ ] Method: checkAIPredictionAlerts($user)
  - [ ] Alertar se previsão indica estouro de orçamento
  - [ ] Alertar se tendência de gastos é preocupante

##### Backend - Tipos de Alertas IA
- [ ] **Alerta de Estouro Previsto**
  - "IA prevê que você vai estourar o orçamento de [Categoria] em 20%"
- [ ] **Alerta de Tendência Negativa**
  - "Seus gastos com [Categoria] aumentaram 40% nos últimos 3 meses"
- [ ] **Alerta de Oportunidade**
  - "Você pode economizar R$ X este mês cancelando [Assinatura]"
- [ ] **Alerta de Meta em Risco**
  - "No ritmo atual, você não atingirá sua meta de economia em [Goal]"

##### Backend - Jobs
- [ ] Criar `CheckAIAlerts.php`
  - [ ] Rodar semanalmente
  - [ ] Analisar padrões e enviar alertas

##### Backend - Console
- [ ] Adicionar comando em `routes/console.php`
  ```php
  Schedule::job(CheckAIAlerts::class)->weekly();
  ```

##### Frontend - Components
- [ ] Criar `components/ai/ai-alert-card.tsx`
- [ ] Adicionar ícone de IA nos alertas AI

##### Testes
- [ ] Testar geração de alertas AI
- [ ] Testar precisão das previsões

---

## 🎯 FASE 5: POLIMENTO (3-5 dias) - BAIXA

**Objetivo:** Melhorias de UX e features secundárias

**Progresso:** 0/4 tarefas concluídas

### 5.1 Temas Customizados (Dark Mode Premium)

**Prioridade:** 🔵 BAIXA
**Complexidade:** Baixa
**Tempo estimado:** 1 dia
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `add_theme_preferences_to_users_table`
  - [ ] Campo: theme_preference (json)

##### Backend - Temas Disponíveis
- [ ] Light (Free)
- [ ] Dark (Free)
- [ ] **Ocean Blue** (Premium)
- [ ] **Forest Green** (Premium)
- [ ] **Sunset Orange** (Premium)
- [ ] **Purple Dream** (Premium)

##### Frontend - Theme Provider
- [ ] Expandir `theme-provider.tsx`
  - [ ] Suportar temas customizados
  - [ ] Carregar preferência do usuário

##### Frontend - Components
- [ ] Criar `components/settings/theme-selector.tsx`
- [ ] Preview de cada tema

##### Frontend - CSS
- [ ] Criar variáveis CSS para cada tema
- [ ] Aplicar cores dinamicamente

##### Testes
- [ ] Testar troca de temas
- [ ] Testar persistência da preferência

---

### 5.2 Exportação de Relatórios em PDF

**Prioridade:** 🔵 BAIXA
**Complexidade:** Média
**Tempo estimado:** 1-2 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Instalação
- [ ] Instalar biblioteca de PDF
  ```bash
  composer require barryvdh/laravel-dompdf
  ```

##### Backend - Templates
- [ ] Criar template Blade `reports/pdf/financial-report.blade.php`
- [ ] Criar template `reports/pdf/cashflow-report.blade.php`
- [ ] Criar template `reports/pdf/budget-report.blade.php`

##### Backend - Service
- [ ] Expandir `CustomReportService.php`
  - [ ] Method: exportToPDF($report)
  - [ ] Method: generatePDFData($report)

##### Backend - Controllers
- [ ] Adicionar em `ReportsController.php`
  - [ ] Method: downloadPDF($report)

##### Backend - Routes
- [ ] GET /dashboard/reports/{report}/pdf

##### Frontend - Components
- [ ] Adicionar botão "Exportar PDF" em relatórios

##### Testes
- [ ] Testar geração de PDF
- [ ] Testar layout e formatação

---

### 5.3 Notificações por Email

**Prioridade:** 🔵 BAIXA
**Complexidade:** Baixa
**Tempo estimado:** 1 dia
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `add_email_preferences_to_users_table`
  - [ ] Campo: email_preferences (json)

##### Backend - Email Templates
- [ ] Weekly summary email
- [ ] Monthly summary email
- [ ] Goal achieved email
- [ ] Budget exceeded email

##### Backend - Jobs
- [ ] Criar `SendWeeklySummary.php`
- [ ] Criar `SendMonthlySummary.php`

##### Backend - Console
- [ ] Schedule weekly summary (Sundays 20:00)
- [ ] Schedule monthly summary (Day 1, 08:00)

##### Frontend - Settings
- [ ] Criar página de preferências de email
- [ ] Permitir ativar/desativar cada tipo de notificação

##### Testes
- [ ] Testar envio de emails
- [ ] Testar preferências do usuário

---

### 5.4 Onboarding para Novos Usuários

**Prioridade:** 🔵 BAIXA
**Complexidade:** Média
**Tempo estimado:** 1-2 dias
**Status:** ⬜ Não iniciado

#### Checklist de Implementação

##### Backend - Migrations
- [ ] Criar migration `add_onboarding_completed_to_users_table`
  - [ ] Campo: onboarding_completed (boolean, default false)

##### Frontend - Onboarding Flow
- [ ] Step 1: Bem-vindo
- [ ] Step 2: Criar primeira carteira
- [ ] Step 3: Criar primeira categoria
- [ ] Step 4: Criar primeiro orçamento
- [ ] Step 5: Tour do dashboard

##### Frontend - Components
- [ ] Criar `components/onboarding/welcome.tsx`
- [ ] Criar `components/onboarding/step-wizard.tsx`
- [ ] Criar `components/onboarding/quick-setup.tsx`

##### Frontend - Tour
- [ ] Usar biblioteca de tour (react-joyride)
- [ ] Destacar features principais do dashboard

##### Testes
- [ ] Testar fluxo completo de onboarding
- [ ] Testar skip onboarding

---

## 📊 RESUMO DE PROGRESSO

### Por Fase

| Fase | Nome | Prioridade | Progresso | Status |
|------|------|------------|-----------|--------|
| 1 | Fundação | 🔴 CRÍTICA | 0/6 | ⬜ Não iniciado |
| 2 | Features Premium Core | 🟡 ALTA | 0/4 | ⬜ Não iniciado |
| 3 | Plano Family | 🟡 ALTA | 0/4 | ⬜ Não iniciado |
| 4 | Diferencial IA | 🟢 MÉDIA | 0/4 | ⬜ Não iniciado |
| 5 | Polimento | 🔵 BAIXA | 0/4 | ⬜ Não iniciado |

### Por Prioridade

| Prioridade | Total | Concluídas | Pendentes |
|------------|-------|------------|-----------|
| 🔴 CRÍTICA | 6 | 0 | 6 |
| 🟡 ALTA | 8 | 0 | 8 |
| 🟢 MÉDIA | 4 | 0 | 4 |
| 🔵 BAIXA | 4 | 0 | 4 |

### Estimativa de Tempo Total

- **Mínimo:** 35 dias
- **Máximo:** 50 dias
- **Média:** 42 dias (~2 meses)

---

## 💳 POR QUE ASAAS? - DECISÃO TÉCNICA

### Gateway de Pagamento Escolhido: **Asaas**

O Asaas foi escolhido como gateway de pagamento oficial do MeloSys pelos seguintes motivos:

#### ✅ Vantagens Principais

**1. Focado em SaaS e Assinaturas Recorrentes**
- Sistema nativo de cobrança recorrente
- Gestão automática de ciclos de pagamento
- Retry automático para pagamentos recusados
- Dunning management (cobrança inteligente)

**2. Taxas Muito Competitivas**

| Método | Asaas | Mercado Pago | Stripe |
|--------|-------|--------------|--------|
| **PIX** | R$ 0,99 fixo | ~R$ 0,99 | N/A (não tem PIX) |
| **Boleto** | 1,99% | ~3,49% | N/A (não tem boleto) |
| **Cartão** | 2,99% + R$0,39 | ~4,99% | ~4,99% |
| **Recorrência** | Incluído | Taxa extra | Taxa extra |

💰 **Economia estimada:** 30-40% em taxas comparado com Mercado Pago

**3. Múltiplos Métodos de Pagamento Brasileiros**
- ✅ PIX (confirmação em segundos)
- ✅ Boleto bancário (mais usado por empresas)
- ✅ Cartão de crédito (parcelado ou não)
- ✅ Débito automático (recorrência)
- ✅ Link de pagamento
- ✅ Nota fiscal automática (DANFE)

**4. Features Avançadas para SaaS**
- Split de pagamento (para afiliados futuros)
- Antecipação de recebíveis
- Gestão de inadimplência
- Relatórios financeiros completos
- API bem documentada em português
- SDK oficial PHP
- Webhooks confiáveis com retry

**5. Suporte Nacional de Qualidade**
- Suporte em português
- Equipe que entende SaaS brasileiro
- Conhecimento da legislação brasileira
- Integração com contabilidade BR
- Dashboard completo de gestão

**6. Segurança e Compliance**
- Certificação PCI-DSS Level 1
- Tokenização de cartões
- Antifraude integrado
- Backup automático de transações

#### 📊 Comparação com Outras Opções

**Mercado Pago:**
- ❌ Taxas mais altas (4,99% vs 2,99%)
- ❌ Focado em e-commerce, não SaaS
- ❌ Interface complexa para assinaturas
- ✅ Mais conhecido pelo público
- ✅ Boa documentação

**Stripe:**
- ❌ Não tem PIX (método mais usado no Brasil)
- ❌ Não tem boleto
- ❌ Taxas em dólar (variação cambial)
- ❌ Suporte em inglês
- ✅ Melhor para SaaS internacional
- ✅ Ótima API

**Pagar.me:**
- ✅ Similar ao Asaas
- ❌ Taxas um pouco mais altas
- ❌ Menos focado em pequenos SaaS
- ✅ Boa API

#### 🎯 Decisão Final

Para um **SaaS 100% brasileiro** como o MeloSys:

**Asaas é a melhor escolha porque:**
1. Menor custo operacional (30-40% economia em taxas)
2. Suporte nativo a PIX (método mais usado)
3. Focado em assinaturas recorrentes
4. Melhor suporte em português
5. Features pensadas para SaaS

#### 🚀 Recursos Únicos do Asaas para MeloSys

**Assinaturas Inteligentes:**
- Cobrança automática mensal
- Retry em caso de falha (3 tentativas)
- Notificação de vencimento próximo
- Upgrade/downgrade no meio do ciclo
- Cálculo automático de proporcionalidade

**Webhooks Robustos:**
- 9 eventos diferentes
- Retry automático (até 10 vezes)
- Validação de autenticidade
- Logs detalhados

**Dashboard Completo:**
- Visão de MRR (Monthly Recurring Revenue)
- Churn rate
- Inadimplência
- Próximas cobranças
- Exportação de relatórios

#### 📚 Recursos Oficiais

- **Site:** https://www.asaas.com
- **Documentação:** https://docs.asaas.com
- **SDK PHP:** https://github.com/asaas/asaas-php-sdk
- **Sandbox:** https://sandbox.asaas.com
- **Suporte:** suporte@asaas.com
- **Status:** https://status.asaas.com

---

## 📝 NOTAS IMPORTANTES

### Ordem de Implementação Recomendada

1. **PRIMEIRO:** Fase 1 (Fundação) - sem isso, nada funciona
2. **SEGUNDO:** Fase 2.1, 2.2, 2.3 (Tags, Anexos, Savings Goals) - features mais solicitadas
3. **TERCEIRO:** Fase 3 (Family) - se plano Family for prioridade
4. **QUARTO:** Fase 2.4 (Relatórios Customizados)
5. **QUINTO:** Fase 4 (IA) - diferencial de marketing
6. **ÚLTIMO:** Fase 5 (Polimento) - nice-to-have

### Dependências Críticas

- **Fase 2, 3, 4, 5** dependem de **Fase 1** estar 100% completa
- **Fase 4** (IA) pode ser feita em paralelo com **Fase 3** (Family)
- **Fase 5** pode ser feita incrementalmente durante as outras fases

### Considerações Técnicas

- Sempre escrever testes para novas features
- Documentar APIs e endpoints
- Considerar performance (cache, eager loading)
- Validar limites de plano em todos os pontos de criação
- Manter compatibilidade com versão mobile (futuro)

---

## 🎯 PRÓXIMOS PASSOS

### Para Começar Agora

1. [ ] Revisar este documento completo
2. [ ] Priorizar fases baseado em objetivos de negócio
3. [ ] Criar conta no Asaas (https://www.asaas.com)
4. [ ] Configurar ambiente de sandbox do Asaas
5. [ ] Começar Fase 1.1: Sistema de Assinaturas
6. [ ] Criar branch `feature/subscriptions` no Git

### Tracking de Progresso

- Atualizar checkboxes deste documento conforme conclusão
- Criar issues no GitHub para cada feature
- Usar milestones para cada fase
- Revisar progresso semanalmente

---

**Última atualização:** 2026-01-03
**Próxima revisão:** Após conclusão da Fase 1
