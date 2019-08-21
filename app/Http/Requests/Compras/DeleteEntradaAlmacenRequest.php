<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 20/08/2019
 * Time: 09:20 PM
 */

namespace App\Http\Requests\Compras;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class DeleteEntradaAlmacenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('eliminar_entrada_almacen');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Validator::extend('saldos_inventario', function ($attribute, $value, $parameters, $validator) {
            dd("aqu111i", $validator->getData(), $attribute, $value, $parameters);
            if (isset($validator->getData()['partidas'])) {
//                $suma_debe = 0;
//                $suma_haber = 0;
//                foreach ($validator->getData()['movimientos']['data'] as $movimiento) {
//                    $suma_debe = $movimiento['id_tipo_movimiento_poliza'] == 1 ? $suma_debe + $movimiento['importe'] : $suma_debe;
//                    $suma_haber = $movimiento['id_tipo_movimiento_poliza'] == 2 ? $suma_haber + $movimiento['importe'] : $suma_haber;
//                }
//
//                return abs($suma_debe - $suma_haber) < 0.99;
            } else {
                return false;
            }
        });

        return [
            'partidas.*.inventario' => ['saldos_inventario'],
            'motivo' => ['required']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}