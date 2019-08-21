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
            'vencimiento' => ['required', 'date_format:"Y-m-d"'],
            'cumplimiento' => ['required', 'date_format:"Y-m-d"'],
            'importe' => ['required' ],
            'observaciones' => ['required', 'string'],
            'id_antecedente' => ['required'],
            'id_costo' => ['required', 'exists:cadeco.dbo.costos,id_costo']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}