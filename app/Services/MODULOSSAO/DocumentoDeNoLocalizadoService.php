<?php


namespace App\Services\MODULOSSAO;


use App\Models\CADECO\Empresa;
use App\Models\MODULOSSAO\ControlRemesas\Documento;
use App\Models\MODULOSSAO\ControlRemesas\DocumentoDeNoLocalizado;
use App\Models\MODULOSSAO\ControlRemesas\Remesa;
use App\Repositories\Repository;

class DocumentoDeNoLocalizadoService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * DocumentoDeNoLocalizadoService constructor.
     * @param DocumentoDeNoLocalizado $model
     */
    public function __construct(DocumentoDeNoLocalizado $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        return $this->repository->all($data);
    }

    public function paginate($data)
    {
        if (isset($data['anio']))
        {
            $remesas = Remesa::withoutGlobalScopes()->where([['Anio', 'LIKE', '%' . $data['anio'] . '%']])->pluck('IDRemesa');
            $documentos = Documento::withoutGlobalScopes()->whereIn('IDRemesa', $remesas)->pluck('IDDocumento');
            $this->repository->whereIn(['id_documento', $documentos]);
        }

        if (isset($data['numeroSemana']))
        {
            $remesas = Remesa::withoutGlobalScopes()->where([['NumeroSemana', 'LIKE', '%' . $data['numeroSemana'] . '%']])->pluck('IDRemesa');
            $documentos = Documento::withoutGlobalScopes()->whereIn('IDRemesa', $remesas)->pluck('IDDocumento');
            $this->repository->whereIn(['id_documento', $documentos]);
        }


        if (isset($data['numeroRemesa']))
        {
            $remesas = Remesa::withoutGlobalScopes()->where([['Folio', 'LIKE', '%' . $data['numeroRemesa'] . '%']])->pluck('IDRemesa');
            $documentos = Documento::withoutGlobalScopes()->whereIn('IDRemesa', $remesas)->pluck('IDDocumento');
            $this->repository->whereIn(['id_documento', $documentos]);
        }

        if (isset($data['proveedor']))
        {
            $empresa = Empresa::where([['razon_social', 'LIKE', '%' . $data['proveedor'] . '%']])->pluck('id_empresa');
            $documentos = Documento::whereIn('IDDestinatario', $empresa);
            $this->repository->whereIn(['id_documento', $documentos]);
        }

        if (isset($data['rfc']))
        {
            $empresa = Empresa::where([['rfc', 'LIKE', '%' . $data['rfc'] . '%']])->pluck('id_empresa');
            $documentos = Documento::whereIn('IDDestinatario', $empresa);
            $this->repository->whereIn(['id_documento', $documentos]);
        }

        if (isset($data['refencia']))
        {
            $documentos = Documento::where([['Refencia', 'LIKE', '%' . $data['refencia'] . '%']])->pluck('IDDocumento');;
            $this->repository->whereIn(['id_documento', $documentos]);
        }

        if (isset($data['moneda']))
        {
            $documentos = Documento::where([['Moneda', 'LIKE', '%' . $data['moneda'] . '%']])->pluck('IDDocumento');;
            $this->repository->whereIn(['id_documento', $documentos]);
        }

       return  $this->repository->paginate($data);
    }

    public function autorizar($id)
    {
        $documento = $this->repository->show($id);
        if ($documento->estado != 0) {
            abort(400, "El pago de la factura se encuentra con un estado " . $documento->estado_format . " previamente.");
        }
        return $documento->autorizar();
    }

    public function rechazar(array  $data, $id)
    {
        $documento = $this->repository->show($id);
        if ($documento->estado != 0) {
            abort(400, "El pago de la factura se encuentra con un estado " . $documento->estado_format . " previamente.");
        }
        return $documento->rechazar($data['motivo']);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }
}
