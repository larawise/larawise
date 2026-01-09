<?php

namespace Larawise\Database\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Translatable
{
    protected ?string $translationLocale = null;

    /**
     * Automatically cast all translatable attributes to arrays.
     *
     * @return void
     */
    public function initializeTranslatable()
    {
        $this->mergeCasts(array_fill_keys($this->translatableAttributes(), 'array'));
    }

    public static function usingLocale(string $locale): self
    {
        return (new self)->withLocale($locale);
    }

    public function useFallbackLocale(): bool
    {
        if (property_exists($this, 'useFallbackLocale')) {
            return $this->useFallbackLocale;
        }

        return true;
    }

    /**
     * Get the value of an attribute, resolving translations if applicable.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::getAttributeValue($key);
        }

        return $this->translate($key, $this->getLocale(), $this->useFallbackLocale());
    }

    /**
     * Prepare a translatable attribute for array conversion.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    protected function mutateAttributeForArray($key, $value)
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::mutateAttributeForArray($key, $value);
        }

        $translations = $this->getTranslations($key);

        return array_map(fn ($value) => parent::mutateAttributeForArray($key, $value), $translations);
    }

    /**
     * Set the value of an attribute, resolving translation logic if applicable.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if (! $this->isTranslatableAttribute($key)) {
            return parent::setAttribute($key, $value);
        }

        if (is_array($value) && (! array_is_list($value) || count($value) === 0)) {
            return $this->setTranslations($key, $value);
        }

        return $this->setTranslation($key, $this->getLocale(), $value);
    }

    /**
     * Retrieve the translated value for a given attribute and locale, with full fallback and mutator support.
     *
     * @param string $key
     * @param string $locale
     * @param bool $useFallbackLocale
     *
     * @return mixed
     */
    public function translate($key, $locale = '', $useFallbackLocale = true)
    {
        $normalizedLocale = $this->normalizeLocale($key, $locale, $useFallbackLocale);

        $isKeyMissingFromLocale = ($locale !== $normalizedLocale);

        $translations = $this->getTranslations($key);

        $baseKey = Str::before($key, '->'); // get base key in case it is JSON nested key

        $translatableConfig = app(Translatable::class);

        if (is_null(self::getAttributeFromArray($baseKey))) {
            $translation = null;
        } else {
            $translation = isset($translations[$normalizedLocale]) ? $translations[$normalizedLocale] : null;
            $translation ??= ($translatableConfig->allowNullForTranslation) ? null : '';
        }

        if ($isKeyMissingFromLocale && $translatableConfig->missingKeyCallback) {
            try {
                $callbackReturnValue = ($translatableConfig->missingKeyCallback)($this, $key, $locale, $translation, $normalizedLocale);
                if (is_string($callbackReturnValue)) {
                    $translation = $callbackReturnValue;
                }
            } catch (Exception) {
                // prevent the fallback to crash
            }
        }

        $key = str_replace('->', '-', $key);

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        if ($this->hasAttributeMutator($key)) {
            return $this->mutateAttributeMarkedAttribute($key, $translation);
        }

        return $translation;
    }

    /**
     * Retrieve the translated value for a given attribute and locale, falling back if missing.
     *
     * @param string $key
     * @param string $locale
     *
     * @return mixed
     */
    public function translateWithFallback($key, $locale)
    {
        return $this->translate($key, $locale, true);
    }

    /**
     * Retrieve the translated value for a given attribute and locale, without fallback.
     *
     * @param string $key
     * @param string $locale
     *
     * @return mixed
     */

    public function translateWithoutFallback($key, $locale)
    {
        return $this->translate($key, $locale, false);
    }








    public function getTranslations(?string $key = null, ?array $allowedLocales = null): array
    {
        if ($key !== null) {
            $this->guardAgainstNonTranslatableAttribute($key);

            if ($this->isNestedKey($key)) {
                [$key, $nestedKey] = explode('.', str_replace('->', '.', $key), 2);
            }

            return array_filter(
                Arr::get($this->fromJson($this->getAttributeFromArray($key)), $nestedKey ?? null, []),
                fn ($value, $locale) => $this->filterTranslations($value, $locale, $allowedLocales, $translatableConfig->allowNullForTranslation, $translatableConfig->allowEmptyStringForTranslation),
                ARRAY_FILTER_USE_BOTH,
            );
        }

        return array_reduce($this->translatableAttributes(), function ($result, $item) use ($allowedLocales) {
            $result[$item] = $this->getTranslations($item, $allowedLocales);

            return $result;
        });
    }

    public function setTranslation(string $key, string $locale, $value): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        $translations = $this->getTranslations($key);

        $oldValue = $translations[$locale] ?? '';

        $mutatorKey = str_replace('->', '-', $key);

        if ($this->hasSetMutator($mutatorKey)) {
            $method = 'set'.Str::studly($mutatorKey).'Attribute';

            $this->{$method}($value, $locale);

            $value = $this->attributes[$key];
        } elseif ($this->hasAttributeSetMutator($mutatorKey)) { // handle new attribute mutator
            $this->setAttributeMarkedMutatedAttributeValue($mutatorKey, $value);

            $value = $this->attributes[$mutatorKey];
        }

        $translations[$locale] = $value;

        if ($this->isNestedKey($key)) {
            unset($this->attributes[$key], $this->attributes[$mutatorKey]);

            $this->fillJsonAttribute($key, $translations);
        } else {
            $this->attributes[$key] = json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $this;
    }

    public function setTranslations(string $key, array $translations): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        if (! empty($translations)) {
            foreach ($translations as $locale => $translation) {
                $this->setTranslation($key, $locale, $translation);
            }
        } else {
            $this->attributes[$key] = $this->asJson([]);
        }

        return $this;
    }

    public function forgetTranslation(string $key, string $locale): self
    {
        $translations = $this->getTranslations($key);

        unset(
            $translations[$locale],
            $this->$key
        );

        $this->setTranslations($key, $translations);

        return $this;
    }

    public function forgetTranslations(string $key, bool $asNull = false): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        collect($this->getTranslatedLocales($key))->each(function (string $locale) use ($key) {
            $this->forgetTranslation($key, $locale);
        });

        if ($asNull) {
            $this->attributes[$key] = null;
        }

        return $this;
    }

    public function forgetAllTranslations(string $locale): self
    {
        collect($this->translatableAttributes())->each(function (string $attribute) use ($locale) {
            $this->forgetTranslation($attribute, $locale);
        });

        return $this;
    }

    public function getTranslatedLocales(string $key): array
    {
        return array_keys($this->getTranslations($key));
    }

    public function isNestedKey(string $key): bool
    {
        return str_contains($key, '->');
    }

    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->translatableAttributes());
    }

    public function hasTranslation(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?: $this->getLocale();

        return isset($this->getTranslations($key)[$locale]);
    }

    public function replaceTranslations(string $key, array $translations): self
    {
        foreach ($this->getTranslatedLocales($key) as $locale) {
            $this->forgetTranslation($key, $locale);
        }

        $this->setTranslations($key, $translations);

        return $this;
    }

    protected function guardAgainstNonTranslatableAttribute(string $key): void
    {
        // if (! $this->isTranslatableAttribute($key)) {
        //     throw TranslationException::make($key, $this);
        // }
    }

    protected function normalizeLocale(string $key, string $locale, bool $useFallbackLocale): string
    {
        $translatedLocales = $this->getTranslatedLocales($key);

        if (in_array($locale, $translatedLocales)) {
            return $locale;
        }

        if (! $useFallbackLocale) {
            return $locale;
        }

        if (method_exists($this, 'getFallbackLocale')) {
            $fallbackLocale = $this->getFallbackLocale();
        }

        $fallbackConfig = app(Translatable::class);

        $fallbackLocale ??= config('app.fallback_locale');

        if (! is_null($fallbackLocale) && in_array($fallbackLocale, $translatedLocales)) {
            return $fallbackLocale;
        }

        if (! empty($translatedLocales) && $fallbackConfig->fallbackAny) {
            return $translatedLocales[0];
        }

        return $locale;
    }

    /**
     * Determine if a translation value passes filtering rules.
     *
     * @param mixed $value
     * @param string|null $locale
     * @param array|null $allowedLocales
     * @param bool $allowNull
     * @param bool $allowEmptyString
     *
     * @return bool
     */
    protected function filterTranslations($value = null, $locale = null, $allowedLocales = null, $allowNull = false, $allowEmptyString = false)
    {
        if ($value === null && ! $allowNull) {
            return false;
        }

        if ($value === '' && ! $allowEmptyString) {
            return false;
        }

        if ($allowedLocales === null) {
            return true;
        }

        if (! in_array($locale, $allowedLocales)) {
            return false;
        }

        return true;
    }

    public function withLocale($locale)
    {
        $this->translationLocale = $locale;

        return $this;
    }

    public function getLocale()
    {
        return $this->translationLocale ?: config('app.locale');
    }

    /**
     * Get the list of translatable attributes defined on the model.
     *
     * @return array<string>
     */
    public function translatableAttributes()
    {
        return is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    /**
     * Returns all translations for translatable attributes.
     *
     * @return Attribute
     */
    public function translations()
    {
        return Attribute::get(function () {
            return collect($this->translatableAttributes())
                ->mapWithKeys(function (string $key) {
                    return [$key => $this->getTranslations($key)];
                })
                ->toArray();
        });
    }

    /**
     * Get all unique locales used across translatable attributes.
     *
     * @return array<string>
     */
    public function locales()
    {
        return array_unique(
            array_reduce($this->translatableAttributes(), function ($result, $item) {
                return array_merge($result, $this->getTranslatedLocales($item));
            }, [])
        );
    }

    /**
     * Filter models where the given locale exists in the JSON column.
     *
     * @param Builder $query
     * @param string $column
     * @param string $locale
     *
     * @return void
     */
    public function scopeWhereLocale(Builder $query, $column, $locale)
    {
        $query->whereNotNull("{$column}->{$locale}");
    }

    /**
     * Filter models where at least one of the given locales exists in the JSON column.
     *
     * @param Builder $query
     * @param string $column
     * @param array<string> $locales
     *
     * @return void
     */
    public function scopeWhereLocales(Builder $query, $column, $locales)
    {
        $query->where(function (Builder $query) use ($column, $locales) {
            foreach ($locales as $locale) {
                $query->orWhereNotNull("{$column}->{$locale}");
            }
        });
    }

    /**
     * Filter models where the JSON column contains a specific value for a given locale.
     *
     * @param Builder $query
     * @param string $column
     * @param string $locale
     * @param mixed  $value
     * @param string $operand
     *
     * @return void
     */
    public function scopeWhereJsonContainsLocale(Builder $query, $column, $locale, $value, $operand = '=')
    {
        $query->where("{$column}->{$locale}", $operand, $value);
    }

    /**
     * Filter models where any of the given locales contain the specified value.
     *
     * @param Builder $query
     * @param string $column
     * @param array<string> $locales
     * @param mixed $value
     * @param string $operand
     *
     * @return void
     */
    public function scopeWhereJsonContainsLocales(Builder $query, $column, $locales, $value, $operand = '=')
    {
        $query->where(function (Builder $query) use ($column, $locales, $value, $operand) {
            foreach ($locales as $locale) {
                $query->orWhere("{$column}->{$locale}", $operand, $value);
            }
        });
    }
}
