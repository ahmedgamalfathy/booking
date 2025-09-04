<?php
namespace App\Services\Select;

use App\Models\Setting\Param\Param;

class ParameterSelectService
{

    public function getAllParameters()
    {
        return Param::get(['id as value', 'type as label']);
    }
}
