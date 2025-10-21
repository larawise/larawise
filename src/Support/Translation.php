<?php

namespace Larawise\Support;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonSerializable;
use Stringable;

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
class Translation implements JsonSerializable, Stringable
{
    use ForwardsCalls;

    /**
     * The translation transformation callback.
     *
     * @var (callable(\Illuminate\Support\Stringable):(\Stringable|string))|null
     */
    public $transformCallback;

    /**
     * Create a new pending translation.
     *
     * @param string|null $key
     * @param array $replace
     * @param string|null $locale
     *
     * @return void
     */
    public function __construct(
        public $key = null,
        public $replace = [],
        public $locale = null
    ) { }

    /**
     * Transform the translation.
     *
     * @param (callable(\Illuminate\Support\Stringable):(\Stringable|string)) $transformCallback
     *
     * @return $this
     */
    public function transform(callable $transformCallback)
    {
        $this->transformCallback = $transformCallback;

        return $this;
    }

    /**
     * Get the resolved value.
     *
     * @param string|null $locale
     *
     * @return string
     */
    public function value($locale = null)
    {
        $locale ??= $this->locale;

        return (string) with(Str::of(
            transform(__($this->key, $this->replace, $locale), function ($translation) {
                return is_string($translation) ? $translation : $this->key;
            }) ?? ''
        ), $this->transformCallback);
    }

    /**
     * Dynamically proxy method calls to Stringable.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo(Str::of($this->value()), $method, $parameters);
    }

    /**
     * Get the translation as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value();
    }

    /**
     * Get the translation as json.
     *
     * @return string
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): string
    {
        return $this->value();
    }
}
