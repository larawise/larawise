<?php

namespace Larawise\Dashboard\Menu;

use Illuminate\Support\Collection;
use Larawise\Facades\Menu;
use Larawise\Dashboard\Search\SearchProvider;
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
class MenuProvider extends SearchProvider
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
        return $this->searchRecursive($keyword, Menu::all());
    }

    /**
     * Recursively traverses the menu tree and collects matching items.
     *
     * @param string $keyword
     * @param Collection $menu
     * @param string $prefix
     *
     * @return Collection
     */
    protected function searchRecursive($keyword, $menu, $prefix = '')
    {
        $items = collect();

        foreach ($menu as $item) {
            // Translate the menu item's name
            $name = trans($item['name']);

            // Translate the menu item's description
            $description = trans($item['description']);

            // Recursively search children if present
            if (! empty($item['children'])) {
                $children = $this->searchRecursive($keyword, $item['children'], $name);

                // If any children matched, merge them and skip current item
                if ($children->isNotEmpty()) {
                    $items = $items->merge($children);
                    continue;
                }
            }

            // If name matches keyword and URL is present, add to results
            if ($this->stringContains($name, $keyword) && ! empty($item['url'])) {
                $items->push(
                    new SearchResult(
                        icon: $item['icon'],
                        title: $name,
                        description: $description,
                        url: $item['url'],
                        parents: $prefix !== '' ? [$prefix] : [],
                    )
                );
            }
        }

        return $items;

    }
}
