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

### 3. ⏸️ Configurar Webhooks em Produção
**Prioridade**: CRÍTICA
**Status**: PENDENTE

**Problema Atual**:
- Webhooks só funcionam com `SimulateWebhook` command
- Asaas não consegue enviar webhooks para localhost

**Solução para Desenvolvimento**:
- [ ] Instalar e configurar ngrok
- [ ] Configurar URL do ngrok no dashboard Asaas
- [ ] Testar recebimento de webhooks reais

**Solução para Produção**:
- [ ] Configurar domínio público (ex: https://melosys.com.br/webhook/asaas)
- [ ] Certificado SSL válido (HTTPS obrigatório)
- [ ] Configurar URL no dashboard Asaas
- [ ] Testar webhook signature validation
- [ ] Monitorar logs de webhooks

**Segurança**:
- [ ] Verificar signature em todos os webhooks
- [ ] Rate limiting no endpoint de webhook
- [ ] Logs de tentativas de webhook inválidas

---

## 🟡 IMPORTANTE - Recomendado para Produção

### 4. ⏸️ Sistema de Renovação e Falhas de Pagamento
**Prioridade**: IMPORTANTE
**Status**: PENDENTE

**O que implementar**:
- [ ] Webhook handler para `SUBSCRIPTION_PAYMENT_OVERDUE`
- [ ] Lógica para suspender assinatura após X tentativas falhadas
- [ ] Email notificando usuário sobre falha de pagamento
- [ ] Página para atualizar método de pagamento
- [ ] Retry automático de pagamento (se configurado no Asaas)
- [ ] Grace period de 3-7 dias antes de cancelar

**Fluxo de falha**:
1. Pagamento mensal falha
2. Webhook SUBSCRIPTION_PAYMENT_OVERDUE recebido
3. Email enviado ao usuário
4. Status da assinatura: `payment_failed` (novo status)
5. Após 3 dias sem pagamento: suspender acesso
6. Após 7 dias: cancelar assinatura definitivamente

---

### 5. ⏸️ Valor Proporcional (Prorated) em Upgrades
**Prioridade**: IMPORTANTE
**Status**: PENDENTE

**Problema Atual**:
- Upgrade no meio do mês cobra valor cheio do novo plano
- Usuário paga 2x no mesmo mês (plano antigo + plano novo)

**Solução**:
- [ ] Calcular dias restantes do período atual
- [ ] Calcular valor proporcional a cobrar
- [ ] Aplicar crédito do plano anterior
- [ ] Ajustar próxima cobrança

**Exemplo**:
```
Plano atual: R$ 29,90/mês (pago dia 1)
Upgrade dia 15 para R$ 59,90/mês
Dias restantes: 15 dias

Valor a cobrar no upgrade:
- Plano novo (15 dias): R$ 29,95
- Crédito plano antigo (15 dias): -R$ 14,95
- Total a cobrar: R$ 15,00

Próxima cobrança: R$ 59,90 (valor cheio)
```

---

### 6. ⏸️ Notificações por Email
**Prioridade**: IMPORTANTE
**Status**: PENDENTE

**Emails a implementar**:
- [ ] **Pagamento Confirmado**: "Seu pagamento foi aprovado!"
- [ ] **Assinatura Ativada**: "Bem-vindo ao plano Premium!"
- [ ] **Pagamento Falhou**: "Problema com seu pagamento"
- [ ] **Assinatura Expira em X dias**: "Renove sua assinatura"
- [ ] **Assinatura Expirada**: "Sua assinatura foi cancelada"
- [ ] **Upgrade Confirmado**: "Você agora está no plano Family!"
- [ ] **Recibo de Pagamento**: PDF anexo com recibo

**Implementação**:
- [ ] Criar Mailables para cada tipo de email
- [ ] Templates Blade para emails
- [ ] Queue jobs para envio assíncrono
- [ ] Configurar Resend (já configurado no projeto)
- [ ] Testes de envio de email

---

## 🟢 OPCIONAL - Melhorias Futuras

### 7. ⏸️ Painel Administrativo
**Prioridade**: BAIXA
**Status**: PENDENTE

- [ ] Dashboard com métricas de assinaturas
- [ ] Listar todos os pagamentos
- [ ] Cancelar/reembolsar manualmente
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
- 🔄 Fase 2 - Produção: **20%** (2/10 concluídos)

**Total**: 8/16 itens completados (50%)

### Itens Críticos para Produção
- ✅ **1/3** Assinatura recorrente mensal - CONCLUÍDO
- ✅ **2/3** Campo CPF (Progressivo) - CONCLUÍDO
- ⏸️ **0/3** Webhooks em produção - PENDENTE

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
