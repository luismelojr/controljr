<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1f2937;
            background: #ffffff;
        }

        .container {
            padding: 20px;
            max-width: 100%;
        }

        /* Header */
        .header {
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .logo {
            font-size: 24pt;
            font-weight: bold;
            color: #3b82f6;
        }

        .report-info {
            text-align: right;
        }

        .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .report-subtitle {
            font-size: 10pt;
            color: #6b7280;
        }

        /* Summary Section */
        .summary {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-title {
            font-size: 12pt;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 5px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .summary-item {
            background: #ffffff;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .summary-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 14pt;
            font-weight: bold;
            color: #1f2937;
        }

        /* Filters Section */
        .filters {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 10px 15px;
            margin-bottom: 20px;
        }

        .filters-title {
            font-size: 10pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }

        .filters-content {
            font-size: 9pt;
            color: #78350f;
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table thead {
            background: #3b82f6;
            color: #ffffff;
        }

        .data-table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .data-table th.text-right {
            text-align: right;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .data-table tbody tr:hover {
            background: #f3f4f6;
        }

        .data-table td {
            padding: 8px;
            font-size: 9pt;
        }

        .data-table td.text-right {
            text-align: right;
        }

        .data-table td.font-bold {
            font-weight: bold;
        }

        .data-table tfoot {
            background: #f3f4f6;
            font-weight: bold;
        }

        .data-table tfoot td {
            padding: 10px 8px;
            border-top: 2px solid #3b82f6;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            border-top: 2px solid #e5e7eb;
            padding: 10px 20px;
            font-size: 8pt;
            color: #6b7280;
            background: #ffffff;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }

        /* Utilities */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-primary {
            color: #3b82f6;
        }

        .text-success {
            color: #10b981;
        }

        .text-danger {
            color: #ef4444;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div>
                    <div class="logo">ControlJr</div>
                    <div style="font-size: 8pt; color: #6b7280; margin-top: 3px;">
                        Sistema de Controle Financeiro
                    </div>
                </div>
                <div class="report-info">
                    <div class="report-title">{{ $reportName }}</div>
                    <div class="report-subtitle">{{ $reportType }}</div>
                    <div class="report-subtitle">
                        Gerado em {{ \Carbon\Carbon::parse($generatedAt)->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters (if any) -->
        @if(isset($filters) && count($filters) > 0)
        <div class="filters">
            <div class="filters-title">Filtros Aplicados:</div>
            <div class="filters-content">
                @if(isset($filters['start_date']) && isset($filters['end_date']))
                    <strong>Período:</strong>
                    {{ \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') }}
                    até
                    {{ \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') }}
                    @if(isset($filters['category_ids']) || isset($filters['wallet_ids']) || isset($filters['status']))
                        <span style="margin: 0 5px;">•</span>
                    @endif
                @endif

                @if(isset($filters['category_ids']) && count($filters['category_ids']) > 0)
                    <strong>Categorias:</strong> {{ count($filters['category_ids']) }} selecionada(s)
                    @if(isset($filters['wallet_ids']) || isset($filters['status']))
                        <span style="margin: 0 5px;">•</span>
                    @endif
                @endif

                @if(isset($filters['wallet_ids']) && count($filters['wallet_ids']) > 0)
                    <strong>Carteiras:</strong> {{ count($filters['wallet_ids']) }} selecionada(s)
                    @if(isset($filters['status']))
                        <span style="margin: 0 5px;">•</span>
                    @endif
                @endif

                @if(isset($filters['status']) && $filters['status'] !== 'all')
                    <strong>Status:</strong> {{ $filters['status'] === 'paid' ? 'Pagas' : 'Pendentes' }}
                @endif
            </div>
        </div>
        @endif

        <!-- Summary -->
        @if(isset($summary))
        <div class="summary">
            <div class="summary-title">Resumo do Relatório</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Total</div>
                    <div class="summary-value">{{ number_format($summary['total'] / 100, 2, ',', '.') }}</div>
                </div>

                @if(isset($summary['count']))
                <div class="summary-item">
                    <div class="summary-label">Registros</div>
                    <div class="summary-value">{{ $summary['count'] }}</div>
                </div>
                @endif

                @if(isset($summary['average']))
                <div class="summary-item">
                    <div class="summary-label">Média</div>
                    <div class="summary-value">{{ number_format($summary['average'] / 100, 2, ',', '.') }}</div>
                </div>
                @endif

                @if(isset($summary['categories_count']))
                <div class="summary-item">
                    <div class="summary-label">Categorias</div>
                    <div class="summary-value">{{ $summary['categories_count'] }}</div>
                </div>
                @endif

                @if(isset($summary['wallets_count']))
                <div class="summary-item">
                    <div class="summary-label">Carteiras</div>
                    <div class="summary-value">{{ $summary['wallets_count'] }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Descrição</th>
                    <th class="text-right" style="width: 20%;">Valor (R$)</th>
                    @if(isset($data[0]['percentage']))
                    <th class="text-right" style="width: 15%;">Percentual</th>
                    @endif
                    @if(isset($data[0]['count']))
                    <th class="text-right" style="width: 15%;">Quantidade</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                <tr>
                    <td class="font-bold">{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right font-bold">{{ number_format($item['value'] / 100, 2, ',', '.') }}</td>
                    @if(isset($item['percentage']))
                    <td class="text-right">{{ number_format($item['percentage'], 2, ',', '.') }}%</td>
                    @endif
                    @if(isset($item['count']))
                    <td class="text-right">{{ $item['count'] }}</td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px;">
                        Nenhum dado disponível para este período
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if(isset($summary) && count($data) > 0)
            <tfoot>
                <tr>
                    <td colspan="2">TOTAL</td>
                    <td class="text-right">{{ number_format($summary['total'] / 100, 2, ',', '.') }}</td>
                    @if(isset($data[0]['percentage']))
                    <td class="text-right">100,00%</td>
                    @endif
                    @if(isset($data[0]['count']) && isset($summary['count']))
                    <td class="text-right">{{ $summary['count'] }}</td>
                    @endif
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div>
                © {{ date('Y') }} ControlJr - Sistema de Controle Financeiro
            </div>
            <div>
                Página <span class="pageNumber"></span> de <span class="totalPages"></span>
            </div>
        </div>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
