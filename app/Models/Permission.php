<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as ModelsPermission;
use App\Traits\Filterable;

class Permission extends ModelsPermission
{
    use Filterable;

    protected $searchable = [
        'name',
        'description',
    ];

    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'guard_name',
        'description',
    ];
}
