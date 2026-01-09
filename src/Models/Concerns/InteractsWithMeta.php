<?php

namespace Larawise\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Larawise\Models\Metabox;
use Larawise\Facades\Metabox as MetaBoxSupport;
use Larawise\Mediaify\Facades\Mediaify;

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
trait InteractsWithMeta
{
    public function metadata(): MorphMany
    {
        return $this
            ->morphMany(Metabox::class, 'reference')
            ->select([
                'reference_id',
                'reference_type',
                'meta_key',
                'meta_value',
            ]);
    }

    public function getMetaData(string $key, bool $single = false): array|string|null
    {
        $field = $this->metadata
            ->where('meta_key', apply_filters('stored_meta_box_key', $key, $this))
            ->first();

        if (! $field) {
            $field = $this->metadata->where('meta_key', $key)->first();
        }

        if (! $field) {
            return $single ? '' : [];
        }

        return MetaBoxSupport::getMetaData($field, $key, $single);
    }

    public function saveMetaDataFromFormRequest(array|string $fields, Request $request): void
    {
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $field) {
            if (! $request->has($field)) {
                continue;
            }

            if ($request->hasFile($field . '_input')) {
                $uploadFolder = $this->upload_folder ?: Str::plural(Str::slug(class_basename($this)));

                $result = Mediaify::handleUpload($request->file($field . '_input'), 0, $uploadFolder);

                if (! $result['error']) {
                    $request->merge([$field => $result['data']->url]);
                }
            }

            if ($request->filled($field)) {
                MetaBoxSupport::saveMetaBoxData($this, $field, $request->input($field));
            } else {
                MetaBoxSupport::deleteMetaData($this, $field);
            }
        }
    }
}
