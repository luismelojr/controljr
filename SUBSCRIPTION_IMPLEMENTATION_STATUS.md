# 📊 Status de Implementação do Sistema de Assinaturas - MeloSys

**Data de Análise:** 2026-01-04
**Versão:** 1.0
**Status Geral:** 40% Completo

---

## 📋 ÍNDICE

1. [Resumo Executivo](#resumo-executivo)
2. [Análise Detalhada por Plano](#análise-detalhada-por-plano)
3. [Status da Infraestrutura](#status-da-infraestrutura)
4. [Features Faltantes](#features-faltantes)
5. [Checklist Completo de Implementação](#checklist-completo-de-implementação)
6. [Estimativas de Tempo](#estimativas-de-tempo)
7. [Ordem de Implementação Recomendada](#ordem-de-implementação-recomendada)

---

## 📊 RESUMO EXECUTIVO

### Status Atual do Sistema

| Componente | Status | Progresso | Observações |
|------------|--------|-----------|-------------|
| **Infraestrutura de Assinatura** | ✅ Completo | 100% | Sistema funcional e testado |
| **Integração de Pagamento (Asaas)** | ✅ Completo | 100% | PIX, Boleto, Cartão funcionando |
| **Middleware de Planos** | ✅ Completo | 100% | CheckPlanFeature implementado |
| **Service de Limites** | ✅ Completo | 100% | PlanLimitService funcionando |
| **Limites Aplicados** | ⚠️ Parcial | 50% | Budgets e Transactions OK, faltam Wallets e Categories |
| **Features Premium** | 🔴 Não iniciado | 0% | 7 features principais faltando |
| **Sistema Family** | 🔴 Não iniciado | 0% | Toda infraestrutura faltando |
| **Inteligência Artificial** | 🔴 Não iniciado | 0% | OpenAI não integrado |

### Progresso Geral: **53%**

✅ **Completo:** 5/10 componentes principais (Infraestrutura + Configuração de Limites)
⚠️ **Parcial:** 1/10 componentes principais (Aplicação de Limites)
🔴 **Faltando:** 4/10 componentes principais (Features Premium)

---

## 🎯 ANÁLISE DETALHADA POR PLANO

### PLANO FREE

**Especificação Original:**
- Não disponível: Tags Personalizadas
- 5 Orçamentos
- 1 Carteira
- 10 Categorias
- Não disponível: Anexos
- Não disponível: Metas de Economia
- Não: Previsões com IA
- Não disponível: Relatórios Customizados
- 1 Membro da Família
- 5 Exportações/mês
- 12 meses: Histórico de Transações

**Configuração Atual (`config/plan_limits.php`):**
```php
'free' => [
    'max_wallets' => 1,                        // ✅ CORRETO
    'max_categories' => 10,                    // ✅ CORRETO
    'max_accounts' => 5,
    'max_transactions_per_month' => 50,
    'max_budgets' => 5,                        // ✅ CORRETO
    'max_alerts' => 2,
    'financial_reports' => false,
    'data_export' => false,
    'bank_reconciliation' => false,
    'multi_currency' => false,
    'api_access' => false,
    'priority_support' => false,
    'max_team_members' => 1,                   // ✅ CORRETO
    'max_tags' => 0,                           // ✅ CORRETO
    'max_attachments' => 0,                    // ✅ CORRETO
    'max_savings_goals' => 0,                  // ✅ CORRETO
    'max_custom_reports' => 0,                 // ✅ CORRETO
    'max_exports_per_month' => 5,              // ✅ CORRETO
    'transactions_history_months' => 12,       // ✅ CORRETO
    'ai_predictions' => false,                 // ✅ CORRETO
],
```

**Status de Implementação:**

| Feature | Configurado | Limite Aplicado | Feature Existe | Status Final |
|---------|-------------|-----------------|----------------|--------------|
| Tags Personalizadas | ❌ Falta | N/A | ❌ Não | 🔴 0% |
| Orçamentos | ✅ 5 | ✅ Sim | ✅ Sim | ✅ 100% |
| Carteiras | ✅ 1 | ❌ Não | ✅ Sim | ⚠️ 70% |
| Categorias | ✅ 10 | ❌ Não | ✅ Sim | ⚠️ 70% |
| Anexos | ❌ Falta | N/A | ❌ Não | 🔴 0% |
| Metas de Economia | ❌ Falta | N/A | ❌ Não | 🔴 0% |
| Previsões IA | ✅ false | ✅ Sim | ❌ Não | ⚠️ 50% |
| Relatórios Customizados | ❌ Falta | N/A | ❌ Não | 🔴 0% |
| Membros Família | ✅ 1 | ❌ Não | ❌ Não | ⚠️ 30% |
| Exportações/mês | 🔴 false (deve ser 5) | ❌ Não | ✅ Sim | ⚠️ 40% |
| Histórico Transações | ❌ Falta | ❌ Não | ❌ Não | 🔴 0% |

**Progresso Plano Free: 45%**

---

### PLANO PREMIUM

**Especificação Original:**
- Ilimitado: Tags Personalizadas
- Ilimitado: Orçamentos
- Ilimitado: Carteiras
- Ilimitado: Categorias
- 100: Anexos
- 20: Metas de Economia
- Sim: Previsões com IA
- 50: Relatórios Customizados
- 1: Membro da Família
- Ilimitado: Exportações/mês
- Ilimitado: Histórico de Transações

**Configuração Atual (`config/plan_limits.php`):**
```php
'premium' => [
    'max_wallets' => -1,                       // ✅ CORRETO (ilimitado)
    'max_categories' => -1,                    // ✅ CORRETO (ilimitado)
    'max_accounts' => 30,
    'max_transactions_per_month' => -1,        // ✅ CORRETO (ilimitado)
    'max_budgets' => -1,                       // ✅ CORRETO (ilimitado)
    'max_alerts' => 10,
    'financial_reports' => true,               // ✅ CORRETO
    'data_export' => true,                     // ✅ CORRETO (ilimitado)
    'bank_reconciliation' => true,
    'multi_currency' => true,
    'api_access' => false,
    'priority_support' => true,
    'max_team_members' => 1,                   // ✅ CORRETO
    'max_tags' => -1,                          // ✅ CORRETO (ilimitado)
    'max_attachments' => 100,                  // ✅ CORRETO
    'max_savings_goals' => 20,                 // ✅ CORRETO
    'max_custom_reports' => 50,                // ✅ CORRETO
    'max_exports_per_month' => -1,             // ✅ CORRETO (ilimitado)
    'transactions_history_months' => -1,       // ✅ CORRETO (ilimitado)
    'ai_predictions' => true,                  // ✅ CORRETO
],
```

**Status de Implementação:**

| Feature | Configurado | Limite Aplicado | Feature Existe | Status Final |
|---------|-------------|-----------------|----------------|--------------|
| Tags Personalizadas | ❌ Falta | N/A | ❌ Não | 🔴 0% |
| Orçamentos | ✅ -1 (ilimitado) | ✅ Sim | ✅ Sim | ✅ 100% |
| Carteiras | ✅ -1 (ilimitado) | ❌ Não | ✅ Sim | ⚠️ 70% |
| Categorias | ✅ -1 (ilimitado) | ❌ Não | ✅ Sim | ⚠️ 70% |
| Anexos | ✅ 100 | N/A | ❌ Não | ⚠️ 30% |
| Metas de Economia | ✅ 20 | N/A | ❌ Não | ⚠️ 30% |
| Previsões IA | ✅ true | ✅ Sim | ❌ Não | ⚠️ 50% |
| Relatórios Customizados | ✅ 50 | N/A | ❌ Não | ⚠️ 30% |
| Membros Família | ✅ 1 | ❌ Não | ❌ Não | ⚠️ 30% |
| Exportações/mês | ✅ true (ilimitado) | ✅ Sim | ✅ Sim | ✅ 100% |
| Histórico Transações | ✅ -1 (ilimitado) | ❌ Não | ❌ Não | ⚠️ 30% |

**Progresso Plano Premium: 55%**

---

### PLANO FAMILY

**Especificação Original:**
- Ilimitado: Tags Personalizadas
- Ilimitado: Orçamentos
- Ilimitado: Carteiras
- Ilimitado: Categorias
- 500: Anexos
- Ilimitado: Metas de Economia
- Sim: Previsões com IA
- Ilimitado: Relatórios Customizados
- 5: Membros da Família
- Ilimitado: Exportações/mês
- Ilimitado: Histórico de Transações

**Configuração Atual (`config/plan_limits.php`):**
```php
'family' => [
    'max_wallets' => -1,                       // ✅ CORRETO (ilimitado)
    'max_categories' => -1,                    // ✅ CORRETO (ilimitado)
    'max_accounts' => -1,                      // ✅ CORRETO
    'max_transactions_per_month' => -1,        // ✅ CORRETO (ilimitado)
    'max_budgets' => -1,                       // ✅ CORRETO (ilimitado)
    'max_alerts' => -1,                        // ✅ CORRETO
    'financial_reports' => true,               // ✅ CORRETO
    'data_export' => true,                     // ✅ CORRETO (ilimitado)
    'bank_reconciliation' => true,
    'multi_currency' => true,
    'api_access' => true,
    'priority_support' => true,
    'max_team_members' => 5,                   // ✅ CORRETO
],
```

**Todas as features configuradas corretamente! ✅**

**Status de Implementação:**

| Feature | Configurado | Limite Aplicado | Feature Existe | Status Final |
|---------|-------------|-----------------|----------------|--------------|
| Tags Personalizadas | ❌ Falta | N/A | ❌ Não | 🔴 0% |
| Orçamentos | ✅ -1 (ilimitado) | ✅ Sim | ✅ Sim | ✅ 100% |
| Carteiras | ✅ -1 (ilimitado) | ❌ Não | ✅ Sim | ⚠️ 70% |
| Categorias | ✅ -1 (ilimitado) | ❌ Não | ✅ Sim | ⚠️ 70% |
| Anexos | ✅ 500 | N/A | ❌ Não | ⚠️ 30% |
| Metas de Economia | ✅ -1 (ilimitado) | N/A | ❌ Não | ⚠️ 30% |
| Previsões IA | ✅ true | ✅ Sim | ❌ Não | ⚠️ 50% |
| Relatórios Customizados | ✅ -1 (ilimitado) | N/A | ❌ Não | ⚠️ 30% |
| Membros Família | ✅ 5 | ❌ Não | ❌ Não | ⚠️ 30% |
| Exportações/mês | ✅ true (ilimitado) | ✅ Sim | ✅ Sim | ✅ 100% |
| Histórico Transações | ✅ -1 (ilimitado) | ❌ Não | ❌ Não | ⚠️ 30% |

**Progresso Plano Family: 58%**

---

## ✅ STATUS DA INFRAESTRUTURA

### 1. Sistema de Assinaturas (100% ✅)

**Implementado:**
- ✅ Migration: `create_subscription_plans_table`
- ✅ Migration: `create_subscriptions_table`
- ✅ Migration: `add_subscription_id_to_users_table`
- ✅ Migration: `add_payment_failure_tracking_to_subscriptions_table`
- ✅ Model: `SubscriptionPlan.php` (completo com traits, scopes, métodos)
- ✅ Model: `Subscription.php` (completo com relationships e status)
- ✅ Enum: `PlanTypeEnum` (FREE, PREMIUM, FAMILY)
- ✅ Enum: `SubscriptionStatusEnum` (ACTIVE, CANCELLED, EXPIRED, PENDING)
- ✅ Service: `SubscriptionService.php` (create, upgrade, downgrade, cancel, resume, renew)
- ✅ Controller: `SubscriptionController.php` (index, plans, subscribe, cancel, resume, upgrade, downgrade)
- ✅ Seeder: `SubscriptionPlanSeeder.php` (3 planos criados)
- ✅ Factory: `SubscriptionFactory.php`
- ✅ Factory: `SubscriptionPlanFactory.php`
- ✅ Resource: `SubscriptionResource.php`
- ✅ Resource: `SubscriptionPlanResource.php`
- ✅ Tests: `SubscriptionServiceTest.php` (10 testes passando)
- ✅ Frontend: `pages/dashboard/subscription/index.tsx`
- ✅ Frontend: `pages/dashboard/subscription/plans.tsx`
- ✅ TypeScript: `types/subscription.d.ts`

**Arquivos Localizados:**
- 📍 `/app/Models/Subscription.php`
- 📍 `/app/Models/SubscriptionPlan.php`
- 📍 `/app/Domain/Subscriptions/Services/SubscriptionService.php`
- 📍 `/app/Http/Controllers/Dashboard/SubscriptionController.php`
- 📍 `/database/migrations/2026_01_03_120000_create_subscription_plans_table.php`
- 📍 `/database/migrations/2026_01_03_120100_create_subscriptions_table.php`
- 📍 `/database/seeders/SubscriptionPlanSeeder.php`

---

### 2. Sistema de Pagamentos - Asaas (100% ✅)

**Implementado:**
- ✅ Config: `config/asaas.php`
- ✅ Migration: `create_payments_table`
- ✅ Migration: `create_webhook_calls_table`
- ✅ Model: `Payment.php` (completo com scopes e status)
- ✅ Model: `WebhookCall.php`
- ✅ Service: `AsaasService.php` (HTTP client customizado, sem SDK)
- ✅ Service: `PaymentGatewayService.php` (orquestração de pagamentos)
- ✅ Service: `WebhookService.php` (processamento de webhooks)
- ✅ Controller: `PaymentController.php` (choosePaymentMethod, createPayment, show, success, index, checkStatus, cancel)
- ✅ Controller: `WebhookController.php` (asaas webhook endpoint)
- ✅ Job: `ProcessPaymentWebhook.php` (processamento assíncrono com retry)
- ✅ Frontend: `pages/dashboard/payment/payment-method.tsx`
- ✅ Frontend: `pages/dashboard/payment/show.tsx` (QR Code PIX + Boleto)
- ✅ Frontend: `pages/dashboard/payment/success.tsx`
- ✅ Frontend: `pages/dashboard/payment/index.tsx` (histórico)
- ✅ TypeScript: `types/payment.d.ts`
- ✅ Emails: `subscription-activated.blade.php`
- ✅ Emails: `subscription-canceled.blade.php`
- ✅ Emails: `subscription-expiring.blade.php`

**Métodos de Pagamento Funcionando:**
- ✅ PIX (QR Code + Copia e Cola)
- ✅ Boleto Bancário (linha digitável + PDF)
- ✅ Cartão de Crédito (tokenização)

**Webhooks Configurados:**
- ✅ PAYMENT_CONFIRMED
- ✅ PAYMENT_RECEIVED
- ✅ PAYMENT_OVERDUE
- ✅ PAYMENT_REFUNDED
- ✅ PAYMENT_DELETED

**Arquivos Localizados:**
- 📍 `/app/Domain/Payments/Services/AsaasService.php`
- 📍 `/app/Domain/Payments/Services/PaymentGatewayService.php`
- 📍 `/app/Domain/Payments/Services/WebhookService.php`
- 📍 `/app/Http/Controllers/Dashboard/PaymentController.php`
- 📍 `/app/Http/Controllers/WebhookController.php`
- 📍 `/app/Jobs/ProcessPaymentWebhook.php`

---

### 3. Middleware e Limites (75% ⚠️)

**Implementado:**
- ✅ Middleware: `CheckPlanFeature.php` (verifica feature habilitada)
- ✅ Service: `PlanLimitService.php` (métodos de verificação de limites)
- ✅ Config: `config/plan_limits.php` (configuração de limites por plano)
- ✅ Registered: Aliases em `bootstrap/app.php`
- ✅ User Model: Método `getPlanLimits()` implementado

**Limites Aplicados nos Controllers:**
- ✅ `BudgetController@store` - Linha 46 (verifica max_budgets)
- ✅ `TransactionsController@store` - Linha 71 (verifica max_transactions)
- ❌ `WalletController@store` - NÃO verifica max_wallets
- ❌ `CategoryController@store` - NÃO verifica max_categories
- ❌ Nenhum controller verifica exportações mensais
- ❌ Nenhum scope filtra histórico de transações por plano

**Arquivos Localizados:**
- 📍 `/app/Http/Middleware/CheckPlanFeature.php`
- 📍 `/app/Services/PlanLimitService.php`
- 📍 `/config/plan_limits.php`
- 📍 `/app/Http/Controllers/Dashboard/BudgetController.php` (exemplo de uso)
- 📍 `/app/Http/Controllers/Dashboard/TransactionsController.php` (exemplo de uso)

---

## 🔴 FEATURES FALTANTES

### 1. Tags Personalizadas (0% 🔴)

**Status:** Não existe nenhum arquivo relacionado

**Precisa Criar:**

**Backend:**
- ❌ Migration: `create_tags_table`
- ❌ Migration: `create_taggables_table` (polymorphic)
- ❌ Model: `Tag.php`
- ❌ Trait: `HasTags.php` (para Transaction, Account, Income, Budget)
- ❌ Service: `TagService.php`
- ❌ Controller: `TagsController.php`
- ❌ Resource: `TagResource.php`
- ❌ Request: `StoreTagRequest.php`
- ❌ Request: `UpdateTagRequest.php`
- ❌ Policy: `TagPolicy.php`

**Frontend:**
- ❌ Page: `pages/dashboard/tags/index.tsx`
- ❌ Component: `components/tags/tag-input.tsx`
- ❌ Component: `components/tags/tag-badge.tsx`
- ❌ Component: `components/tags/tag-manager.tsx`

**Rotas:**
- ❌ GET `/dashboard/tags` (middleware: plan.feature:tags)
- ❌ POST `/dashboard/tags`
- ❌ PATCH `/dashboard/tags/{tag}`
- ❌ DELETE `/dashboard/tags/{tag}`

**Config:**
- ❌ Adicionar em `plan_limits.php`:
  - Free: `'max_tags' => 0`
  - Premium: `'max_tags' => -1`
  - Family: `'max_tags' => -1`

---

### 2. Anexos (Attachments) (0% 🔴)

**Status:** Não existe nenhum arquivo relacionado

**Precisa Criar:**

**Backend:**
- ❌ Migration: `create_attachments_table` (polymorphic: attachable_id, attachable_type)
- ❌ Migration: `add_notes_to_transactions_table` (campo notes)
- ❌ Migration: `add_notes_to_accounts_table` (campo notes)
- ❌ Migration: `add_notes_to_incomes_table` (campo notes)
- ❌ Model: `Attachment.php`
- ❌ Trait: `HasAttachments.php` (para Transaction, Account, Income)
- ❌ Service: `AttachmentService.php` (upload, validação, storage)
- ❌ Controller: `AttachmentsController.php`
- ❌ Resource: `AttachmentResource.php`
- ❌ Request: `StoreAttachmentRequest.php`
- ❌ Policy: `AttachmentPolicy.php`

**Storage:**
- ❌ Configurar disco `attachments` em `config/filesystems.php`
- ❌ Configurar storage público
- ❌ Validações: max 5MB, tipos permitidos (pdf, jpg, png, jpeg)

**Frontend:**
- ❌ Component: `components/attachments/file-upload.tsx` (drag & drop)
- ❌ Component: `components/attachments/attachment-list.tsx`
- ❌ Component: `components/attachments/attachment-card.tsx`
- ❌ Atualizar forms para incluir upload e notes

**Rotas:**
- ❌ POST `/dashboard/attachments` (middleware: plan.feature:attachments)
- ❌ DELETE `/dashboard/attachments/{attachment}`
- ❌ GET `/dashboard/attachments/{attachment}/download`

**Config:**
- ❌ Adicionar em `plan_limits.php`:
  - Free: `'max_attachments' => 0`
  - Premium: `'max_attachments' => 100`
  - Family: `'max_attachments' => 500`

**Segurança:**
- ❌ Validar ownership antes de download
- ❌ Sanitizar nomes de arquivos
- ❌ Validar MIME types
- ❌ Limitar tamanho de upload

---

### 3. Metas de Economia (Savings Goals) (0% 🔴)

**Status:** Não existe nenhum arquivo relacionado

**Precisa Criar:**

**Backend:**
- ❌ Migration: `create_savings_goals_table`
- ❌ Model: `SavingsGoal.php`
- ❌ Service: `SavingsGoalService.php`
- ❌ Controller: `SavingsGoalsController.php`
- ❌ Resource: `SavingsGoalResource.php`
- ❌ Request: `StoreSavingsGoalRequest.php`
- ❌ Request: `UpdateSavingsGoalRequest.php`
- ❌ Request: `AddContributionRequest.php`
- ❌ Policy: `SavingsGoalPolicy.php`

**Frontend:**
- ❌ Page: `pages/dashboard/savings-goals/index.tsx`
- ❌ Page: `pages/dashboard/savings-goals/create.tsx`
- ❌ Page: `pages/dashboard/savings-goals/edit.tsx`
- ❌ Page: `pages/dashboard/savings-goals/show.tsx`
- ❌ Component: `components/savings/goal-card.tsx` (progress bar circular)
- ❌ Component: `components/savings/goal-form.tsx`
- ❌ Component: `components/savings/contribution-modal.tsx`
- ❌ Component: `components/savings/goal-progress-chart.tsx`
- ❌ Dashboard: Widget de metas ativas

**Rotas:**
- ❌ GET `/dashboard/savings-goals` (middleware: plan.feature:savings_goals)
- ❌ POST `/dashboard/savings-goals`
- ❌ GET `/dashboard/savings-goals/{goal}`
- ❌ PATCH `/dashboard/savings-goals/{goal}`
- ❌ DELETE `/dashboard/savings-goals/{goal}`
- ❌ POST `/dashboard/savings-goals/{goal}/contribute`

**Config:**
- ❌ Adicionar em `plan_limits.php`:
  - Free: `'max_savings_goals' => 0`
  - Premium: `'max_savings_goals' => 20`
  - Family: `'max_savings_goals' => -1`

---

### 4. Relatórios Customizados (0% 🔴)

**Status:** Não existe nenhum arquivo relacionado

**Precisa Criar:**

**Backend:**
- ❌ Migration: `create_saved_reports_table`
- ❌ Model: `SavedReport.php`
- ❌ Enum: `ReportTypeEnum.php` (6 tipos diferentes)
- ❌ Service: `CustomReportService.php`
- ❌ Controller: Expandir `ReportsController.php` com métodos saved
- ❌ Resource: `SavedReportResource.php`
- ❌ Request: `GenerateCustomReportRequest.php`
- ❌ Request: `SaveReportRequest.php`
- ❌ Policy: `SavedReportPolicy.php`

**PDF Export:**
- ❌ Instalar: `composer require barryvdh/laravel-dompdf`
- ❌ Template: `resources/views/reports/pdf/financial-report.blade.php`
- ❌ Template: `resources/views/reports/pdf/cashflow-report.blade.php`
- ❌ Template: `resources/views/reports/pdf/budget-report.blade.php`

**Frontend:**
- ❌ Page: `pages/dashboard/reports/custom.tsx`
- ❌ Page: `pages/dashboard/reports/saved.tsx`
- ❌ Page: `pages/dashboard/reports/builder.tsx`
- ❌ Component: `components/reports/report-builder.tsx`
- ❌ Component: `components/reports/filter-panel.tsx`
- ❌ Component: `components/reports/chart-selector.tsx`
- ❌ Component: `components/reports/saved-report-card.tsx`

**Rotas:**
- ❌ GET `/dashboard/reports/custom` (middleware: plan.feature:custom_reports)
- ❌ POST `/dashboard/reports/custom/generate`
- ❌ POST `/dashboard/reports/saved`
- ❌ GET `/dashboard/reports/saved`
- ❌ GET `/dashboard/reports/saved/{report}`
- ❌ DELETE `/dashboard/reports/saved/{report}`
- ❌ GET `/dashboard/reports/saved/{report}/pdf`

**Config:**
- ❌ Adicionar em `plan_limits.php`:
  - Free: `'max_custom_reports' => 0`
  - Premium: `'max_custom_reports' => 50`
  - Family: `'max_custom_reports' => -1`

---

### 5. Sistema Family (0% 🔴)

**Status:** Não existe nenhum arquivo relacionado

**Precisa Criar:**

**Backend - Migrations:**
- ❌ Migration: `create_family_groups_table`
- ❌ Migration: `create_family_group_members_table`
- ❌ Migration: `create_family_invitations_table`
- ❌ Migration: `add_family_group_id_to_tables` (transactions, wallets, accounts, incomes, budgets, categories, savings_goals)

**Backend - Models:**
- ❌ Model: `FamilyGroup.php`
- ❌ Model: `FamilyGroupMember.php`
- ❌ Model: `FamilyInvitation.php`
- ❌ Enum: `FamilyRoleEnum.php` (ADMIN, MEMBER, VIEWER)
- ❌ Trait: `BelongsToFamilyGroup.php` (para todos os models de recursos)

**Backend - Services:**
- ❌ Service: `FamilyGroupService.php` (create, invite, remove, permissions)
- ❌ Expandir: `DashboardOrchestrationService.php` (dashboard consolidado)

**Backend - Controllers:**
- ❌ Controller: `FamilyGroupsController.php`
- ❌ Controller: `FamilyInvitationsController.php`

**Backend - Policies:**
- ❌ Policy: `FamilyGroupPolicy.php`
- ❌ Atualizar TODAS as policies existentes para considerar family_group_id:
  - TransactionPolicy, WalletPolicy, AccountPolicy, IncomePolicy, BudgetPolicy, CategoryPolicy, SavingsGoalPolicy

**Backend - Middleware:**
- ❌ Middleware: `CheckFamilyAccess.php`

**Backend - Notifications:**
- ❌ Mail: `FamilyInvitationMail.php`
- ❌ Notification: `FamilyMemberJoinedNotification.php`
- ❌ Notification: `FamilyMemberLeftNotification.php`

**Frontend:**
- ❌ Page: `pages/dashboard/family/index.tsx`
- ❌ Page: `pages/dashboard/family/members.tsx`
- ❌ Page: `pages/dashboard/family/settings.tsx`
- ❌ Page: `pages/dashboard/family/dashboard.tsx` (consolidado)
- ❌ Page: `pages/family/invite.tsx` (pública)
- ❌ Component: `components/family/member-card.tsx`
- ❌ Component: `components/family/invite-modal.tsx`
- ❌ Component: `components/family/invite-form.tsx`
- ❌ Component: `components/family/role-selector.tsx`
- ❌ Component: `components/family/consolidated-stats.tsx`
- ❌ Component: `components/family/consolidated-balance.tsx`
- ❌ Component: `components/family/expenses-by-member.tsx`
- ❌ Component: `components/family/family-cashflow-chart.tsx`
- ❌ Component: `components/family/shared-budgets.tsx`
- ❌ Component: `components/family/pending-invitations.tsx`

**Rotas:**
- ❌ GET `/dashboard/family` (middleware: plan.feature:family_members)
- ❌ POST `/dashboard/family`
- ❌ PATCH `/dashboard/family`
- ❌ DELETE `/dashboard/family`
- ❌ GET `/dashboard/family/members`
- ❌ POST `/dashboard/family/invite`
- ❌ DELETE `/dashboard/family/members/{user}`
- ❌ PATCH `/dashboard/family/members/{user}/role`
- ❌ POST `/dashboard/family/leave`
- ❌ GET `/family/invite/{token}` (público)
- ❌ POST `/family/invite/{token}/accept`
- ❌ POST `/family/invite/{token}/decline`
- ❌ DELETE `/dashboard/family/invitations/{invitation}`

---

### 6. Inteligência Artificial (0% 🔴)

**Status:** Não existe nenhum arquivo relacionado

**Precisa Criar:**

**Setup:**
- ❌ Instalar: `composer require openai-php/client`
- ❌ Config: `config/openai.php`
- ❌ Env: `OPENAI_API_KEY`

**Backend - Predictions:**
- ❌ Migration: `create_ai_insights_table`
- ❌ Model: `AIInsight.php`
- ❌ Service: `OpenAIService.php` (client wrapper)
- ❌ Service: `AIFinancialPredictionService.php`
- ❌ Service: `AIInsightsService.php`
- ❌ Controller: `AIInsightsController.php`
- ❌ Expandir: `DashboardController.php` com método predictions()
- ❌ Job: `GenerateMonthlyInsights.php` (roda dia 1)
- ❌ Job: `CheckAIAlerts.php` (roda semanalmente)
- ❌ Resource: `AIInsightResource.php`

**Backend - Alerts:**
- ❌ Expandir: `AlertService.php` com tipo AI_PREDICTION
- ❌ Adicionar métodos: checkAIPredictionAlerts()

**Frontend:**
- ❌ Page: `pages/dashboard/ai/predictions.tsx`
- ❌ Page: `pages/dashboard/ai/insights.tsx`
- ❌ Component: `components/ai/prediction-card.tsx`
- ❌ Component: `components/ai/forecast-chart.tsx`
- ❌ Component: `components/ai/trend-indicator.tsx`
- ❌ Component: `components/ai/insight-card.tsx`
- ❌ Component: `components/ai/insights-list.tsx`
- ❌ Component: `components/ai/insight-notification.tsx`
- ❌ Component: `components/ai/ai-alert-card.tsx`
- ❌ Dashboard: Widget de insights

**Rotas:**
- ❌ GET `/dashboard/ai/predictions` (middleware: plan.feature:ai_predictions)
- ❌ GET `/dashboard/ai/insights`
- ❌ POST `/dashboard/ai/insights/generate`
- ❌ PATCH `/dashboard/ai/insights/{insight}/read`

**Features IA:**
- ❌ Prever gastos do próximo mês
- ❌ Prever por categoria (3 meses)
- ❌ Detectar anomalias de gasto
- ❌ Sugerir meta de economia realista
- ❌ Prever quando atingir savings goal
- ❌ Otimização de orçamento
- ❌ Oportunidades de economia
- ❌ Alertas de tendências
- ❌ Recomendações personalizadas

**Segurança:**
- ❌ Rate limiting
- ❌ Cache de respostas (1 hora)
- ❌ Validar inputs antes de enviar para API
- ❌ Não enviar dados sensíveis

---

### 7. Histórico de Transações Limitado por Plano (0% 🔴)

**Status:** Não existe filtro temporal baseado em plano

**Precisa Criar:**

**Backend:**
- ❌ Scope: `Transaction::scopeWithinHistoryLimit($query, User $user)`
- ❌ Aplicar scope em: TransactionsController@index
- ❌ Aplicar scope em: DashboardController (todos os métodos que buscam transactions)
- ❌ Aplicar scope em: ReportsController (todos os métodos)

**Config:**
- ❌ Adicionar em `plan_limits.php`:
  - Free: `'transactions_history_months' => 12`
  - Premium: `'transactions_history_months' => -1`
  - Family: `'transactions_history_months' => -1`

**Lógica:**
```php
// Em Transaction.php
public function scopeWithinHistoryLimit($query, User $user)
{
    $limits = $user->getPlanLimits();
    $historyLimit = $limits['transactions_history_months'] ?? -1;

    if ($historyLimit === -1) {
        return $query; // Sem limite
    }

    $cutoffDate = now()->subMonths($historyLimit);
    return $query->where('created_at', '>=', $cutoffDate);
}
```

---

### 8. Contador de Exportações Mensais (0% 🔴)

**Status:** Exportação existe mas não há controle de limite mensal

**Precisa Criar:**

**Backend:**
- ❌ Migration: `add_exports_tracking_to_users_table`
  - Campo: `exports_count` (integer, default 0)
  - Campo: `exports_reset_at` (timestamp, nullable)
- ❌ Atualizar: User Model com métodos de exportação
- ❌ Middleware ou Service: Verificar limite antes de exportar
- ❌ Job: `ResetMonthlyExportsCount.php` (roda dia 1 de cada mês)
- ❌ Atualizar: ExportService para incrementar contador

**Config:**
- ❌ Atualizar em `plan_limits.php`:
  - Free: `'max_exports_per_month' => 5`
  - Premium: `'max_exports_per_month' => -1`
  - Family: `'max_exports_per_month' => -1`

**Lógica:**
```php
// Em ExportService.php
public function canExport(User $user): bool
{
    $limits = $user->getPlanLimits();
    $maxExports = $limits['max_exports_per_month'] ?? -1;

    if ($maxExports === -1) {
        return true; // Ilimitado
    }

    // Reset contador se passou o mês
    if ($user->exports_reset_at?->isPast()) {
        $user->update([
            'exports_count' => 0,
            'exports_reset_at' => now()->addMonth(),
        ]);
    }

    return $user->exports_count < $maxExports;
}

public function incrementExportCount(User $user): void
{
    $user->increment('exports_count');

    if (!$user->exports_reset_at) {
        $user->update(['exports_reset_at' => now()->addMonth()]);
    }
}
```

**Schedule:**
```php
// Em routes/console.php
Schedule::job(ResetMonthlyExportsCount::class)->monthly();
```

---

## ✅ CHECKLIST COMPLETO DE IMPLEMENTAÇÃO

### 🔴 FASE 0: CORREÇÕES URGENTES (1-2 dias)

**Prioridade:** CRÍTICA
**Tempo:** 1-2 dias
**Objetivo:** Corrigir configurações e aplicar limites faltantes

#### 0.1 Atualizar Configuração de Limites

- [x] **Arquivo:** `config/plan_limits.php` ✅ **CONCLUÍDO**
  - ✅ Free: 1 carteira, 5 orçamentos, todas features novas adicionadas
  - ✅ Premium: Wallets, Categories e Budgets ilimitados, todas features novas adicionadas
  - ✅ Family: Todas as features configuradas corretamente

#### 0.2 Aplicar Limite em Wallets

- [ ] **Arquivo:** `app/Http/Controllers/Dashboard/WalletController.php`
- [ ] **Método:** `store()`
- [ ] **Adicionar antes de criar wallet:**
  ```php
  $currentCount = $request->user()->wallets()->count();

  if (\App\Http\Middleware\CheckPlanFeature::hasReachedLimit($request, 'max_wallets', $currentCount)) {
      \App\Facades\Toast::error('Você atingiu o limite de carteiras do seu plano.')
          ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
          ->persistent();

      return back();
  }
  ```

#### 0.3 Aplicar Limite em Categories

- [ ] **Arquivo:** `app/Http/Controllers/Dashboard/CategoryController.php`
- [ ] **Método:** `store()`
- [ ] **Adicionar antes de criar categoria:**
  ```php
  $currentCount = $request->user()->categories()->count();

  if (\App\Http\Middleware\CheckPlanFeature::hasReachedLimit($request, 'max_categories', $currentCount)) {
      \App\Facades\Toast::error('Você atingiu o limite de categorias do seu plano.')
          ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
          ->persistent();

      return back();
  }
  ```

#### 0.4 Testar Limites

- [ ] **Criar testes:**
  - `tests/Feature/WalletLimitTest.php`
  - `tests/Feature/CategoryLimitTest.php`
- [ ] **Testar manualmente:**
  - Plano Free: Criar 3 carteiras, tentar 4ª (deve bloquear)
  - Plano Free: Criar 10 categorias, tentar 11ª (deve bloquear)
  - Plano Premium: Criar 50+ carteiras (deve permitir)
  - Plano Family: Criar 100+ categorias (deve permitir)

---

### 🟡 FASE 1: TAGS PERSONALIZADAS (2-3 dias)

**Prioridade:** ALTA
**Tempo:** 2-3 dias
**Objetivo:** Permitir que usuários Premium/Family criem tags personalizadas

#### 1.1 Backend - Database

- [ ] **Criar Migration:** `database/migrations/xxxx_create_tags_table.php`
  ```php
  Schema::create('tags', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->string('name');
      $table->string('color', 7)->default('#3B82F6'); // Hex color
      $table->timestamps();

      $table->unique(['user_id', 'name']); // Evitar duplicatas
      $table->index('user_id');
  });
  ```

- [ ] **Criar Migration:** `database/migrations/xxxx_create_taggables_table.php`
  ```php
  Schema::create('taggables', function (Blueprint $table) {
      $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
      $table->morphs('taggable'); // taggable_id, taggable_type
      $table->timestamps();

      $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
  });
  ```

- [ ] **Rodar migrations:** `php artisan migrate`

#### 1.2 Backend - Models

- [ ] **Criar Model:** `app/Models/Tag.php`
  ```php
  <?php
  namespace App\Models;

  use App\Traits\HasUuidCustom;
  use Illuminate\Database\Eloquent\Model;

  class Tag extends Model
  {
      use HasUuidCustom;

      protected $fillable = ['uuid', 'user_id', 'name', 'color'];

      public function user()
      {
          return $this->belongsTo(User::class);
      }

      public function transactions()
      {
          return $this->morphedByMany(Transaction::class, 'taggable');
      }

      public function accounts()
      {
          return $this->morphedByMany(Account::class, 'taggable');
      }

      public function incomes()
      {
          return $this->morphedByMany(Income::class, 'taggable');
      }

      public function budgets()
      {
          return $this->morphedByMany(Budget::class, 'taggable');
      }
  }
  ```

- [ ] **Criar Trait:** `app/Traits/HasTags.php`
  ```php
  <?php
  namespace App\Traits;

  use App\Models\Tag;

  trait HasTags
  {
      public function tags()
      {
          return $this->morphToMany(Tag::class, 'taggable');
      }

      public function attachTag($tag)
      {
          $this->tags()->attach($tag);
      }

      public function detachTag($tag)
      {
          $this->tags()->detach($tag);
      }

      public function syncTags($tags)
      {
          $this->tags()->sync($tags);
      }
  }
  ```

- [ ] **Adicionar trait em models:**
  - `app/Models/Transaction.php` - adicionar `use HasTags;`
  - `app/Models/Account.php` - adicionar `use HasTags;`
  - `app/Models/Income.php` - adicionar `use HasTags;`
  - `app/Models/Budget.php` - adicionar `use HasTags;`

#### 1.3 Backend - Service

- [ ] **Criar Service:** `app/Domain/Tags/Services/TagService.php`
  ```php
  <?php
  namespace App\Domain\Tags\Services;

  use App\Models\Tag;
  use App\Models\User;

  class TagService
  {
      public function getUserTags(User $user)
      {
          return $user->tags()->orderBy('name')->get();
      }

      public function create(User $user, array $data): Tag
      {
          return $user->tags()->create($data);
      }

      public function update(Tag $tag, array $data): Tag
      {
          $tag->update($data);
          return $tag->fresh();
      }

      public function delete(Tag $tag): bool
      {
          return $tag->delete();
      }
  }
  ```

#### 1.4 Backend - Controller

- [ ] **Criar Controller:** `app/Http/Controllers/Dashboard/TagsController.php`
  ```php
  <?php
  namespace App\Http\Controllers\Dashboard;

  use App\Domain\Tags\Services\TagService;
  use App\Http\Controllers\Controller;
  use App\Models\Tag;
  use Illuminate\Http\Request;
  use Inertia\Inertia;

  class TagsController extends Controller
  {
      public function __construct(protected TagService $tagService) {}

      public function index(Request $request)
      {
          $tags = $this->tagService->getUserTags($request->user());

          return Inertia::render('dashboard/tags/index', [
              'tags' => $tags,
          ]);
      }

      public function store(Request $request)
      {
          $currentCount = $request->user()->tags()->count();

          if (\App\Http\Middleware\CheckPlanFeature::hasReachedLimit($request, 'max_tags', $currentCount)) {
              \App\Facades\Toast::error('Você atingiu o limite de tags do seu plano.')
                  ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
                  ->persistent();

              return back();
          }

          $validated = $request->validate([
              'name' => 'required|string|max:50',
              'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
          ]);

          $this->tagService->create($request->user(), $validated);

          \App\Facades\Toast::success('Tag criada com sucesso!');

          return back();
      }

      public function update(Request $request, Tag $tag)
      {
          $this->authorize('update', $tag);

          $validated = $request->validate([
              'name' => 'required|string|max:50',
              'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
          ]);

          $this->tagService->update($tag, $validated);

          \App\Facades\Toast::success('Tag atualizada com sucesso!');

          return back();
      }

      public function destroy(Tag $tag)
      {
          $this->authorize('delete', $tag);

          $this->tagService->delete($tag);

          \App\Facades\Toast::success('Tag excluída com sucesso!');

          return back();
      }
  }
  ```

#### 1.5 Backend - Policy

- [ ] **Criar Policy:** `app/Policies/TagPolicy.php`
  ```php
  <?php
  namespace App\Policies;

  use App\Models\Tag;
  use App\Models\User;

  class TagPolicy
  {
      public function viewAny(User $user): bool
      {
          return true;
      }

      public function view(User $user, Tag $tag): bool
      {
          return $user->id === $tag->user_id;
      }

      public function create(User $user): bool
      {
          return true;
      }

      public function update(User $user, Tag $tag): bool
      {
          return $user->id === $tag->user_id;
      }

      public function delete(User $user, Tag $tag): bool
      {
          return $user->id === $tag->user_id;
      }
  }
  ```

#### 1.6 Backend - Routes

- [ ] **Adicionar em:** `routes/web.php`
  ```php
  Route::middleware(['auth'])->prefix('dashboard')->group(function () {
      // Tags (Premium/Family)
      Route::middleware('plan.feature:max_tags')->group(function () {
          Route::get('/tags', [TagsController::class, 'index'])->name('dashboard.tags.index');
          Route::post('/tags', [TagsController::class, 'store'])->name('dashboard.tags.store');
          Route::patch('/tags/{tag}', [TagsController::class, 'update'])->name('dashboard.tags.update');
          Route::delete('/tags/{tag}', [TagsController::class, 'destroy'])->name('dashboard.tags.destroy');
      });
  });
  ```

- [ ] **Gerar rotas Wayfinder:** `php artisan wayfinder:generate`

#### 1.7 Frontend - Components

- [ ] **Criar Component:** `resources/js/components/tags/tag-badge.tsx`
  ```typescript
  interface TagBadgeProps {
    name: string;
    color: string;
    onRemove?: () => void;
  }

  export function TagBadge({ name, color, onRemove }: TagBadgeProps) {
    return (
      <span
        className="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium"
        style={{ backgroundColor: color + '20', color: color }}
      >
        {name}
        {onRemove && (
          <button onClick={onRemove} className="hover:opacity-70">
            <X className="h-3 w-3" />
          </button>
        )}
      </span>
    );
  }
  ```

- [ ] **Criar Component:** `resources/js/components/tags/tag-input.tsx` (multi-select com criação inline)

- [ ] **Criar Component:** `resources/js/components/tags/tag-manager.tsx` (CRUD de tags)

#### 1.8 Frontend - Page

- [ ] **Criar Page:** `resources/js/pages/dashboard/tags/index.tsx`

#### 1.9 Atualizar Forms Existentes

- [ ] **Adicionar tag input em:**
  - `resources/js/pages/dashboard/transactions/form.tsx`
  - `resources/js/pages/dashboard/accounts/form.tsx`
  - `resources/js/pages/dashboard/incomes/form.tsx`
  - `resources/js/pages/dashboard/budgets/form.tsx`

#### 1.10 Testes

- [ ] **Criar:** `tests/Feature/TagsTest.php`
- [ ] **Testar:**
  - Criar tag (Premium OK, Free bloqueado)
  - Atualizar tag (apenas owner)
  - Deletar tag
  - Limite de tags por plano
  - Associar tag a transaction/account/income/budget

---

### 🟡 FASE 2: METAS DE ECONOMIA (3-4 dias)

**Prioridade:** ALTA
**Tempo:** 3-4 dias
**Objetivo:** Permitir criação e acompanhamento de metas de economia

#### 2.1 Backend - Database

- [ ] **Criar Migration:** `database/migrations/xxxx_create_savings_goals_table.php`
  ```php
  Schema::create('savings_goals', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
      $table->string('name');
      $table->text('description')->nullable();
      $table->bigInteger('target_amount_cents');
      $table->bigInteger('current_amount_cents')->default(0);
      $table->date('target_date')->nullable();
      $table->string('icon')->default('🎯');
      $table->string('color', 7)->default('#10B981');
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index('user_id');
      $table->index('is_active');
  });
  ```

- [ ] **Rodar migration:** `php artisan migrate`

#### 2.2 Backend - Model

- [ ] **Criar Model:** `app/Models/SavingsGoal.php`
  ```php
  <?php
  namespace App\Models;

  use App\Traits\HasUuidCustom;
  use App\Traits\HasMoneyAccessors;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Casts\Attribute;

  class SavingsGoal extends Model
  {
      use HasUuidCustom, HasMoneyAccessors;

      protected $fillable = [
          'uuid', 'user_id', 'category_id', 'name', 'description',
          'target_amount_cents', 'current_amount_cents', 'target_date',
          'icon', 'color', 'is_active'
      ];

      protected $casts = [
          'target_amount_cents' => 'integer',
          'current_amount_cents' => 'integer',
          'target_date' => 'date',
          'is_active' => 'boolean',
      ];

      // Relationships
      public function user()
      {
          return $this->belongsTo(User::class);
      }

      public function category()
      {
          return $this->belongsTo(Category::class);
      }

      // Accessors
      protected function progressPercentage(): Attribute
      {
          return Attribute::make(
              get: fn () => $this->target_amount_cents > 0
                  ? min(100, round(($this->current_amount_cents / $this->target_amount_cents) * 100, 2))
                  : 0
          );
      }

      protected function remainingAmount(): Attribute
      {
          return Attribute::make(
              get: fn () => max(0, $this->target_amount_cents - $this->current_amount_cents)
          );
      }

      protected function remainingAmountFormatted(): Attribute
      {
          return Attribute::make(
              get: fn () => $this->formatCentsAsBRL($this->remaining_amount)
          );
      }

      protected function daysRemaining(): Attribute
      {
          return Attribute::make(
              get: fn () => $this->target_date ? now()->diffInDays($this->target_date, false) : null
          );
      }

      // Methods
      public function addProgress(int $amountCents): self
      {
          $this->increment('current_amount_cents', $amountCents);

          if ($this->current_amount_cents >= $this->target_amount_cents) {
              $this->complete();
          }

          return $this->fresh();
      }

      public function removeProgress(int $amountCents): self
      {
          $this->decrement('current_amount_cents', $amountCents);
          return $this->fresh();
      }

      public function complete(): self
      {
          $this->update(['is_active' => false]);
          return $this->fresh();
      }

      // Scopes
      public function scopeActive($query)
      {
          return $query->where('is_active', true);
      }

      public function scopeCompleted($query)
      {
          return $query->where('is_active', false);
      }
  }
  ```

#### 2.3 Backend - Service

- [ ] **Criar Service:** `app/Domain/SavingsGoals/Services/SavingsGoalService.php`

#### 2.4 Backend - Controller

- [ ] **Criar Controller:** `app/Http/Controllers/Dashboard/SavingsGoalsController.php` (index, store, show, update, destroy, addContribution)

#### 2.5 Backend - Requests

- [ ] **Criar Request:** `app/Http/Requests/StoreSavingsGoalRequest.php`
- [ ] **Criar Request:** `app/Http/Requests/UpdateSavingsGoalRequest.php`
- [ ] **Criar Request:** `app/Http/Requests/AddContributionRequest.php`

#### 2.6 Backend - Policy

- [ ] **Criar Policy:** `app/Policies/SavingsGoalPolicy.php`

#### 2.7 Backend - Routes

- [ ] **Adicionar em:** `routes/web.php`
  ```php
  Route::middleware('plan.feature:max_savings_goals')->group(function () {
      Route::resource('/savings-goals', SavingsGoalsController::class);
      Route::post('/savings-goals/{savingsGoal}/contribute', [SavingsGoalsController::class, 'addContribution'])
          ->name('dashboard.savings-goals.contribute');
  });
  ```

#### 2.8 Frontend - Components

- [ ] **Criar:** `components/savings/goal-card.tsx` (progress bar circular)
- [ ] **Criar:** `components/savings/goal-form.tsx`
- [ ] **Criar:** `components/savings/contribution-modal.tsx`
- [ ] **Criar:** `components/savings/goal-progress-chart.tsx`

#### 2.9 Frontend - Pages

- [ ] **Criar:** `pages/dashboard/savings-goals/index.tsx`
- [ ] **Criar:** `pages/dashboard/savings-goals/create.tsx`
- [ ] **Criar:** `pages/dashboard/savings-goals/edit.tsx`
- [ ] **Criar:** `pages/dashboard/savings-goals/show.tsx`

#### 2.10 Dashboard Widget

- [ ] **Adicionar widget em:** `pages/dashboard/index.tsx` (mostrar metas ativas com progresso)

#### 2.11 Testes

- [ ] **Criar:** `tests/Feature/SavingsGoalsTest.php`

---

### 🟡 FASE 3: ANEXOS (3-4 dias)

**Prioridade:** ALTA
**Tempo:** 3-4 dias
**Objetivo:** Permitir upload de arquivos (notas fiscais, comprovantes)

#### 3.1 Backend - Database

- [ ] **Criar Migration:** `database/migrations/xxxx_create_attachments_table.php`
- [ ] **Criar Migration:** `database/migrations/xxxx_add_notes_to_transactions_table.php`
- [ ] **Criar Migration:** `database/migrations/xxxx_add_notes_to_accounts_table.php`
- [ ] **Criar Migration:** `database/migrations/xxxx_add_notes_to_incomes_table.php`

#### 3.2 Backend - Storage

- [ ] **Configurar em:** `config/filesystems.php`
  ```php
  'disks' => [
      'attachments' => [
          'driver' => 'local',
          'root' => storage_path('app/attachments'),
          'url' => env('APP_URL').'/storage/attachments',
          'visibility' => 'private',
      ],
  ],
  ```

- [ ] **Criar link simbólico:** `php artisan storage:link`

#### 3.3 Backend - Model

- [ ] **Criar Model:** `app/Models/Attachment.php`
- [ ] **Criar Trait:** `app/Traits/HasAttachments.php`
- [ ] **Adicionar trait em:** Transaction, Account, Income

#### 3.4 Backend - Service

- [ ] **Criar Service:** `app/Domain/Attachments/Services/AttachmentService.php`
  ```php
  public function upload(User $user, $file, $attachable): Attachment
  {
      // Verificar limite do plano
      $currentCount = $user->attachments()->count();
      if (CheckPlanFeature::hasReachedLimit($request, 'max_attachments', $currentCount)) {
          throw new \Exception('Limite de anexos atingido');
      }

      // Validar tipo e tamanho
      $this->validateFile($file);

      // Upload
      $path = $file->store('attachments', 'attachments');

      // Criar registro
      return Attachment::create([
          'user_id' => $user->id,
          'attachable_id' => $attachable->id,
          'attachable_type' => get_class($attachable),
          'original_name' => $file->getClientOriginalName(),
          'file_name' => basename($path),
          'file_path' => $path,
          'mime_type' => $file->getMimeType(),
          'file_size' => $file->getSize(),
      ]);
  }
  ```

#### 3.5 Backend - Controller

- [ ] **Criar Controller:** `app/Http/Controllers/Dashboard/AttachmentsController.php`

#### 3.6 Backend - Validation

- [ ] **Criar Request:** `app/Http/Requests/StoreAttachmentRequest.php`
  ```php
  public function rules()
  {
      return [
          'file' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png', // 5MB
      ];
  }
  ```

#### 3.7 Backend - Policy

- [ ] **Criar Policy:** `app/Policies/AttachmentPolicy.php`

#### 3.8 Backend - Routes

- [ ] **Adicionar em:** `routes/web.php`

#### 3.9 Frontend - Components

- [ ] **Criar:** `components/attachments/file-upload.tsx` (drag & drop, preview, progress)
- [ ] **Criar:** `components/attachments/attachment-list.tsx`
- [ ] **Criar:** `components/attachments/attachment-card.tsx`

#### 3.10 Atualizar Forms

- [ ] **Adicionar upload em:**
  - Transaction form
  - Account form
  - Income form
- [ ] **Adicionar campo notes em:**
  - Transaction form
  - Account form
  - Income form

#### 3.11 Segurança

- [ ] **Implementar:**
  - Validação de ownership no download
  - Sanitização de nomes de arquivos
  - Validação de MIME types
  - Proteção contra directory traversal

#### 3.12 Testes

- [ ] **Criar:** `tests/Feature/AttachmentsTest.php`

---

### 🟢 FASE 4: HISTÓRICO DE TRANSAÇÕES (1 dia)

**Prioridade:** MÉDIA
**Tempo:** 1 dia
**Objetivo:** Limitar acesso a transações antigas baseado no plano

#### 4.1 Backend - Scope

- [ ] **Adicionar em:** `app/Models/Transaction.php`
  ```php
  public function scopeWithinHistoryLimit($query, User $user)
  {
      $limits = $user->getPlanLimits();
      $historyLimit = $limits['transactions_history_months'] ?? -1;

      if ($historyLimit === -1) {
          return $query; // Ilimitado
      }

      $cutoffDate = now()->subMonths($historyLimit);
      return $query->where('created_at', '>=', $cutoffDate);
  }
  ```

#### 4.2 Aplicar Scope nos Controllers

- [ ] **Atualizar:** `app/Http/Controllers/Dashboard/TransactionsController.php@index`
  ```php
  $transactions = QueryBuilder::for(Transaction::class)
      ->where('user_id', auth()->id())
      ->withinHistoryLimit(auth()->user()) // ➕ ADICIONAR
      ->allowedFilters([...])
      ->paginate();
  ```

- [ ] **Atualizar:** `app/Http/Controllers/Dashboard/DashboardController.php` (todos os métodos que buscam transactions)

- [ ] **Atualizar:** `app/Http/Controllers/Dashboard/ReportsController.php` (todos os métodos)

#### 4.3 Frontend - Aviso

- [ ] **Adicionar aviso em:** `pages/dashboard/transactions/index.tsx`
  ```typescript
  {user.currentSubscription?.plan?.slug === 'free' && (
    <Alert>
      <InfoIcon className="h-4 w-4" />
      <AlertDescription>
        Plano Free: Exibindo transações dos últimos 12 meses.
        Faça upgrade para acesso ilimitado.
      </AlertDescription>
    </Alert>
  )}
  ```

#### 4.4 Testes

- [ ] **Criar:** `tests/Feature/TransactionHistoryLimitTest.php`
- [ ] **Testar:**
  - Free: Apenas 12 meses visíveis
  - Premium: Todas as transações visíveis
  - Family: Todas as transações visíveis

---

### 🟢 FASE 5: CONTADOR DE EXPORTAÇÕES (1 dia)

**Prioridade:** MÉDIA
**Tempo:** 1 dia
**Objetivo:** Limitar exportações mensais para plano Free

#### 5.1 Backend - Migration

- [ ] **Criar Migration:** `database/migrations/xxxx_add_exports_tracking_to_users_table.php`
  ```php
  Schema::table('users', function (Blueprint $table) {
      $table->integer('exports_count')->default(0);
      $table->timestamp('exports_reset_at')->nullable();
  });
  ```

- [ ] **Rodar:** `php artisan migrate`

#### 5.2 Backend - User Model

- [ ] **Adicionar métodos em:** `app/Models/User.php`
  ```php
  public function canExport(): bool
  {
      $limits = $this->getPlanLimits();
      $maxExports = $limits['max_exports_per_month'] ?? -1;

      if ($maxExports === -1) {
          return true; // Ilimitado
      }

      // Reset contador se passou o mês
      if ($this->exports_reset_at && $this->exports_reset_at->isPast()) {
          $this->resetExportsCount();
      }

      return $this->exports_count < $maxExports;
  }

  public function incrementExportCount(): void
  {
      $this->increment('exports_count');

      if (!$this->exports_reset_at) {
          $this->update(['exports_reset_at' => now()->addMonth()]);
      }
  }

  public function resetExportsCount(): void
  {
      $this->update([
          'exports_count' => 0,
          'exports_reset_at' => now()->addMonth(),
      ]);
  }
  ```

#### 5.3 Backend - Atualizar Export Controllers

- [ ] **Atualizar todos os controllers de export:**
  - `app/Http/Controllers/Dashboard/ExportsController.php`
  - Adicionar verificação antes de exportar:
  ```php
  if (!auth()->user()->canExport()) {
      Toast::error('Você atingiu o limite de exportações deste mês.')
          ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
          ->persistent();

      return back();
  }

  // ... exportar ...

  auth()->user()->incrementExportCount();
  ```

#### 5.4 Backend - Job de Reset

- [ ] **Criar Job:** `app/Jobs/ResetMonthlyExportsCount.php`
  ```php
  public function handle()
  {
      User::whereNotNull('exports_reset_at')
          ->where('exports_reset_at', '<=', now())
          ->update([
              'exports_count' => 0,
              'exports_reset_at' => now()->addMonth(),
          ]);
  }
  ```

- [ ] **Agendar em:** `routes/console.php`
  ```php
  Schedule::job(ResetMonthlyExportsCount::class)->monthly();
  ```

#### 5.5 Frontend - Indicador

- [ ] **Adicionar em botões de export:**
  ```typescript
  {user.currentSubscription?.plan?.slug === 'free' && (
    <p className="text-xs text-muted-foreground">
      {user.exports_count}/5 exportações este mês
    </p>
  )}
  ```

#### 5.6 Testes

- [ ] **Criar:** `tests/Feature/ExportsLimitTest.php`

---

### 🟢 FASE 6: RELATÓRIOS CUSTOMIZADOS (4-5 dias)

**Prioridade:** MÉDIA
**Tempo:** 4-5 dias
**Objetivo:** Criar e salvar relatórios personalizados

*(Checklist detalhado similar aos anteriores...)*

---

### 🟡 FASE 7: SISTEMA FAMILY (10-14 dias)

**Prioridade:** ALTA (se oferecer plano Family)
**Tempo:** 10-14 dias
**Objetivo:** Compartilhamento entre múltiplos usuários

*(Checklist detalhado com 50+ itens...)*

---

### 🟢 FASE 8: INTELIGÊNCIA ARTIFICIAL (10-14 dias)

**Prioridade:** MÉDIA (diferencial competitivo)
**Tempo:** 10-14 dias
**Objetivo:** Previsões e insights financeiros

*(Checklist detalhado com integração OpenAI...)*

---

## ⏱️ ESTIMATIVAS DE TEMPO

### Por Fase

| Fase | Nome | Prioridade | Complexidade | Tempo Mínimo | Tempo Máximo | Média |
|------|------|------------|--------------|--------------|--------------|-------|
| 0 | Correções Urgentes | 🔴 CRÍTICA | Baixa | 1 dia | 2 dias | 1.5 dias |
| 1 | Tags Personalizadas | 🟡 ALTA | Baixa | 2 dias | 3 dias | 2.5 dias |
| 2 | Metas de Economia | 🟡 ALTA | Média | 3 dias | 4 dias | 3.5 dias |
| 3 | Anexos | 🟡 ALTA | Média | 3 dias | 4 dias | 3.5 dias |
| 4 | Histórico Limitado | 🟢 MÉDIA | Baixa | 0.5 dia | 1 dia | 0.75 dia |
| 5 | Contador de Exports | 🟢 MÉDIA | Baixa | 0.5 dia | 1 dia | 0.75 dia |
| 6 | Relatórios Customizados | 🟢 MÉDIA | Média | 4 dias | 5 dias | 4.5 dias |
| 7 | Sistema Family | 🟡 ALTA | Muito Alta | 10 dias | 14 dias | 12 dias |
| 8 | Inteligência Artificial | 🟢 MÉDIA | Alta | 10 dias | 14 dias | 12 dias |
| **TOTAL** | | | | **34 dias** | **48 dias** | **41 dias** |

### Por Prioridade

| Prioridade | Total de Fases | Tempo Mínimo | Tempo Máximo | Média |
|------------|----------------|--------------|--------------|-------|
| 🔴 CRÍTICA | 1 | 1 dia | 2 dias | 1.5 dias |
| 🟡 ALTA | 4 | 18 dias | 25 dias | 21.5 dias |
| 🟢 MÉDIA | 4 | 15 dias | 21 dias | 18 dias |

---

## 🎯 ORDEM DE IMPLEMENTAÇÃO RECOMENDADA

### Caminho Crítico (MVP Premium)

Para ter um sistema premium funcional o mais rápido possível:

1. **Semana 1-2:**
   - ✅ Fase 0: Correções Urgentes (1-2 dias)
   - ✅ Fase 1: Tags Personalizadas (2-3 dias)
   - ✅ Fase 2: Metas de Economia (3-4 dias)
   - ✅ Fase 4: Histórico Limitado (1 dia)

   **Resultado:** Premium já tem valor (tags + metas + histórico ilimitado)

2. **Semana 3:**
   - ✅ Fase 3: Anexos (3-4 dias)
   - ✅ Fase 5: Contador de Exports (1 dia)

   **Resultado:** Premium 100% funcional conforme especificação

3. **Semana 4-5:**
   - ✅ Fase 6: Relatórios Customizados (4-5 dias)

   **Resultado:** Premium com todas as features principais

4. **Semana 6-8:**
   - ✅ Fase 7: Sistema Family (10-14 dias)

   **Resultado:** Plano Family disponível

5. **Semana 9-11:**
   - ✅ Fase 8: Inteligência Artificial (10-14 dias)

   **Resultado:** Diferencial competitivo com IA

### Caminho Alternativo (Focado em Revenue)

Se priorizar features que geram mais valor percebido:

1. **Fase 0** → **Fase 2** (Metas) → **Fase 1** (Tags) → **Fase 6** (Relatórios) → **Fase 3** (Anexos) → **Fase 4/5** (Limites) → **Fase 8** (IA) → **Fase 7** (Family)

### Caminho Incremental (Entregas Semanais)

Release semanal com valor agregado:

- **Sprint 1:** Fase 0 + Fase 4 + Fase 5 (limites corretos)
- **Sprint 2:** Fase 1 (Tags)
- **Sprint 3:** Fase 2 (Metas)
- **Sprint 4:** Fase 3 (Anexos)
- **Sprint 5-6:** Fase 6 (Relatórios)
- **Sprint 7-9:** Fase 7 (Family)
- **Sprint 10-12:** Fase 8 (IA)

---

## 📈 TRACKING DE PROGRESSO

### Como Usar Este Documento

1. **Marcar checkboxes:** Use `[x]` para marcar itens concluídos
2. **Atualizar percentuais:** Recalcular progresso de cada fase
3. **Documentar blockers:** Anotar problemas encontrados
4. **Estimar real vs planejado:** Comparar tempo gasto vs estimado

### Template de Update Semanal

```markdown
## Update Semanal - [Data]

### Progresso Geral
- Progresso Anterior: X%
- Progresso Atual: Y%
- Delta: +Z%

### Fases Completadas Esta Semana
- [ ] Fase X: Nome

### Em Progresso
- [ ] Fase Y: Nome (70% completo)

### Próxima Semana
- [ ] Iniciar Fase Z
- [ ] Completar Fase Y

### Blockers
- Nenhum / [Descrição do blocker]

### Notas
- [Observações relevantes]
```

---

## 🎯 CRITÉRIOS DE ACEITAÇÃO

### Fase 0 - Correções Urgentes

- [ ] Config de limites atualizado conforme especificação
- [ ] Wallets bloqueados ao atingir limite (Free: 3, Premium: ilimitado)
- [ ] Categories bloqueados ao atingir limite (Free: 10, Premium: ilimitado)
- [ ] Testes passando para todos os limites
- [ ] Toast exibido corretamente ao atingir limite

### Fase 1 - Tags

- [ ] Premium/Family podem criar tags ilimitadas
- [ ] Free vê erro ao tentar criar tag
- [ ] Tags associadas a transactions/accounts/incomes/budgets
- [ ] UI de tags funcional (badge, input, manager)
- [ ] Policy impede acesso a tags de outros usuários
- [ ] 100% dos testes passando

### Fase 2 - Metas de Economia

- [ ] Premium pode criar até 20 metas, Family ilimitado
- [ ] Free vê erro ao tentar criar meta
- [ ] Cálculo de progresso correto (%)
- [ ] Contribuições incrementam progresso
- [ ] Meta completa automaticamente ao atingir 100%
- [ ] Widget no dashboard mostrando metas ativas
- [ ] 100% dos testes passando

*(Continue para cada fase...)*

---

## 📚 RECURSOS E REFERÊNCIAS

### Documentação Oficial

- **Laravel 12:** https://laravel.com/docs/12.x
- **Inertia.js:** https://inertiajs.com/
- **React 19:** https://react.dev/
- **Tailwind CSS v4:** https://tailwindcss.com/
- **Shadcn/ui:** https://ui.shadcn.com/
- **Asaas API:** https://docs.asaas.com/
- **OpenAI API:** https://platform.openai.com/docs/

### Código de Referência

- **PREMIUM_FEATURES_ROADMAP.md:** Roadmap completo de features
- **CLAUDE.md:** Instruções do projeto
- **PRODUCTION_CHECKLIST.md:** Checklist de produção

### Arquivos Chave

- `config/plan_limits.php` - Limites por plano
- `app/Services/PlanLimitService.php` - Service de limites
- `app/Http/Middleware/CheckPlanFeature.php` - Middleware de verificação
- `app/Domain/Subscriptions/Services/SubscriptionService.php` - Lógica de assinatura

---

**Última Atualização:** 2026-01-04
**Próxima Revisão:** Após conclusão de cada fase
**Maintainer:** @melosys-dev
**Status:** 🚧 Em Implementação
