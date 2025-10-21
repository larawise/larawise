<?php

use Larawise\Support\Contracts\TranslateableContract;
use Larawise\Support\Translation;

if (! function_exists('translatable')) {
    /**
     * Create a new translatable message instance.
     *
     * @param Translation|string|null $key
     * @param array $replace
     * @param string|null $locale
     *
     * @return TranslateableContract
     */
    function translatable($key = null, $replace = [], $locale = null)
    {
        if ($key instanceof Translation) {
            return $key;
        }

        return new Translation($key, $replace, $locale);
    }
}

if (! function_exists('t')) {
    /**
     * Alias for the translatable() helper.
     *
     * @see translatable()
     *
     * @param Translation|string|null $key
     * @param array $replace
     * @param string|null $locale
     *
     * @return TranslateableContract
     */
    function t($key = null, $replace = [], $locale = null)
    {
        if ($key instanceof Translation) {
            return $key;
        }

        return new Translation($key, $replace, $locale);
    }
}
