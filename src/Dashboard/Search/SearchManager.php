<?php

namespace Larawise\Dashboard\Search;

use Illuminate\Support\Collection;
use Larawise\Contracts\Dashboard\Search\SearchManagerContract;
use Larawise\Contracts\Dashboard\Search\SearchProviderContract;

/**
 * Srylius - The ultimate symphony for technology architecture!
 *
 * @package     Larawise
 * @subpackage  Core
 * @version     v1.0.0
 * @author      Selçuk Çukur <hk@selcukcukur.com.tr>
 * @copyright   Srylius Teknoloji Limited Şirketi
 *
 * @see https://docs.larawise.com/ Larawise : Docs
 */
class SearchManager implements SearchManagerContract
{
    /**
     * Create a new search manager instance.
     *
     * @param array $providers
     * @param array $resolved
     *
     * @return void
     */
    public function __construct(
        protected $providers = [],
        protected $resolved = [],
    ) { }

    /**
     * Registers a new search provider class.
     *
     * @param string $provider
     *
     * @return $this
     */
    public function extend($provider)
    {
        // Add the provider class to the list of registered providers
        $this->providers[] = $provider;

        return $this;
    }

    /**
     * Resolves and caches a search provider instance.
     *
     * @param string $provider
     *
     * @return SearchProviderContract
     */
    protected function resolve($provider)
    {
        // Lazily resolve and cache the provider instance
        return $this->resolved[$provider] ??= app($provider);
    }

    /**
     * Executes a search query and returns matching results.
     *
     * @param string $keyword
     * @param int $limit
     *
     * @return Collection<SearchResult>
     */
    public function search($keyword, $limit = 20)
    {
        $result = collect();

        foreach ($this->providers as $provider) {
            // Resolve the provider and execute its search logic
            $result = $result->merge(
                $this->resolve($provider)->search($keyword)->take($limit)
            );
        }

        return $result;
    }
}
