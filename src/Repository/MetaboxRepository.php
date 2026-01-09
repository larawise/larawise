<?php

namespace Larawise\Repository;

use Larawise\Models\Metabox;
use Larawise\Contracts\MetaboxContract;

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
class MetaboxRepository extends Repository implements MetaboxContract
{
    /**
     * Create a new metabox repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(new Metabox);
    }
}
