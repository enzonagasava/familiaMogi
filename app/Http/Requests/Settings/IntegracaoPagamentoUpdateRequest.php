<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IntegracaoPagamento;
use Illuminate\Validation\Rule;

class IntegracaoPagamentoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // true se o usuário autenticado puder editar
        return true;
    }

    public function rules(): array
    {
        return [
            'public_key' => ['nullable', 'string'],
            'access_key' => ['nullable', 'string'],
        ];
    }
}
