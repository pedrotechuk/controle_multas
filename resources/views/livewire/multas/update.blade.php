<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Illuminate\Validation\Rule;
use Mary\Traits\Toast;
use App\Models\Status;
use App\Models\StatusFinal;
use App\Models\Propriedade;
use App\Models\Multa;

use function Livewire\Volt\{rules, state, layout, mount, uses};

uses([Toast::class]);

state(['id'])->url();


state(['all_data' => []]);

state(['unidades' => [], 'propriedades' => [], 'locais' => []]);
state(['id', 'unidade', 'multa', 'data_ciencia', 'data_multa', 'data_limite', 'responsavel', 'propriedade', 'local', 'auto_infracao', 'condutor', 'data_identificacao', 'data_identificacao_detran', 'status']);

mount(function ($id) {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::with(['propriedade_model'])->findOrFail($id);

    $this->id = $this->multa->id;
    $this->unidade = $this->multa->unidade;
    $this->data_ciencia = $this->multa->data_ciencia;
    $this->data_multa = $this->multa->data_multa;
    $this->propriedade = $this->multa->propriedade_model->id ?? null;
    $this->responsavel = $this->multa->responsavel;
    $this->auto_infracao = $this->multa->auto_infracao;
    $this->condutor = $this->multa->condutor;
    $this->data_identificacao = $this->multa->data_identificacao;
    $this->data_identificacao_detran = $this->multa->data_identificacao_detran;
    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virginia Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];
    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local])->toArray();
    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name])->toArray();
    $this->status_finals = StatusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name])->toArray();
});

rules([
    'unidade' => ['nullable'],
    'data_ciencia' => ['nullable'],
    'data_multa' => ['nullable'],
    'responsavel' => ['nullable'],
    'propriedade' => ['nullable'],
    'auto_infracao' => ['nullable'],
    'condutor' => ['nullable'],
    'data_identificacao' => ['nullable'],
    'data_identificacao_detran' => ['nullable'],
]);

$update = function () {
    try {
        $data = $this->validate();

        if (isset($data['data_multa'])) {
            $data['data_limite'] = Carbon::parse($data['data_multa'])->addDays(40)->format('Y-m-d\TH:i');
        }

        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }
        $this->multa->update($data);

        $this->success('Informações salvas com sucesso!');
        return redirect(route('dashboard'));
    } catch (Exception $e) {
        dd($e->getMessage());
        return $this->error('Não foi possível atualizar dados, verifique os campos e tente novamente!');

    }
};

layout('layouts.app');
?>

<div>
    <form wire:submit.prevent="update">
        <x-card shadow separator class="bg-gray-100 p-4 shadow-md rounded">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold">Editar Multa N°{{ $this->id }}</h2>
                    <p class="text-gray-600">Criada em: {{ Carbon::parse($this->multa->created_at)->format('d/m/Y') }}
                        por {{ $this->multa->created_by }}</p>
                </div>
                <x-button class="btn-sm btn-outline" label="VOLTAR" icon="o-arrow-uturn-left"
                          link="{{ route('dashboard') }}"/>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4 bg-white p-4 shadow rounded">
                <x-select label="Unidade:" placeholder="Selecione uma unidade..." placeholder-value="0"
                          :options="$this->unidades" wire:model.live="unidade" icon="o-building-office-2"/>
                <x-datetime label="Data da ciência da multa:" wire:model="data_ciencia" icon="o-calendar"
                            type="datetime-local"/>
                <x-datetime label="Data da infração:" wire:model="data_multa" icon="o-calendar"
                            type="datetime-local"/>
                <x-select label="Propriedade/Local:" placeholder="Selecione a propriedade/local..." placeholder-value="0"
                          :options="$this->propriedades" wire:model.live="propriedade" icon="o-building-office"/>
                <x-input label="Responsável:" wire:model="responsavel" placeholder="Ex: João da Silva" icon="o-user"/>
                <x-input label="N° Auto Infração:" wire:model.live.debounce.300ms="auto_infracao"
                         oninput="this.value = this.value.toUpperCase()"
                         placeholder="Digite o n° da auto infração" icon="o-clipboard-document-list"/>
                <x-input label="Condutor: (Caso não identificado deixe em branco)" placeholder="Informe o condutor..."
                         wire:model.live="condutor" icon="o-building-office-2"/>
                <x-datetime label="Data da identificação:" wire:model="data_identificacao" icon="o-calendar"
                            type="datetime-local"/>
                <x-datetime label="Data da identificação no Detran:" wire:model="data_identificacao_detran"
                            icon="o-calendar" type="datetime-local"/>
            </div>
        </x-card>
    <div class="flex items-end mt-3">
        <x-button label="SALVAR" type="submit" class="btn-success w-full"/>
    </div>
    </form>
</div>
