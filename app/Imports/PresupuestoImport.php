<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PresupuestoImport implements ToCollection
{
    public function collection(Collection $collection)
    {
        return $collection;
    }
}