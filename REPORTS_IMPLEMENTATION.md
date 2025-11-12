# 📊 Sistema de Relatórios - MeloSys

## 📋 Índice
1. [Visão Geral](#visão-geral)
2. [Arquitetura Técnica](#arquitetura-técnica)
3. [Relatórios Disponíveis](#relatórios-disponíveis)
4. [Estrutura de Arquivos](#estrutura-de-arquivos)
5. [Passo a Passo - Backend](#passo-a-passo-backend)
6. [Passo a Passo - Frontend](#passo-a-passo-frontend)
7. [Fluxo de Dados](#fluxo-de-dados)
8. [Exemplos de Uso](#exemplos-de-uso)
9. [Checklist de Implementação](#checklist-de-implementação)

---

## 🎯 Visão Geral

Sistema de relatórios flexível e configurável para o MeloSys que permite:
- **Criar relatórios personalizados** através de um wizard de 4 etapas
- **Aplicar filtros dinâmicos** (período, categorias, carteiras, valores, etc.)
- **Visualizar em múltiplos formatos** (tabela, gráficos de pizza, barras, linhas)
- **Exportar** em PDF, Excel, CSV
- **Salvar configurações** como favoritos para reuso
- **Executar relatórios salvos** com um clique

### Tecnologias Utilizadas
- **Backend**: Laravel 12 + Inertia.js
- **Frontend**: React 19 + TypeScript + Tailwind CSS v4
- **Gráficos**: Recharts (biblioteca React)
- **Exportação**:
  - **Excel/CSV**: Laravel Excel (maatwebsite/excel)
  - **PDF**: barryvdh/laravel-dompdf
- **Cache**: Laravel Cache (10 minutos para performance)

---

## 🏗️ Arquitetura Técnica

### Padrão de Design
- **Domain-Driven Design (DDD)**: Lógica de negócio em `app/Domain/Reports/`
- **DTOs (Data Transfer Objects)**: Transferência type-safe de dados
- **Services**: Lógica de geração de relatórios
- **Resources**: Transformação de dados para frontend
- **Inertia.js**: SPA com SSR, sem necessidade de API JSON

### Fluxo de Navegação
```
┌────────────────────────────────────────────────────────┐
│ 1. Lista de Relatórios Salvos                         │
│    /dashboard/reports                                  │
│    - Relatórios do usuário                            │
│    - Templates pré-configurados                       │
└────────────────┬───────────────────────────────────────┘
                 │
                 │ [Criar Novo]
                 ▼
┌────────────────────────────────────────────────────────┐
│ 2. Report Builder (Wizard 4 Etapas)                   │
│    /dashboard/reports/builder                          │
│                                                         │
│    Etapa 1: Escolher Tipo                             │
│    Etapa 2: Aplicar Filtros                           │
│    Etapa 3: Escolher Visualização                     │
│    Etapa 4: Gerar/Salvar                              │
└────────────────┬───────────────────────────────────────┘
                 │
                 │ [Gerar]
                 ▼
┌────────────────────────────────────────────────────────┐
│ 3. Visualização do Relatório                          │
│    /dashboard/reports/view                             │
│    - Gráficos interativos                             │
│    - Tabelas com dados                                │
│    - Botões de exportação                             │
└────────────────────────────────────────────────────────┘
```

---

## 📊 Relatórios Disponíveis

### 1. Despesas por Categoria
- **Descrição**: Agrupa despesas pagas por categoria
- **Visualizações**: Tabela, Gráfico de Pizza, Gráfico de Barras
- **Filtros**: Período, Categorias, Carteiras, Status, Faixa de Valor
- **Campos**: Categoria, Total, Quantidade, % do Total, Média

### 2. Despesas por Carteira
- **Descrição**: Agrupa despesas pagas por carteira
- **Visualizações**: Tabela, Gráfico de Pizza, Gráfico de Barras
- **Filtros**: Período, Carteiras, Categorias, Status
- **Campos**: Carteira, Total, Quantidade, % do Total

### 3. Evolução de Despesas
- **Descrição**: Mostra evolução de despesas ao longo do tempo
- **Visualizações**: Gráfico de Linhas, Gráfico de Barras, Tabela
- **Filtros**: Período, Granularidade (mensal/semanal/diário), Categorias
- **Campos**: Data, Total, Variação %

### 4. Top Despesas
- **Descrição**: Lista as maiores despesas do período
- **Visualizações**: Tabela, Gráfico de Barras Horizontal
- **Filtros**: Período, Limite (Top 5, 10, 20, 50), Categorias, Carteiras
- **Campos**: Nome, Data, Categoria, Carteira, Valor, Parcela

### 5. Receitas por Categoria
- **Descrição**: Agrupa receitas recebidas por categoria
- **Visualizações**: Tabela, Gráfico de Pizza, Gráfico de Barras
- **Filtros**: Período, Categorias, Status
- **Campos**: Categoria, Total, Quantidade, % do Total

### 6. Cashflow Mensal
- **Descrição**: Comparação entre receitas e despesas mensais
- **Visualizações**: Gráfico de Barras Empilhadas, Gráfico de Linhas, Tabela
- **Filtros**: Período (mínimo 3 meses)
- **Campos**: Mês, Receitas, Despesas, Saldo Líquido

### 7. Desempenho de Carteiras
- **Descrição**: Análise de uso e saldo de cada carteira
- **Visualizações**: Tabela, Cards com KPIs
- **Filtros**: Tipo de carteira, Status
- **Campos**: Nome, Saldo Inicial, Receitas, Despesas, Saldo Final, % Uso Limite (cartões)

### 8. Comparação de Períodos
- **Descrição**: Compara dois períodos diferentes
- **Visualizações**: Tabela comparativa, Gráfico de Barras lado a lado
- **Filtros**: Período 1, Período 2, Categorias
- **Campos**: Métrica, Período 1, Período 2, Variação Absoluta, Variação %

---

## 📁 Estrutura de Arquivos

```
# BACKEND
app/
├── Domain/
│   └── Reports/
│       ├── DTO/
│       │   ├── GenerateReportData.php
│       │   ├── ReportFiltersData.php
│       │   └── SaveReportConfigData.php
│       │
│       ├── Services/
│       │   ├── ReportService.php              # Lógica principal
│       │   ├── ReportBuilderService.php       # Monta queries dinâmicas
│       │   ├── ReportExportService.php        # PDF, Excel, CSV
│       │   └── ReportCacheService.php         # Cache de relatórios
│       │
│       └── Queries/
│           ├── BaseReportQuery.php            # Query base abstrata
│           ├── ExpensesByCategoryQuery.php
│           ├── ExpensesByWalletQuery.php
│           ├── ExpensesEvolutionQuery.php
│           ├── TopExpensesQuery.php
│           ├── IncomesByCategoryQuery.php
│           ├── CashflowMonthlyQuery.php
│           ├── WalletPerformanceQuery.php
│           └── PeriodComparisonQuery.php
│
├── Enums/
│   ├── ReportTypeEnum.php                     # Tipos de relatórios
│   ├── VisualizationTypeEnum.php              # Tipos de visualização
│   └── ExportFormatEnum.php                   # Formatos de exportação
│
├── Http/
│   ├── Controllers/
│   │   └── Dashboard/
│   │       └── ReportsController.php
│   │
│   ├── Requests/
│   │   └── Reports/
│   │       ├── GenerateReportRequest.php
│   │       └── SaveReportRequest.php
│   │
│   └── Resources/
│       ├── ReportResource.php
│       └── SavedReportResource.php
│
└── Models/
    └── SavedReport.php                        # Relatórios salvos

database/
└── migrations/
    └── xxxx_create_saved_reports_table.php

# FRONTEND
resources/js/
├── pages/
│   └── dashboard/
│       └── reports/
│           ├── index.tsx                      # Lista de relatórios
│           ├── builder.tsx                    # Wizard de criação
│           └── view.tsx                       # Visualização
│
├── components/
│   └── reports/
│       ├── wizard/
│       │   ├── step-1-report-type.tsx
│       │   ├── step-2-filters.tsx
│       │   ├── step-3-visualization.tsx
│       │   └── step-4-actions.tsx
│       │
│       ├── filters/
│       │   ├── period-filter.tsx
│       │   ├── category-multi-select-filter.tsx
│       │   ├── wallet-multi-select-filter.tsx
│       │   ├── status-filter.tsx
│       │   └── amount-range-filter.tsx
│       │
│       ├── visualizations/
│       │   ├── report-table.tsx
│       │   ├── pie-chart-view.tsx
│       │   ├── bar-chart-view.tsx
│       │   ├── line-chart-view.tsx
│       │   └── kpi-cards-view.tsx
│       │
│       ├── export/
│       │   └── export-buttons.tsx
│       │
│       └── saved/
│           ├── saved-report-card.tsx
│           └── report-templates.tsx
│
└── types/
    └── reports.d.ts                           # Tipos TypeScript
```

---

## 🔧 Passo a Passo - Backend

### **ETAPA 1: Database e Models**

#### 1.1. Criar Migration para SavedReport
```bash
php artisan make:migration create_saved_reports_table
```

**Campos:**
- `id` (uuid, primary key)
- `user_id` (uuid, foreign key)
- `name` (string, nome do relatório)
- `description` (text, nullable)
- `report_type` (string, enum: expenses_by_category, etc.)
- `filters` (json, filtros aplicados)
- `visualization` (json, configuração de visualização)
- `is_template` (boolean, se é template do sistema)
- `is_favorite` (boolean)
- `last_run_at` (timestamp, nullable)
- `run_count` (integer, quantas vezes foi executado)
- `timestamps`

#### 1.2. Criar Model SavedReport
```bash
php artisan make:model SavedReport
```

**Relacionamentos:**
- `belongsTo(User::class)`

**Casts:**
- `filters` → `array`
- `visualization` → `array`
- `is_template` → `boolean`
- `is_favorite` → `boolean`

**Scopes:**
- `scopeFavorites()` - apenas favoritos
- `scopeUserReports()` - apenas do usuário logado
- `scopeTemplates()` - apenas templates do sistema

---

### **ETAPA 2: Enums**

#### 2.1. Criar ReportTypeEnum
```php
enum ReportTypeEnum: string
{
    case EXPENSES_BY_CATEGORY = 'expenses_by_category';
    case EXPENSES_BY_WALLET = 'expenses_by_wallet';
    case EXPENSES_EVOLUTION = 'expenses_evolution';
    case TOP_EXPENSES = 'top_expenses';
    case INCOMES_BY_CATEGORY = 'incomes_by_category';
    case CASHFLOW_MONTHLY = 'cashflow_monthly';
    case WALLET_PERFORMANCE = 'wallet_performance';
    case PERIOD_COMPARISON = 'period_comparison';

    public function label(): string { /* ... */ }
    public function description(): string { /* ... */ }
    public function icon(): string { /* ... */ }
}
```

#### 2.2. Criar VisualizationTypeEnum
```php
enum VisualizationTypeEnum: string
{
    case TABLE = 'table';
    case PIE_CHART = 'pie_chart';
    case BAR_CHART = 'bar_chart';
    case LINE_CHART = 'line_chart';
    case KPI_CARDS = 'kpi_cards';
}
```

#### 2.3. Criar ExportFormatEnum
```php
enum ExportFormatEnum: string
{
    case PDF = 'pdf';
    case EXCEL = 'excel';
    case CSV = 'csv';
}
```

---

### **ETAPA 3: DTOs**

#### 3.1. GenerateReportData
```php
class GenerateReportData
{
    public function __construct(
        public ReportTypeEnum $reportType,
        public ReportFiltersData $filters,
        public VisualizationTypeEnum $visualizationType,
        public bool $includeChart,
        public bool $includeTable,
    ) {}

    public static function fromRequest(Request $request): self { /* ... */ }
    public static function fromSavedReport(SavedReport $report): self { /* ... */ }
}
```

#### 3.2. ReportFiltersData
```php
class ReportFiltersData
{
    public function __construct(
        public ?string $startDate,
        public ?string $endDate,
        public ?string $periodType, // 'last_month', 'last_3_months', etc.
        public array $categoryIds,
        public array $walletIds,
        public ?array $statuses,
        public ?float $minAmount,
        public ?float $maxAmount,
        public ?int $limit, // Para Top N
        public ?string $groupBy, // Para evolução: 'day', 'week', 'month'
    ) {}

    public static function fromRequest(Request $request): self { /* ... */ }
    public function toArray(): array { /* ... */ }
}
```

#### 3.3. SaveReportConfigData
```php
class SaveReportConfigData
{
    public function __construct(
        public string $name,
        public ?string $description,
        public GenerateReportData $reportConfig,
        public bool $isFavorite,
    ) {}

    public static function fromRequest(Request $request): self { /* ... */ }
}
```

---

### **ETAPA 4: Query Classes (Pattern Strategy)**

#### 4.1. BaseReportQuery (Abstract)
```php
abstract class BaseReportQuery
{
    abstract public function execute(string $userId, ReportFiltersData $filters): array;

    protected function applyPeriodFilter($query, ReportFiltersData $filters) { /* ... */ }
    protected function applyCategoryFilter($query, ReportFiltersData $filters) { /* ... */ }
    protected function applyWalletFilter($query, ReportFiltersData $filters) { /* ... */ }
}
```

#### 4.2. ExpensesByCategoryQuery
```php
class ExpensesByCategoryQuery extends BaseReportQuery
{
    public function execute(string $userId, ReportFiltersData $filters): array
    {
        $query = Transaction::query()
            ->where('user_id', $userId)
            ->where('status', TransactionStatusEnum::PAID)
            ->with('category');

        $this->applyPeriodFilter($query, $filters);
        $this->applyCategoryFilter($query, $filters);
        $this->applyWalletFilter($query, $filters);

        $results = $query->get()
            ->groupBy('category_id')
            ->map(function ($transactions) {
                $total = $transactions->sum(fn($t) => $t->amount);
                $category = $transactions->first()->category;

                return [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'total' => $total,
                    'count' => $transactions->count(),
                    'average' => $total / $transactions->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $grandTotal = $results->sum('total');

        return [
            'data' => $results->map(fn($item) => [
                ...$item,
                'percentage' => $grandTotal > 0 ? ($item['total'] / $grandTotal) * 100 : 0,
            ]),
            'summary' => [
                'total' => $grandTotal,
                'count' => $results->sum('count'),
                'average' => $results->avg('average'),
                'categories_count' => $results->count(),
            ],
        ];
    }
}
```

**Criar classes similares para:**
- `ExpensesByWalletQuery`
- `ExpensesEvolutionQuery`
- `TopExpensesQuery`
- `IncomesByCategoryQuery`
- `CashflowMonthlyQuery`
- `WalletPerformanceQuery`
- `PeriodComparisonQuery`

---

### **ETAPA 5: Services**

#### 5.1. ReportService (Orquestrador Principal)
```php
class ReportService
{
    public function __construct(
        private ReportBuilderService $builderService,
        private ReportCacheService $cacheService,
    ) {}

    /**
     * Gera um relatório com base na configuração
     */
    public function generate(GenerateReportData $data, string $userId): array
    {
        // Tenta buscar do cache
        $cacheKey = $this->cacheService->getCacheKey($data, $userId);

        return Cache::remember($cacheKey, 600, function() use ($data, $userId) {
            // Executa a query apropriada
            $rawData = $this->builderService->executeQuery($data->reportType, $userId, $data->filters);

            // Formata os dados para visualização
            return [
                'report_type' => $data->reportType->value,
                'filters' => $data->filters->toArray(),
                'data' => $rawData['data'],
                'summary' => $rawData['summary'],
                'chart_data' => $this->formatChartData($rawData['data'], $data->visualizationType),
                'generated_at' => now()->toISOString(),
            ];
        });
    }

    /**
     * Salva configuração de relatório
     */
    public function saveConfig(SaveReportConfigData $data, string $userId): SavedReport
    {
        return SavedReport::create([
            'user_id' => $userId,
            'name' => $data->name,
            'description' => $data->description,
            'report_type' => $data->reportConfig->reportType->value,
            'filters' => $data->reportConfig->filters->toArray(),
            'visualization' => [
                'type' => $data->reportConfig->visualizationType->value,
                'include_chart' => $data->reportConfig->includeChart,
                'include_table' => $data->reportConfig->includeTable,
            ],
            'is_favorite' => $data->isFavorite,
        ]);
    }

    /**
     * Executa relatório salvo
     */
    public function runSavedReport(SavedReport $report): array
    {
        $data = GenerateReportData::fromSavedReport($report);

        $result = $this->generate($data, $report->user_id);

        // Atualiza estatísticas
        $report->increment('run_count');
        $report->update(['last_run_at' => now()]);

        return $result;
    }

    /**
     * Formata dados para gráfico
     */
    private function formatChartData(Collection $data, VisualizationTypeEnum $type): array
    {
        return match($type) {
            VisualizationTypeEnum::PIE_CHART => $this->formatPieChartData($data),
            VisualizationTypeEnum::BAR_CHART => $this->formatBarChartData($data),
            VisualizationTypeEnum::LINE_CHART => $this->formatLineChartData($data),
            default => [],
        };
    }
}
```

#### 5.2. ReportBuilderService
```php
class ReportBuilderService
{
    private array $queryMap = [
        'expenses_by_category' => ExpensesByCategoryQuery::class,
        'expenses_by_wallet' => ExpensesByWalletQuery::class,
        'expenses_evolution' => ExpensesEvolutionQuery::class,
        'top_expenses' => TopExpensesQuery::class,
        'incomes_by_category' => IncomesByCategoryQuery::class,
        'cashflow_monthly' => CashflowMonthlyQuery::class,
        'wallet_performance' => WalletPerformanceQuery::class,
        'period_comparison' => PeriodComparisonQuery::class,
    ];

    public function executeQuery(ReportTypeEnum $type, string $userId, ReportFiltersData $filters): array
    {
        $queryClass = $this->queryMap[$type->value] ?? throw new \Exception("Query não encontrada");

        $query = app($queryClass);

        return $query->execute($userId, $filters);
    }
}
```

#### 5.3. ReportExportService
```php
use Spatie\LaravelPdf\Facades\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportService
{
    /**
     * Exporta relatório como PDF usando Spatie Laravel PDF
     */
    public function exportPDF(array $reportData, SavedReport $report): \Spatie\LaravelPdf\PdfBuilder
    {
        return Pdf::view('reports.pdf', [
            'report' => $report,
            'data' => $reportData,
        ])
        ->format('a4')
        ->name("{$report->name}.pdf");
    }

    /**
     * Exporta relatório como Excel usando Laravel Excel
     */
    public function exportExcel(array $reportData, SavedReport $report)
    {
        return Excel::download(
            new ReportExport($reportData, $report),
            "{$report->name}.xlsx",
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Exporta relatório como CSV usando Laravel Excel
     */
    public function exportCSV(array $reportData, SavedReport $report)
    {
        return Excel::download(
            new ReportExport($reportData, $report),
            "{$report->name}.csv",
            \Maatwebsite\Excel\Excel::CSV
        );
    }
}

/**
 * Classe Export para Laravel Excel
 */
class ReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        private array $reportData,
        private SavedReport $report
    ) {}

    public function collection()
    {
        return collect($this->reportData['data']);
    }

    public function headings(): array
    {
        // Retorna cabeçalhos baseados no tipo de relatório
        return array_keys((array) $this->reportData['data'][0]);
    }
}
```

#### 5.4. ReportCacheService
```php
class ReportCacheService
{
    public function getCacheKey(GenerateReportData $data, string $userId): string
    {
        return "report_{$userId}_{$data->reportType->value}_" . md5(json_encode($data->filters->toArray()));
    }

    public function clearUserCache(string $userId): void
    {
        Cache::tags(["reports_user_{$userId}"])->flush();
    }
}
```

---

### **ETAPA 6: Controllers**

#### 6.1. ReportsController
```php
class ReportsController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private ReportExportService $exportService,
    ) {}

    /**
     * Lista relatórios salvos + templates
     */
    public function index(): Response
    {
        $savedReports = auth()->user()->savedReports()
            ->latest()
            ->get();

        $templates = SavedReport::templates()->get();

        return Inertia::render('dashboard/reports/index', [
            'savedReports' => SavedReportResource::collection($savedReports),
            'templates' => SavedReportResource::collection($templates),
        ]);
    }

    /**
     * Mostra wizard de criação
     */
    public function builder(): Response
    {
        // Buscar dados necessários para filtros
        $categories = auth()->user()->categories()->active()->get();
        $wallets = auth()->user()->wallets()->active()->get();

        return Inertia::render('dashboard/reports/builder', [
            'categories' => CategoryResource::collection($categories),
            'wallets' => WalletResource::collection($wallets),
            'reportTypes' => $this->getReportTypes(),
            'visualizationTypes' => $this->getVisualizationTypes(),
        ]);
    }

    /**
     * Gera relatório
     */
    public function generate(GenerateReportRequest $request): Response
    {
        $data = GenerateReportData::fromRequest($request);

        $reportData = $this->reportService->generate($data, auth()->id());

        return Inertia::render('dashboard/reports/view', [
            'report' => $reportData,
            'config' => $request->validated(),
        ]);
    }

    /**
     * Salva configuração de relatório
     */
    public function save(SaveReportRequest $request): RedirectResponse
    {
        $data = SaveReportConfigData::fromRequest($request);

        $savedReport = $this->reportService->saveConfig($data, auth()->id());

        Toast::success('Relatório salvo com sucesso!')->flash();

        return redirect()->route('dashboard.reports.index');
    }

    /**
     * Executa relatório salvo
     */
    public function run(SavedReport $report): Response
    {
        $this->authorize('view', $report);

        $reportData = $this->reportService->runSavedReport($report);

        return Inertia::render('dashboard/reports/view', [
            'report' => $reportData,
            'savedReport' => new SavedReportResource($report),
        ]);
    }

    /**
     * Exporta relatório como PDF
     */
    public function exportPDF(SavedReport $report)
    {
        $this->authorize('view', $report);

        $reportData = $this->reportService->runSavedReport($report);
        $pdf = $this->exportService->exportPDF($reportData, $report);

        return $pdf->download("{$report->name}.pdf");
    }

    /**
     * Exporta relatório como Excel
     */
    public function exportExcel(SavedReport $report)
    {
        $this->authorize('view', $report);

        $reportData = $this->reportService->runSavedReport($report);

        return $this->exportService->exportExcel($reportData, $report);
    }

    /**
     * Exporta relatório como CSV
     */
    public function exportCSV(SavedReport $report)
    {
        $this->authorize('view', $report);

        $reportData = $this->reportService->runSavedReport($report);

        return $this->exportService->exportCSV($reportData, $report);
    }

    /**
     * Deleta relatório salvo
     */
    public function destroy(SavedReport $report): RedirectResponse
    {
        $this->authorize('delete', $report);

        $report->delete();

        Toast::success('Relatório excluído com sucesso!')->flash();

        return redirect()->route('dashboard.reports.index');
    }

    private function getReportTypes(): array
    {
        return collect(ReportTypeEnum::cases())->map(fn($type) => [
            'value' => $type->value,
            'label' => $type->label(),
            'description' => $type->description(),
            'icon' => $type->icon(),
        ])->toArray();
    }

    private function getVisualizationTypes(): array
    {
        return collect(VisualizationTypeEnum::cases())->map(fn($type) => [
            'value' => $type->value,
            'label' => $type->label(),
        ])->toArray();
    }
}
```

---

### **ETAPA 7: Routes**

```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::prefix('dashboard')->as('dashboard.')->group(function () {

        Route::prefix('reports')->as('reports.')->group(function () {
            // Páginas Inertia
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/builder', [ReportsController::class, 'builder'])->name('builder');
            Route::post('/generate', [ReportsController::class, 'generate'])->name('generate');
            Route::post('/save', [ReportsController::class, 'save'])->name('save');
            Route::get('/{report}/run', [ReportsController::class, 'run'])->name('run');
            Route::delete('/{report}', [ReportsController::class, 'destroy'])->name('destroy');

            // Downloads (NÃO são Inertia)
            Route::get('/{report}/export/pdf', [ReportsController::class, 'exportPDF'])->name('export.pdf');
            Route::get('/{report}/export/excel', [ReportsController::class, 'exportExcel'])->name('export.excel');
            Route::get('/{report}/export/csv', [ReportsController::class, 'exportCSV'])->name('export.csv');
        });

    });
});
```

---

## 🎨 Passo a Passo - Frontend

### **ETAPA 1: Types TypeScript**

```typescript
// resources/js/types/reports.d.ts
export type ReportType =
    | 'expenses_by_category'
    | 'expenses_by_wallet'
    | 'expenses_evolution'
    | 'top_expenses'
    | 'incomes_by_category'
    | 'cashflow_monthly'
    | 'wallet_performance'
    | 'period_comparison';

export type VisualizationType =
    | 'table'
    | 'pie_chart'
    | 'bar_chart'
    | 'line_chart'
    | 'kpi_cards';

export interface ReportFilters {
    start_date?: string;
    end_date?: string;
    period_type?: string;
    category_ids?: string[];
    wallet_ids?: string[];
    statuses?: string[];
    min_amount?: number;
    max_amount?: number;
    limit?: number;
    group_by?: 'day' | 'week' | 'month';
}

export interface ReportConfig {
    report_type: ReportType;
    filters: ReportFilters;
    visualization_type: VisualizationType;
    include_chart: boolean;
    include_table: boolean;
}

export interface ReportData {
    report_type: ReportType;
    filters: ReportFilters;
    data: any[];
    summary: {
        total: number;
        count: number;
        average?: number;
    };
    chart_data: any[];
    generated_at: string;
}

export interface SavedReport {
    uuid: string;
    name: string;
    description?: string;
    report_type: ReportType;
    filters: ReportFilters;
    visualization: {
        type: VisualizationType;
        include_chart: boolean;
        include_table: boolean;
    };
    is_template: boolean;
    is_favorite: boolean;
    last_run_at?: string;
    run_count: number;
    created_at: string;
}
```

---

### **ETAPA 2: Páginas Principais**

#### 2.1. reports/index.tsx
```tsx
// Lista de relatórios salvos e templates
export default function ReportsIndex({ savedReports, templates }) {
    return (
        <DashboardLayout title="Relatórios">
            <div className="space-y-6">
                {/* Botão criar novo */}
                <Button asChild>
                    <Link href={route('dashboard.reports.builder')}>
                        + Criar Novo Relatório
                    </Link>
                </Button>

                {/* Templates pré-configurados */}
                <section>
                    <h2>Templates</h2>
                    <div className="grid grid-cols-3 gap-4">
                        {templates.map(template => (
                            <TemplateCard key={template.uuid} template={template} />
                        ))}
                    </div>
                </section>

                {/* Relatórios salvos pelo usuário */}
                <section>
                    <h2>Meus Relatórios</h2>
                    <div className="grid grid-cols-3 gap-4">
                        {savedReports.map(report => (
                            <SavedReportCard key={report.uuid} report={report} />
                        ))}
                    </div>
                </section>
            </div>
        </DashboardLayout>
    );
}
```

#### 2.2. reports/builder.tsx
```tsx
// Wizard de 4 etapas
export default function ReportBuilder({ categories, wallets, reportTypes, visualizationTypes }) {
    const [currentStep, setCurrentStep] = useState(1);
    const [config, setConfig] = useState<Partial<ReportConfig>>({
        include_chart: true,
        include_table: true,
        filters: {},
    });

    const updateConfig = (key: string, value: any) => {
        setConfig(prev => ({ ...prev, [key]: value }));
    };

    const handleGenerate = () => {
        router.post(route('dashboard.reports.generate'), config);
    };

    return (
        <DashboardLayout title="Criar Relatório">
            {/* Stepper visual */}
            <ReportStepper currentStep={currentStep} />

            {/* Conteúdo da etapa */}
            {currentStep === 1 && (
                <Step1ReportType
                    reportTypes={reportTypes}
                    selected={config.report_type}
                    onSelect={(type) => updateConfig('report_type', type)}
                    onNext={() => setCurrentStep(2)}
                />
            )}

            {currentStep === 2 && (
                <Step2Filters
                    reportType={config.report_type}
                    categories={categories}
                    wallets={wallets}
                    filters={config.filters}
                    onChange={(filters) => updateConfig('filters', filters)}
                    onNext={() => setCurrentStep(3)}
                    onBack={() => setCurrentStep(1)}
                />
            )}

            {currentStep === 3 && (
                <Step3Visualization
                    visualizationTypes={visualizationTypes}
                    selected={config.visualization_type}
                    includeChart={config.include_chart}
                    includeTable={config.include_table}
                    onChange={updateConfig}
                    onNext={() => setCurrentStep(4)}
                    onBack={() => setCurrentStep(2)}
                />
            )}

            {currentStep === 4 && (
                <Step4Actions
                    config={config}
                    onGenerate={handleGenerate}
                    onBack={() => setCurrentStep(3)}
                />
            )}
        </DashboardLayout>
    );
}
```

#### 2.3. reports/view.tsx
```tsx
// Visualização do relatório gerado
export default function ReportView({ report, config, savedReport }) {
    return (
        <DashboardLayout title="Relatório">
            <div className="space-y-6">
                {/* Header com info do relatório */}
                <ReportHeader report={report} savedReport={savedReport} />

                {/* Summary cards */}
                <div className="grid grid-cols-4 gap-4">
                    <Card>
                        <CardTitle>Total</CardTitle>
                        <CardContent>{formatCurrency(report.summary.total)}</CardContent>
                    </Card>
                    <Card>
                        <CardTitle>Quantidade</CardTitle>
                        <CardContent>{report.summary.count}</CardContent>
                    </Card>
                    {/* ... */}
                </div>

                {/* Gráfico */}
                {config.include_chart && (
                    <ReportChart
                        type={config.visualization_type}
                        data={report.chart_data}
                    />
                )}

                {/* Tabela */}
                {config.include_table && (
                    <ReportTable data={report.data} />
                )}

                {/* Botões de ação */}
                <ExportButtons
                    reportId={savedReport?.uuid}
                    config={config}
                />
            </div>
        </DashboardLayout>
    );
}
```

---

### **ETAPA 3: Componentes do Wizard**

#### 3.1. Step1ReportType
```tsx
export function Step1ReportType({ reportTypes, selected, onSelect, onNext }) {
    return (
        <div className="space-y-6">
            <h2>Escolha o tipo de relatório</h2>

            <div className="grid grid-cols-2 gap-4">
                {reportTypes.map(type => (
                    <Card
                        key={type.value}
                        className={cn(
                            'cursor-pointer transition-all',
                            selected === type.value && 'ring-2 ring-primary'
                        )}
                        onClick={() => onSelect(type.value)}
                    >
                        <CardHeader>
                            <div className="text-4xl mb-2">{type.icon}</div>
                            <CardTitle>{type.label}</CardTitle>
                            <CardDescription>{type.description}</CardDescription>
                        </CardHeader>
                    </Card>
                ))}
            </div>

            <Button onClick={onNext} disabled={!selected}>
                Próximo
            </Button>
        </div>
    );
}
```

#### 3.2. Step2Filters
```tsx
export function Step2Filters({ reportType, categories, wallets, filters, onChange, onNext, onBack }) {
    // Filtros disponíveis dependem do tipo de relatório
    const availableFilters = getAvailableFilters(reportType);

    return (
        <div className="space-y-6">
            <h2>Aplicar Filtros</h2>

            <div className="grid grid-cols-2 gap-4">
                {/* Período (sempre disponível) */}
                <PeriodFilter
                    value={filters.period_type}
                    startDate={filters.start_date}
                    endDate={filters.end_date}
                    onChange={(value) => onChange({ ...filters, ...value })}
                />

                {/* Categorias (se disponível) */}
                {availableFilters.includes('categories') && (
                    <CategoryMultiSelectFilter
                        options={categories}
                        selected={filters.category_ids || []}
                        onChange={(ids) => onChange({ ...filters, category_ids: ids })}
                    />
                )}

                {/* Carteiras (se disponível) */}
                {availableFilters.includes('wallets') && (
                    <WalletMultiSelectFilter
                        options={wallets}
                        selected={filters.wallet_ids || []}
                        onChange={(ids) => onChange({ ...filters, wallet_ids: ids })}
                    />
                )}

                {/* Faixa de valor (se disponível) */}
                {availableFilters.includes('amount_range') && (
                    <AmountRangeFilter
                        min={filters.min_amount}
                        max={filters.max_amount}
                        onChange={(range) => onChange({ ...filters, ...range })}
                    />
                )}

                {/* Status (se disponível) */}
                {availableFilters.includes('status') && (
                    <StatusFilter
                        selected={filters.statuses || []}
                        onChange={(statuses) => onChange({ ...filters, statuses })}
                    />
                )}
            </div>

            <div className="flex gap-2">
                <Button variant="outline" onClick={onBack}>Voltar</Button>
                <Button onClick={onNext}>Próximo</Button>
            </div>
        </div>
    );
}
```

#### 3.3. Step3Visualization
```tsx
export function Step3Visualization({ visualizationTypes, selected, includeChart, includeTable, onChange, onNext, onBack }) {
    return (
        <div className="space-y-6">
            <h2>Escolha como visualizar</h2>

            <div className="grid grid-cols-3 gap-4">
                {visualizationTypes.map(type => (
                    <Card
                        key={type.value}
                        className={cn(
                            'cursor-pointer',
                            selected === type.value && 'ring-2 ring-primary'
                        )}
                        onClick={() => onChange('visualization_type', type.value)}
                    >
                        <CardHeader>
                            <CardTitle>{type.label}</CardTitle>
                        </CardHeader>
                    </Card>
                ))}
            </div>

            <div className="space-y-2">
                <Checkbox
                    checked={includeChart}
                    onCheckedChange={(checked) => onChange('include_chart', checked)}
                    label="Mostrar gráfico"
                />
                <Checkbox
                    checked={includeTable}
                    onCheckedChange={(checked) => onChange('include_table', checked)}
                    label="Mostrar tabela"
                />
            </div>

            <div className="flex gap-2">
                <Button variant="outline" onClick={onBack}>Voltar</Button>
                <Button onClick={onNext}>Próximo</Button>
            </div>
        </div>
    );
}
```

#### 3.4. Step4Actions
```tsx
export function Step4Actions({ config, onGenerate, onBack }) {
    const [saveName, setSaveName] = useState('');
    const [shouldSave, setShouldSave] = useState(false);

    const handleGenerate = () => {
        if (shouldSave && saveName) {
            router.post(route('dashboard.reports.save'), {
                name: saveName,
                ...config,
            });
        } else {
            onGenerate();
        }
    };

    return (
        <div className="space-y-6">
            <h2>Gerar Relatório</h2>

            {/* Resumo da configuração */}
            <Card>
                <CardHeader>
                    <CardTitle>Resumo</CardTitle>
                </CardHeader>
                <CardContent>
                    <dl>
                        <dt>Tipo:</dt>
                        <dd>{config.report_type}</dd>

                        <dt>Filtros:</dt>
                        <dd>{/* Lista filtros aplicados */}</dd>

                        <dt>Visualização:</dt>
                        <dd>{config.visualization_type}</dd>
                    </dl>
                </CardContent>
            </Card>

            {/* Opção de salvar */}
            <div className="space-y-2">
                <Checkbox
                    checked={shouldSave}
                    onCheckedChange={setShouldSave}
                    label="Salvar configuração como favorito"
                />

                {shouldSave && (
                    <Input
                        placeholder="Nome do relatório"
                        value={saveName}
                        onChange={(e) => setSaveName(e.target.value)}
                    />
                )}
            </div>

            <div className="flex gap-2">
                <Button variant="outline" onClick={onBack}>Voltar</Button>
                <Button onClick={handleGenerate}>
                    {shouldSave ? 'Salvar e Gerar' : 'Gerar Relatório'}
                </Button>
            </div>
        </div>
    );
}
```

---

### **ETAPA 4: Componentes de Visualização**

#### 4.1. PieChartView (usando Recharts)
```tsx
import { PieChart, Pie, Cell, ResponsiveContainer, Legend, Tooltip } from 'recharts';

export function PieChartView({ data }) {
    const COLORS = ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8'];

    return (
        <ResponsiveContainer width="100%" height={400}>
            <PieChart>
                <Pie
                    data={data}
                    cx="50%"
                    cy="50%"
                    labelLine={false}
                    label={(entry) => `${entry.name}: ${entry.percentage.toFixed(1)}%`}
                    outerRadius={120}
                    fill="#8884d8"
                    dataKey="total"
                >
                    {data.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                </Pie>
                <Tooltip />
                <Legend />
            </PieChart>
        </ResponsiveContainer>
    );
}
```

#### 4.2. BarChartView
```tsx
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';

export function BarChartView({ data }) {
    return (
        <ResponsiveContainer width="100%" height={400}>
            <BarChart data={data}>
                <CartesianGrid strokeDasharray="3 3" />
                <XAxis dataKey="name" />
                <YAxis />
                <Tooltip />
                <Legend />
                <Bar dataKey="total" fill="#8884d8" />
            </BarChart>
        </ResponsiveContainer>
    );
}
```

#### 4.3. ReportTable
```tsx
export function ReportTable({ data }) {
    return (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Nome</TableHead>
                    <TableHead>Total</TableHead>
                    <TableHead>Quantidade</TableHead>
                    <TableHead>%</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {data.map((item, index) => (
                    <TableRow key={index}>
                        <TableCell>{item.name}</TableCell>
                        <TableCell>{formatCurrency(item.total)}</TableCell>
                        <TableCell>{item.count}</TableCell>
                        <TableCell>{item.percentage.toFixed(1)}%</TableCell>
                    </TableRow>
                ))}
            </TableBody>
        </Table>
    );
}
```

---

### **ETAPA 5: Botões de Exportação**

```tsx
export function ExportButtons({ reportId, config }) {
    const handleExport = (format: 'pdf' | 'excel' | 'csv') => {
        window.location.href = route(`dashboard.reports.export.${format}`, { report: reportId });
    };

    return (
        <div className="flex gap-2">
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button>
                        <Download className="mr-2 h-4 w-4" />
                        Exportar
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent>
                    <DropdownMenuItem onClick={() => handleExport('pdf')}>
                        📄 PDF
                    </DropdownMenuItem>
                    <DropdownMenuItem onClick={() => handleExport('excel')}>
                        📊 Excel
                    </DropdownMenuItem>
                    <DropdownMenuItem onClick={() => handleExport('csv')}>
                        📋 CSV
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    );
}
```

---

## 🔄 Fluxo de Dados

### **Geração de Relatório:**
```
User (Frontend)
    ↓ Configura wizard (steps 1-4)
    ↓ Clica "Gerar"
    ↓
router.post('reports.generate', config)
    ↓
ReportsController::generate()
    ↓
ReportService::generate()
    ↓
ReportBuilderService::executeQuery()
    ↓
ExpensesByCategoryQuery::execute()
    ↓
[DB Query + Aggregation]
    ↓
Format data for chart
    ↓
Cache result (10 min)
    ↓
Return data via Inertia
    ↓
ReportView.tsx renderiza
```

### **Exportação:**
```
User clica "Exportar PDF"
    ↓
window.location.href = route('reports.export.pdf')
    ↓
ReportsController::exportPDF()
    ↓
ReportExportService::exportPDF()
    ↓
Load view + Generate PDF
    ↓
return $pdf->download()
    ↓
Browser baixa arquivo
```

---

## 📦 Dependências Necessárias

### Backend
```bash
# Exportação Excel/CSV
composer require maatwebsite/excel

# Exportação PDF
composer require spatie/laravel-pdf

# Publicar configurações
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

### Frontend
```bash
# Biblioteca de gráficos
npm install recharts

# Manipulação de datas
npm install date-fns

# Ícones (se não tiver)
npm install lucide-react
```

---

## ✅ Checklist de Implementação

### Backend
- [ ] 1. Criar migration `create_saved_reports_table`
- [ ] 2. Criar model `SavedReport`
- [ ] 3. Criar enums: `ReportTypeEnum`, `VisualizationTypeEnum`, `ExportFormatEnum`
- [ ] 4. Criar DTOs: `GenerateReportData`, `ReportFiltersData`, `SaveReportConfigData`
- [ ] 5. Criar `BaseReportQuery` abstrata
- [ ] 6. Criar queries específicas (8 queries)
- [ ] 7. Criar `ReportService`
- [ ] 8. Criar `ReportBuilderService`
- [ ] 9. Criar `ReportExportService`
- [ ] 10. Criar `ReportCacheService`
- [ ] 11. Criar `ReportsController` com todos os métodos
- [ ] 12. Criar FormRequests: `GenerateReportRequest`, `SaveReportRequest`
- [ ] 13. Criar Resources: `ReportResource`, `SavedReportResource`
- [ ] 14. Criar Policy: `SavedReportPolicy`
- [ ] 15. Adicionar rotas em `web.php`
- [ ] 16. Criar view Blade para PDF: `resources/views/reports/pdf.blade.php`

### Frontend
- [ ] 17. Criar types TypeScript: `reports.d.ts`
- [ ] 18. Criar página `reports/index.tsx`
- [ ] 19. Criar página `reports/builder.tsx`
- [ ] 20. Criar página `reports/view.tsx`
- [ ] 21. Criar componentes wizard (4 steps)
- [ ] 22. Criar componentes de filtros (5 filtros)
- [ ] 23. Criar componentes de visualização (4 tipos)
- [ ] 24. Criar `ReportTable` component
- [ ] 25. Criar `ExportButtons` component
- [ ] 26. Criar `SavedReportCard` component
- [ ] 27. Criar `TemplateCard` component
- [ ] 28. Criar `ReportHeader` component
- [ ] 29. Criar `ReportStepper` component
- [ ] 30. Adicionar link no menu da dashboard

### Testes
- [ ] 31. Testes unitários para cada Query
- [ ] 32. Testes para ReportService
- [ ] 33. Testes para ReportExportService
- [ ] 34. Testes de integração para Controller
- [ ] 35. Testes E2E com Pest/Dusk

### Documentação
- [ ] 36. Atualizar CLAUDE.md com documentação de relatórios
- [ ] 37. Criar seeders com templates de exemplo
- [ ] 38. Criar tutorial/guia do usuário (opcional)

---

## 🎯 Ordem de Implementação Recomendada

### Fase 1: Fundação Backend (Etapas 1-4)
1. **Database & Models** (1-2)
2. **Enums** (3)
3. **DTOs** (4)

### Fase 2: Lógica de Negócio (Etapas 5-8)
4. **Query Classes** (5-6) - Começar com 1 query simples para testar
5. **Services** (7-10)
6. **Requests & Resources** (12-13)
7. **Policy** (14)

### Fase 3: Exposição API (Etapas 9-10)
8. **Controller** (11) - Implementar método por método
9. **Routes** (15)

### Fase 4: Interface Frontend (Etapas 11-16)
10. **Frontend Types** (17)
11. **Frontend Pages** (18-20) - Uma por vez
12. **Frontend Components** (21-29) - Conforme necessidade
13. **Menu Link** (30)

### Fase 5: Exportação (Etapa 17)
14. **View Blade para PDF** (16)
15. **ReportExportService completo** com Spatie PDF

### Fase 6: Qualidade (Etapas 18-20)
16. **Testes** (31-35)
17. **Documentação** (36-38)

---

## ✅ CHECKLIST ORDENADO DE IMPLEMENTAÇÃO

### 📋 FASE 1: PREPARAÇÃO E INSTALAÇÃO

#### Passo 1: Instalar Dependências
```bash
- [x] Executar: composer require maatwebsite/excel
- [x] Executar: composer require spatie/laravel-pdf
- [x] Executar: npm install recharts date-fns
- [x] Publicar configs: php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

---

### 🗄️ FASE 2: DATABASE E MODELS

#### Passo 2: Criar Migration SavedReport
```bash
- [x] Executar: php artisan make:migration create_saved_reports_table
- [x] Definir campos:
      - id (bigint, primary)
      - uuid (uuid, unique)
      - user_id (foreignId)
      - name (string)
      - description (text, nullable)
      - report_type (string)
      - filters (json)
      - visualization (json)
      - is_template (boolean)
      - is_favorite (boolean)
      - last_run_at (timestamp, nullable)
      - run_count (integer, default 0)
      - timestamps
- [x] Adicionar índices: user_id, report_type, is_favorite
- [x] Executar: php artisan migrate
```

#### Passo 3: Criar Model SavedReport
```bash
- [x] Executar: php artisan make:model SavedReport
- [x] Adicionar trait HasUuidCustom
- [x] Definir $fillable
- [x] Definir casts (filters: array, visualization: array, is_template: boolean)
- [x] Adicionar relationship: belongsTo(User::class)
- [x] Criar scopes: scopeFavorites(), scopeUserReports(), scopeTemplates()
- [x] Adicionar método incrementRunCount()
- [x] Adicionar relacionamento no User model
```

---

### 🎯 FASE 3: ENUMS

#### Passo 4: Criar ReportTypeEnum
```bash
- [x] Criar arquivo: app/Enums/ReportTypeEnum.php
- [x] Definir cases (8 tipos de relatórios)
- [x] Criar método label(): string
- [x] Criar método description(): string
- [x] Criar método icon(): string
```

#### Passo 5: Criar VisualizationTypeEnum
```bash
- [x] Criar arquivo: app/Enums/VisualizationTypeEnum.php
- [x] Definir cases: TABLE, PIE_CHART, BAR_CHART, LINE_CHART, KPI_CARDS
- [x] Criar método label(): string
- [x] Criar método icon(): string
```

#### Passo 6: Criar ExportFormatEnum
```bash
- [x] Criar arquivo: app/Enums/ExportFormatEnum.php
- [x] Definir cases: PDF, EXCEL, CSV
- [x] Criar método label(): string
- [x] Criar método mimeType(): string
- [x] Criar método extension(): string
- [x] Criar método icon(): string
```

---

### 📦 FASE 4: DTOs

#### Passo 7: Criar ReportFiltersData
```bash
- [x] Criar arquivo: app/Domain/Reports/DTO/ReportFiltersData.php
- [x] Definir propriedades (startDate, endDate, periodType, categoryIds, etc.)
- [x] Criar método: fromRequest(Request): self
- [x] Criar método: fromArray(array): self
- [x] Criar método: toArray(): array
```

#### Passo 8: Criar GenerateReportData
```bash
- [x] Criar arquivo: app/Domain/Reports/DTO/GenerateReportData.php
- [x] Definir propriedades (reportType, filters, visualizationType, userId)
- [x] Criar método: fromRequest(Request, userId): self
- [x] Criar método: fromSavedReport(SavedReport): self
- [x] Criar método: toArray(): array
```

#### Passo 9: Criar SaveReportConfigData
```bash
- [x] Criar arquivo: app/Domain/Reports/DTO/SaveReportConfigData.php
- [x] Definir propriedades (name, description, reportConfig, isFavorite, isTemplate)
- [x] Criar método: fromRequest(Request, userId): self
- [x] Criar método: toArray(): array
```

---

### 🔍 FASE 5: QUERY CLASSES

#### Passo 10: Criar BaseReportQuery
```bash
- [x] Criar arquivo: app/Domain/Reports/Queries/BaseReportQuery.php
- [x] Definir como abstract class
- [x] Criar método abstrato: execute(string $userId, ReportFiltersData $filters): array
- [x] Criar métodos helper:
      - applyPeriodFilter($query, $filters, $dateColumn)
      - applyCategoryFilter($query, $filters)
      - applyWalletFilter($query, $filters)
      - applyStatusFilter($query, $filters, $statusColumn)
      - applyAmountRangeFilter($query, $filters, $amountColumn)
      - centsToReais($cents)
      - formatNumber($number)
```

#### Passo 11: Criar ExpensesByCategoryQuery
```bash
- [x] Criar arquivo: app/Domain/Reports/Queries/ExpensesByCategoryQuery.php
- [x] Estender BaseReportQuery
- [x] Implementar método execute()
- [x] Agrupar transações por categoria
- [x] Calcular: total, count, average, percentage
- [x] Retornar array com 'data' e 'summary'
```

#### Passo 12: Criar demais Query Classes
```bash
- [x] ExpensesByWalletQuery
- [x] ExpensesEvolutionQuery
- [x] TopExpensesQuery
- [x] CashflowQuery
- [ ] IncomesByCategoryQuery (opcional - pode ser adicionada depois)
- [ ] IncomesByWalletQuery (opcional - pode ser adicionada depois)
- [ ] IncomesEvolutionQuery (opcional - pode ser adicionada depois)
```

---

### ⚙️ FASE 6: SERVICES

#### Passo 13: Criar ReportCacheService
```bash
- [x] Criar arquivo: app/Domain/Reports/Services/ReportCacheService.php
- [x] Criar método: getCacheKey(GenerateReportData, string $userId): string
- [x] Criar métodos: get(), put(), has(), forget()
- [x] Criar método: clearUserCache(string $userId): void
- [x] Criar método: clearAllReportsCache(): void
- [x] Criar método: getCacheTtl(): int
```

#### Passo 14: Criar ReportBuilderService
```bash
- [x] Criar arquivo: app/Domain/Reports/Services/ReportBuilderService.php
- [x] Criar array $queryMap mapeando tipos → classes
- [x] Criar método: executeQuery(ReportTypeEnum, string $userId, ReportFiltersData): array
- [x] Implementar lógica de resolução dinâmica de queries
- [x] Criar método: isSupported(ReportTypeEnum): bool
- [x] Criar método: getSupportedReportTypes(): array
- [x] Criar método: getAvailableVisualizations(ReportTypeEnum): array
```

#### Passo 15: Criar ReportExportService
```bash
- [x] Criar arquivo: app/Domain/Reports/Services/ReportExportService.php
- [x] Criar método: export() com match para diferentes formatos
- [x] Criar método: exportToPdf() usando Spatie PDF
- [x] Criar método: exportToExcel() usando Laravel Excel
- [x] Criar método: exportToCsv() usando Laravel Excel
- [x] Criar método: generateFilename()
- [x] Criar método: getDownloadUrl(string $path): string
- [x] Criar método: deleteExport(string $path): bool
- [x] Criar método: cleanOldExports(): int
```

#### Passo 16: Criar ReportService (ORQUESTRADOR PRINCIPAL)
```bash
- [x] Criar arquivo: app/Domain/Reports/Services/ReportService.php
- [x] Injetar dependências: ReportCacheService, ReportBuilderService, ReportExportService
- [x] Criar método: generateReport(GenerateReportData): array
      - Implementar lógica de cache (10 min)
      - Executar query via ReportBuilderService
      - Adicionar metadata
- [x] Criar método: saveReportConfig(SaveReportConfigData): SavedReport
- [x] Criar método: runSavedReport(SavedReport): array
- [x] Criar método: exportReport(): string
- [x] Criar métodos auxiliares:
      - getUserReports(), getUserFavorites(), getTemplates()
      - updateSavedReport(), deleteSavedReport(), toggleFavorite()
      - clearUserCache(), isReportTypeSupported(), getAvailableVisualizations()
```

---

### 🎫 FASE 7: REQUESTS E RESOURCES

#### Passo 17: Criar FormRequests
```bash
- [x] Executar: php artisan make:request Reports/GenerateReportRequest
      - Validar: report_type (required, enum)
      - Validar: visualization_type (nullable, enum)
      - Validar: filters (start_date, end_date, period_type, category_ids, wallet_ids, min_amount, max_amount, status, limit)
      - Validar: Lógica de datas (start <= end)
      - Validar: Existência de IDs (categories, wallets)
      - Mensagens customizadas em português
- [x] Executar: php artisan make:request Reports/SaveReportRequest
      - Herda de GenerateReportRequest
      - Validar: name (required, max:255)
      - Validar: description (nullable, max:1000)
      - Validar: is_favorite, is_template (boolean)
```

#### Passo 18: Criar Resources
```bash
- [x] Executar: php artisan make:resource SavedReportResource
      - Mapear campos do model para frontend
      - Incluir: uuid, name, description, report_type
      - Incluir: report_type_label, report_type_description, report_type_icon
      - Incluir: filters, visualization (type, label, icon)
      - Incluir: is_template, is_favorite, run_count
      - Incluir: last_run_at (ISO + human readable)
      - Incluir: created_at, updated_at (ISO)
```

#### Passo 19: Criar ReportExport Class
```bash
- [x] Criar: app/Exports/ReportExport.php
- [x] Implementar: FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
- [x] Criar método: collection() - Retorna dados para exportação
- [x] Criar método: headings() - Define cabeçalhos baseado no tipo
- [x] Criar método: map() - Mapeia linhas baseado no tipo
- [x] Criar método: title() - Define nome da planilha
- [x] Criar método: styles() - Aplica estilização (header em negrito)
```

#### Passo 20: Criar Policy (opcional - pode ser feito depois)
```bash
- [ ] Executar: php artisan make:policy SavedReportPolicy --model=SavedReport
- [ ] Implementar: viewAny, view, create, update, delete
- [ ] Garantir que usuário só acessa seus próprios relatórios
```

---

### 🎮 FASE 8: CONTROLLER E ROUTES

#### Passo 20: Criar ReportsController
```bash
- [x] Executar: php artisan make:controller Dashboard/ReportsController
- [x] Injetar: ReportService (via constructor injection)
- [x] Implementar método: index() - Lista relatórios salvos + favoritos + templates
- [x] Implementar método: builder() - Wizard de criação (com categories e wallets)
- [x] Implementar método: generate(GenerateReportRequest) - Gera relatório (retorna JSON)
- [x] Implementar método: store(SaveReportRequest) - Salva configuração
- [x] Implementar método: show(SavedReport) - Mostra relatório salvo
- [x] Implementar método: run(SavedReport) - Executa relatório salvo (retorna JSON)
- [x] Implementar método: update(Request, SavedReport) - Atualiza relatório
- [x] Implementar método: destroy(SavedReport) - Deleta relatório
- [x] Implementar método: toggleFavorite(SavedReport) - Marca/desmarca favorito
- [x] Implementar método: export(SavedReport) - Exporta em PDF/Excel/CSV (único método)
- [x] Criar métodos helper: getReportTypes(), getVisualizationTypes()
- [x] Adicionar verificações de ownership (403 se não for dono)
- [x] Adicionar tratamento de exceções com Toast
```

#### Passo 21: Adicionar Routes
```bash
- [x] Editar: routes/web.php
- [x] Criar grupo: /dashboard/reports com prefix e name
- [x] Adicionar rotas:
      - GET    /                      → index (lista relatórios)
      - GET    /builder               → builder (wizard)
      - POST   /generate              → generate (preview)
      - POST   /save                  → store (salvar config)
      - GET    /{report:uuid}         → show (visualizar salvo)
      - POST   /{report:uuid}/run     → run (executar salvo)
      - PATCH  /{report:uuid}         → update (atualizar)
      - DELETE /{report:uuid}         → destroy (deletar)
      - POST   /{report:uuid}/favorite → toggleFavorite (favoritar)
      - GET    /{report:uuid}/export  → export (download com ?format=pdf|excel|csv)
- [x] Usar route model binding com uuid
```

---

### 🎨 FASE 9: FRONTEND - TYPES

#### Passo 22: Criar Types TypeScript
```bash
- [x] Criar arquivo: resources/js/types/reports.d.ts
- [x] Definir type: ReportType (union de strings)
- [x] Definir type: VisualizationType
- [x] Definir interface: ReportFilters
- [x] Definir interface: ReportConfig
- [x] Definir interface: ReportData
- [x] Definir interface: SavedReport
- [x] Definir interface: ChartData
```

---

### 📄 FASE 10: FRONTEND - PÁGINAS PRINCIPAIS

#### Passo 23: Criar Página de Listagem
```bash
- [x] Criar arquivo: resources/js/pages/dashboard/reports/index.tsx
- [x] Receber props: savedReports, templates
- [x] Criar seção: Templates pré-configurados
- [x] Criar seção: Meus Relatórios Salvos
- [x] Adicionar botão: "Criar Novo Relatório" → builder
- [x] Implementar grid responsivo (3 colunas)
```

#### Passo 24: Criar Página Builder (Wizard)
```bash
- [x] Criar arquivo: resources/js/pages/dashboard/reports/builder.tsx
- [x] Receber props: categories, wallets, reportTypes, visualizationTypes
- [x] Implementar state: currentStep (1-4)
- [x] Implementar state: config (ReportConfig)
- [x] Criar navegação entre steps (next, prev)
- [x] Implementar função: handleGenerate() via router.post
- [x] Renderizar componente Step1/2/3/4 condicionalmente
```

#### Passo 25: Criar Página de Visualização
```bash
- [x] Criar arquivo: resources/js/pages/dashboard/reports/view.tsx
- [x] Receber props: report (ReportData), config, savedReport?
- [x] Criar header com título e informações
- [x] Criar cards de summary (total, count, average)
- [x] Renderizar gráfico condicionalmente (se config.include_chart)
- [x] Renderizar tabela condicionalmente (se config.include_table)
- [x] Adicionar botões de exportação
```

---

### 🧩 FASE 11: FRONTEND - COMPONENTES DO WIZARD

#### Passo 26: Criar Step 1 - Tipo de Relatório
```bash
- [ ] Criar: resources/js/components/reports/wizard/step-1-report-type.tsx
- [ ] Receber props: reportTypes, selected, onSelect, onNext
- [ ] Renderizar grid de cards clicáveis (2 colunas)
- [ ] Destacar card selecionado (ring-2 ring-primary)
- [ ] Mostrar ícone, título e descrição de cada tipo
- [ ] Desabilitar botão "Próximo" se não selecionou
```

#### Passo 27: Criar Step 2 - Filtros
```bash
- [ ] Criar: resources/js/components/reports/wizard/step-2-filters.tsx
- [ ] Receber props: reportType, categories, wallets, filters, onChange
- [ ] Implementar função: getAvailableFilters(reportType)
- [ ] Renderizar filtros condicionalmente conforme tipo
- [ ] Grid 2 colunas para os filtros
- [ ] Botões: Voltar e Próximo
```

#### Passo 28: Criar Step 3 - Visualização
```bash
- [ ] Criar: resources/js/components/reports/wizard/step-3-visualization.tsx
- [ ] Receber props: visualizationTypes, selected, includeChart, includeTable
- [ ] Grid de cards para tipos de visualização (3 colunas)
- [ ] Checkboxes: "Mostrar gráfico" e "Mostrar tabela"
- [ ] Destacar visualização selecionada
```

#### Passo 29: Criar Step 4 - Ações
```bash
- [ ] Criar: resources/js/components/reports/wizard/step-4-actions.tsx
- [ ] Receber props: config, onGenerate, onBack
- [ ] State local: saveName, shouldSave
- [ ] Card de resumo da configuração
- [ ] Checkbox: "Salvar configuração como favorito"
- [ ] Input: Nome do relatório (se shouldSave = true)
- [ ] Botão principal: "Salvar e Gerar" ou "Gerar Relatório"
```

---

### 🔧 FASE 12: FRONTEND - COMPONENTES DE FILTROS

#### Passo 30: Criar PeriodFilter
```bash
- [ ] Criar: resources/js/components/reports/filters/period-filter.tsx
- [ ] Select com opções: Último mês, 3 meses, 6 meses, ano, customizado
- [ ] DateRangePicker (se selecionar "customizado")
- [ ] onChange retorna: { period_type, start_date?, end_date? }
```

#### Passo 31: Criar CategoryMultiSelectFilter
```bash
- [ ] Criar: resources/js/components/reports/filters/category-multi-select-filter.tsx
- [ ] Usar componente TextMultiSelect existente
- [ ] Receber: options (categories), selected (ids[])
- [ ] onChange retorna: array de IDs selecionados
```

#### Passo 32: Criar WalletMultiSelectFilter
```bash
- [ ] Criar: resources/js/components/reports/filters/wallet-multi-select-filter.tsx
- [ ] Usar componente TextMultiSelect existente
- [ ] Receber: options (wallets), selected (ids[])
```

#### Passo 33: Criar StatusFilter
```bash
- [ ] Criar: resources/js/components/reports/filters/status-filter.tsx
- [ ] Checkboxes: Pagas, Pendentes, Todas
- [ ] onChange retorna: array de status selecionados
```

#### Passo 34: Criar AmountRangeFilter
```bash
- [ ] Criar: resources/js/components/reports/filters/amount-range-filter.tsx
- [ ] Dois inputs: min e max (type="number")
- [ ] Formatação em R$
- [ ] onChange retorna: { min_amount, max_amount }
```

---

### 📊 FASE 13: FRONTEND - COMPONENTES DE VISUALIZAÇÃO

#### Passo 35: Criar PieChartView (Recharts)
```bash
- [ ] Criar: resources/js/components/reports/visualizations/pie-chart-view.tsx
- [ ] Importar: PieChart, Pie, Cell, Tooltip, Legend (recharts)
- [ ] Receber props: data (array)
- [ ] Definir COLORS array (5-10 cores)
- [ ] ResponsiveContainer altura 400px
- [ ] Labels com percentual
- [ ] Tooltip formatado em R$
```

#### Passo 36: Criar BarChartView (Recharts)
```bash
- [ ] Criar: resources/js/components/reports/visualizations/bar-chart-view.tsx
- [ ] Importar: BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend
- [ ] Receber props: data, dataKey (padrão: 'total')
- [ ] ResponsiveContainer altura 400px
- [ ] Tooltip formatado em R$
```

#### Passo 37: Criar LineChartView (Recharts)
```bash
- [ ] Criar: resources/js/components/reports/visualizations/line-chart-view.tsx
- [ ] Importar: LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend
- [ ] Suporte para múltiplas linhas (ex: receitas e despesas)
- [ ] ResponsiveContainer altura 400px
```

#### Passo 38: Criar ReportTable
```bash
- [ ] Criar: resources/js/components/reports/visualizations/report-table.tsx
- [ ] Usar componentes Table, TableHeader, TableBody, TableRow, TableCell
- [ ] Receber props: data (array), columns? (configurável)
- [ ] Colunas dinâmicas baseadas nas chaves do primeiro item
- [ ] Formatação automática: valores em R$, datas em pt-BR
- [ ] Rodapé com totais (se aplicável)
```

---

### 💾 FASE 14: FRONTEND - COMPONENTES AUXILIARES

#### Passo 39: Criar ReportHeader
```bash
- [ ] Criar: resources/js/components/reports/report-header.tsx
- [ ] Mostrar: nome do relatório, tipo, data de geração
- [ ] Se savedReport existe: mostrar quantas vezes foi executado
- [ ] Botão: "Editar" (se savedReport)
```

#### Passo 40: Criar ExportButtons
```bash
- [ ] Criar: resources/js/components/reports/export/export-buttons.tsx
- [ ] DropdownMenu com opções: PDF, Excel, CSV
- [ ] Ícone de Download
- [ ] Cada opção faz: window.location.href = route('export.pdf/excel/csv')
- [ ] Mostrar loading state durante download
```

#### Passo 41: Criar SavedReportCard
```bash
- [ ] Criar: resources/js/components/reports/saved/saved-report-card.tsx
- [ ] Card clicável que redireciona para run
- [ ] Mostrar: nome, descrição, tipo, ícone
- [ ] Badge: "Favorito" (se is_favorite)
- [ ] Informações: última execução, quantas vezes rodou
- [ ] Botões: Executar, Editar, Excluir (DropdownMenu)
```

#### Passo 42: Criar TemplateCard
```bash
- [ ] Criar: resources/js/components/reports/saved/template-card.tsx
- [ ] Similar ao SavedReportCard mas com estilo diferente
- [ ] Badge: "Template"
- [ ] Apenas botão: "Usar Template"
```

#### Passo 43: Criar ReportStepper (Indicador Visual)
```bash
- [ ] Criar: resources/js/components/reports/report-stepper.tsx
- [ ] 4 steps numerados: 1. Tipo, 2. Filtros, 3. Visualização, 4. Gerar
- [ ] Destaca step atual
- [ ] Steps completados com check mark
- [ ] Linha conectando os steps
```

---

### 🎨 FASE 15: FRONTEND - VIEW BLADE PARA PDF

#### Passo 44: Criar View Blade
```bash
- [ ] Criar: resources/views/reports/pdf.blade.php
- [ ] Layout simples com CSS inline
- [ ] Header: logo, nome do relatório, data
- [ ] Seção de summary
- [ ] Tabela com dados do relatório
- [ ] Footer: página X de Y, gerado em [data]
- [ ] Testar renderização com Spatie PDF
```

---

### 🔗 FASE 16: INTEGRAÇÃO FINAL

#### Passo 45: Adicionar Link no Menu
```bash
- [ ] Editar: resources/js/components/dashboard/app-sidebar.tsx
- [ ] Adicionar item de menu: "Relatórios"
- [ ] Ícone: BarChart3 ou FileText
- [ ] Route: dashboard.reports.index
- [ ] Posição: Entre Dashboard e Carteiras
```

#### Passo 46: Atualizar CLAUDE.md
```bash
- [ ] Adicionar seção sobre Relatórios
- [ ] Documentar tipos de relatórios disponíveis
- [ ] Explicar filtros e visualizações
- [ ] Exemplos de uso
```

---

### 🧪 FASE 17: TESTES (OPCIONAL MAS RECOMENDADO)

#### Passo 47: Testes Unitários - Queries
```bash
- [ ] Criar: tests/Unit/Reports/ExpensesByCategoryQueryTest.php
- [ ] Testar: agrupamento correto por categoria
- [ ] Testar: cálculo de totais e percentuais
- [ ] Testar: aplicação de filtros
```

#### Passo 48: Testes Unitários - Services
```bash
- [ ] Criar: tests/Unit/Reports/ReportServiceTest.php
- [ ] Testar: generate() retorna estrutura correta
- [ ] Testar: cache funciona (segunda chamada não executa query)
- [ ] Testar: saveConfig() cria SavedReport
```

#### Passo 49: Testes Feature - Controller
```bash
- [ ] Criar: tests/Feature/Reports/ReportsControllerTest.php
- [ ] Testar: index() retorna relatórios do usuário
- [ ] Testar: generate() cria relatório
- [ ] Testar: exportPDF() retorna arquivo PDF
- [ ] Testar: usuário não acessa relatórios de outros
```

#### Passo 50: Build e Deploy
```bash
- [ ] Executar: npm run build
- [ ] Executar: php artisan config:cache
- [ ] Executar: php artisan route:cache
- [ ] Testar em staging antes de produção
```

---

## 📝 Notas Importantes

1. **Performance**: Sempre usar cache para relatórios pesados
2. **Paginação**: Considerar paginar resultados se houver muitos dados
3. **Limites**: Definir limites de período (ex: máximo 2 anos)
4. **Validação**: Validar filtros no backend (não confiar no frontend)
5. **Segurança**: Policy para garantir que usuário só acessa seus relatórios
6. **UX**: Loading states durante geração de relatórios
7. **Erro Handling**: Tratar erros de queries vazias, datas inválidas, etc.

---

## 🚀 Próximos Passos Após Implementação

1. **Dashboard de Relatórios**: Widget na home mostrando relatórios recentes
2. **Agendamento**: Permitir agendar relatórios recorrentes (cron jobs)
3. **Envio por Email**: Enviar relatório automaticamente por email
4. **Compartilhamento**: Compartilhar relatório com outros usuários
5. **BI Avançado**: Gráficos mais complexos (heatmap, waterfall, etc.)
6. **Alertas**: Criar alertas baseados em resultados de relatórios
7. **Comparação Visual**: Overlay de múltiplos períodos no mesmo gráfico
8. **Export para Google Sheets**: Integração com Google Sheets API

---

**Data de criação**: 2025-11-11
**Última atualização**: 2025-11-11
**Versão**: 1.0
**Autor**: Claude Code (Anthropic)
