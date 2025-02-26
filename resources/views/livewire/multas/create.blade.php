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

state(['modal_multa'])->modelable();

state(['all_data' => []]);

state(['unidades' => [], 'propriedades' => [], 'locais' => []]);
state(['unidade', 'data_ciencia', 'data_multa', 'data_limite', 'responsavel', 'propriedade', 'local', 'auto_infracao']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::find($this->id);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->locais = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->status_finals = StatusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);
});

$save = function () {
    $data = $this->validate(
        [
            'unidade' => ['required'],
            'data_ciencia' => ['required', 'date'],
            'data_multa' => ['required', 'date'],
            'responsavel' => ['required'],
            'propriedade' => ['required'],
            'local' => ['required'],
            'auto_infracao' => [
                'required',
                Rule::unique('multas', 'auto_infracao')->whereNull('deleted_at'),
            ],
        ],
        [
            'unidade.required' => 'Selecione a unidade.',
            'data_ciencia.required' => 'Selecione a data de ciência da multa.',
            'data_multa.required' => 'Selecione a data da infração.',
            'responsavel.required' => 'Informe o responsável.',
            'propriedade.required' => 'Selecione a unidade proprietária.',
            'local.required' => 'Selecione o local da infração.',
            'auto_infracao.required' => 'Informe o n° da auto infração.',
            'auto_infracao.unique' => 'Auto infração já utilizada.',
        ]
    );

    $data['data_limite'] = Carbon::parse($data['data_multa'])->addDays(40)->format('Y-m-d');

    $this->all_data = array_merge($this->all_data, $data);

    try {
        Multa::create([
            'unidade' => $this->all_data['unidade'],
            'data_ciencia' => $this->all_data['data_ciencia'],
            'data_multa' => $this->all_data['data_multa'],
            'data_limite' => $this->all_data['data_limite'],
            'responsavel' => $this->all_data['responsavel'],
            'propriedade' => $this->all_data['propriedade'],
            'local' => $this->all_data['local'],
            'auto_infracao' => $this->all_data['auto_infracao'],
            'status' => 1,
            'created_by' => Ad::username(),
            'updated_by' => Ad::username(),
        ]);

        $this->success('Multa cadastrada com sucesso!');

        $this->reset([
            'modal_multa', 'unidade', 'data_ciencia', 'data_multa', 'data_limite',
            'responsavel', 'propriedade', 'local', 'auto_infracao'
        ]);

        return redirect(route('dashboard'));
    } catch (Exception $e) {
        dd($e->getMessage());
        return $this->error('Não foi possível cadastrar a multa.');
    }
};

layout('layouts.app');
?>

<div>
    <h1 class="font-bold uppercase text-center text-gray-700 underline">INFORMAÇÕES DA MULTA</h1>

    <form wire:submit.prevent="save">
        @csrf
        <div class="flex flex-col gap-2">
            <x-select label="Unidade:" placeholder="Selecione uma unidade..." placeholder-value="0"
                      :options="$this->unidades" wire:model.live="unidade" icon="o-building-office-2"/>
            <x-datetime label="Data da ciência da multa:" wire:model="data_ciencia" icon="o-calendar"
                        type="datetime-local"/>
            <x-datetime label="Data da infração:" wire:model="data_multa" icon="o-calendar"
                        type="datetime-local"/>

            <div class="flex flex-row w-full justify-between">
                <x-select label="Propriedade:" placeholder="Selecione a proprietária..." placeholder-value="0"
                          :options="$this->propriedades" wire:model.live="propriedade" icon="o-building-office"/>
                <x-select label="Local:" placeholder="Selecione o local..." placeholder-value="0"
                          :options="$this->locais" wire:model.live="local" icon="o-building-office"/>
            </div>

            <x-input label="Responsável:" wire:model="responsavel" placeholder="Ex: João da Silva" icon="o-user"/>
            <x-input label="N° Auto Infração:" wire:model.live.debounce.300ms="auto_infracao"
                     oninput="this.value = this.value.toUpperCase()"
                     placeholder="Digite o n° da auto infração" icon="o-clipboard-document-list"/>

            <div class="flex flex-row justify-evenly items-center mt-2">
                <x-button class="btn-sm " label="VOLTAR" icon="m-arrow-uturn-left"
                          link="{{ route('dashboard') }}"/>
                <x-button class="btn-sm btn-success text-white" label="SALVAR" icon="o-check" wire:click="save"/>
            </div>
        </div>
    </form>
</div>
