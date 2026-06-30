<?php

namespace App\Http\Requests\Admin\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnuncioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'produto_id' => ['required', 'integer', 'exists:tenant_content.produtos,id'],
            'anuncio_ativo' => ['sometimes', 'boolean'],
            'anuncio_status' => ['nullable', 'string', 'max:255'],
            'anuncio_tipos' => ['required', 'array', 'min:1'],
            'anuncio_tipos.*' => [
                'required',
                'string',
                Rule::in(['Google_ads', 'Instagram_ads', 'Whatsapp_campaign', 'Site_anuncio']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'produto_id.required' => 'Selecione um produto para anunciar.',
            'produto_id.exists' => 'O produto selecionado não foi encontrado.',
            'anuncio_tipos.required' => 'Selecione pelo menos um tipo de anúncio.',
            'anuncio_tipos.min' => 'Selecione pelo menos um tipo de anúncio.',
            'anuncio_tipos.*.in' => 'Um tipo de anúncio selecionado é inválido.',
        ];
    }
}
