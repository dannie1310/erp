<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 10/10/2019
 * Time: 08:19 PM
 */

namespace App\Http\Requests\Almacenes;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreEntradaAlmacenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_entrada_almacen');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        try {
            $regex = Obra::query()->find(Context::getIdObra())->datosContables->FormatoCuentaRegExp;
        } catch (\Exception $e) {
            $regex = "";
        }

        return [
            'remision' => ['required'],
            'id_orden_compra' => ['required', 'exists:cadeco.dbo.transacciones,id_transaccion'],
            'observaciones' => ['required'],
            'fecha' => ['required'],
            'partidas' => ['required']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}