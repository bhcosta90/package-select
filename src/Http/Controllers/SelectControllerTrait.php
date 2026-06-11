<?php

declare(strict_types = 1);

namespace Brcas\Select\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

/**
 * Abstract controller for <x-brcas-select /> API endpoints.
 *
 * Extend this class and implement query() and resource().
 * Search, pagination, ID resolution and response format are handled automatically.
 */
trait SelectControllerTrait
{
    /**
     * Return the base Eloquent query builder.
     */
    abstract protected function query(Request $request): Builder;

    /**
     * Return the JsonResource class used to transform each item.
     *
     * @return class-string<JsonResource>
     */
    abstract protected function resource(): string;

    /**
     * Handle the incoming request.
     */
    public function index(Request $request): JsonResponse
    {
        $query    = $this->query($request);
        $resource = $this->resource();
        $this->applyFilters($query, $request);

        // Initial value resolution - fetch by IDs
        if ($request->has('ids')) {
            $ids   = $request->input('ids');
            $ids   = is_array($ids) ? $ids : explode(',', (string) $ids);
            $items = $query->whereIn($this->keyColumn(), $ids)->get();

            return response()->json([
                'data' => $resource::collection($items),
            ]);
        }

        $search    = $request->input('search', '');
        $hasSearch = mb_strlen($search ?: '') > 0;

        // Cache empty-search results (initial listing)
        if (!$hasSearch && $this->cacheTtl() > 0) {
            $cacheKey = $this->buildCacheKey($request);

            $cached = Cache::remember($cacheKey, $this->cacheTtl(), function () use ($query, $resource) {
                $paginator = $query
                    ->orderBy($this->orderBy(), $this->orderDirection())
                    ->paginate($this->perPage());

                return [
                    'data' => $resource::collection($paginator->items())->resolve(),
                    'meta' => [
                        'total'         => $paginator->total(),
                        'has_more_page' => $paginator->hasMorePages(),
                    ],
                ];
            });

            return response()->json($cached);
        }

        // Search with min length check
        if ($hasSearch) {
            $columns = (array) $this->searchColumns();
            $query->whereAny($columns, \Illuminate\Container\Container::getInstance()->make('config')->get('select.search_operator', 'like'), "%{$search}%");
        }

        // Paginate
        $paginator = $query
            ->orderBy($this->orderBy(), $this->orderDirection())
            ->paginate($this->perPage());

        return response()->json([
            'data' => $resource::collection($paginator->items()),
            'meta' => [
                'total'         => $paginator->total(),
                'has_more_page' => $paginator->hasMorePages(),
            ],
        ]);
    }

    /**
     * Column(s) used for search (LIKE %term%).
     *
     * @return string|string[]
     */
    protected function searchColumns(): string | array
    {
        return 'name';
    }

    /**
     * Number of items per page.
     */
    protected function perPage(): int
    {
        return 15;
    }

    /**
     * Default ordering column.
     */
    protected function orderBy(): string
    {
        return 'name';
    }

    /**
     * Default ordering direction.
     */
    protected function orderDirection(): string
    {
        return 'asc';
    }

    /**
     * Primary key column used for ID lookups.
     */
    protected function keyColumn(): string
    {
        return 'id';
    }

    /**
     * Cache duration in seconds for empty-search results.
     * Return 0 to disable caching.
     */
    protected function cacheTtl(): int
    {
        return 300; // 5 minutes
    }

    /**
     * Build a unique cache key based on the controller class, page, and extra params.
     */
    protected function buildCacheKey(Request $request): string
    {
        $params = $request->except(['search', 'ids']);
        ksort($params);

        return 'brcas_select:' . static::class . ':' . md5(json_encode($params));
    }

    /**
     * Apply extra filters from the request (e.g. params passed via :params).
     */
    protected function applyFilters(Builder $query, Request $request): void
    {
        //
    }
}
