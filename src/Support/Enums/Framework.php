<?php

namespace Larawise\Support\Enums;

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
enum Framework: int
{
    /**
     * `Native` represents the front-end framework
     */
    case NATIVE = 0;

    /**
     * `Inertia` represents the front-end framework
     */
    case INERTIA = 1;

    /**
     * `React.js` represents the front-end framework
     */
    case REACT = 2;

    /**
     * `Vue.js` represents the front-end framework
     */
    case VUE = 3;

    /**
     * `Livewire` represents the front-end framework
     */
    case LIVEWIRE = 4;
}
