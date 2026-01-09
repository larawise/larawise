<?php

namespace Larawise\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

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
 *
 * @method static \Larawise\Database\Eloquent\Builder query()
 */
class Model extends EloquentModel
{
    use Concerns\InteractsWithBuilder;
}
