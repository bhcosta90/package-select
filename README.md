# Brcas Select

A powerful, zero-dependency select component for **Laravel** powered by **Alpine.js**. Works with Livewire, plain Blade, static options, remote URLs with search/pagination, optgroups, multiple selection, custom slots, and more — all in a single Blade file.

![Laravel](https://img.shields.io/badge/Laravel-11.x%20|%2012.x-red)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-blue)
![License](https://img.shields.io/badge/license-MIT-green)

---

## ✨ Features

- 🔌 **Two data modes** — Static options array or remote URL (API) with search & infinite scroll
- ⚡ **Livewire & Blade** — Full support for `wire:model` and `x-model`
- 🏷️ **Single & Multiple** — Tag-based multi-select with removable chips
- 📂 **Optgroup** — Group options with visual headers
- 🎨 **Fully themeable** — Global config or per-component Tailwind classes
- 🔍 **Searchable** — Real-time filtering with debounce
- ♾️ **Infinite scroll** — Auto-loads more results from API on scroll
- 🎰 **Custom slot** — Render each dropdown item with your own HTML template
- 🌐 **i18n ready** — All visible strings use `@lang()` for easy translation
- 🧩 **Self-contained** — Single Blade file, no JS build step required
- ⬆️⬇️ **Smart dropdown** — Auto-detects direction (up/down) based on viewport
- ✖️ **Clear button** — Optional, auto-hidden when `required`
- 📝 **Placeholder option** — Adds a "Select..." empty option at the top

---

## 📦 Installation

```bash
composer require brcas/select
```

Laravel auto-discovers the package — no manual provider registration needed.

### Tailwind CSS

Add the package views to your Tailwind content scan so the classes get compiled:

```css
/* resources/css/app.css */
@source '../../vendor/brcas/select/resources/**/*.blade.php';
```

---

## 🚀 Quick Start

```blade
{{-- Static options --}}
<x-bhcosta90::select
    name="status"
    :options="['pending' => 'Pending', 'approved' => 'Approved']"
/>

{{-- Remote URL --}}
<x-bhcosta90::select url="/api/users" wire:model="user_id" />

{{-- Multiple with Livewire --}}
<x-bhcosta90::select
    url="/api/tags"
    multiple
    wire:model.live="selectedTags"
/>
```

---

## 📖 Usage

### Static Options

#### Flat array (`key => label`)

```blade
<x-bhcosta90::select
    wire:model="status"
    :options="[
        'pending'  => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ]"
/>
```

#### With placeholder (empty option)

```blade
<x-bhcosta90::select
    wire:model="priority"
    placeholder="Select a priority..."
    :options="[
        1 => 'Low',
        2 => 'Medium',
        3 => 'High',
    ]"
/>
```

#### With optgroup

```blade
<x-bhcosta90::select
    wire:model="status"
    placeholder="Select..."
    :options="[
        'In Progress' => [
            'pending'    => 'Pending',
            'processing' => 'Processing',
        ],
        'Completed' => [
            'approved'  => 'Approved',
            'rejected'  => 'Rejected',
            'cancelled' => 'Cancelled',
        ],
    ]"
/>
```

#### With PHP Enum

The component accepts a `BackedEnum` class directly. Each case must have a `label()` method for the display text. Without `label()`, the case `->name` is used as fallback.

```php
enum StatusEnum: string
{
    case Pending   = 'pending';
    case Approved  = 'approved';
    case Rejected  = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }
}
```

Pass the class itself or `::cases()`:

```blade
{{-- Pass the Enum class --}}
<x-bhcosta90::select
    wire:model="status"
    :options="StatusEnum::class"
/>

{{-- Or pass the cases array --}}
<x-bhcosta90::select
    wire:model="status"
    :options="StatusEnum::cases()"
/>

{{-- With placeholder --}}
<x-bhcosta90::select
    wire:model="status"
    placeholder="Select a status..."
    :options="StatusEnum::class"
/>
```

#### Multiple selection

```blade
<x-bhcosta90::select
    wire:model.live="roles"
    multiple
    :options="[
        'admin'  => 'Administrator',
        'editor' => 'Editor',
        'viewer' => 'Viewer',
    ]"
/>
```

#### Required (hides the clear ✕ button)

```blade
<x-bhcosta90::select
    wire:model="status"
    required
    :options="['active' => 'Active', 'inactive' => 'Inactive']"
/>
```

---

### Remote URL (API)

The component fetches data from your API endpoint with search, pagination, and initial value resolution.

#### Basic

```blade
<x-bhcosta90::select url="/api/users" wire:model.live="user_id" />
```

#### With custom keys

```blade
<x-bhcosta90::select
    url="/api/countries"
    value-key="code"
    label-key="title"
    wire:model="country"
/>
```

#### With extra parameters

```blade
<x-bhcosta90::select
    url="/api/users"
    wire:model="user_id"
    :params="['role' => 'admin', 'active' => true]"
/>
```

#### Dynamic parameters (reactive with Alpine)

```blade
<div x-data="{ categoryId: 1 }">
    <select x-model="categoryId">...</select>

    <x-bhcosta90::select
        url="/api/products"
        name="product"
        x-params="{ category_id: categoryId }"
    />
</div>
```

> When `x-params` changes, the component automatically resets and reloads.

#### Custom slot (custom item template)

```blade
<x-bhcosta90::select url="/api/users" wire:model="user_id">
    <div class="flex flex-col px-4 py-2">
        <span class="font-medium">[:name:]</span>
        <span class="text-xs text-gray-400">[:email:]</span>
    </div>
</x-bhcosta90::select>
```

Use `[:field:]` placeholders — they are replaced with the actual values from each item.

---

### Blade (without Livewire)

Use `name`, `:value` / `:values`, and `x-model` instead of `wire:model`:

#### Single

```blade
<div x-data="{ country: 'br' }">
    <x-bhcosta90::select
        name="country"
        :value="'br'"
        x-model="country"
        :options="['us' => 'United States', 'br' => 'Brazil', 'de' => 'Germany']"
    />
</div>
```

#### Multiple

```blade
<div x-data="{ tags: ['php', 'go'] }">
    <x-bhcosta90::select
        name="tags"
        multiple
        :values="['php', 'go']"
        x-model="tags"
        :options="['php' => 'PHP', 'js' => 'JavaScript', 'go' => 'Go', 'rust' => 'Rust']"
    />
</div>
```

#### URL mode (Blade)

```blade
<div x-data="{ user: 5 }">
    <x-bhcosta90::select
        url="/api/users"
        name="user_id"
        :value="5"
        x-model="user"
    />
</div>
```

> Hidden `<input>` elements are automatically rendered for form submissions.

---

## 🌐 API Endpoint Contract

When using URL mode, your API must return JSON in this format:

### Search endpoint

```
GET /api/users?search=john&page=1&any_extra_param=value
```

```json
{
    "data": [
        { "id": 1, "name": "John Doe", "email": "john@example.com" },
        { "id": 2, "name": "Jane Doe", "email": "jane@example.com" }
    ],
    "meta": {
        "total": 42,
        "has_more_page": true
    }
}
```

### Initial value resolution

When the component has a pre-selected value, it fetches the label(s):

```
GET /api/users?ids[]=1&ids[]=5
```

Same response format — the component reads `data[*].{valueKey}` and `data[*].{labelKey}`.

| Field | Description |
|-------|-------------|
| `data` | Array of result objects |
| `meta.total` | Total matching records (shown in footer) |
| `meta.has_more_page` | `true` if more pages exist (enables infinite scroll) |

### Laravel example (manual)

```php
Route::get('/api/users', function (Request $request) {
    $query = User::query();

    // Initial value resolution
    if ($request->has('ids')) {
        return ['data' => $query->whereIn('id', $request->input('ids'))->get()];
    }

    // Search
    if ($search = $request->input('search')) {
        $query->where('name', 'like', "%{$search}%");
    }

    $paginator = $query->paginate(15);

    return [
        'data' => $paginator->items(),
        'meta' => [
            'total'         => $paginator->total(),
            'has_more_page' => $paginator->hasMorePages(),
        ],
    ];
});
```

### Using `SelectControllerTrait` (recommended)

The package includes an abstract `SelectControllerTrait` that handles all the boilerplate — ID resolution, search, pagination, and response formatting. Just extend it:

```php
use App\Http\Resources\UserResource;
use App\Models\User;
use Brcas\Select\Http\Controllers\SelectControllerTrait;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserSelectController
{
    use \Brcas\Select\Http\Controllers\SelectControllerTrait;

    protected function query(Request $request): Builder
    {
        return User::query();
    }

    protected function resource(): string
    {
        return UserResource::class;
    }
}
```

```php
// routes/api.php
Route::get('users', UserSelectController::class);
```

That's it — search, pagination, ID lookup, and the correct JSON format are all handled automatically.

#### Overridable methods

| Method | Default | Description |
|--------|---------|-------------|
| `query(Request)` | **(required)** | Return the base Eloquent query builder |
| `resource()` | **(required)** | Return the `JsonResource` class name |
| `searchColumns()` | `'name'` | Column(s) used for search |
| `perPage()` | `15` | Number of items per page |
| `orderBy()` | `'name'` | Default ordering column |
| `orderDirection()` | `'asc'` | Default ordering direction |
| `keyColumn()` | `'id'` | Primary key column for ID lookups |
| `minSearchLength()` | `2` | Minimum characters to trigger search on server |
| `cacheTtl()` | `300` | Cache duration (seconds) for empty-search results. `0` = disabled |
| `buildCacheKey(Request)` | *auto* | Cache key built from controller class + request params |
| `applyFilters(Builder, Request)` | *(no-op)* | Custom filtering logic (e.g. `:params`) |

#### Full example with customization

```php
class ProductSelectController
{
    use \Brcas\Select\Http\Controllers\SelectControllerTrait;
    
    protected function query(Request $request): Builder
    {
        return Product::query()->with('category');
    }

    protected function resource(): string
    {
        return ProductResource::class;
    }

    protected function searchColumns(): string|array
    {
        return ['name', 'sku', 'description'];
    }

    protected function perPage(): int
    {
        return 20;
    }

    protected function orderBy(): string
    {
        return 'created_at';
    }

    protected function orderDirection(): string
    {
        return 'desc';
    }

    protected function applyFilters(Builder $query, Request $request): void
    {
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($request->boolean('active')) {
            $query->where('active', true);
        }
    }
}
```

```blade
<x-bhcosta90::select
    url="/api/products"
    value-key="id"
    label-key="name"
    wire:model="product_id"
    :params="['category_id' => $categoryId, 'active' => true]"
/>
```

---

## ⚙️ Props Reference

### Data

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `url` | `string` | `''` | API endpoint for remote search |
| `options` | `array` | `[]` | Static options: `[key => label]`, `[['value'=>..,'label'=>..]]`, or optgroup format |
| `value-key` | `string` | `'id'` | Key used as the value in API responses |
| `label-key` | `string` | `'name'` | Key used as the display label in API responses |
| `multiple` | `bool` | `false` | Enable multiple selection |
| `name` | `string` | `''` | Input name for form submissions (Blade mode) |
| `value` | `mixed` | `null` | Pre-selected value (Blade single mode) |
| `values` | `array` | `[]` | Pre-selected values (Blade multiple mode) |
| `params` | `array` | `[]` | Extra query parameters sent with every API request |
| `placeholder` | `string` | `''` | Adds an empty option at the top (single only) |
| `required` | `bool` | `false` | Hides the clear (✕) button |
| `min-search-length` | `int` | `2` | Minimum characters to trigger search (0 = open on focus still works) |

### Directives

| Directive | Context | Description |
|-----------|---------|-------------|
| `wire:model` | Livewire | Two-way binding with Livewire property |
| `wire:model.live` | Livewire | Live (real-time) two-way binding |
| `x-model` | Blade/Alpine | Two-way binding with Alpine.js data |
| `x-params` | Blade/Alpine | Reactive extra params (resets on change) |

### Theme (Tailwind classes)

All theme props fall back to `config/select.php`. Pass them per-component to override:

| Prop | Config Key | Default |
|------|-----------|---------|
| `input-border` | `input_border` | `border-gray-300` |
| `input-focus-border` | `input_focus_border` | `focus-within:border-blue-500` |
| `input-focus-ring` | `input_focus_ring` | `focus-within:ring-blue-500` |
| `dropdown-border` | `dropdown_border` | `border-gray-200` |
| `item-hover-bg` | `item_hover_bg` | `hover:bg-blue-50` |
| `item-hover-text` | `item_hover_text` | `hover:text-blue-700` |
| `item-selected-bg` | `item_selected_bg` | `bg-blue-50` |
| `item-selected-text` | `item_selected_text` | `text-blue-700` |
| `item-selected-icon` | `item_selected_icon` | `text-blue-600` |
| `placeholder-text` | `placeholder_text` | `italic text-gray-400` |
| `tag-bg` | `tag_bg` | `bg-blue-100` |
| `tag-text` | `tag_text` | `text-blue-700` |
| `tag-hover-text` | `tag_hover_text` | `hover:text-blue-900` |
| `footer-border` | `footer_border` | `border-gray-100` |
| `footer-bg` | `footer_bg` | `bg-white` |
| `footer-text` | `footer_text` | `text-gray-400` |
| `empty-border` | `empty_border` | `border-gray-200` |
| `empty-bg` | `empty_bg` | `bg-white` |
| `empty-text` | `empty_text` | `text-gray-500` |

#### Per-component override example

```blade
<x-bhcosta90::select
    wire:model="status"
    item-hover-bg="hover:bg-green-50"
    item-selected-bg="bg-green-100"
    tag-bg="bg-green-100"
    tag-text="text-green-800"
    :options="['active' => 'Active', 'inactive' => 'Inactive']"
/>
```

---

## 🎨 Global Theme (config)

Publish the config to customize all selects at once:

```bash
php artisan vendor:publish --tag=select-config
```

This creates `config/select.php`:

```php
return [
    // Search operator for all SelectController endpoints.
    // Use 'like' for MySQL/SQLite or 'ilike' for PostgreSQL.
    'search_operator'    => 'like',

    'input_border'       => 'border-gray-300',
    'input_focus_border' => 'focus-within:border-blue-500',
    'input_focus_ring'   => 'focus-within:ring-blue-500',
    'dropdown_border'    => 'border-gray-200',
    'item_hover_bg'      => 'hover:bg-blue-50',
    'item_hover_text'    => 'hover:text-blue-700',
    'item_selected_bg'   => 'bg-blue-50',
    'item_selected_text' => 'text-blue-700',
    'item_selected_icon' => 'text-blue-600',
    'placeholder_text'   => 'italic text-gray-400',
    'tag_bg'             => 'bg-blue-100',
    'tag_text'           => 'text-blue-700',
    'tag_hover_text'     => 'hover:text-blue-900',
    'footer_border'      => 'border-gray-100',
    'footer_bg'          => 'bg-white',
    'footer_text'        => 'text-gray-400',
    'empty_border'       => 'border-gray-200',
    'empty_bg'           => 'bg-white',
    'empty_text'         => 'text-gray-500',
];
```

> Without publishing, the built-in defaults are used automatically.

---

## 🌍 Translations (i18n)

All user-facing strings use Laravel's `@lang()` with plain English phrases as keys:

| Key | Default (English) |
|-----|-------------------|
| `Loading...` | Loading... |
| `Search...` | Search... |
| `No results found.` | No results found. |
| `1–:showing of :total records` | 1–:showing of :total records |

To translate, create a JSON lang file — e.g. `lang/pt_BR.json`:

```json
{
    "Loading...": "Carregando...",
    "Search...": "Pesquisar...",
    "No results found.": "Nenhum resultado encontrado.",
    "1–:showing of :total records": "1–:showing de :total registros"
}
```

---

## 🧩 Publishing Views

To customize the component markup itself:

```bash
php artisan vendor:publish --tag=select-views
```

This copies the Blade file to `resources/views/vendor/select/components/select.blade.php`.

---

## ⌨️ Keyboard Support

| Key | Action |
|-----|--------|
| `↓` Arrow Down | Highlight next item (skips group headers) |
| `↑` Arrow Up | Highlight previous item (skips group headers) |
| `Enter` | Select highlighted item |
| `Escape` | Close dropdown |
| Type any text | Filter / search results (with 150ms debounce) |

---

## 📁 Package Structure

```
brcas/select/
├── composer.json
├── README.md
├── config/
│   └── select.php                        # Theme defaults (publishable)
├── resources/
│   └── views/
│       └── components/
│           └── select.blade.php          # The component (publishable)
└── src/
    ├── SelectServiceProvider.php          # Auto-discovered provider
    └── Http/
        └── Controllers/
            └── SelectController.php       # Abstract controller for API endpoints
```

---

## 📋 Options Format Reference

```php
// Flat (most common)
[1 => 'Option A', 2 => 'Option B']

// Key-value objects
[['value' => 1, 'label' => 'Option A'], ['value' => 2, 'label' => 'Option B']]

// Optgroup
[
    'Group A' => ['key1' => 'Label 1', 'key2' => 'Label 2'],
    'Group B' => ['key3' => 'Label 3'],
]

// Enum class (requires label() method on each case)
StatusEnum::class

// Enum cases
StatusEnum::cases()
```

---

## 🛠 Requirements

- PHP 8.2+
- Laravel 11.x or 12.x
- Alpine.js 3.x
- Tailwind CSS 3.x / 4.x

---

## 📄 License

MIT © [brcas](https://github.com/brcas)

