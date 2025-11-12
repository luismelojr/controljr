import { Category } from './category';
import { WalletInterface } from './wallet';

/**
 * Report Type Enum - must match App\Enums\ReportTypeEnum
 */
export type ReportType =
    | 'expenses_by_category'
    | 'expenses_by_wallet'
    | 'expenses_evolution'
    | 'top_expenses'
    | 'income_by_category'
    | 'income_by_wallet'
    | 'income_evolution'
    | 'cashflow';

/**
 * Visualization Type Enum - must match App\Enums\VisualizationTypeEnum
 */
export type VisualizationType =
    | 'table'
    | 'pie_chart'
    | 'bar_chart'
    | 'line_chart'
    | 'kpi_cards';

/**
 * Export Format Enum - must match App\Enums\ExportFormatEnum
 */
export type ExportFormat = 'pdf' | 'excel' | 'csv';

/**
 * Period Type for filtering
 */
export type PeriodType = 'daily' | 'weekly' | 'monthly';

/**
 * Transaction status for filtering
 */
export type ReportStatusFilter = 'pending' | 'paid' | 'all';

/**
 * Report Type Option (from backend enum)
 */
export interface ReportTypeOption {
    value: ReportType;
    label: string;
    description: string;
    icon: string; // lucide-react icon name
}

/**
 * Visualization Type Option (from backend enum)
 */
export interface VisualizationTypeOption {
    value: VisualizationType;
    label: string;
    icon: string; // lucide-react icon name
}

/**
 * Export Format Option (from backend enum)
 */
export interface ExportFormatOption {
    value: ExportFormat;
    label: string;
    icon: string; // lucide-react icon name
    mime_type: string;
    extension: string;
}

/**
 * Report Filters - must match App\Domain\Reports\DTO\ReportFiltersData
 */
export interface ReportFilters {
    start_date?: string | null;
    end_date?: string | null;
    period_type?: PeriodType | null;
    category_ids?: string[] | null;
    wallet_ids?: string[] | null;
    min_amount?: number | null;
    max_amount?: number | null;
    status?: ReportStatusFilter | null;
    limit?: number | null; // For top N queries
}

/**
 * Visualization Configuration
 */
export interface VisualizationConfig {
    type: VisualizationType;
    label: string;
    icon: string;
}

/**
 * Report Configuration for generation
 * Used in the wizard builder
 */
export interface ReportConfig {
    report_type: ReportType;
    filters: ReportFilters;
    visualization_type: VisualizationType;
}

/**
 * Report Summary Statistics
 */
export interface ReportSummary {
    total: number;
    count: number;
    average?: number;
    categories_count?: number;
    wallets_count?: number;
    [key: string]: number | undefined; // Allow dynamic summary fields
}

/**
 * Chart Data Point (generic structure)
 */
export interface ChartDataPoint {
    name: string;
    value: number;
    percentage?: number;
    [key: string]: string | number | undefined; // Allow dynamic fields
}

/**
 * Report Data returned from backend
 */
export interface ReportData {
    report_type: ReportType;
    filters: ReportFilters;
    data: ChartDataPoint[]; // The actual report data
    summary: ReportSummary;
    generated_at: string;
}

/**
 * Saved Report Resource - must match App\Http\Resources\SavedReportResource
 */
export interface SavedReport {
    uuid: string;
    name: string;
    description?: string | null;
    report_type: ReportType;
    report_type_label: string;
    report_type_description: string;
    report_type_icon: string;
    config: ReportFilters;
    visualization: VisualizationConfig;
    visualization_type: VisualizationType;
    is_template: boolean;
    is_favorite: boolean;
    last_run_at?: string | null;
    last_run_at_human?: string | null;
    run_count: number;
    created_at: string;
    updated_at: string;
}

/**
 * Type alias for SavedReportResource (same as SavedReport)
 */
export type SavedReportResource = SavedReport;

/**
 * Request payload for generating a report
 */
export interface GenerateReportRequest {
    report_type: ReportType;
    visualization_type: VisualizationType;
    start_date?: string;
    end_date?: string;
    period_type?: PeriodType;
    category_ids?: string[];
    wallet_ids?: string[];
    min_amount?: number;
    max_amount?: number;
    status?: ReportStatusFilter;
    limit?: number;
}

/**
 * Request payload for saving a report configuration
 */
export interface SaveReportRequest extends GenerateReportRequest {
    name: string;
    description?: string;
    is_favorite?: boolean;
}

/**
 * Props for Reports Index Page
 */
export interface ReportsIndexProps {
    savedReports: SavedReport[];
    templates: SavedReport[];
    favorites: SavedReport[];
}

/**
 * Props for Report Builder Page
 */
export interface ReportBuilderProps {
    categories: Category[];
    wallets: WalletInterface[];
    report_types: ReportTypeOption[];
    visualization_types: VisualizationTypeOption[];
}

/**
 * Props for Report View Page
 */
export interface ReportViewProps {
    report: ReportData;
    config: ReportConfig;
    savedReport?: SavedReport;
}

/**
 * Props for Report Show Page (viewing saved report)
 */
export interface ReportShowProps {
    report: ReportData;
    savedReport: SavedReport;
}
