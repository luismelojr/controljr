# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application with React/Inertia.js frontend, using TypeScript and Tailwind CSS v4. The application is called "ControlJr" and features user authentication, a dashboard, and a custom Toast notification system.

## Tech Stack

**Backend:**
- Laravel 12 (PHP 8.2+)
- Inertia.js for SSR-capable SPA
- Laravel Socialite for OAuth authentication (Google)
- Ziggy for Laravel route helpers in JavaScript
- Pest for testing
- SQLite database (default)
- Queue system with database driver
- Maatwebsite Excel for spreadsheet exports
- Spatie Laravel PDF for PDF generation
- Spatie Query Builder for advanced filtering
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
└── Reconciliation/
    └── Services/
        └── ReconciliationService.php
```

### Frontend Architecture

React components and pages follow Inertia.js conventions:

- **Pages** (`resources/js/pages/`): Inertia page components
  - Each page corresponds to an Inertia render in a Laravel controller
  - Organized by feature (auth/, dashboard/, etc.)

- **Components** (`resources/js/components/`):
  - `ui/`: shadcn/ui components
  - `layouts/`: Layout wrappers (e.g., `auth-layout.tsx`)
  - `providers/`: React context providers (e.g., `theme-provider.tsx`)

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
- `text-multi-select.tsx`: Multi-select with command palette
- `text-mask.tsx`: Masked input (e.g., phone numbers)
- `text-area-custom.tsx`: Textarea with label
- `custom-toast.tsx`: Custom toast notification component
- `google-login-button.tsx`: Google OAuth login button component
- `sidebar.tsx`, `sheet.tsx`, `tooltip.tsx`, `separator.tsx`, `skeleton.tsx`: shadcn/ui components

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
- Test environment uses SQLite in-memory database

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
- Most models have a `status` field (active/inactive)
- Transactions and Incomes support recurring patterns
- Transactions include reconciliation tracking (`is_reconciled`, `external_id`)
- Budgets enforce unique constraint per user/category/period
- Models include soft deletes where appropriate

## Important Patterns

1. **Always use DTOs** for passing data between controllers and services
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
