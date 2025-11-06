<?php

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IntegracaoPagamento;
use App\Http\Requests\Settings\IntegracaoPagamentoUpdateRequest;



class PagamentoConfigController extends Controller
{
    public function index(){
        $MetodoPagamento = IntegracaoPagamento::firstOrFail();

        return inertia('admin/configuracoes/PagamentoConfig', [
            'MetodoPagamento' => $MetodoPagamento
        ]);
    }

    public function update(IntegracaoPagamentoUpdateRequest $request){
        $MetodoPagamento = IntegracaoPagamento::firstOrFail();

        $MetodoPagamento->update($request->validated());

        return back()->with('success', 'Informações das credenciais atualizadas com sucesso!');
    }
}
