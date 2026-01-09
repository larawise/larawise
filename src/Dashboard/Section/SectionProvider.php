<?php

namespace Larawise\Dashboard\Section;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Larawise\Facades\Section as SectionFacade;
use Larawise\Contracts\Dashboard\Section\SectionContract;
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
class SectionProvider extends SearchProvider
{
    /**
     * Caches resolved group names or other computed values for reuse.
     *
     * @var array
     */
    protected $cached = [];

    /**
     * Executes a search query and returns matching results.
     *
     * @param string $keyword
     *
     * @return Collection<SearchResult>
     */
    public function search($keyword)
    {
        $items = [];

        // Traverse all section groups
        foreach (SectionFacade::all() as $group => $sections) {
            /** @var SectionContract $section */
            foreach ($sections as $section) {
                $children = $section->items();

                // Skip empty sections
                if (! empty($children)) {
                    foreach ($children as $item) {
                        // Check if title or description contains the keyword
                        if (($this->stringContains($item->getTitle(), $keyword) || $this->stringContains($item->getDescription(), $keyword)) && ! empty($item->getUrl())) {
                            // Generate a unique key to prevent duplicates
                            $key = Str::slug("{$item->getTitle()}-{$item->getUrl()}");

                            if (array_key_exists($key, $items)) {
                                continue;
                            }

                            // Add matched item to results
                            $items[$key] = new SearchResult(
                                icon: $item->icon(),
                                title: $item->title(),
                                description: $item->description(),
                                url: $item->url(),
                                parents: [
                                    $this->groupName($group),
                                    $section->title(),
                                ],
                            );
                        }
                    }
                }
            }
        }

        return collect($items);
    }

    /**
     * Resolves and caches the display name of a section group.
     *
     * @param string $group
     *
     * @return string
     */
    protected function groupName($group)
    {
        // Lazily resolve and cache the group name
        return $this->cached[$group] ??= SectionFacade::withGroup($group)->groupName();
    }
}
