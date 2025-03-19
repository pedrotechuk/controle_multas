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
state(['multa', 'status_final', 'unidade', 'data_ciencia', 'descontado', 'nao_identificacao', 'nao_desconto', 'cod_triare', 'data_multa', 'data_limite', 'responsavel', 'responsavel', 'propriedade', 'auto_infracao', 'finalizado_por']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::with(['propriedade_model', 'nao_identificado_model', 'nao_descontado_model'])->find($this->id);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->nao_identificados = NaoIdentificado::whereNull('deleted_at')->orderBy('justificativa', 'asc')->get()->map(fn($nao_identificados) => ['id' => $nao_identificados->id, 'name' => $nao_identificados->justificativa]);

    $this->nao_descontos = NaoDescontado::whereNull('deleted_at')->orderBy('justificativa', 'asc')->get()->map(fn($nao_descontados) => ['id' => $nao_descontados->id, 'name' => $nao_descontados->justificativa]);

    $this->status_finals = StatusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);
});

$finalize = function () {
    $data = $this->validate(
        [
            'status_final' => ['required'],
            'descontado' => ['nullable'],
            'nao_identificacao' => ['nullable', 'required_unless:status_final,1,2'],
            'nao_desconto' => ['nullable', 'required_unless:status_final,1,3'],
            'cod_triare' => ['required'],
        ],
        [
            'status_final.required' => 'Selecione o status final.',
            'nao_identificacao.required_if' => 'Informe o motivo da não identificacão.',
            'nao_desconto.required_if' => 'Informe o motivo da não identificacão.',
            'cod_triare.required' => 'Código triare é obrigatório.'
        ]
    );
    $this->all_data = array_merge($this->all_data, $data);

    try {
        $this->multa->update([
            'status_final' => $this->all_data['status_final'],
            'descontado' =>  $this->all_data['descontado'] ?? null,
            'nao_identificacao' => $this->all_data['nao_identificacao'] ?? null,
            'nao_desconto' => $this->all_data['nao_desconto'] ?? null,
            'cod_triare' => $this->all_data['cod_triare'] ?? null,
            'data_finalizada' => Carbon::now(),
            'status' => 4,
            'finalizado_por' => Ad::username(),
        ]);

        $this->success('Multa finalizada com sucesso!');

        $this->reset(['status_final']);

        return redirect(route('dashboard'));
    } catch (Exception $e) {
        return $this->error('Não foi possível finalizar a multa.');
    }
};

layout('layouts.app');
?>

<div>
    <x-card title="Finalizar Multa N°{{ $this->id }}"
            subtitle="Criada em: {{ Carbon::parse($this->multa->created_at)->format('d/m/Y') }} por {{ $this->multa->created_by }}"
            shadow separator class="bg-gray-100 p-4 shadow-md rounded">
        <div class="grid grid-cols-4 gap-2">
            <x-input readonly label="Unidade:"
                     value="{{ $this->multa->unidade == 1 ? 'Maringá' : ($this->multa->unidade == 3 ? 'Guarapuava' : ($this->multa->unidade == 7 ? 'Ponta Grossa' : ($this->multa->unidade == 10 ? 'Norte Pioneiro' : ''))) }}"/>
            <x-input readonly label="Auto Infração:" value="{{ $this->multa->auto_infracao }}"/>
            <x-input readonly label="Responsável:" value="{{ $this->multa->responsavel }}"/>
            <x-input readonly label="Corresponsável:" value="{{ $this->multa->corresponsavel ?? 'Não definido'}}"/>
            <x-input readonly label="Condutor:" value="{{ $this->multa->condutor}}"/>
            <x-input readonly label="Data Multa:"
                     value="{{ Carbon::parse($this->multa->data_multa)->format('d/m/Y') }}"/>
            <x-input readonly label="Data Limite:"
                     value="{{ Carbon::parse($this->multa->data_limite)->format('d/m/Y') }}"/>
            <x-input readonly label="Data Ciência:"
                     value="{{ Carbon::parse($this->multa->data_ciencia)->format('d/m/Y') }}"/>
            <x-input readonly label="Data Identificação:"
                     value="{{ Carbon::parse($this->multa->data_identificacao)->format('d/m/Y') }}"/>
            <x-input readonly label="Data Identificação Detran:"
                     value="{{ Carbon::parse($this->multa->data_identificacao_detran)->format('d/m/Y') }}"/>
            <x-input readonly label="Propriedade:"
                     value="{{ $this->multa->propriedade_model->local }} - {{ match($multa->unidade) {
                                1 => 'Maringá',
                                3 => 'Guarapuava',
                                7 => 'Ponta Grossa',
                                10 => 'Norte Pioneiro',
                                default => $multa->unidade
                            } }}"
            />
            <x-input readonly label="Criado por:" value="{{ $this->multa->created_by}}"/>
        </div>
    </x-card>

    <div class="mt-2">
        <x-select class="mb-2" label="Status Final:" placeholder="Selecione o status final..." placeholder-value="0"
                  :options="$this->multa->condutor === 'Não Identificado'
        ? collect($this->status_finals)->filter(fn($status) => in_array($status['id'], [3, 4]))->toArray()
        : collect($this->status_finals)->filter(fn($status) => in_array($status['id'], [1, 2]))->toArray()"
                  wire:model.live="status_final" icon="o-check"/>


        @if($this->status_final == 2)
            <x-select class="mt-1 mb-1" label="Qual o motivo do não desconto?" placeholder="Selecione o motivo..." placeholder-value="0"
                      :options="$this->nao_descontos" wire:model.live="nao_desconto" icon="o-document-text"/>
        @elseif($this->status_final == 3)
            <x-select class="mt-1 mb-1" label="Qual o motivo da não identificação?" placeholder="Selecione o motivo..." placeholder-value="0"
                      :options="$this->nao_identificados" wire:model.live="nao_identificacao" icon="o-document-text"/>
            <x-input class="mt-1 mb-1" label="Quem será responsabilizado pelo desconto?" wire:model="descontado"
                     placeholder="Ex: João da Silva" icon="o-user"/>
        @elseif($this->status_final == 4)
            <x-select class="mt-1 mb-1" label="Qual o motivo da não identificação?" placeholder="Selecione o motivo..." placeholder-value="0"
                      :options="$this->nao_identificados" wire:model.live="nao_identificacao" icon="o-document-text"/>
            <x-select class="mt-1 mb-1" label="Qual o motivo do não desconto?" placeholder="Selecione o motivo..." placeholder-value="0"
                      :options="$this->nao_descontos" wire:model.live="nao_desconto" icon="o-document-text"/>
        @endif

        <x-input label="Código Triare:" type='number' wire:model="cod_triare" placeholder="12345...."
                 icon='o-computer-desktop'/>

        <button type="button" style="background-color: green;"
                class="w-full text-white font-bold p-2 rounded mt-2 btn-success" wire:click="finalize({{ $multa->id }})"
                wire:confirm="Deseja realmente finalizar essa multa?">
            FINALIZAR MULTA
        </button>
    </div>
</div>
