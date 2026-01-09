<?php

namespace Larawise\Support\Traits;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
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
trait Throttleable
{
    /**
     * The rate limiter instance responsible for tracking and enforcing limits.
     *
     * @var RateLimiter
     */
    protected $limiter;

    /**
     * Create a new rate limiter instance.
     *
     * @param RateLimiter $limiter
     *
     * @return void
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Get the number of attempts for the given key.
     *
     * @param Request $request
     *
     * @return int
     */
    public function attempts(Request $request)
    {
        return $this->limiter->attempts($this->key($request));
    }

    /**
     * Determine if the request source has exceeded the allowed number of attempts.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function tooManyAttempts(Request $request)
    {
        return $this->limiter->tooManyAttempts($this->key($request), $this->maxAttempts());
    }

    /**
     * Increment the action attempts for the user.
     *
     * @param Request $request
     *
     * @return void
     */
    public function increment(Request $request)
    {
        $this->limiter->hit($this->key($request), $this->decaySeconds());
    }

    /**
     * Determine the number of seconds until logging in is available again.
     *
     * @param Request $request
     *
     * @return int
     */
    public function availableIn(Request $request)
    {
        return $this->limiter->availableIn($this->key($request));
    }

    /**
     * Clear the rate limit lock for the given request source.
     *
     * @param Request $request
     *
     * @return void
     */
    public function clear(Request $request)
    {
        $this->limiter->clear($this->key($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function key(Request $request)
    {
        return method_exists($this, 'key')
            ? $this->key($request)
            : Str::transliterate($request->ip());
    }

    /**
     * Get the time window in seconds during which rate limit attempts are tracked.
     *
     * @return int
     */
    abstract protected function decaySeconds();

    /**
     * Get the maximum number of allowed attempts within the decay window.
     *
     * @return int
     */
    abstract protected function maxAttempts();
}
