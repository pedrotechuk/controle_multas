<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;
use App\Models\Status;
use App\Models\StatusFinal;
use App\Models\Propriedade;
use App\Models\Multa;

use function Livewire\Volt\{state, layout, mount, uses};

uses([Toast::class]);

state(['id'])->url();

state(['modal_ident_interna'])->modelable();

state(['all_data' => []]);

state(['unidades' => [], 'propriedades' => [], 'locais' => []]);
state(['unidade', 'data_ciencia', 'data_multa', 'data_limite', 'responsavel', 'propriedade', 'local', 'auto_infracao']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('admin.users.delete')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::find($this->id);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->locais = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->status_finals = StatusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);
});

$update = function () {
    $data = $this->validate(
        [
            'condutor' => ['required'],
            'data_identificacao' => ['required', 'date'],
        ],
        [
            'condutor.required' => 'Informe o condutor.',
            'data_identificacao.required' => 'Selecione a data da identificação.',
        ]
    );

    $data['data_limite'] = Carbon::parse($data['data_multa'])->addDays(40)->format('Y-m-d');

    $this->all_data = array_merge($this->all_data, $data);

    try {
        Multa::updated([
            'condutor' => $this->all_data['condutor'],
            'data_identificacao' => $this->all_data['data_identificacao'],
            'status' => 1,
            'updated_by' => Ad::username(),
        ]);

        $this->success('Multa cadastrada com sucesso!');

        $this->reset([
            'modal_multa', 'unidade', 'data_ciencia', 'data_multa', 'data_limite',
            'responsavel', 'propriedade', 'local', 'auto_infracao'
        ]);

        return redirect(route('dashboard'));
    } catch (Exception $e) {
        return $this->error('Não foi possível cadastrar a multa.');
    }
};

layout('layouts.app');
?>

<div>
    <h1 class="font-bold uppercase text-center text-gray-700 underline">IDENTIFICAÇÃO INTERNA</h1>

    <form wire:submit.prevent="save">
        @csrf
        <div class="flex flex-col gap-2">
            <x-input label="Condutor:" placeholder="Informe o condutor..." placeholder-value="0"
                      :options="$this->unidades" wire:model.live="condutor" icon="o-building-office-2"/>
            <x-datetime label="Data da identificação:" wire:model="data_identificacao" icon="o-calendar"
                        type="datetime-local"/>

            <div class="flex flex-row justify-evenly items-center mt-2">
                <x-button class="btn-sm " label="VOLTAR" icon="m-arrow-uturn-left"
                          link="{{ route('dashboard') }}"/>
                <x-button class="btn-sm btn-success text-white" label="SALVAR" icon="o-check" wire:click="update"/>
            </div>
        </div>
    </form>
</div>
