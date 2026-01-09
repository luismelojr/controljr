export const getFeatureLabel = (key: string): string => {
    const labels: Record<string, string> = {
        categories: 'Categorias',
        wallets: 'Carteiras',
        budgets: 'Orçamentos',
        savings_goals: 'Metas de Economia',
        export_per_month: 'Exportações/mês',
        transactions_history_months: 'Histórico de Transações',
        tags: 'Tags Personalizadas',
        attachments: 'Anexos',
        custom_reports: 'Relatórios Customizados',
        ai_predictions: 'Previsões com IA',
    };

    return labels[key] || key;
};

export const getFeatureValue = (value: number | boolean): string => {
    if (typeof value === 'boolean') {
        return value ? 'Sim' : 'Não';
    }

    if (value === -1) {
        return 'Ilimitado';
    }

    if (value === 0) {
        return 'Não disponível';
    }

    return value.toString();
};
