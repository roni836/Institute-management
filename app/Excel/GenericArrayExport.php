<?php
namespace App\Excel;

use Maatwebsite\Excel\Concerns\FromArray;

class GenericArrayExport implements FromArray
{
    protected $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function array(): array
    {
        return $this->data;
    }
}
