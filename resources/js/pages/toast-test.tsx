import CustomToast from '@/components/ui/custom-toast';
import { router } from '@inertiajs/react';

interface TestCase {
    title: string;
    description: string;
    action: () => void;
    code: string;
    category: 'basic' | 'builder' | 'advanced' | 'special';
}

export default function ToastTest() {
    const testCases: TestCase[] = [
        // Básicos
        {
            title: 'Success Toast',
            description: 'Toast de sucesso básico',
            category: 'basic',
            action: () => router.post('/toast-test/success'),
            code: `Toast::success('Operação realizada com sucesso!');
return redirect()->back();`,
        },
        {
            title: 'Error Toast',
            description: 'Toast de erro',
            category: 'basic',
            action: () => router.post('/toast-test/error'),
            code: `Toast::error('Erro ao processar a operação.');
return redirect()->back();`,
        },
        {
            title: 'Warning Toast',
            description: 'Toast de aviso',
            category: 'basic',
            action: () => router.post('/toast-test/warning'),
            code: `Toast::warning('Atenção: Verifique os dados antes de continuar.');
return redirect()->back();`,
        },
        {
            title: 'Info Toast',
            description: 'Toast informativo',
            category: 'basic',
            action: () => router.post('/toast-test/info'),
            code: `Toast::info('Informação importante sobre o sistema.');
return redirect()->back();`,
        },
        {
            title: 'Loading Toast',
            description: 'Toast de carregamento',
            category: 'basic',
            action: () => router.post('/toast-test/loading'),
            code: `Toast::loading('Processando dados...');
return redirect()->back();`,
        },

        // Builder Pattern
        {
            title: 'Com Título',
            description: 'Toast com título personalizado',
            category: 'builder',
            action: () => router.post('/toast-test/with-title'),
            code: `Toast::create('Esta é uma mensagem com título personalizado')
    ->title('Título Personalizado')
    ->success()
    ->flash();
return redirect()->back();`,
        },
        {
            title: 'Com Descrição',
            description: 'Toast com título e descrição',
            category: 'builder',
            action: () => router.post('/toast-test/with-description'),
            code: `Toast::create('Mensagem principal')
    ->title('Com Descrição')
    ->description('Esta é uma descrição detalhada do que aconteceu no sistema.')
    ->info()
    ->flash();
return redirect()->back();`,
        },
        {
            title: 'Duração Personalizada',
            description: 'Toast que permanece por 10 segundos',
            category: 'builder',
            action: () => router.post('/toast-test/custom-duration'),
            code: `Toast::create('Esta mensagem desaparece em 10 segundos')
    ->title('Duração Personalizada')
    ->duration(10000)
    ->success()
    ->flash();
return redirect()->back();`,
        },

        // Avançados
        {
            title: 'Toast Persistente',
            description: 'Toast que não desaparece automaticamente',
            category: 'advanced',
            action: () => router.post('/toast-test/persistent'),
            code: `Toast::create('Esta mensagem não desaparece automaticamente')
    ->title('Toast Persistente')
    ->persistent()
    ->info()
    ->flash();
return redirect()->back();`,
        },
        {
            title: 'Não Dismissível',
            description: 'Toast que não pode ser fechado manualmente',
            category: 'advanced',
            action: () => router.post('/toast-test/non-dismissible'),
            code: `Toast::create('Esta mensagem não pode ser fechada manualmente')
    ->title('Não Dismissível')
    ->nonDismissible()
    ->duration(3000)
    ->warning()
    ->flash();
return redirect()->back();`,
        },
        {
            title: 'Com Ações',
            description: 'Toast com botões de ação',
            category: 'advanced',
            action: () => router.post('/toast-test/with-actions'),
            code: `Toast::create('Você tem uma nova notificação')
    ->title('Notificação com Ações')
    ->action('Ver Detalhes', '/toast-test', 'GET')
    ->action('Marcar como Lida', '/toast-test/mark-read', 'POST')
    ->info()
    ->flash();
return redirect()->back();`,
        },
        {
            title: 'Com Progresso',
            description: 'Toast de loading com barra de progresso',
            category: 'advanced',
            action: () => router.post('/toast-test/with-progress'),
            code: `Toast::create('Upload em progresso...')
    ->title('Upload de Arquivo')
    ->data(['progress' => 65])
    ->loading()
    ->flash();
return redirect()->back();`,
        },
        {
            title: 'Exemplo Complexo',
            description: 'Toast com todas as funcionalidades',
            category: 'advanced',
            action: () => router.post('/toast-test/complex'),
            code: `Toast::create('Sistema atualizado com sucesso!')
    ->title('Atualização Completa')
    ->description('Todas as funcionalidades foram atualizadas. Você pode continuar usando o sistema normalmente.')
    ->action('Ver Changelog', '/toast-test', 'GET')
    ->action('Configurações', '/toast-test', 'GET')
    ->data(['version' => '2.1.0', 'features' => ['novo-dashboard', 'melhor-performance']])
    ->success()
    ->flash();
return redirect()->back();`,
        },
        {
            title: 'Com Som',
            description: 'Toast com som de alerta',
            category: 'advanced',
            action: () => router.post('/toast-test/with-sound'),
            code: `Toast::create('Notificação com som de alerta')
    ->title('Toast com Som')
    ->sound('alert')
    ->info()
    ->flash();
return redirect()->back();`,
        },
        {
            title: 'Som Personalizado',
            description: 'Toast com arquivo de som personalizado',
            category: 'advanced',
            action: () => router.post('/toast-test/with-custom-sound'),
            code: `Toast::create('Mensagem de sucesso com som personalizado')
    ->title('Som Personalizado')
    ->sound('success-sound.mp3')
    ->success()
    ->flash();
return redirect()->back();`,
        },

        // Especiais
        {
            title: 'Múltiplos Toasts',
            description: 'Vários toasts ao mesmo tempo',
            category: 'special',
            action: () => router.post('/toast-test/multiple-toasts'),
            code: `Toast::success('Primeiro toast');
Toast::info('Segundo toast');
Toast::warning('Terceiro toast');
return redirect()->back();`,
        },
        {
            title: 'Limpar Toasts',
            description: 'Remove todos os toasts da tela',
            category: 'special',
            action: () => router.post('/toast-test/clear'),
            code: `Toast::clear();
return redirect()->back();`,
        },
    ];

    const categories = {
        basic: 'Toasts Básicos',
        builder: 'Builder Pattern',
        advanced: 'Funcionalidades Avançadas',
        special: 'Funcionalidades Especiais',
    };

    const categoryColors = {
        basic: 'bg-blue-100 text-blue-800 border-blue-200',
        builder: 'bg-green-100 text-green-800 border-green-200',
        advanced: 'bg-purple-100 text-purple-800 border-purple-200',
        special: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    };

    return (
        <div className="min-h-screen bg-gray-50 py-8">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">Sistema de Toast - Página de Teste</h1>
                    <p className="mt-2 text-lg text-gray-600">
                        Teste todas as funcionalidades do sistema de toast. Clique nos botões para executar e veja o código correspondente.
                    </p>
                </div>

                {Object.entries(categories).map(([categoryKey, categoryName]) => {
                    const categoryTests = testCases.filter((test) => test.category === categoryKey);

                    return (
                        <div key={categoryKey} className="mb-12">
                            <h2 className="mb-6 text-2xl font-semibold text-gray-800">{categoryName}</h2>
                            <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                {categoryTests.map((test, index) => (
                                    <div key={index} className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                                        <div className="mb-4 flex items-center justify-between">
                                            <h3 className="text-lg font-medium text-gray-900">{test.title}</h3>
                                            <span className={`rounded-full px-2 py-1 text-xs font-medium ${categoryColors[test.category]}`}>
                                                {test.category}
                                            </span>
                                        </div>

                                        <p className="mb-4 text-sm text-gray-600">{test.description}</p>

                                        <button
                                            onClick={test.action}
                                            className="mb-4 w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                                        >
                                            Executar
                                        </button>

                                        <details className="group">
                                            <summary className="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                                Ver código
                                            </summary>
                                            <div className="mt-2 rounded-md bg-gray-800 p-3">
                                                <pre className="overflow-x-auto text-xs text-gray-300">
                                                    <code>{test.code}</code>
                                                </pre>
                                            </div>
                                        </details>
                                    </div>
                                ))}
                            </div>
                        </div>
                    );
                })}

                <div className="mt-12 rounded-lg border border-gray-200 bg-white p-6">
                    <h3 className="mb-4 text-lg font-medium text-gray-900">Teste de Validação</h3>
                    <p className="mb-4 text-sm text-gray-600">Submeta o formulário em branco para ver os toasts de validação em ação.</p>
                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            router.post('/toast-test/validation', {
                                email: '',
                                name: '',
                            });
                        }}
                        className="space-y-4"
                    >
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                type="email"
                                name="email"
                                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700">Nome</label>
                            <input
                                type="text"
                                name="name"
                                className="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none sm:text-sm"
                            />
                        </div>
                        <button
                            type="submit"
                            className="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700"
                        >
                            Testar Validação (submeter vazio)
                        </button>
                    </form>
                    <details className="mt-4">
                        <summary className="cursor-pointer text-sm font-medium text-indigo-600 hover:text-indigo-800">Ver código</summary>
                        <div className="mt-2 rounded-md bg-gray-800 p-3">
                            <pre className="overflow-x-auto text-xs text-gray-300">
                                <code>{`$request->validate([
    'email' => 'required|email',
    'name' => 'required|min:3'
]);

Toast::validation(['email', 'name']);
return redirect()->back()->withErrors([
    'email' => 'O email é obrigatório.',
    'name' => 'O nome deve ter pelo menos 3 caracteres.'
]);`}</code>
                            </pre>
                        </div>
                    </details>
                </div>
            </div>
            <CustomToast />
        </div>
    );
}
