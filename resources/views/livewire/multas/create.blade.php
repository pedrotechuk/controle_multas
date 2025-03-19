<?php

use App\Models\User;
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

state(['unidades' => [], 'propriedades' => [], 'usuarios' => []]);
state(['unidade', 'data_ciencia', 'data_multa', 'data_limite', 'responsavel', 'propriedade', 'placa', 'auto_infracao', 'cod_infracao', 'valor_pago']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::find($this->id);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->usuarios = User::whereNull('deleted_at')->orderBy('nome_completo', 'asc')->get()->map(fn($usuario) => ['id' => $usuario->name, 'name' => $usuario->nome_completo]);

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

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
            'placa' => ['nullable'],
            'auto_infracao' => [
                'required',
                Rule::unique('multas', 'auto_infracao')->whereNull('deleted_at'),
            ],
            'cod_infracao' => ['required'],
            'valor_pago' => ['required', 'min:0.01'],
        ],
        [
            'unidade.required' => 'Selecione a unidade.',
            'data_ciencia.required' => 'Selecione a data de ciência da multa.',
            'data_multa.required' => 'Selecione a data da infração.',
            'responsavel.required' => 'Informe o responsável.',
            'propriedade.required' => 'Selecione a unidade proprietária.',
            'auto_infracao.required' => 'Informe o n° da auto infração.',
            'auto_infracao.unique' => 'Auto infração já utilizada.',
            'cod_infracao.required' => 'Informe o código da infração',
            'valor_pago.required' => 'Informe o valor pago',
            'valor_pago.min' => 'Valor de frete não pode ser vazio.',
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
            'placa' => $this->all_data['placa'],
            'auto_infracao' => $this->all_data['auto_infracao'],
            'cod_infracao' => $this->all_data['cod_infracao'],
            'valor_pago' => $this->all_data['valor_pago'],
            'status' => 1,
            'created_by' => Ad::username(),
            'updated_by' => Ad::username(),
        ]);

        $this->success('Multa cadastrada com sucesso!');

        $this->reset([
            'modal_multa', 'unidade', 'data_ciencia', 'data_multa', 'data_limite',
            'responsavel', 'propriedade', 'placa', 'auto_infracao', 'cod_infracao', 'valor_pago'
        ]);

        return redirect(route('dashboard'));
    } catch (Exception $e) {
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

            <div class="flex flex-row w-full justify-between mt-2 -mb-4">
                <x-select label="Propriedade/Local:" placeholder="Selecione..." placeholder-value="0"
                          :options="$this->propriedades" wire:model.live="propriedade" icon="o-building-office"/>

                <x-input label="Placa do Veículo" wire:model="placa"
                         class="uppercase !flex !flex-1" icon="m-table-cells" hint="Opcional"/>
            </div>
            <x-select label="Responsável:" wire:model="responsavel" placeholder="Selecione o responsável..." placeholder-value="0"
                      :options="$this->usuarios" wire:model.live="responsavel" icon="o-user"/>
            <x-input label="N° Auto Infração:" wire:model.live.debounce.300ms="auto_infracao"
                     oninput="this.value = this.value.toUpperCase()"
                     placeholder="Digite o n° da auto infração" icon="o-clipboard-document-list"/>
            <x-input label="Código da infração:" type='number' wire:model="cod_infracao" placeholder="12345...."
                     icon='o-computer-desktop'/>
            <x-input label="Valor pago:" placeholder="Ex.: 150,00" prefix="R$" money
                     wire:model="valor_pago"  />
            <div class="flex flex-row justify-evenly items-center mt-2">
                <x-button class="btn-sm " label="VOLTAR" icon="m-arrow-uturn-left"
                          link="{{ route('dashboard') }}"/>
                <x-button class="btn-sm btn-success text-white" label="SALVAR" icon="o-check" wire:click="save"/>
            </div>
        </div>
    </form>
</div>
