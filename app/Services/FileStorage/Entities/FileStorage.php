<?php

namespace App\Services\FileStorage\Entities;

use Illuminate\Database\Eloquent\Model;

class FileStorage extends Model
{
    protected $table = "aws";

    protected $guarded = [];
}
