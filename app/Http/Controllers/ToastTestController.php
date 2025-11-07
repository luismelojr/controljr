<?php

namespace App\Http\Controllers;

use App\Facades\Toast;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ToastTestController extends Controller
{
    public function index()
    {
        return Inertia::render('toast-test');
    }

    public function success()
    {
        Toast::success('Operação realizada com sucesso!');
        return redirect()->back();
    }

    public function error()
    {
        Toast::error('Erro ao processar a operação.');
        return redirect()->back();
    }

    public function warning()
    {
        Toast::warning('Atenção: Verifique os dados antes de continuar.');
        return redirect()->back();
    }

    public function info()
    {
        Toast::info('Informação importante sobre o sistema.');
        return redirect()->back();
    }

    public function loading()
    {
        Toast::loading('Processando dados...');
        return redirect()->back();
    }

    public function withTitle()
    {
        Toast::create('Esta é uma mensagem com título personalizado')
            ->title('Título Personalizado')
            ->success()
            ->flash();
        return redirect()->back();
    }

    public function withDescription()
    {
        Toast::create('Mensagem principal')
            ->title('Com Descrição')
            ->description('Esta é uma descrição detalhada do que aconteceu no sistema.')
            ->info()
            ->flash();
        return redirect()->back();
    }

    public function persistent()
    {
        Toast::create('Esta mensagem não desaparece automaticamente')
            ->title('Toast Persistente')
            ->persistent()
            ->info()
            ->flash();
        return redirect()->back();
    }

    public function nonDismissible()
    {
        Toast::create('Esta mensagem não pode ser fechada manualmente')
            ->title('Não Dismissível')
            ->nonDismissible()
            ->duration(3000)
            ->warning()
            ->flash();
        return redirect()->back();
    }

    public function withActions()
    {
        Toast::create('Você tem uma nova notificação')
            ->title('Notificação com Ações')
            ->action('Ver Detalhes', '/toast-test', 'GET')
            ->action('Marcar como Lida', '/toast-test/mark-read', 'POST')
            ->info()
            ->flash();
        return redirect()->back();
    }

    public function withProgress()
    {
        Toast::create('Upload em progresso...')
            ->title('Upload de Arquivo')
            ->data(['progress' => 65])
            ->loading()
            ->flash();
        return redirect()->back();
    }

    public function customDuration()
    {
        Toast::create('Esta mensagem desaparece em 10 segundos')
            ->title('Duração Personalizada')
            ->duration(10000)
            ->success()
            ->flash();
        return redirect()->back();
    }

    public function multipleToasts()
    {
        Toast::success('Primeiro toast');
        Toast::info('Segundo toast');
        Toast::warning('Terceiro toast');
        return redirect()->back();
    }

    public function validationExample(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|min:3'
        ]);

        Toast::validation(['email', 'name']);
        return redirect()->back()->withErrors([
            'email' => 'O email é obrigatório.',
            'name' => 'O nome deve ter pelo menos 3 caracteres.'
        ]);
    }

    public function markRead()
    {
        Toast::success('Mensagem marcada como lida!');
        return redirect()->back();
    }

    public function clearToasts()
    {
        Toast::clear();
        return redirect()->back();
    }

    public function complexExample()
    {
        Toast::create('Sistema atualizado com sucesso!')
            ->title('Atualização Completa')
            ->description('Todas as funcionalidades foram atualizadas. Você pode continuar usando o sistema normalmente.')
            ->action('Ver Changelog', '/toast-test', 'GET')
            ->action('Configurações', '/toast-test', 'GET')
            ->data(['version' => '2.1.0', 'features' => ['novo-dashboard', 'melhor-performance']])
            ->success()
            ->flash();
        return redirect()->back();
    }
}
