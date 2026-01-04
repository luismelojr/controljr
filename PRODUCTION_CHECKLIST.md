# Checklist de Produção - Sistema de Assinaturas

**Status Atual**: MVP funcional para testes em sandbox
**Objetivo**: Sistema completo pronto para produção

---

## 🔴 CRÍTICO - Obrigatório para Produção

### 1. ✅ Implementar Assinatura Recorrente Mensal

**Prioridade**: CRÍTICA
**Status**: ✅ **CONCLUÍDO**

**Problema Atual**:

- ~~Cada assinatura cria um pagamento único~~
- ~~Usuário precisa pagar manualmente todo mês~~
- ~~Não existe cobrança automática recorrente~~

**Solução Implementada**:

- ✅ Usar `createSubscription()` do Asaas em vez de `createPayment()`
- ✅ Asaas criará cobranças mensais automaticamente
- ✅ Webhooks notificarão sobre cada cobrança mensal

**Arquivos modificados**:

- ✅ `PaymentGatewayService.php` - Método `createRecurringSubscription()` criado
- ✅ `PaymentGatewayService.php` - Método `cancelRecurringSubscription()` criado
- ✅ `SubscriptionPlan.php` - Método `getAmountInReais()` adicionado
- ✅ `WebhookService.php` - Handlers para eventos de subscription adicionados
- ✅ `WebhookEventData.php` - Campo `subscription` adicionado ao DTO
- ✅ `SubscriptionService.php` - Injeção de `PaymentGatewayService` adicionada
- ✅ `SubscriptionService.php` - Cancelamento de subscription recorrente no Asaas

**Eventos Asaas implementados**:

- ✅ `SUBSCRIPTION_CREATED` - Assinatura recorrente criada
- ✅ `SUBSCRIPTION_UPDATED` - Assinatura atualizada
- ✅ `SUBSCRIPTION_DELETED` - Assinatura cancelada

**Como funciona agora**:

1. **Plano FREE**: Cria pagamento único (sem cobrança)
2. **Planos PAGOS** (Premium/Family):
    - Cria assinatura recorrente no Asaas (`cycle: MONTHLY`)
    - Asaas gera cobrança mensal automaticamente
    - Salva `external_subscription_id` na tabela `subscriptions`
    - Webhook `PAYMENT_RECEIVED` ativa a assinatura a cada pagamento
3. **Cancelamento**:
    - Cancela subscription recorrente no Asaas automaticamente
    - Impede cobranças futuras
    - Aplica grace period conforme configurado

**Data de conclusão**: 2026-01-04

---

### 2. ✅ Adicionar Campo CPF na Tabela Users (CPF Progressivo)

**Prioridade**: CRÍTICA
**Status**: ✅ **CONCLUÍDO**

**Problema Atual**:

- ~~Todos os usuários usam o mesmo CPF de teste: `24971563792`~~
- ~~Não é possível usar em produção com clientes reais~~

**Solução Implementada** (Opção C - CPF Progressivo):

- ✅ Migration: Campo `cpf` adicionado (nullable, unique)
- ✅ Validação: Regra `ValidCpf` com algoritmo completo de validação de CPF
- ✅ Backend: `UserProfileController` com endpoint para atualizar CPF
- ✅ PaymentController: Verifica CPF antes de processar pagamento de planos pagos
- ✅ AsaasService: Usa `$user->cpf` real e lança exception se não tiver
- ✅ Frontend: Modal `CpfModal` para coletar CPF quando necessário
- ✅ Frontend: Integração em `payment-method.tsx` com detecção automática

**Como funciona**:

1. **Cadastro inicial**: CPF não é obrigatório (baixa fricção)
2. **Plano FREE**: Não precisa de CPF
3. **Upgrade para Premium/Family**: Modal aparece solicitando CPF
4. **Validação**: CPF é validado (dígitos verificadores + unicidade)
5. **Pagamento**: Apenas processa se tiver CPF válido

**Arquivos modificados**:

- ✅ `database/migrations/2026_01_04_115118_add_cpf_to_users_table.php`
- ✅ `app/Models/User.php` - Campo `cpf` no fillable
- ✅ `app/Rules/ValidCpf.php` - Validação completa de CPF brasileiro
- ✅ `app/Http/Controllers/Dashboard/UserProfileController.php` - CRUD de CPF
- ✅ `app/Http/Controllers/Dashboard/PaymentController.php` - Verificação de CPF
- ✅ `app/Domain/Payments/Services/AsaasService.php` - Usa CPF real
- ✅ `resources/js/components/payment/cpf-modal.tsx` - Modal React
- ✅ `resources/js/pages/dashboard/payment/payment-method.tsx` - Integração
- ✅ `routes/web.php` - Rotas para CPF

**Data de conclusão**: 2026-01-04

---

### 3. ✅ Configurar Webhooks em Produção

**Prioridade**: CRÍTICA
**Status**: ✅ **CONCLUÍDO**

**Problema Atual**:

- ~~Webhooks só funcionam com `SimulateWebhook` command~~
- ~~Asaas não consegue enviar webhooks para localhost~~

**Solução Implementada**:

**✅ Documentação Completa**:

- Guia detalhado criado: `WEBHOOK_SETUP.md`
- Instruções para desenvolvimento (ngrok)
- Instruções para produção (domínio público)
- Troubleshooting completo
- Exemplos de configuração

**✅ Ferramentas de Validação**:

- Comando: `php artisan webhook:validate` - Valida configuração
- Comando: `php artisan webhook:validate --url=https://abc.ngrok.io` - Testa URL externa
- Endpoint: `GET /webhook/health` - Health check público
- Endpoint: `POST /webhook/test` - Teste de webhook (apenas dev)

**✅ Segurança**:

- Webhook signature validation implementada
- HMAC-SHA256 com hash_equals (timing-safe)
- Validação de token configurado
- Logs de tentativas inválidas

**Como usar**:

**Desenvolvimento (Ngrok)**:

```bash
# 1. Iniciar ngrok
ngrok http 8000

# 2. Validar configuração
php artisan webhook:validate --url=https://abc123.ngrok.io

# 3. Configurar no Asaas Sandbox
# URL: https://abc123.ngrok.io/webhook/asaas
# Token: Valor de ASAAS_WEBHOOK_TOKEN

# 4. Testar
php artisan asaas:simulate-webhook 1 PAYMENT_RECEIVED
```

**Produção**:

```bash
# 1. Configurar domínio com HTTPS
# URL: https://seudominio.com.br/webhook/asaas

# 2. Validar
php artisan webhook:validate

# 3. Configurar no Asaas Produção
# Usar API key de produção

# 4. Monitorar
tail -f storage/logs/laravel.log | grep webhook
```

**Arquivos criados**:

- ✅ `WEBHOOK_SETUP.md` - Documentação completa (250+ linhas)
- ✅ `app/Console/Commands/ValidateWebhookSetup.php` - Comando de validação
- ✅ `WebhookController::healthCheck()` - Endpoint de health check
- ✅ `WebhookController::test()` - Endpoint de teste
- ✅ `routes/web.php` - Rotas adicionadas

**Data de conclusão**: 2026-01-04

---

## 🟡 IMPORTANTE - Recomendado para Produção

### 4. ✅ Sistema de Renovação e Falhas de Pagamento

**Prioridade**: IMPORTANTE
**Status**: ✅ **CONCLUÍDO**

**O que foi implementado**:

- ✅ Webhook handler para `PAYMENT_OVERDUE` atualizado
- ✅ Novo status `payment_failed` para assinaturas
- ✅ Grace period configurável (7 dias por padrão)
- ✅ Contagem de tentativas falhadas (`failed_payments_count`)
- ✅ Comando para cancelar assinaturas após grace period expirado
- ✅ Reset automático de falhas quando pagamento é bem sucedido
- ⏸️ Email notificando usuário sobre falha de pagamento (TODO: Item 6)
- ⏸️ Página para atualizar método de pagamento (funcionalidade futura)

**Fluxo implementado**:

1. Pagamento mensal falha
2. Webhook `PAYMENT_OVERDUE` recebido
3. Assinatura marcada como `payment_failed`
4. Grace period de 7 dias iniciado
5. Durante grace period: usuário mantém acesso
6. Após 7 dias: comando `subscriptions:check-grace-periods` cancela assinatura
7. Se pagamento bem sucedido: falhas resetadas, assinatura volta para `active`

**Arquivos modificados/criados**:

- ✅ `database/migrations/2026_01_04_*_add_payment_failure_tracking_to_subscriptions_table.php`
- ✅ `app/Enums/SubscriptionStatusEnum.php` - Status `PAYMENT_FAILED` adicionado
- ✅ `app/Models/Subscription.php` - Métodos de gerenciamento de falhas
- ✅ `app/Domain/Payments/Services/WebhookService.php` - Handler `PAYMENT_OVERDUE` atualizado
- ✅ `app/Console/Commands/CheckExpiredGracePeriods.php` - Comando criado
- ✅ `routes/console.php` - Comando agendado diariamente às 06:00
- ✅ `config/subscriptions.php` - Configurações de grace period e features

**Configuração**:

```bash
# .env (opcional, já tem valores padrão)
SUBSCRIPTION_GRACE_PERIOD_DAYS=7
SUBSCRIPTION_MAX_FAILED_PAYMENTS=3
```

**Comandos**:

```bash
# Verificar grace periods expirados (dry run)
php artisan subscriptions:check-grace-periods --dry-run

# Cancelar assinaturas com grace period expirado
php artisan subscriptions:check-grace-periods
```

**Data de conclusão**: 2026-01-04

---

### 5. ✅ Valor Proporcional (Prorated) em Upgrades

**Prioridade**: IMPORTANTE
**Status**: ✅ **CONCLUÍDO**

**Problema Atual**:

- ~~Upgrade no meio do mês cobra valor cheio do novo plano~~
- ~~Usuário paga 2x no mesmo mês (plano antigo + plano novo)~~

**Solução Implementada**:

- ✅ Cálculo proporcional: (Preço Novo - Preço Antigo) \* (Dias Restantes / 30)
- ✅ Cobrança Híbrida:
    1. Cria pagamento ÚNICO imediato apenas com a diferença (prorated)
    2. Agenda assinatura recorrente do plano novo para o fim do ciclo atual
- ✅ Ciclo de cobrança mantido: Se vence dia 1, continua vencendo dia 1
- ✅ Controller atualizado para detectar upgrade e processar corretamente

**Como funciona**:

1. Usuário clica em Upgrade dia 15 (Ciclo vence dia 1)
2. Sistema cria Assinatura Pendente (Plano Novo)
3. Ao pagar:
    - Cobra proporcional (15 dias de diferença) via PIX/Cartão agora
    - Agenda nova assinatura no Asaas para começar dia 1 do próximo mês
    - Mantém acesso imediato ao plano novo

**Arquivos modificados**:

- ✅ `SubscriptionService.php` - Lógica de cálculo e orquestração
- ✅ `PaymentGatewayService.php` - Método `createUpgradeSubscription` (Híbrido)
- ✅ `AsaasService.php` - Suporte a agendamento (`nextDueDate`)
- ✅ `PaymentController.php` - Detecção de upgrade e chamada correta

**Exemplo**:

```
Plano atual: R$ 29,90/mês (pago dia 1)
Upgrade dia 15 para R$ 59,90/mês
Dias restantes: 15 dias

Valor a cobrar HOJE: R$ 15,00 (diferença proporcional)
Próxima cobrança: R$ 59,90 (No dia 1 do próximo mês)
```

---

### 6. ✅ Notificações por Email

**Prioridade**: IMPORTANTE
**Status**: ✅ **CONCLUÍDO**

**Emails a implementar**:

- [x] **Pagamento Confirmado**: "Seu pagamento foi aprovado!"
- [x] **Assinatura Ativada**: "Bem-vindo ao plano Premium!"
- [x] **Pagamento Falhou**: "Problema com seu pagamento"
- [x] **Assinatura Expira em X dias**: "Renove sua assinatura" (Comando Scheduled)
- [x] **Assinatura Expirada**: "Sua assinatura foi cancelada"
- [x] **Upgrade Confirmado**: "Você agora está no plano Family!"
- [ ] **Recibo de Pagamento**: PDF anexo com recibo (Mover para Item 8 - Faturas em PDF)

**Implementação**:

- [x] Criar Mailables para cada tipo de email
- [x] Templates Blade para emails
- [ ] Queue jobs para envio assíncrono (Usando Sync por enquanto ou Queue padrão)
- [x] Configurar Resend (já configurado no projeto)
- [x] Testes de envio de email

---

## 🟢 OPCIONAL - Melhorias Futuras

### 7. ✅ Painel Administrativo

**Prioridade**: MÉDIA
**Status**: ✅ **CONCLUÍDO**

**Funcionalidades**:

- [x] **Dashboard Admin**: Métricas gerais (MRR, Total de Assinantes)
- [x] **Listagem de Assinaturas**: Ver status, filtrar
- [x] **Listagem de Pagamentos**: Histórico completo
- [x] **Gestão**: Cancelar assinatura manualmente (Admin)
- [ ] Ver detalhes de webhooks recebidos
- [ ] Gráficos de receita mensal
- [ ] Exportar relatórios

---

### 8. ⏸️ Faturas em PDF

**Prioridade**: BAIXA
**Status**: PENDENTE

- [ ] Gerar PDF com dados da cobrança
- [ ] Logo da empresa
- [ ] Dados fiscais (CNPJ, etc)
- [ ] Download de faturas antigas
- [ ] Envio automático por email

---

### 9. ⏸️ Melhorias de Confiabilidade

**Prioridade**: MÉDIA
**Status**: PENDENTE

- [ ] Queue jobs para processar webhooks (em vez de síncrono)
- [ ] Retry logic para webhooks falhados
- [ ] Idempotency keys para evitar duplicação
- [ ] Logs estruturados com contexto
- [ ] Monitoramento com Sentry/Bugsnag
- [ ] Alertas de falhas críticas

---

### 10. ⏸️ Aplicar Middleware de Features

**Prioridade**: BAIXA
**Status**: PENDENTE

**Já existe**:

- `CheckSubscription` middleware
- `CheckPlanFeature` middleware
- `PlanLimitService`

**O que falta**:

- [ ] Definir quais rotas requerem quais features
- [ ] Aplicar middleware nas rotas protegidas
- [ ] Página de "upgrade necessário" quando feature bloqueada
- [ ] Verificações de limite (ex: máximo de transações)

**Exemplo**:

```php
Route::middleware(['auth', 'check.plan.feature:advanced_reports'])
    ->get('/dashboard/reports/advanced', ...);
```

---

## 📊 Progresso Geral

- ✅ Fase 1 - Fundação: **100%** (6/6 completo)
- 🔄 Fase 2 - Produção: **50%** (5/10 concluídos)

**Total**: 11/16 itens completados (68%)

### Itens Críticos para Produção

- ✅ **1/3** Assinatura recorrente mensal - CONCLUÍDO
- ✅ **2/3** Campo CPF (Progressivo) - CONCLUÍDO
- ✅ **3/3** Webhooks em produção - CONCLUÍDO

### Itens Importantes para Produção

- ✅ **1/3** Falhas de pagamento - CONCLUÍDO
- ⏸️ **0/3** Notificações por email - PENDENTE
- ✅ **1/3** Valor proporcional - CONCLUÍDO

---

## 🎯 Ordem de Implementação Recomendada

1. **AGORA**: Assinatura recorrente mensal (Item 1) - EM ANDAMENTO
2. **DEPOIS**: Campo CPF (Item 2)
3. **DEPOIS**: Webhooks em produção (Item 3)
4. **DEPOIS**: Falhas de pagamento (Item 4)
5. **DEPOIS**: Notificações por email (Item 6)
6. **FUTURO**: Valor proporcional (Item 5)
7. **FUTURO**: Melhorias opcionais (Itens 7-10)

---

## 📝 Notas

**Data de criação**: 2026-01-04
**Última atualização**: 2026-01-04
**Versão**: 1.2

**Mudanças na v1.2**:

- ✅ Item 2 (Campo CPF Progressivo) concluído
- Implementado sistema de CPF progressivo (só pede quando necessário)
- Validação completa de CPF brasileiro com dígitos verificadores
- Modal frontend para coleta de CPF antes do pagamento
- AsaasService agora usa CPF real de cada usuário

**Mudanças na v1.1**:

- ✅ Item 1 (Assinatura Recorrente Mensal) concluído
- Adicionado suporte completo para subscriptions recorrentes no Asaas
- Implementado cancelamento automático de subscriptions no Asaas
- Webhooks configurados para eventos de subscription

Este checklist será atualizado conforme o progresso da implementação.
