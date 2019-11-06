<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 02/10/2019
 * Time: 05:53 PM
 */

namespace App\Http\Requests\Finanzas;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreCargaLayoutPagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_carga_layout_pago');
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

        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}