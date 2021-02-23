<?php


namespace App\Imports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PolizaImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @return Collection
     */
    public function collection(Collection $collection)
    {
        return $collection;
    }
}
