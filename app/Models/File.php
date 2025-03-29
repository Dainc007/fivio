<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class File extends Model
{
    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
