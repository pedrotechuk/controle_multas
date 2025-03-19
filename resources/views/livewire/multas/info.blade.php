<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Illuminate\Validation\Rule;
use Mary\Traits\Toast;
use App\Models\Status;
use App\Models\StatusFinal;
use App\Models\Propriedade;
use App\Models\Multa;

use function Livewire\Volt\{state, layout, mount, uses};

uses([Toast::class]);

state(['id'])->url();
state(['all_data' => []]);

state(['modal_multa' => false]);
state(['modal_ident_interna' => false]);
state(['modal_ident_detran' => false]);


state(['unidades' => [], 'propriedades' => [], 'statuses' => [], 'status_finals' => []]);
state(['filters', 'unidade', 'multa', 'data_ciencia', 'data_multa', 'data_limite', 'responsavel', 'propriedade', 'auto_infracao', 'condutor', 'condutor_modal', 'data_identificacao', 'identificador_interno', 'data_identificacao_detran', 'identificador_detran', 'status', 'status_final']);


mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::with('infracao')->find($this->id);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('id', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->status_finals = statusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);

});

layout('layouts.app');
?>

<div>
    <x-card shadow separator class="bg-gray-100 p-4 shadow-md rounded">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">Informações da Multa N°{{$this->multa->id }}</h2>
                <p class="text-gray-600">Código da Infração: {{$this->multa->infracao->cod }}</p>
            </div>
            <x-button class="btn-sm btn-outline" label="VOLTAR" icon="o-arrow-uturn-left"
                      @click="window.history.back()"/>
        </div>
        <div class="grid grid-cols-7 gap-2 mt-4">
            <x-input readonly label="Responsável:" value="{{$this->multa->infracao->responsavel}}"/>
            <x-input readonly label="Valor Infração:" value="R$ {{number_format($this->multa->infracao->valor, 2, ',', '.')}}"/>

            @php
                $classeTexto = 'text-gray-900'; // Cor padrão
                if ($this->multa && $this->multa->valor_pago !== null) {
                    if ($this->multa->valor_pago > $this->multa->infracao->valor) {
                        $classeTexto = 'text-red-600 font-bold';
                    } elseif ($this->multa->valor_pago < $this->multa->infracao->valor) {
                        $classeTexto = 'text-green-600 font-bold';
                    }
                }
            @endphp

            <x-input readonly label="Valor pago:" class="{{ $classeTexto }}"
                     value="{{ $this->multa->valor_pago !== null ? 'R$ ' . number_format($this->multa->valor_pago, 2, ',', '.') : 'Não Informado' }}"/>

            <x-input readonly label="Orgão Atuador:" value="{{$this->multa->infracao->orgao_atuador}}"/>
            <x-input readonly label="Art. CTB:" value="{{$this->multa->infracao->art_ctb}}"/>
            <x-input readonly label="Pontos:" value="{{$this->multa->infracao->pontos}}"/>
            <x-input readonly label="Gravidade:" value="{{$this->multa->infracao->gravidade}}"/>
            <div class="col-span-7 mt-2">
                <x-input readonly label="Auto Infração:" value="{{$this->multa->infracao->infracao}}"/>
            </div>
        </div>
    </x-card>

</div>
