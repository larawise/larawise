<?php

namespace Larawise\Support\Contracts;

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
interface TranslateableContract
{
    /**
     * Resolve the translated value.
     *
     * @param string|null $locale
     *
     * @return string
     */
    public function value($locale = null);
}
