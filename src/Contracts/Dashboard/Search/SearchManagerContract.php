<?php

namespace Larawise\Contracts\Dashboard\Search;

use Illuminate\Support\Collection;
use Larawise\Dashboard\Search\SearchResult;

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
interface SearchManagerContract
{
    /**
     * Registers a new search provider class.
     *
     * @param string $provider
     *
     * @return $this
     */
    public function extend($provider);

    /**
     * Executes a search query and returns matching results.
     *
     * @param string $keyword
     * @param int $limit
     *
     * @return Collection<SearchResult>
     */
    public function search($keyword, $limit = 20);
}
