<?php


namespace App\Observers\CTPQ;


use App\Models\CTPQ\OtherMetadata\Documento;

class DocumentoObserver
{
    /**
     * @param Documento $documento
     */
    public function creating(Documento $documento)
    {
        $documento->TimeStamp =  date('Y-m-d h:i:s');
        $documento->EmisionDate = date('Y-m-d h:i:s');
    }
}
