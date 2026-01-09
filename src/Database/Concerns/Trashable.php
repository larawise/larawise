<?php

namespace Larawise\Database\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;

trait Trashable
{
    use SoftDeletes;
}
