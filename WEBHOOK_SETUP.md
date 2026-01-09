# 🔔 Configuração de Webhooks Asaas - Guia Completo

**Última atualização**: 2026-01-04
**Versão**: 1.0

---

## 📋 Índice

1. [O que são Webhooks?](#o-que-são-webhooks)
2. [Configuração para Desenvolvimento (Ngrok)](#configuração-para-desenvolvimento-ngrok)
3. [Configuração para Produção](#configuração-para-produção)
4. [Eventos Suportados](#eventos-suportados)
5. [Segurança (Signature Validation)](#segurança-signature-validation)
6. [Testando Webhooks](#testando-webhooks)
7. [Troubleshooting](#troubleshooting)
8. [Logs e Monitoramento](#logs-e-monitoramento)

---

## O que são Webhooks?

Webhooks são notificações automáticas enviadas pelo **Asaas** para o seu servidor quando eventos importantes acontecem:

- ✅ Pagamento confirmado
- ✅ Pagamento recebido
- ✅ Pagamento atrasado
- ✅ Assinatura criada
- ✅ Assinatura cancelada
- ❌ Pagamento falhou

**Fluxo**:
```
Asaas                          Seu Servidor
  |                                  |
  |  1. Pagamento confirmado         |
  |--------------------------------->|
  |  POST /webhook/asaas             |
  |  { event: "PAYMENT_RECEIVED" }   |
  |                                  |
  |  2. Seu servidor processa        |
  |     - Ativa assinatura           |
  |     - Envia email ao usuário     |
  |     - Atualiza banco             |
  |                                  |
  |  3. Responde HTTP 200            |
  |<---------------------------------|
  |  { success: true }               |
```

**Por que webhooks são críticos?**
- Sem webhooks: Pagamentos não são confirmados automaticamente
- Assinaturas ficam pendentes para sempre
- Cobranças mensais não renovam acesso
- Sistema não funciona em produção

---

## Configuração para Desenvolvimento (Ngrok)

### ⚠️ Problema: Localhost não é acessível pela internet

O Asaas precisa **enviar** webhooks para seu servidor, mas `http://localhost:8000` só funciona na sua máquina.

### ✅ Solução: Ngrok (Túnel HTTPS)

Ngrok cria um túnel que expõe seu localhost para a internet:

```
Internet → https://abc123.ngrok.io → Seu Localhost:8000
```

---

### 📦 Passo 1: Instalar Ngrok

#### **macOS (Homebrew)**:
```bash
brew install ngrok/ngrok/ngrok
```

#### **Linux**:
```bash
curl -s https://ngrok-agent.s3.amazonaws.com/ngrok.asc | \
  sudo tee /etc/apt/trusted.gpg.d/ngrok.asc >/dev/null && \
  echo "deb https://ngrok-agent.s3.amazonaws.com buster main" | \
  sudo tee /etc/apt/sources.list.d/ngrok.list && \
  sudo apt update && sudo apt install ngrok
```

#### **Windows**:
Baixe em: https://ngrok.com/download

---

### 🔑 Passo 2: Criar Conta e Autenticar

1. Acesse: https://dashboard.ngrok.com/signup
2. Crie uma conta gratuita
3. Copie seu **authtoken**
4. Configure:

```bash
ngrok config add-authtoken SEU_TOKEN_AQUI
```

---

### 🚀 Passo 3: Iniciar Ngrok

Com seu servidor Laravel rodando em `localhost:8000`:

```bash
ngrok http 8000
```

**Output**:
```
Session Status                online
Account                       seu-email@example.com
Forwarding                    https://abc123.ngrok.io -> http://localhost:8000
```

✅ Copie a URL: `https://abc123.ngrok.io`

---

### ⚙️ Passo 4: Configurar Webhook no Asaas

1. Acesse: https://sandbox.asaas.com (ou https://asaas.com para produção)
2. Login → **Configurações** → **Webhooks**
3. Clique em **"Novo Webhook"**
4. Configure:

| Campo | Valor |
|-------|-------|
| **URL do Webhook** | `https://abc123.ngrok.io/webhook/asaas` |
| **Tipo de Autenticação** | `Token de Acesso` |
| **Token** | Seu `ASAAS_WEBHOOK_TOKEN` do `.env` |
| **Eventos** | Selecione todos (ou específicos) |
| **Versão da API** | `v3` |

5. **Salvar**

---

### ✅ Passo 5: Testar Webhook

#### **Opção A: Gerar Pagamento de Teste**

```bash
# 1. No navegador: Selecione um plano Premium
# 2. Escolha PIX como método
# 3. Na sandbox do Asaas, vá em "Cobranças"
# 4. Encontre a cobrança criada
# 5. Clique em "Marcar como Pago"
# 6. Asaas envia webhook automaticamente
```

#### **Opção B: Usar Comando Simulado**

```bash
php artisan asaas:simulate-webhook 1 PAYMENT_RECEIVED
```

#### **Verificar Logs**:

```bash
tail -f storage/logs/laravel.log | grep -i webhook
```

**Sucesso**:
```
[2026-01-04 11:51:18] local.INFO: Processing Asaas webhook
{"event":"PAYMENT_RECEIVED","payment_id":"pay_..."}

[2026-01-04 11:51:18] local.INFO: Payment received
{"payment_id":1,"subscription_id":2}

[2026-01-04 11:51:18] local.INFO: Subscription activated via webhook
{"subscription_id":2,"user_id":1}
```

---

## Configuração para Produção

### 🌐 Requisitos

1. **Domínio público** com HTTPS (obrigatório)
   - ✅ `https://melosys.com.br/webhook/asaas`
   - ❌ `http://melosys.com.br/webhook/asaas` (HTTP não aceito)

2. **Certificado SSL válido**
   - Let's Encrypt (gratuito)
   - Cloudflare SSL
   - Outro provedor

3. **Servidor acessível** pela internet
   - VPS (DigitalOcean, AWS, etc)
   - Shared hosting com domínio

---

### 📋 Checklist de Produção

#### **1. Verificar .env**

```env
# .env
ASAAS_API_KEY=your_production_api_key_here
ASAAS_ENVIRONMENT=production  # Não "sandbox"!
ASAAS_WEBHOOK_TOKEN=D0huhVms60gJhqIiDV99dYNzr1GBGoAHPszR7aSNlN4=
```

⚠️ **Importante**: Use API Key de **PRODUÇÃO**, não de sandbox!

#### **2. Configurar URL no Asaas (Produção)**

1. Login em: https://www.asaas.com
2. **Configurações** → **Webhooks** → **Novo Webhook**
3. URL: `https://seudominio.com.br/webhook/asaas`
4. Token: Copie de `ASAAS_WEBHOOK_TOKEN`
5. Eventos: Selecione todos
6. **Salvar**

#### **3. Testar em Produção**

```bash
# No servidor de produção
tail -f storage/logs/laravel.log | grep webhook

# Gere uma cobrança real de teste (R$ 1,00)
# Pague via PIX ou cartão de teste
# Verifique se webhook foi recebido
```

#### **4. Monitoramento**

Configure alertas para:
- ❌ Webhook falhou (HTTP 500)
- ❌ Signature inválida
- ✅ Pagamento confirmado
- ✅ Assinatura ativada

---

## Eventos Suportados

### 📨 Eventos de Pagamento Único

| Evento | Quando dispara | Ação do Sistema |
|--------|----------------|-----------------|
| `PAYMENT_CREATED` | Pagamento criado | Log apenas |
| `PAYMENT_UPDATED` | Status mudou | Atualiza status |
| `PAYMENT_CONFIRMED` | Confirmado pelo gateway | Ativa assinatura |
| `PAYMENT_RECEIVED` | Dinheiro recebido | Ativa assinatura |
| `PAYMENT_OVERDUE` | Vencido | Cancela assinatura |
| `PAYMENT_REFUNDED` | Reembolsado | Marca como refunded |
| `PAYMENT_DELETED` | Cancelado | Marca como cancelled |

### 🔄 Eventos de Assinatura Recorrente

| Evento | Quando dispara | Ação do Sistema |
|--------|----------------|-----------------|
| `SUBSCRIPTION_CREATED` | Assinatura criada | Log apenas |
| `SUBSCRIPTION_UPDATED` | Atualizada (upgrade/downgrade) | Log |
| `SUBSCRIPTION_DELETED` | Cancelada no Asaas | Cancela no banco |

**Nota**: Para cobranças mensais recorrentes, o Asaas envia `PAYMENT_RECEIVED` a cada mês quando a cobrança é paga.

---

## Segurança (Signature Validation)

### 🔐 Como Funciona

O Asaas assina cada webhook com HMAC-SHA256:

```
signature = HMAC-SHA256(payload_json, webhook_token)
```

**Seu servidor valida**:
```php
$expectedSignature = hash_hmac('sha256', $payload, $webhookToken);
$isValid = hash_equals($expectedSignature, $receivedSignature);
```

### ✅ Implementação Atual

Arquivo: `app/Domain/Payments/Services/WebhookService.php`

```php
public function verifyWebhookSignature(string $payload, string $signature): bool
{
    $webhookToken = config('asaas.webhook_token');

    if (! $webhookToken) {
        Log::warning('Webhook token not configured');
        return false;
    }

    $expectedSignature = hash_hmac('sha256', $payload, $webhookToken);

    return hash_equals($expectedSignature, $signature);
}
```

### 🧪 Testar Validação

```bash
# Simular webhook COM signature válida
curl -X POST https://seudominio.com/webhook/asaas \
  -H "Content-Type: application/json" \
  -H "Asaas-Signature: SIGNATURE_AQUI" \
  -d '{"event":"PAYMENT_RECEIVED","payment":{"id":"pay_123"}}'

# Simular webhook SEM signature (deve falhar)
curl -X POST https://seudominio.com/webhook/asaas \
  -H "Content-Type: application/json" \
  -d '{"event":"PAYMENT_RECEIVED","payment":{"id":"pay_123"}}'
```

---

## Testando Webhooks

### 🧪 Método 1: Comando Artisan (Local)

```bash
php artisan asaas:simulate-webhook {payment_id} {event}

# Exemplos:
php artisan asaas:simulate-webhook 1 PAYMENT_RECEIVED
php artisan asaas:simulate-webhook 2 PAYMENT_CONFIRMED
php artisan asaas:simulate-webhook 3 PAYMENT_OVERDUE
```

### 🌐 Método 2: Ngrok + Sandbox Asaas

1. **Inicie ngrok**: `ngrok http 8000`
2. **Configure webhook** no Asaas Sandbox
3. **Crie um pagamento** no sistema
4. **No Asaas Sandbox**: Marque como pago
5. **Webhook enviado** automaticamente

### ✅ Método 3: Testes Automatizados

```bash
# Execute testes de webhook
php artisan test --filter=WebhookTest
```

---

## Troubleshooting

### ❌ Problema: "Webhook token not configured"

**Causa**: `ASAAS_WEBHOOK_TOKEN` não está no `.env`

**Solução**:
```bash
# .env
ASAAS_WEBHOOK_TOKEN=D0huhVms60gJhqIiDV99dYNzr1GBGoAHPszR7aSNlN4=
```

---

### ❌ Problema: Webhook não chega no servidor

**Causas possíveis**:
1. Ngrok não está rodando
2. URL incorreta no Asaas
3. Firewall bloqueando
4. Servidor Laravel não está rodando

**Debug**:
```bash
# 1. Verificar se Laravel está rodando
curl http://localhost:8000

# 2. Verificar se ngrok está ativo
curl https://abc123.ngrok.io

# 3. Verificar logs do ngrok
# (Terminal onde ngrok está rodando mostra requests)

# 4. Testar endpoint diretamente
curl -X POST https://abc123.ngrok.io/webhook/asaas \
  -H "Content-Type: application/json" \
  -d '{"event":"PAYMENT_CREATED","payment":{"id":"test"}}'
```

---

### ❌ Problema: "Payment not found for webhook"

**Causa**: Webhook chegou mas payment não existe no banco

**Debug**:
```bash
# Ver logs
tail -f storage/logs/laravel.log | grep "Payment not found"

# Verificar se payment existe
php artisan tinker
>>> Payment::where('external_payment_id', 'pay_123')->first();
```

**Solução**: Certifique-se que o pagamento foi criado ANTES do webhook chegar

---

### ❌ Problema: Signature inválida

**Causas**:
1. Token errado no `.env`
2. Token errado no Asaas dashboard
3. Payload modificado no meio do caminho

**Debug**:
```php
// Adicione em WebhookController:
Log::debug('Webhook signature check', [
    'received_signature' => $request->header('Asaas-Signature'),
    'payload' => $request->getContent(),
    'expected_token' => config('asaas.webhook_token'),
]);
```

---

## Logs e Monitoramento

### 📝 Logs Atuais

Todos os eventos de webhook são logados em `storage/logs/laravel.log`:

**Sucesso**:
```
[INFO] Processing Asaas webhook {"event":"PAYMENT_RECEIVED"}
[INFO] Payment received {"payment_id":1}
[INFO] Subscription activated via webhook {"subscription_id":2}
```

**Erro**:
```
[ERROR] Webhook processing failed {"event":"PAYMENT_RECEIVED","error":"..."}
[WARNING] Payment not found for webhook {"external_payment_id":"pay_123"}
[WARNING] Webhook token not configured
```

### 🔍 Comandos Úteis

```bash
# Ver todos os webhooks
tail -f storage/logs/laravel.log | grep webhook

# Ver apenas erros
tail -f storage/logs/laravel.log | grep -i "ERROR.*webhook"

# Ver pagamentos confirmados
tail -f storage/logs/laravel.log | grep "Payment received"

# Ver assinaturas ativadas
tail -f storage/logs/laravel.log | grep "Subscription activated"
```

### 📊 Monitoramento em Produção

Recomendado:
- **Sentry**: Alertas de erros em tempo real
- **LogRocket**: Replay de sessões de usuário
- **New Relic**: APM e performance
- **Papertrail**: Aggregação de logs

---

## 🎯 Checklist Final

### Desenvolvimento
- [ ] Ngrok instalado e configurado
- [ ] Ngrok rodando (`ngrok http 8000`)
- [ ] URL do ngrok copiada
- [ ] Webhook configurado no Asaas Sandbox
- [ ] Token configurado no `.env`
- [ ] Teste realizado com `asaas:simulate-webhook`
- [ ] Logs verificados (sem erros)

### Produção
- [ ] Domínio com HTTPS configurado
- [ ] Certificado SSL válido
- [ ] API Key de PRODUÇÃO no `.env`
- [ ] `ASAAS_ENVIRONMENT=production`
- [ ] Webhook configurado no Asaas Produção
- [ ] Token de produção configurado
- [ ] Teste realizado com pagamento real
- [ ] Monitoramento configurado (Sentry, logs)
- [ ] Alertas configurados para erros

---

## 📚 Recursos

- **Docs Asaas**: https://docs.asaas.com/docs/webhooks
- **Ngrok Docs**: https://ngrok.com/docs
- **Laravel Logs**: https://laravel.com/docs/logging

---

**Data**: 2026-01-04
**Autor**: Sistema MeloSys
**Versão**: 1.0
