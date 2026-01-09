<?php

namespace Srylius\Support\Enums;

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
enum Response: string
{
    /**
     * Represents a successful response.
     *
     * @var string
     */
    case SUCCESS = 'successful';

    /**
     * Represents an error response.
     *
     * @var string
     */
    case ERROR = 'error';

    /**
     * Represents an informational response.
     *
     * @var string
     */
    case INFO = 'information';

    /**
     * Represents a warning response.
     *
     * @var string
     */
    case WARNING = 'warning';
}
