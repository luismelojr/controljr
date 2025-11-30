# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application with React/Inertia.js frontend, using TypeScript and Tailwind CSS v4. The application is called "MeloSys" and is a comprehensive financial management system featuring user authentication, budget tracking, transaction management, financial reporting, bank reconciliation, and a custom Toast notification system.

## Tech Stack

**Backend:**
- Laravel 12 (PHP 8.2+)
- Inertia.js for SSR-capable SPA
- Laravel Socialite for OAuth authentication (Google)
- Ziggy for Laravel route helpers in JavaScript
- Pest for testing
- SQLite database (default)
- Queue system with database driver
- Maatwebsite Excel for spreadsheet exports (CSV and Excel)
- Spatie Laravel Data for DTOs with validation
- Spatie Query Builder for advanced filtering and sorting
- Resend for transactional emails

**Frontend:**
- React 19 with TypeScript
- Tailwind CSS v4
- shadcn/ui components (New York style)
- Vite for bundling
- Laravel Wayfinder for type-safe routing
- Radix UI primitives
- Recharts for data visualization
- date-fns for date manipulation
- react-imask for input masking
- react-currency-input-field for money inputs
- sonner for toast notifications
- cmdk for command palette interfaces

## Development Commands

### Initial Setup
```bash
composer setup  # Runs composer install, copies .env, generates key, migrates DB, npm install, npm build
```

### Development Server
```bash
composer dev    # Starts Laravel server + queue listener + Pail logs + Vite dev server concurrently
```

For SSR mode:
```bash
composer dev:ssr  # Builds SSR bundle and starts all services
```

### Frontend Development
```bash
npm run dev           # Start Vite dev server only
npm run build         # Build for production
npm run build:ssr     # Build with SSR support
npm run format        # Format code with Prettier
npm run format:check  # Check code formatting
npm run lint          # Run ESLint with auto-fix
npm run types         # Type-check TypeScript without emitting
```

### Testing
```bash
composer test           # Run all Pest tests
php artisan test        # Alternative test command
php artisan test --filter=TestName  # Run specific test
```

### Code Quality
```bash
./vendor/bin/pint       # Run Laravel Pint code formatter
./vendor/bin/pest       # Run Pest tests directly
```

### Database
```bash
php artisan migrate              # Run migrations
php artisan migrate:fresh        # Drop all tables and re-run migrations
php artisan migrate:fresh --seed # Recreate DB and run seeders
php artisan db:seed              # Run database seeders
```

### Queue Management
```bash
php artisan queue:work          # Process queue jobs
php artisan queue:listen        # Listen for jobs with auto-reload
```

### Logs
```bash
php artisan pail                # Tail application logs in real-time
```

### Wayfinder
```bash
php artisan wayfinder:generate  # Generate type-safe route definitions for frontend
```

## Architecture

### Domain-Driven Design Pattern

The backend uses a Domain-Driven approach with the following structure:

- **Controllers** (`app/Http/Controllers/*`): Handle HTTP requests, minimal logic
- **Domain Layer** (`app/Domain/*`): Contains business logic organized by domain
  - `DTOs/`: Data Transfer Objects for type-safe data passing
  - `Services/`: Business logic and domain operations
- **Models** (`app/Models/`): Eloquent models
- **Resources** (`app/Http/Resources/`): API resource transformations
- **Requests** (`app/Http/Requests/`): Form request validation

Example domain structure:
```
app/Domain/
├── Users/
│   ├── DTO/
│   │   ├── RegisterUserData.php
│   │   ├── LoginUserData.php
│   │   └── SocialLoginData.php
│   └── Services/
│       └── UserService.php
├── Auth/
│   ├── DTO/
│   │   ├── ForgotPasswordData.php
│   │   └── ResetPasswordData.php
│   └── Services/
│       └── PasswordResetService.php
├── Wallets/
│   ├── DTO/
│   └── Services/
│       └── WalletService.php
├── Accounts/
│   ├── DTO/
│   └── Services/
│       └── AccountService.php
├── Categories/
│   ├── DTO/
│   └── Services/
│       └── CategoryService.php
├── Transactions/
│   └── Services/
│       └── TransactionService.php
├── Incomes/
│   ├── DTO/
│   └── Services/
│       └── IncomeService.php
├── IncomeTransactions/
│   └── Services/
│       └── IncomeTransactionService.php
├── Alerts/
│   ├── DTO/
│   └── Services/
│       └── AlertService.php
├── Dashboard/
│   └── Services/
│       └── DashboardService.php
├── Budgets/
│   ├── DTO/
│   └── Services/
│       └── BudgetService.php
├── Reconciliation/
│   └── Services/
│       └── ReconciliationService.php
└── Exports/
    ├── DTO/
    │   ├── ExportFiltersData.php
    │   ├── ExportTransactionsData.php
    │   ├── ExportIncomesData.php
    │   └── ExportAccountsData.php
    ├── Services/
    │   ├── ExportService.php
    │   ├── ExcelExportService.php
    │   └── CsvExportService.php
    └── Exports/
        ├── TransactionsExport.php
        ├── IncomesExport.php
        ├── AccountsExport.php
        └── BudgetsExport.php
```

### Frontend Architecture

React components and pages follow Inertia.js conventions:

- **Pages** (`resources/js/pages/`): Inertia page components
  - Each page corresponds to an Inertia render in a Laravel controller
  - Organized by feature (auth/, dashboard/, etc.)

- **Components** (`resources/js/components/`):
  - `ui/`: shadcn/ui components and custom wrappers
  - `layouts/`: Layout wrappers (e.g., `auth-layout.tsx`, `dashboard-layout.tsx`)
  - `providers/`: React context providers (e.g., `theme-provider.tsx`)
  - `dashboard/`: Dashboard-specific components (15+ components)
  - `datatable/`: Reusable data table components with filtering/sorting
  - `budgets/`: Budget card and form components
  - `alerts/`: Alert components
  - `wallets/`: Wallet-specific components
  - `Reports/`: Financial reporting chart components
  - `landing/`: Landing page sections (hero, features, pricing, header, footer)

- **Actions** (`resources/js/actions/`): Type-safe route definitions generated by Wayfinder
  - Mirrors Laravel controller structure: `App/Http/Controllers/Auth/LoginController`
  - Provides TypeScript autocomplete for routes

- **Routes** (`resources/js/routes/`): Route helper modules organized by feature
  - Each feature has an index file (e.g., `login/index.ts`, `dashboard/index.ts`)

- **Wayfinder** (`resources/js/wayfinder/`): Generated type-safe routing helpers

### Custom Toast System

The application has a sophisticated custom Toast notification system:

**Backend (`app/Services/ToastService.php`):**
- Service-based Toast creation with builder pattern
- Flash messages to session for Inertia to pick up
- Support for: success, error, warning, info, loading types
- Advanced features: persistent toasts, validation toasts, actions, custom durations
- Facade available: `Toast::success('Message')`

**Frontend (`resources/js/components/ui/custom-toast.tsx`):**
- Custom React component for displaying toasts
- Receives toasts via Inertia shared props

**Usage in Controllers:**
```php
use App\Facades\Toast;

Toast::success('Operation completed successfully');
Toast::error('Something went wrong')->persistent();
Toast::validation(['email', 'password']); // Show validation errors as toasts
```

**Shared Data:**
All toasts are automatically shared to frontend via `HandleInertiaRequests` middleware (line 51).

### Authentication Flow

**Routes** (`routes/web.php`):
- Guest routes: `/login`, `/register`
- OAuth routes: `/auth/google` (redirect), `/auth/google/callback` (callback)
- Authenticated routes: `/dashboard/*` with `auth` middleware

**Controllers:**
- `LoginController`: Handles email/password login form and authentication
- `RegisterController`: Handles user registration
- `GoogleLoginController`: Handles Google OAuth flow (redirect and callback)
- All use DTOs and `UserService` for business logic

**User Model** (`app/Models/User.php`):
- Uses custom UUID trait (`HasUuidCustom`)
- Status field for account activation
- `google_id` field for Google OAuth integration (nullable, unique)
- Password field is nullable to support OAuth-only accounts

**Social Authentication:**
- Uses Laravel Socialite with Google provider
- `SocialLoginData` DTO for social login data transfer
- `UserService::socialLogin()` handles user creation/update via `updateOrCreate`
- Supports multiple OAuth providers (extensible architecture)

### Inertia Shared Data

All Inertia pages receive these props automatically (`HandleInertiaRequests.php`):
- `name`: App name from config
- `quote`: Random inspiring quote (message + author)
- `auth.user`: Authenticated user resource (or null)
- `toasts`: Array of toast notifications to display
- `unreadNotificationsCount`: Count of unread alert notifications

### Budgets System

The application includes a budget management module for tracking spending limits by category:

**Backend Architecture** (`app/Domain/Budgets/`):**

**Domain Structure:**
- `BudgetService` - Core business logic for budget CRUD operations
- `DTO/` - Data transfer objects for type-safe budget data

**Database Schema:**
- Budgets table with UUID primary key
- Links to user and category
- Amount limit per period
- Recurrence: 'monthly' or 'once'
- Period tracking (first day of month)
- Status field for active/inactive budgets
- Unique constraint on user_id + category_id + period

**Frontend Components** (`resources/js/components/budgets/`):**
- `budget-card.tsx` - Display budget with progress and spending
- `budget-form.tsx` - Create/edit budget form

**Features:**
- Set spending limits per category
- Monthly recurring or one-time budgets
- Track budget vs actual spending
- Visual progress indicators
- Status management (active/inactive)

**Routes:**
- `dashboard.budgets.index` - List all budgets
- `dashboard.budgets.store` - Create new budget
- `dashboard.budgets.update` - Update existing budget
- `dashboard.budgets.destroy` - Delete budget

### Alert System

The application includes a comprehensive alert system for monitoring financial thresholds and due dates:

**Backend Architecture** (`app/Domain/Alerts/`):**

**Domain Structure:**
- `AlertService` - Core business logic for alert management and checking
- `DTO/` - Data transfer objects for alert creation and updates
- Polymorphic relationship with alertable models (Budget, Wallet, Account)

**Alert Types** (`AlertTypeEnum`):**
1. **CREDIT_CARD_USAGE** - Monitor credit card limit usage
   - Triggers when usage percentage exceeds threshold (e.g., 80%, 90%)
   - Checks all credit cards or specific card
   - Updates hourly via scheduled task

2. **BILL_DUE_DATE** - Monitor upcoming bill payments
   - Triggers based on days before due date (e.g., 7, 3, 1 days)
   - Checks pending transactions approaching due date
   - Runs daily at 09:00

3. **ACCOUNT_BALANCE** - Monitor account balance thresholds
   - Triggers when balance falls below specified value
   - Runs daily at 08:00

4. **BUDGET_EXCEEDED** - Monitor budget spending limits
   - Triggers when spending percentage exceeds threshold (e.g., 80%, 100%)
   - Calculates total spent vs budget amount for the period
   - Checks all budgets or specific budget
   - Runs daily at 10:00
   - Shows "Orçamento Próximo do Limite" (< 100%) or "Orçamento Excedido" (>= 100%)

**Alert Configuration:**
- `trigger_value` - Percentage threshold for card/budget alerts, value for balance alerts
- `trigger_days` - Array of days before due date for bill alerts
- `notification_channels` - Array of channels (mail, database)
- `is_active` - Enable/disable alert
- `last_triggered_at` - Timestamp of last notification

**Notification System:**
- In-app notifications stored in `alert_notifications` table
- Email notifications via Laravel notifications
- Notifications include detailed data (amounts, percentages, dates)
- Type classification: INFO, WARNING, DANGER, SUCCESS
- Prevents duplicate notifications on same day for same condition

**Scheduled Tasks** (`routes/console.php`):**
- Credit card alerts: Hourly
- Bill due date alerts: Daily at 09:00
- Account balance alerts: Daily at 08:00
- Budget threshold alerts: Daily at 10:00

**Manual Execution:**
```bash
php artisan alerts:check                    # Check all alert types
php artisan alerts:check --type=budgets     # Check only budget alerts
php artisan alerts:check --type=credit-card # Check only credit card alerts
php artisan alerts:check --type=bills       # Check only bill alerts
php artisan alerts:check --type=accounts    # Check only account balance alerts
```

**Routes:**
- `dashboard.alerts.index` - List all user alerts
- `dashboard.alerts.store` - Create new alert
- `dashboard.alerts.update` - Update existing alert
- `dashboard.alerts.destroy` - Delete alert
- `dashboard.notifications.index` - View all notifications
- `dashboard.notifications.markAsRead` - Mark notification as read

### Data Export System

The application provides comprehensive data export capabilities for financial records:

**Backend Architecture** (`app/Domain/Exports/`):**

**Domain Structure:**
- `ExportService` - Orchestrates export operations
- `ExcelExportService` - Handles Excel file generation
- `CsvExportService` - Handles CSV file generation
- DTOs for type-safe export data (ExportFiltersData, ExportTransactionsData, etc.)
- Export classes implementing `FromCollection` and `WithHeadings` for Maatwebsite/Excel

**Supported Exports:**
- Transactions export with filters (date range, category, wallet, status)
- Incomes export with category and date filters
- Accounts export with status and recurrence filters
- Budgets export with category and period filters

**Export Formats:**
- Excel (.xlsx) via Maatwebsite/Excel
- CSV (.csv) via Maatwebsite/Excel

**Features:**
- Server-side filtering before export
- Custom column headings
- Date formatting for Brazilian locale
- Status and enum label translations
- Download response with proper headers

**Routes:**
- `dashboard.exports.transactions` - Export transactions
- `dashboard.exports.incomes` - Export incomes
- `dashboard.exports.accounts` - Export accounts
- `dashboard.exports.budgets` - Export budgets

**Frontend Integration:**
- `export-button.tsx` component for triggering exports
- Form-based export with filter parameters
- Loading states during export generation

### Financial Reporting System

The application includes comprehensive financial reporting with visual analytics:

**Backend Architecture** (`app/Services/Reporting/`):**

**Service Structure:**
- `ReportService` - Aggregates financial data for reporting
  - `getFinancialOverview()` - Total income, expenses, net result, savings rate
  - `getCashFlowData()` - Daily income/expense breakdown for charts
  - `getExpensesByCategory()` - Category-based expense analysis
  - `getIncomesByCategory()` - Category-based income analysis

**Data Processing:**
- Date range filtering (default: current month)
- Grouping by category with totals and percentages
- Color coding for visual distinction
- Currency formatting (cents to BRL)
- Savings rate calculation

**Frontend Components** (`resources/js/components/Reports/`):**
- `FinancialSummaryCards.tsx` - Overview cards (income, expenses, net, savings rate)
- `CashFlowChart.tsx` - Line/area chart showing daily cash flow trends (Recharts)
- `CategoryBreakdown.tsx` - Bar chart showing expense/income by category (Recharts)

**Features:**
- Real-time financial overview
- Visual cash flow trends over time
- Category-based spending/income analysis
- Responsive chart layouts
- Color-coded categories for easy identification
- Percentage breakdowns by category

**Routes:**
- `dashboard.reports.index` - Financial reports dashboard
- `dashboard.reports.data` - API endpoint for report data

### Bank Reconciliation System

The application includes a bank reconciliation module for matching transactions with bank statements:

**Backend Architecture** (`app/Domain/Reconciliation/`):**

**Domain Structure:**
- `ReconciliationService` - Core business logic for reconciliation operations

**Database Schema:**
- Transactions table extended with:
  - `is_reconciled`: Boolean flag for reconciliation status
  - `external_id`: Bank statement reference ID (indexed)

**Frontend Components:**
- Reconciliation pages in `resources/js/pages/dashboard/reconciliation/`

**Features:**
- Upload bank statements
- Match transactions with bank records
- Mark transactions as reconciled
- Track external bank statement IDs
- Identify discrepancies between system and bank

**Routes:**
- `dashboard.reconciliation.index` - Reconciliation interface
- `dashboard.reconciliation.upload` - Upload bank statement
- `dashboard.reconciliation.reconcile` - Mark transaction as reconciled

## File Organization

### Dashboard Layout & Components

**Layout Structure:**
- `dashboard-layout.tsx`: Main dashboard layout with Sidebar and Header
- Uses shadcn/ui `SidebarProvider` for collapsible sidebar state
- Responsive design with mobile support

**Dashboard Components** (`components/dashboard/`):
- `app-sidebar.tsx`: Navigation sidebar with menu items and logout
- `dashboard-header.tsx`: Top header with search, notifications, and user profile
- `balance-card.tsx`: User balance card with transfer/receive actions
- `stats-card.tsx`: Reusable stats card (Monthly Spent/Income)
- `cashflow-chart.tsx`: Visual cashflow chart with expense/income comparison
- `quick-transfer.tsx`: Contact quick transfer widget
- `recently-activity.tsx`: Recent transactions table
- `savings.tsx`: Savings goals with progress bars

**Sidebar Features:**
- Collapsible navigation
- Menu sections with icons (lucide-react)
- Badge notifications support
- Dark mode toggle
- Logout functionality

### Custom UI Components

The project uses shadcn/ui with custom wrappers:
- `text-input.tsx`: Enhanced input with label integration
- `text-select.tsx`: Select component with label
- `text-multi-select.tsx`: Multi-select with command palette (uses cmdk)
- `text-mask.tsx`: Masked input for phone numbers, etc. (uses react-imask)
- `text-money.tsx`: Currency input with formatting (uses react-currency-input-field)
- `text-area-custom.tsx`: Textarea with label
- `custom-toast.tsx`: Custom toast notification component
- `google-login-button.tsx`: Google OAuth login button component
- `export-button.tsx`: Export functionality trigger with loading states
- `mode-toggle.tsx`: Dark mode toggle component
- `melosys-logo.tsx`: Brand logo component (SVG-based, configurable size and text display)
- `sidebar.tsx`, `sheet.tsx`, `tooltip.tsx`, `separator.tsx`, `skeleton.tsx`: shadcn/ui components

### DataTable Component System

The application includes a reusable DataTable system for displaying and filtering tabular data:

**Components** (`resources/js/components/datatable/`):
- `data-table.tsx`: Main table component with sorting, pagination, and filtering
- `data-table-header.tsx`: Column headers with sort controls
- `data-table-filters.tsx`: Filter interface for columns
- `data-table-filter-badges.tsx`: Active filter display with remove buttons
- `data-table-pagination.tsx`: Pagination controls

**Features:**
- Server-side sorting via Spatie Query Builder
- Server-side filtering with multiple filter types
- Filter badges showing active filters
- Responsive pagination
- TypeScript-safe column definitions
- Reusable across different data types (transactions, incomes, accounts, budgets)

**Integration:**
- Works with Spatie Query Builder backend filtering
- Uses Inertia.js router for state management
- Maintains filter state in URL parameters
- Supports custom filter components per column type

### Query Filtering

The application uses Spatie Query Builder for advanced filtering:

**Custom Filters** (`app/QueryFilters/`):
- `AccountNameFilter`: Filter accounts by name
- `WalletTypeFilter`: Filter wallets by type (bank_account, credit_card, other)
- `CategoryNameFilter`: Filter categories by name

**Usage Pattern:**
```php
QueryBuilder::for(Transaction::class)
    ->allowedFilters([
        AllowedFilter::exact('category_id'),
        AllowedFilter::exact('wallet_id'),
        AllowedFilter::scope('due_date_from'),
        AllowedFilter::scope('due_date_to'),
    ])
    ->allowedSorts(['due_date', 'amount'])
    ->paginate();
```

**Frontend Integration:**
- Filter parameters passed via Inertia requests
- Filter state managed in URL query params
- Real-time filtering without page reload

### Validation

- Custom validation rules in `app/Rules/` (e.g., `BrazilianPhone.php`)
- Form request validation in `app/Http/Requests/`
- Portuguese localization available via `lucascudo/laravel-pt-br-localization`

### Helpers

- `app/Helpers/Helpers.php`: Utility functions (e.g., `formatStringRemoveCharactersSpecial()`)

## Configuration Notes

### Google OAuth Setup

To enable Google login, you need to:

1. Create OAuth credentials in [Google Cloud Console](https://console.cloud.google.com/)
2. Add the following to your `.env` file:
```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
```
3. Configure authorized redirect URIs in Google Cloud Console:
   - Development: `http://localhost:8000/auth/google/callback`
   - Production: `https://yourdomain.com/auth/google/callback`

The Google OAuth configuration is stored in `config/services.php` under the `google` key.

### Tailwind CSS v4
The project uses Tailwind CSS v4 with Vite plugin (`@tailwindcss/vite`). Configuration is in CSS variables (see `components.json`).

### TypeScript Paths
Alias configuration in `tsconfig.json` and `components.json`:
- `@/` → `resources/js/`
- `@/components` → `resources/js/components`
- `@/lib` → `resources/js/lib`
- `@/ui` → `resources/js/components/ui`

### Vite Configuration
- Entry points: `resources/css/app.css`, `resources/js/app.tsx`
- SSR entry: `resources/js/ssr.tsx`
- Plugins: Laravel, React, Tailwind, Wayfinder

## Testing

Uses Pest PHP testing framework with Laravel plugin:
- Unit tests: `tests/Unit/`
- Feature tests: `tests/Feature/`
  - Policy authorization tests (CategoryPolicyTest, WalletPolicyTest)
  - Soft delete functionality tests (BudgetSoftDeleteTest)
  - Query filter tests (CategoryFiltersTest)
- Test environment uses SQLite in-memory database

**Testing Patterns:**
- Use `actingAs()` for authenticated user tests
- Test policy authorization with different user contexts
- Verify soft delete behavior (deleted_at timestamps, withTrashed queries)
- Test Spatie Query Builder filters with various parameters
- Feature tests should cover happy path and edge cases

## Database

Default configuration uses SQLite (`database/database.sqlite`). Standard Laravel migrations in `database/migrations/`:
- Users table with UUID primary key
- Wallets table (user's payment methods)
- Accounts table (expense accounts with recurring support)
- Categories table (expense categories)
- Transactions table (expense records with reconciliation fields)
- Incomes table (recurring income sources)
- Income transactions table (income records)
- Alerts table (user alerts and notifications)
- Alert notifications table (triggered alert instances)
- Budgets table (category spending limits with monthly/one-time recurrence)
- Cache, Jobs, and Session tables for system operations

**Key Models:**
- All primary models use UUID as primary key via `HasUuidCustom` trait
- Most models have a `status` field (active/inactive) with dedicated toggle methods
- Transactions and Incomes support recurring patterns with automatic generation
- Transactions include reconciliation tracking (`is_reconciled`, `external_id`)
- Budgets enforce unique constraint per user/category/period and use soft deletes
- Models include soft deletes where appropriate (Budget model)
- All monetary values stored as integers in cents for precision
- Polymorphic relationships used for flexible associations (Alert model)

**Model Authorization:**
- All models protected by Laravel Policies (`app/Policies/`)
- Policies enforce ownership and permission checks
- Policy tests in `tests/Feature/` (CategoryPolicyTest, WalletPolicyTest)

## Important Patterns

1. **Always use DTOs** for passing data between controllers and services (Spatie Laravel Data)
2. **Use the Toast facade** for user notifications instead of flash messages
3. **Leverage Wayfinder** for type-safe route definitions in React components
4. **Follow domain separation**: Controllers → DTOs → Services → Models
5. **Use Form Requests** for validation logic instead of controller validation
6. **Inertia pages** should be lightweight; business logic stays in backend services
7. **UUID Primary Keys**: All user-created models use UUIDs via `HasUuidCustom` trait
8. **Status Fields**: Most models include status management (active/inactive) with dedicated toggle methods
9. **Recurring Patterns**: Accounts, Incomes, and Budgets support recurring patterns with automatic generation
10. **Reconciliation**: Transactions can be marked as reconciled with external bank statement IDs
11. **Budget Constraints**: Budgets enforce unique constraints per user/category/period to prevent duplicates
12. **Money Storage**: Store all monetary values as integers in cents, never as floats
13. **Policy Authorization**: Use Laravel Policies for all authorization checks, test with different user contexts
14. **Query Filtering**: Use Spatie Query Builder with custom filters for complex queries
15. **Export Pattern**: Use dedicated Export DTOs and Services, support both Excel and CSV formats
16. **Component Reusability**: Use DataTable system for consistent table layouts, custom input wrappers for forms
17. **Type Safety**: Leverage TypeScript strict mode, Wayfinder for routes, Spatie Data for DTOs
18. **Soft Deletes**: Implement soft deletes for data that users may want to restore (e.g., Budgets)
19. **Brand Consistency**: Use `melosys-logo.tsx` component for all logo displays with `showText` prop for control
