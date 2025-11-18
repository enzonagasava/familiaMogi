# FamiliaMogi - AI Coding Assistant Guide

## Architecture Overview

**Stack**: Laravel 12 (PHP 8.2+) + Vue 3 + Inertia.js + TypeScript + TailwindCSS 4
- **Frontend**: Vue 3 Composition API with TypeScript, using Inertia.js for SSR-capable SPA
- **Backend**: Laravel 12 API with JWT authentication (tymon/jwt-auth)
- **UI Components**: Shadcn-vue (reka-ui) + Lucide icons, configured at `components.json`
- **State**: Pinia for global state, Inertia shared data for auth/user context
- **Routing**: Laravel routes with Ziggy for typed frontend route helpers

## Critical Development Patterns

### Authentication Flow
JWT tokens stored in HTTP-only cookies (`jwt_token`). Custom middleware `JwtCookieMiddleware` extracts token from cookie and sets auth guard. All protected routes use `['jwt.cookie', 'auth']` middleware stack. Role-based access via `'admin'` and `'cliente'` middleware.

### Model Conventions
All models extend `BaseModel` (not Eloquent directly), which provides `created_at_formatted` attribute using the global `formatDate()` helper from `app/Helpers/helpers.php`. Always use `BaseModel::class` as parent for new models.

**Key Relationships**:
- `GerenciarPedido` → `Pedido` (hasMany via `cod_pedido`, not `id`)
- `GerenciarPedido` → `Produto` (hasManyThrough via `Pedido`)
- `Produto` → `Tamanho` (belongsToMany with `preco` pivot)
- Use eager loading in controllers: `.with(['cliente:id,nome', 'plataforma:id,nome'])`

### Inertia.js Conventions
- Pages in `resources/js/pages/`, route-based structure: `admin/pedidos/Pedidos.vue`
- Props passed from controllers: `Inertia::render('admin/pedidos/Pedidos', ['pedidos' => $data])`
- TypeScript props: Define types in `resources/js/types/index.d.ts`, extend `AppPageProps<T>`
- Shared data available globally: `auth`, `ziggy`, `quote`, `sidebarOpen` (see `HandleInertiaRequests`)
- Navigation: Use `<Link :href="route('admin.pedidos.index', 'em-andamento')">`

### Vue Component Structure
- **Composition API only** - no Options API
- Props: `defineProps<{ pedidos: Array, status: string }>()`
- TypeScript interfaces in `resources/js/types/index.d.ts`
- Path alias: `@/` maps to `resources/js/`
- UI components ignored in ESLint: `resources/js/components/ui/*`

### Data Formatting
- **Dates**: Use `formatDate($date, 'd/m/Y H:i')` helper in PHP (Brazil timezone)
- **Currency**: `number_format($value, 2, ',', '.')` in PHP controllers
- **Status**: Store as kebab-case (`em-andamento`), format in frontend with `replace(/-/g, ' ')`

### Route Organization
- Public routes: `routes/web.php`
- Admin routes: `routes/admin.php` with `['jwt.cookie', 'auth', 'admin']` middleware
- Client routes: `routes/admin.php` with `['jwt.cookie', 'auth', 'cliente']` middleware
- Settings/profile: `routes/settings.php`
- Auth: `routes/auth.php`

## Development Workflow

### Running the Project
```bash
# Start all services (Laravel, Vite, Queue, Logs)
composer dev

# Alternative: With SSR
composer dev:ssr

# Docker environment
docker-compose up    # App on :8001, Vite on :5173, MySQL on :3307
```

### Testing
```bash
composer test        # Clears config and runs Pest tests
```
**Note**: `RefreshDatabase` is commented out in `tests/Pest.php`

### Code Quality
```bash
npm run lint         # ESLint with auto-fix
npm run format       # Prettier (organize imports + Tailwind)
php artisan pint     # Laravel Pint (PSR-12)
```

### Database
- Migrations: Prefix format `YYYY_MM_DD_HHMMSS_description`
- Important tables: `gerenciar_pedidos`, `pedidos` (linked via `cod_pedido`)
- Seeders in `database/seeders/`, factories in `database/factories/`

## Common Patterns

### Status Flow Management
Pedidos follow fixed status progression: `em-andamento` → `a-caminho` → `finalizado`
- Advance via `PedidoController@avancarStatus` endpoint
- Status filtering in queries: `where('status', $status)` before pagination
- UI: Dynamic badge colors based on status (yellow/blue/green)

### Search & Filtering
Dedicated `SearchController` for type-ahead searches:
- `buscarCliente` - Client autocomplete
- `buscarProduto` - Product autocomplete with pricing
Returns JSON, not Inertia responses.

### File Structure Conventions
- Admin components: `resources/js/components/admin/{feature}/`
- UI components: `resources/js/components/ui/` (Shadcn imports)
- Shared components: `resources/js/components/` root
- Types: Single source at `resources/js/types/index.d.ts`

### Helpers & Utilities
- Global PHP helpers: `app/Helpers/helpers.php` (autoloaded in `composer.json`)
- Axios instance: `resources/js/lib/axios.ts` with `/api` baseURL
- Ziggy routes: Available as `route()` in Vue templates and TypeScript

## Important Notes

- **ESLint ignores**: `vendor/`, `node_modules/`, `public/`, `bootstrap/ssr/`, `resources/js/components/ui/*`
- **Vite config**: Dev server on `0.0.0.0:5173`, production assets in `public/build/`
- **Currency input**: Use `v-money3` directive for Brazilian Real formatting
- **Date handling**: Always use `Carbon` with `'America/Sao_Paulo'` timezone
- **Migrations**: Database is `familiaMogi`, user `familiaMogi`, MySQL 8.0 on port 3307

## When Adding Features

1. Create migration if database changes needed
2. Add/update model in `app/Models/`, extend `BaseModel`
3. Create controller in `app/Http/Controllers/` with Inertia renders
4. Define routes in appropriate route file with middleware
5. Create TypeScript interfaces in `resources/js/types/index.d.ts`
6. Build Vue page in `resources/js/pages/` with proper typing
7. Use existing UI components from `resources/js/components/ui/`
8. Follow existing naming: kebab-case for routes, PascalCase for components
