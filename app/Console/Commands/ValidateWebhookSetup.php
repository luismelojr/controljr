<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ValidateWebhookSetup extends Command
{
    protected $signature = 'webhook:validate {--url= : Test webhook URL (e.g., ngrok URL)}';

    protected $description = 'Validate webhook configuration and connectivity';

    public function handle(): int
    {
        $this->info('🔍 Validating Webhook Configuration...');
        $this->newLine();

        $allPassed = true;

        // 1. Check webhook token
        $allPassed = $this->checkWebhookToken() && $allPassed;

        // 2. Check API key
        $allPassed = $this->checkApiKey() && $allPassed;

        // 3. Check environment
        $allPassed = $this->checkEnvironment() && $allPassed;

        // 4. Test webhook endpoint
        $allPassed = $this->testWebhookEndpoint() && $allPassed;

        // 5. Test webhook with custom URL if provided
        if ($this->option('url')) {
            $allPassed = $this->testExternalWebhook($this->option('url')) && $allPassed;
        }

        $this->newLine();

        if ($allPassed) {
            $this->info('✅ All checks passed! Webhook configuration is valid.');

            return self::SUCCESS;
        }

        $this->error('❌ Some checks failed. Please fix the issues above.');

        return self::FAILURE;
    }

    protected function checkWebhookToken(): bool
    {
        $token = config('asaas.webhook_token');

        if (empty($token)) {
            $this->error('❌ ASAAS_WEBHOOK_TOKEN is not configured in .env');
            $this->line('   Add: ASAAS_WEBHOOK_TOKEN=your_webhook_token_here');

            return false;
        }

        if (strlen($token) < 32) {
            $this->warn('⚠️  Webhook token seems too short (< 32 characters)');
            $this->line('   Consider using a stronger token:');
            $this->line('   openssl rand -base64 32');

            return false;
        }

        $this->info('✅ Webhook token configured ('.strlen($token).' characters)');

        return true;
    }

    protected function checkApiKey(): bool
    {
        $apiKey = config('asaas.api_key');

        if (empty($apiKey)) {
            $this->error('❌ ASAAS_API_KEY is not configured in .env');

            return false;
        }

        $this->info('✅ API key configured');

        return true;
    }

    protected function checkEnvironment(): bool
    {
        $env = config('asaas.environment');

        if (empty($env)) {
            $this->error('❌ ASAAS_ENVIRONMENT is not configured in .env');
            $this->line('   Add: ASAAS_ENVIRONMENT=sandbox (or production)');

            return false;
        }

        if (! in_array($env, ['sandbox', 'production'])) {
            $this->error('❌ Invalid ASAAS_ENVIRONMENT: '.$env);
            $this->line('   Must be: sandbox or production');

            return false;
        }

        if ($env === 'production') {
            $this->warn('⚠️  Running in PRODUCTION mode');
        } else {
            $this->info('✅ Environment: '.$env);
        }

        return true;
    }

    protected function testWebhookEndpoint(): bool
    {
        $this->info('Testing local webhook endpoint...');

        try {
            $url = url('/webhook/health');
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✅ Webhook endpoint is accessible');
                $this->line('   URL: '.$data['webhook_endpoint']);

                return true;
            }

            $this->error('❌ Webhook health check failed (HTTP '.$response->status().')');

            return false;
        } catch (\Exception $e) {
            $this->error('❌ Could not reach webhook endpoint: '.$e->getMessage());
            $this->line('   Make sure Laravel server is running (php artisan serve)');

            return false;
        }
    }

    protected function testExternalWebhook(string $url): bool
    {
        $this->info('Testing external webhook URL...');
        $this->line('   URL: '.$url);

        try {
            // Test health check endpoint
            $healthUrl = rtrim($url, '/').'/webhook/health';
            $response = Http::timeout(10)->get($healthUrl);

            if ($response->successful()) {
                $this->info('✅ External webhook is accessible');
                $data = $response->json();
                $this->line('   Configuration:');
                $this->line('   - Token configured: '.($data['configuration']['webhook_token_configured'] ? 'Yes' : 'No'));
                $this->line('   - API key configured: '.($data['configuration']['api_key_configured'] ? 'Yes' : 'No'));
                $this->line('   - Environment: '.$data['configuration']['environment']);

                return $data['configuration']['webhook_token_configured']
                    && $data['configuration']['api_key_configured'];
            }

            $this->error('❌ External webhook not accessible (HTTP '.$response->status().')');
            $this->line('   Make sure ngrok is running and URL is correct');

            return false;
        } catch (\Exception $e) {
            $this->error('❌ Could not reach external webhook: '.$e->getMessage());
            $this->line('   Check if ngrok is running: ngrok http 8000');

            return false;
        }
    }
}
