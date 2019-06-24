<?php

namespace App\Http\Requests;

use App\Facades\Context;
use App\Models\CADECO\Costo;
use App\Models\CADECO\Obra;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class RevertirAprobacionEstimacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('revertir_aprobacion_estimacion_subcontrato');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Permisos insuficientes');
    }
}