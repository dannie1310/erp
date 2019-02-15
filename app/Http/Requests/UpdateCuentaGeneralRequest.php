<?php

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCuentaGeneralRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('editar_cuenta_general');
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
            'cuenta_contable' => ['filled', "regex:'{$regex}'"],
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}