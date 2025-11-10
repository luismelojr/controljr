# DataTable System Documentation

Sistema completo de DataTable reutilizável para Laravel + Inertia.js + React + TypeScript integrado com Spatie Query Builder.

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Arquitetura](#arquitetura)
- [Backend Setup](#backend-setup)
- [Frontend Setup](#frontend-setup)
- [Componentes](#componentes)
- [Exemplo Completo](#exemplo-completo)
- [Tipos TypeScript](#tipos-typescript)
- [Filtros Avançados](#filtros-avançados)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Visão Geral

O sistema DataTable oferece:

✅ **Paginação** com Laravel + Inertia
✅ **Filtros dinâmicos** com Spatie Query Builder
✅ **Ordenação de colunas** (asc/desc/null)
✅ **Badges de filtros ativos** com remoção individual
✅ **Componentes reutilizáveis** e type-safe
✅ **100% focado em Inertia** - zero abstrações desnecessárias

---

## 🏗️ Arquitetura

```
┌─────────────────────────────────────────────────────┐
│                    Frontend (React)                  │
├─────────────────────────────────────────────────────┤
│  DataTableHeader   │  Title, description, actions   │
│  DataTableFilters  │  Popover com form de filtros   │
│  FilterBadges      │  Mostra filtros ativos         │
│  DataTable         │  Tabela com sort               │
│  DataTablePagination│ Controles de paginação        │
└─────────────────────────────────────────────────────┘
                            ↓
                     router.get() ← Inertia
                            ↓
┌─────────────────────────────────────────────────────┐
│                   Backend (Laravel)                  │
├─────────────────────────────────────────────────────┤
│  Controller        │  Recebe request                │
│  Service           │  QueryBuilder + paginate()     │
│  Spatie Query Builder │ Aplica filtros/sorts        │
│  Resource          │  Transforma response           │
└─────────────────────────────────────────────────────┘
```

---

## 🔧 Backend Setup

### 1. Instalar Spatie Query Builder

```bash
composer require spatie/laravel-query-builder
```

### 2. Criar Service com QueryBuilder

```php
<?php

namespace App\Domain\YourDomain\Services;

use App\Models\YourModel;
use App\QueryFilters\YourCustomFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class YourService
{
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        $baseQuery = YourModel::query();

        return QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::custom('name', new YourCustomFilter()),
                AllowedFilter::exact('status'),
                AllowedFilter::exact('type'),
            ])
            ->allowedSorts([
                'name',
                'created_at',
                AllowedSort::field('custom_alias', 'real_column_name'),
            ])
            ->defaultSort('-created_at')
            ->paginate($perPage)
            ->withQueryString(); // IMPORTANTE: Mantém query params nos links
    }
}
```

### 3. Criar Custom Filter (Opcional)

```php
<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class YourCustomFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->where('name', 'LIKE', "%{$value}%");
    }
}
```

### 4. Controller

```php
<?php

namespace App\Http\Controllers;

use App\Domain\YourDomain\Services\YourService;
use App\Http\Resources\YourResource;
use Inertia\Inertia;
use Inertia\Response;

class YourController extends Controller
{
    public function __construct(private YourService $service) {}

    public function index(): Response
    {
        $perPage = request()->integer('per_page', 15);
        $data = $this->service->getAll($perPage);

        return Inertia::render('your-page/index', [
            'items' => YourResource::collection($data),
            'filters' => request()->only(['filter', 'sort']),
        ]);
    }
}
```

---

## 🎨 Frontend Setup

### 1. Tipos TypeScript

Importe os tipos necessários:

```tsx
import { PaginatedResponse, FilterConfig, ColumnDef } from '@/types/datatable';
import { YourModel } from '@/types/your-model';
```

### 2. Props da Página

```tsx
interface YourPageProps {
    items: PaginatedResponse<YourModel>;
    filters?: {  // ⚠️ IMPORTANTE: filters deve ser opcional
        filter?: Record<string, any>;
        sort?: string;
    };
}
```

### 3. Configuração de Filtros

```tsx
const filterConfigs: FilterConfig[] = [
    {
        key: 'name',
        label: 'Nome',
        type: 'text',
        placeholder: 'Buscar por nome...',
    },
    {
        key: 'status',
        label: 'Status',
        type: 'boolean',
        options: [
            { value: '1', label: 'Ativo' },
            { value: '0', label: 'Inativo' },
        ],
    },
    {
        key: 'type',
        label: 'Tipo',
        type: 'select',
        options: [
            { value: 'type1', label: 'Tipo 1' },
            { value: 'type2', label: 'Tipo 2' },
        ],
    },
];
```

### 4. Definição de Colunas

```tsx
const columns: ColumnDef<YourModel>[] = [
    {
        key: 'name',
        label: 'Nome',
        sortable: true,
        render: (item) => <span className="font-medium">{item.name}</span>,
    },
    {
        key: 'status',
        label: 'Status',
        sortable: true,
        render: (item) => (
            <Badge variant={item.status ? 'default' : 'destructive'}>
                {item.status ? 'Ativo' : 'Inativo'}
            </Badge>
        ),
    },
    {
        key: 'created_at',
        label: 'Criado em',
        sortable: true,
        render: (item) => new Date(item.created_at).toLocaleDateString('pt-BR'),
    },
    {
        key: 'actions',
        label: 'Ações',
        className: 'text-right',
        render: (item) => (
            <div className="flex gap-2 justify-end">
                <Button size="icon-sm" onClick={() => handleEdit(item.uuid)}>
                    <Edit className="h-4 w-4" />
                </Button>
            </div>
        ),
    },
];
```

---

## 📦 Componentes

### DataTableHeader

Título, descrição e botões de ação.

```tsx
<DataTableHeader
    title="Meus Registros"
    description="Gerencie seus registros aqui"
    actions={[
        {
            label: 'Novo Registro',
            onClick: () => router.get(route('your.create')),
            icon: <Plus className="h-4 w-4" />,
            variant: 'default',
        },
    ]}
/>
```

**Props:**
- `title` (string) - Título da página
- `description?` (string) - Descrição opcional
- `actions?` (ActionButton[]) - Botões de ação
- `children?` (ReactNode) - Conteúdo customizado

---

### DataTableFilters

Popover com formulário de filtros.

```tsx
<DataTableFilters
    filters={filterConfigs}
    activeFilters={filters.filter || {}}
    currentSort={filters.sort}
/>
```

**Props:**
- `filters` (FilterConfig[]) - Configuração dos filtros
- `activeFilters` (ActiveFilters) - Filtros atualmente ativos
- `currentSort?` (string) - Ordenação atual

**Tipos de filtro suportados:**
- `text` - Input de texto
- `number` - Input numérico
- `select` - Select dropdown
- `boolean` - Select com Sim/Não
- `date` - Input de data

---

### FilterBadges

Mostra filtros ativos como badges removíveis.

```tsx
<FilterBadges
    filters={filters.filter || {}}
    filterConfigs={filterConfigs}
    currentSort={filters.sort}
/>
```

**Props:**
- `filters` (ActiveFilters) - Filtros ativos
- `filterConfigs` (FilterConfig[]) - Configs para exibir labels
- `currentSort?` (string) - Preserva sort ao remover filtro

---

### DataTable

Tabela principal com ordenação.

```tsx
<DataTable
    data={items.data}
    columns={columns}
    activeSort={activeSort}
    currentFilters={filters.filter || {}}
/>
```

**Props:**
- `data` (T[]) - Array de dados
- `columns` (ColumnDef<T>[]) - Definição das colunas
- `activeSort?` (SortConfig) - Ordenação ativa
- `currentFilters?` (Record<string, any>) - Filtros ativos
- `emptyState?` (ReactNode) - Estado vazio customizado
- `loading?` (boolean) - Mostra skeleton

---

### DataTablePagination

Controles de paginação.

```tsx
<DataTablePagination
    meta={items.meta}
    links={items.links}
/>
```

**Props:**
- `meta` (PaginationMeta) - Metadados da paginação
- `links` (PaginationLinks) - Links de navegação

---

## 💡 Exemplo Completo

```tsx
import {
    DataTable,
    DataTableFilters,
    DataTableHeader,
    DataTablePagination,
    FilterBadges,
} from '@/components/datatable';
import { Category } from '@/types/category';
import { ColumnDef, FilterConfig, PaginatedResponse } from '@/types/datatable';
import { Head, router } from '@inertiajs/react';
import { Plus, Edit, Trash2 } from 'lucide-react';
import { useMemo } from 'react';

interface PageProps {
    categories: PaginatedResponse<Category>;
    filters?: {
        filter?: Record<string, any>;
        sort?: string;
    };
}

export default function CategoriesIndex({ categories, filters }: PageProps) {
    // Parse active filters
    const activeFilters = useMemo(() => filters?.filter || {}, [filters]);

    // Parse active sort
    const activeSort = useMemo(() => {
        const sortValue = filters?.sort;

        if (!sortValue || typeof sortValue !== 'string') {
            return { key: '', direction: null as any };
        }

        const isDesc = sortValue.startsWith('-');
        return {
            key: isDesc ? sortValue.slice(1) : sortValue,
            direction: isDesc ? 'desc' as const : 'asc' as const,
        };
    }, [filters]);

    // Filter configs
    const filterConfigs: FilterConfig[] = [
        {
            key: 'name',
            label: 'Nome',
            type: 'text',
            placeholder: 'Buscar por nome...',
        },
        {
            key: 'status',
            label: 'Status',
            type: 'boolean',
            options: [
                { value: '1', label: 'Ativo' },
                { value: '0', label: 'Inativo' },
            ],
        },
    ];

    // Column definitions
    const columns: ColumnDef<Category>[] = [
        {
            key: 'name',
            label: 'Nome',
            sortable: true,
        },
        {
            key: 'status',
            label: 'Status',
            sortable: true,
            render: (cat) => cat.status ? 'Ativo' : 'Inativo',
        },
        {
            key: 'actions',
            label: 'Ações',
            className: 'text-right',
            render: (cat) => (
                <Button size="icon-sm" onClick={() => handleEdit(cat.uuid)}>
                    <Edit className="h-4 w-4" />
                </Button>
            ),
        },
    ];

    const handleEdit = (uuid: string) => {
        router.get(route('categories.edit', { category: uuid }));
    };

    return (
        <>
            <Head title="Categorias" />

            <div className="space-y-6">
                <DataTableHeader
                    title="Categorias"
                    description="Gerencie suas categorias"
                    actions={[{
                        label: 'Nova Categoria',
                        onClick: () => router.get(route('categories.create')),
                        icon: <Plus className="h-4 w-4" />,
                    }]}
                />

                <div className="flex items-center justify-between">
                    <DataTableFilters
                        filters={filterConfigs}
                        activeFilters={activeFilters}
                        currentSort={filters?.sort}
                    />

                    <FilterBadges
                        filters={activeFilters}
                        filterConfigs={filterConfigs}
                        currentSort={filters?.sort}
                    />
                </div>

                <DataTable
                    data={categories.data}
                    columns={columns}
                    activeSort={activeSort}
                    currentFilters={activeFilters}
                />

                <DataTablePagination
                    meta={categories.meta}
                    links={categories.links}
                />
            </div>
        </>
    );
}
```

---

## 📝 Tipos TypeScript

### PaginatedResponse<T>

```typescript
interface PaginatedResponse<T> {
    data: T[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}
```

### FilterConfig

```typescript
interface FilterConfig {
    key: string;
    label: string;
    type: 'text' | 'select' | 'boolean' | 'date' | 'number';
    placeholder?: string;
    options?: Array<{
        value: string | number | boolean;
        label: string;
    }>;
}
```

### ColumnDef<T>

```typescript
interface ColumnDef<T> {
    key: string;
    label: string;
    sortable?: boolean;
    sortKey?: string; // Alias para sort
    render?: (item: T) => ReactNode;
    className?: string;
}
```

---

## 🔍 Filtros Avançados

### Custom Filter Class

Para filtros complexos, crie uma classe Filter:

```php
<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class DateRangeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $dates = explode(',', $value);

        return $query->whereBetween('created_at', [
            $dates[0] ?? now()->subMonth(),
            $dates[1] ?? now(),
        ]);
    }
}
```

Uso no Service:

```php
AllowedFilter::custom('date_range', new DateRangeFilter())
```

### Scoped Filter

```php
AllowedFilter::scope('with_trashed')
```

### Callback Filter

```php
AllowedFilter::callback('price', function ($query, $value) {
    $query->where('price', '>=', $value);
})
```

---

## 🐛 Troubleshooting

### TypeError: filters.sort.startsWith is not a function

**Problema:** Erro ao tentar acessar `filters.sort`.

**Solução:**
- Marque `filters` como opcional na interface: `filters?: { ... }`
- Use optional chaining: `filters?.sort` e `filters?.filter`
- Adicione validação de tipo antes de usar métodos de string:
  ```tsx
  if (!sortValue || typeof sortValue !== 'string') {
      return { key: '', direction: null };
  }
  ```

### TypeError: Array.prototype.filter called on null or undefined

**Problema:** Erro ao tentar usar `Object.keys()` em um objeto null/undefined.

**Solução:**
- Sempre crie uma versão "safe" do objeto no início do componente:
  ```tsx
  const safeFilters = filters || {};
  const safeActiveFilters = activeFilters || {};
  ```
- Use a versão safe em todas as operações:
  ```tsx
  Object.keys(safeFilters).filter(...)
  ```
- Isso acontece quando o Inertia não envia os filtros na primeira carga

### Filtros não funcionam

**Problema:** Filtros não são aplicados.

**Solução:**
- Verifique se `allowedFilters` está configurado no Service
- Confirme que a URL contém `?filter[key]=value`
- Verifique se o nome do filtro coincide com a coluna do banco

### Paginação perde filtros

**Problema:** Ao mudar de página, filtros são perdidos.

**Solução:**
- Adicione `->withQueryString()` após `paginate()`
- Verifique se o controller passa `request()->only(['filter', 'sort'])`

### Ordenação não funciona

**Problema:** Clicar na coluna não ordena.

**Solução:**
- Confirme que `sortable: true` está definido na coluna
- Verifique se `allowedSorts` inclui a coluna no Service
- Para aliases, use `sortKey` na definição da coluna

### TypeScript errors

**Problema:** Erros de tipo no frontend.

**Solução:**
- Importe tipos de `@/types/datatable`
- Verifique se o modelo tem tipo definido
- Use `useMemo` para parsear filtros e sort

---

## 📚 Recursos Adicionais

- [Spatie Query Builder Docs](https://spatie.be/docs/laravel-query-builder)
- [Inertia.js Docs](https://inertiajs.com/)
- [Laravel Pagination](https://laravel.com/docs/pagination)

---

## ✨ Exemplo de URLs

```
# Página inicial
/categories

# Com filtro de nome
/categories?filter[name]=Food

# Com múltiplos filtros
/categories?filter[name]=Food&filter[status]=1

# Com ordenação
/categories?sort=name

# Ordenação descendente
/categories?sort=-name

# Filtros + ordenação + página
/categories?filter[status]=1&sort=-name&page=2

# Items por página
/categories?per_page=50

# Tudo junto
/categories?filter[name]=Food&filter[status]=1&sort=-created_at&page=3&per_page=25
```

---

**Feito com ❤️ para Laravel + Inertia + React + TypeScript**
