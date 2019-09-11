<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 11/09/2019
 * Time: 05:30 PM
 */

namespace App\Http\Requests\Almacenes;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreAjustePositivoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_distribucion_recursos_remesa');
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
            'id_almacen' => ['required', 'exists:cadeco.dbo.almacenes,id_almacen'],
            'referencia' => ['required']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}