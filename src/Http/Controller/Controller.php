<?php

namespace Larawise\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Srylius\Http\Controllers\Concerns\HasResponse;
use Srylius\Support\Limiter;

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
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HasResponse;

    protected $limiter;

    /**
     * Create a new controller instance.
     *
     * @param Limiter $limiter
     *
     * @return void
     */
    public function __construct(Limiter $limiter)
    {
        $this->limiter = $limiter;
    }
}
