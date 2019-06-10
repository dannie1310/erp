<?php


namespace App\LAYOUT;


class DistribucionRecursoRemesa
{
    public function __construct($id)
    {
        $remesa = \App\Models\CADECO\Finanzas\DistribucionRecursoRemesa::with('partidas')->where('id', '=', $id)->get();

    }

    function create(){
        exit;
    }
}
