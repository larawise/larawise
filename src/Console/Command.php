<?php

namespace Larawise\Console;

use Illuminate\Console\Command as IlluminateCommand;

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
class Command extends IlluminateCommand
{
    /**
     * The larawise formatter instance.
     *
     * @var CommandStyle
     */
    protected $style;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->style = new CommandStyle($this);
    }
}
