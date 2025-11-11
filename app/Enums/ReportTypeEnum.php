<?php

namespace App\Enums;

enum ReportTypeEnum: string
{
    case EXPENSES_BY_CATEGORY = 'expenses_by_category';
    case EXPENSES_BY_WALLET = 'expenses_by_wallet';
    case EXPENSES_EVOLUTION = 'expenses_evolution';
    case TOP_EXPENSES = 'top_expenses';
    case INCOME_BY_CATEGORY = 'income_by_category';
    case INCOME_BY_WALLET = 'income_by_wallet';
    case INCOME_EVOLUTION = 'income_evolution';
    case CASHFLOW = 'cashflow';

    /**
     * Get the label for the report type
     */
    public function label(): string
    {
        return match ($this) {
            self::EXPENSES_BY_CATEGORY => 'Despesas por Categoria',
            self::EXPENSES_BY_WALLET => 'Despesas por Carteira',
            self::EXPENSES_EVOLUTION => 'Evolução de Despesas',
            self::TOP_EXPENSES => 'Top Despesas',
            self::INCOME_BY_CATEGORY => 'Receitas por Categoria',
            self::INCOME_BY_WALLET => 'Receitas por Carteira',
            self::INCOME_EVOLUTION => 'Evolução de Receitas',
            self::CASHFLOW => 'Cashflow (Receitas vs Despesas)',
        };
    }

    /**
     * Get the description for the report type
     */
    public function description(): string
    {
        return match ($this) {
            self::EXPENSES_BY_CATEGORY => 'Agrupa todas as despesas pagas por categoria',
            self::EXPENSES_BY_WALLET => 'Agrupa todas as despesas pagas por carteira',
            self::EXPENSES_EVOLUTION => 'Mostra a evolução de despesas ao longo do tempo',
            self::TOP_EXPENSES => 'Lista as maiores despesas do período',
            self::INCOME_BY_CATEGORY => 'Agrupa todas as receitas recebidas por categoria',
            self::INCOME_BY_WALLET => 'Agrupa todas as receitas recebidas por carteira',
            self::INCOME_EVOLUTION => 'Mostra a evolução de receitas ao longo do tempo',
            self::CASHFLOW => 'Compara receitas vs despesas ao longo do tempo',
        };
    }

    /**
     * Get the icon for the report type (lucide-react icon name)
     */
    public function icon(): string
    {
        return match ($this) {
            self::EXPENSES_BY_CATEGORY => 'PieChart',
            self::EXPENSES_BY_WALLET => 'Wallet',
            self::EXPENSES_EVOLUTION => 'TrendingDown',
            self::TOP_EXPENSES => 'ArrowDown',
            self::INCOME_BY_CATEGORY => 'PieChart',
            self::INCOME_BY_WALLET => 'Wallet',
            self::INCOME_EVOLUTION => 'TrendingUp',
            self::CASHFLOW => 'Activity',
        };
    }
}
