<?php

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Contabilidad\TipoCuentaContable;
use App\Models\CADECO\Obra;
use Dingo\Api\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UpdatePolizaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('editar_prepolizas_generadas');
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

        Validator::extend('tipo_cuenta_contable', function ($attribute, $value, $parameters, $validator) {
            return TipoCuentaContable::query()->find($value);
        });

        Validator::extend('sumas_iguales', function ($attribute, $value, $parameters, $validator) {
            if (isset($validator->getData()['movimientos']['data'])) {
                $suma_debe = 0;
                $suma_haber = 0;
                foreach ($validator->getData()['movimientos']['data'] as $movimiento) {
                    $suma_debe = $movimiento['id_tipo_movimiento_poliza'] == 1 ? $suma_debe + $movimiento['importe'] : $suma_debe;
                    $suma_haber = $movimiento['id_tipo_movimiento_poliza'] == 2 ? $suma_haber + $movimiento['importe'] : $suma_haber;
                }

                return abs($suma_debe - $suma_haber) < 0.99;
            } else {
                return false;
            }
        });

        return [
            'concepto' => ['string', 'max:4000', 'filled'],
            'fecha.date' => ['filled', 'date_format:"Y-m-d"'],
            'movimientos.data' => ['array'],
            'movimientos.data.*.concepto' => ['string', 'max:4000', 'filled'],
            'movimientos.data.*.cuenta_contable' => ['filled', "regex:'{$regex}'"],
            'movimientos.data.*.id_tipo_cuenta_contable' => ['integer', 'tipo_cuenta_contable'],
            'movimientos.data.*.id_tipo_movimiento_poliza' => ['integer', 'exists:cadeco.Contabilidad.tipos_movimientos,id'],
            'movimientos.data.*.importe' => ['numeric', 'sumas_iguales'],
            'movimientos.data.*.referencia' => ['string', 'max:100', 'filled'],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}
