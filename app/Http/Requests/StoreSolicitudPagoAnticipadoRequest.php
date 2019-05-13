<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 13/05/2019
 * Time: 11:50 AM
 */

namespace App\Http\Requests;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudPagoAnticipadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('registrar_solicitud_pago_anticipado');
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
            'fecha_limite' => ['required', 'date_format:"Y-m-d"'],
            'fecha_solicitud' => ['required', 'date_format:"Y-m-d"'],
            'id_antecedente' => ['required', 'exists:cadeco.dbo.transacciones,id_transaccion'],
            'observaciones' => ['required', 'string'],
            'tipo_transaccion' => ['required', 'int'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}