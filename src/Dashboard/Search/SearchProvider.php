<?php

namespace Larawise\Dashboard\Search;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LogicException;
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
abstract class SearchProvider implements SearchProviderContract
{
    /**
     * Executes a search query and returns matching results.
     *
     * @param string $keyword
     *
     * @return Collection<SearchResult>
     */
    public function search($keyword)
    {
        throw new LogicException('Please implement the search() method.');
    }

    /**
     * Performs a case-insensitive substring check.
     *
     * @param string|null $haystack
     * @param string|null $needle
     *
     * @return bool
     */
    protected function stringContains($haystack, $needle)
    {
        return Str::contains(Str::lower((string) $haystack), Str::lower((string) $needle));
    }
}
