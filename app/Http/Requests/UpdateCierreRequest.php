<?php
/**
 * Created by PhpStorm.
 * User: DBenitezc
 * Date: 26/02/2019
 * Time: 10:15 PM
 */

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCierreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('editar_cierre_periodo');
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
            'id' => ['filled', 'numeric', 'exists:cadeco.Contabilidad.cierres,id']
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}