<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/02/2019
 * Time: 10:18 PM
 */

namespace App\Http\Requests;


use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class StoreCierreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
       return auth()->user()->can('generar_cierre_periodo');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        try {
            $regex = Obra::query()->find(Context::getIdObra())->datosContables->FormatoCierreRegExp;
        } catch (\Exception $e) {
            $regex = "";
        }


        return [
            'anio' => ['required', 'integer'],
            'mes' => ['required', 'integer']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}