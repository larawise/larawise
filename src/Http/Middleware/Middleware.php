<?php

namespace Srylius\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Srylius\Http\Responses\Response;

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
class Middleware
{
    /**
     * Handle a incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
