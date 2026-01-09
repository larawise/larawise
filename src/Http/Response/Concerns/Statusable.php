<?php

namespace Srylius\Concerns;

use Srylius\Support\Enums\Response;
use Srylius\Support\Enums\Response as ResponseStatus;

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
trait Statusable
{
    /**
     * The response status.
     *
     * @var ResponseStatus
     */
    protected $status = ResponseStatus::SUCCESS;

    /**
     * Sets the error response type.
     *
     * @return $this
     */
    public function error()
    {
        $this->status = Response::ERROR;

        return $this;
    }

    /**
     * Sets the info response type.
     *
     * @return $this
     */
    public function info()
    {
        $this->status = Response::INFO;

        return $this;
    }

    /**
     * Sets the success response type.
     *
     * @return $this
     */
    public function successful()
    {
        $this->status = Response::SUCCESS;

        return $this;
    }

    /**
     * Sets the warning response type.
     *
     * @return $this
     */
    public function warning()
    {
        $this->status = Response::WARNING;

        return $this;
    }
}
