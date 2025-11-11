<?php

namespace App\Enums;

enum VisualizationTypeEnum: string
{
    case TABLE = 'table';
    case PIE_CHART = 'pie_chart';
    case BAR_CHART = 'bar_chart';
    case LINE_CHART = 'line_chart';
    case KPI_CARDS = 'kpi_cards';

    /**
     * Get the label for the visualization type
     */
    public function label(): string
    {
        return match ($this) {
            self::TABLE => 'Tabela',
            self::PIE_CHART => 'Gráfico de Pizza',
            self::BAR_CHART => 'Gráfico de Barras',
            self::LINE_CHART => 'Gráfico de Linhas',
            self::KPI_CARDS => 'Cards KPI',
        };
    }

    /**
     * Get the icon for the visualization type (lucide-react icon name)
     */
    public function icon(): string
    {
        return match ($this) {
            self::TABLE => 'Table',
            self::PIE_CHART => 'PieChart',
            self::BAR_CHART => 'BarChart',
            self::LINE_CHART => 'LineChart',
            self::KPI_CARDS => 'LayoutGrid',
        };
    }
}
