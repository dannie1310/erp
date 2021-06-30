<?php


namespace App\Services\MODULOSSAO;


use App\Models\MODULOSSAO\ControlRemesas\DocumentoDeNoLocalizado;
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

    public function paginate($data)
    {
       /* if (isset($data['Nombre']))
        {
            $this->repository->where([['Nombre', 'LIKE', '%'.$data['Nombre'].'%']]);
        }

        if (isset($data['empresa']))
        {
            $empresa = Empresa::where([['Empresa', 'LIKE', '%' . $data['empresa'] . '%']])->pluck('IDEmpresa');
            $this->repository->whereIn(['IDEmpresa', $empresa]);
        }

        if (isset($data['tipo']))
        {
            $tipo = TipoProyecto::where([['TipoProyecto', 'LIKE', '%' . $data['tipo'] . '%']])->pluck('IDTipoProyecto');
            $this->repository->whereIn(['IDTipoProyecto', $tipo]);
        }

        if (isset($data['CantidadExtraordinariasPermitidas']))
        {
            $this->repository->where([['CantidadExtraordinariasPermitidas', '=', $data['CantidadExtraordinariasPermitidas']]]);
        }*/
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
