<?php

namespace Larawise\Models\Concerns;

use Illuminate\Support\Str;

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
trait InteractsWithSlug
{
    public static function createSlug(?string $name, int|string|null $id, string $fromColumn = 'slug'): string
    {
        $language = apply_filters('srylius_slugify_language', 'en');

        $slug = Str::slug($name, '-', $language);
        $index = 1;
        $baseSlug = $slug;

        while (
            self::query()
                ->where($fromColumn, $slug)
                ->when($id, fn ($query) => $query->whereNot('id', $id))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $index++;
        }

        if (empty($slug)) {
            $slug = time();
        }

        return $slug;
    }
}
