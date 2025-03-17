<?php

use App\Models\NaoDescontado;
use App\Models\NaoIdentificado;
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

state(['unidades' => [], 'propriedades' => [], 'locais' => [], 'status_finals' => [], 'nao_identificados' => [], 'nao_descontos' => [], 'verifyStatusFinal' => []]);
state(['multa', 'status_final', 'unidade', 'data_ciencia', 'nao_identificacao', 'nao_desconto', 'cod_triare', 'data_multa', 'data_limite', 'responsavel', 'corresponsavel', 'propriedade', 'auto_infracao', 'finalizado_por']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::with(['propriedade_model', 'nao_identificado_model', 'nao_descontado_model'])->find($this->id);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->nao_identificados = NaoIdentificado::whereNull('deleted_at')->orderBy('justificativa', 'asc')->get()->map(fn($nao_identificados) => ['id' => $nao_identificados->id, 'name' => $nao_identificados->nao_identificacao]);

    $this->nao_descontos = NaoDescontado::whereNull('deleted_at')->orderBy('justificativa', 'asc')->get()->map(fn($nao_descontados) => ['id' => $nao_descontados->id, 'name' => $nao_descontados->justificativa]);

    $this->status_finals = StatusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);
});

layout('layouts.app');
?>

<div>
    <x-card shadow separator class="bg-gray-100 p-4 shadow-md rounded">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">Informações Multa N°{{ $this->id }}</h2>
                <p class="text-gray-600">Criada em: {{ Carbon::parse($this->multa->created_at)->format('d/m/Y') }}
                    por {{ $this->multa->created_by }}</p>
            </div>
            <x-button class="btn-sm btn-outline" label="VOLTAR" icon="o-arrow-uturn-left"
                      link="{{ route('consultas.index') }}"/>
        </div>
        <div class="grid grid-cols-3 gap-2 mt-4">
            <x-input readonly label="Unidade:"
                     value="{{ $this->multa->unidade == 1 ? 'Maringá' : ($this->multa->unidade == 3 ? 'Guarapuava' : ($this->multa->unidade == 7 ? 'Ponta Grossa' : ($this->multa->unidade == 10 ? 'Norte Pioneiro' : ''))) }}"/>
            <x-input readonly label="Auto Infração:" value="{{ $this->multa->auto_infracao }}"/>
            <x-input readonly label="Responsável:" value="{{ $this->multa->responsavel }}"/>
            <x-input readonly label="Corresponsável:" value="{{ $this->multa->responsavel_model->nome_completo ?? 'Não definido'}}"/>
            <x-input readonly label="Condutor:" value="{{ $this->multa->condutor}}"/>
            <x-input readonly label="Data Multa:"
                     value="{{ Carbon::parse($this->multa->data_multa)->format('d/m/Y') }}"/>
            <x-input readonly label="Data Limite:"
                     value="{{ Carbon::parse($this->multa->data_limite)->format('d/m/Y') }}"/>
            <x-input readonly label="Data Ciência:"
                     value="{{ Carbon::parse($this->multa->data_ciencia)->format('d/m/Y') }}"/>
            <x-input readonly label="Data Identificação:"
                     value="{{ Carbon::parse($this->multa->data_identificacao)->format('d/m/Y') }}"/>
            <x-input readonly label="Identificador Interno:" value="{{ $this->multa->identificador_interno}}"/>
            <x-input readonly label="Data Identificação Detran:"
                     value="{{ Carbon::parse($this->multa->data_identificacao_detran)->format('d/m/Y') }}"/>
            <x-input readonly label="Identificador Detran:" value="{{ $this->multa->identificador_detran}}"/>
            <x-input readonly label="Propriedade/ Local:" value="{{ $this->multa->propriedade_model->local }}"/>
            <x-input readonly label="Status Final:" value="{{ $this->multa->status_final_model->status_final_name }}"/>
            <x-input readonly label="Finalizado por:" value="{{ $this->multa->finalizado_por}}"/>
            <x-input readonly label="Código da infração:" value="{{ $this->multa->cod_infracao }}"/>
            <x-input readonly label="Código Triare:" value="{{ $this->multa->cod_triare}}"/>
            <x-input readonly label="Motivo não identificação:"
                     value="{{ $this->multa->nao_identificacao == 1 ? 'Perda de prazo' :
                             ( $this->multa->nao_identificacao == 2 ? 'Desligado':
                             ( $this->multa->nao_identificacao == 3 ? 'Autorização Superior' :
                             ( $this->multa->nao_identificacao == 4 ? 'Multa não recebida a tempo' : ''))) ?? 'N/A'}}"/>
            <x-input readonly label="Motivo não desconto:"
                     value="{{ $this->multa->nao_desconto == 1 ? 'Desligado' :
                             ( $this->multa->nao_desconto == 2 ? 'Responsábilidade da empresa':
                             ( $this->multa->nao_desconto == 3 ? 'Autorização Superior' : ''))}}"/>
            <x-input readonly label="Descontado de:" value="{{ $this->multa->descontado}}"/>

            <x-button class="btn-outline mt-7" tooltip="Detalhes da infração." icon="o-information-circle"
                      label="DETALHES DA INFRAÇÃO"
                      link="{{ route('multas.info', ['id' => $this->multa->id]) }}"/>

        </div>
    </x-card>
</div>
