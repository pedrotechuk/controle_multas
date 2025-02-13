<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;
use App\Models\Status;
use App\Models\StatusFinal;
use App\Models\Propriedade;
use App\Models\Multa;

use function Livewire\Volt\{state, layout, mount, uses, with, usesPagination};

usesPagination();

uses([Toast::class]);

state(['id'])->url();
state(['all_data' => []]);

state(['modal_multa' => false]);
state(['modal_ident_interna' => false]);
state(['modal_ident_detran' => false]);


state(['unidades' => [], 'propriedades' => [], 'statuses' => [], 'status_finals' => []]);
state(['unidade', 'multa', 'data_ciencia', 'data_multa', 'data_limite', 'responsavel', 'propriedade', 'local', 'auto_infracao', 'condutor', 'data_identificacao', 'data_identificacao_detran', 'status', 'status_final']);


mount(function () {
    if (!Gate::forUser(Auth::user())->allows('admin.users.delete')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::find($this->id);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->locais = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->status_finals = statusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);

});

with(function () {
    $multas = Multa::query()->orderBy('data_multa', 'desc')
        ->when($this->unidade, fn($query) => $query->where('unidade', $this->unidade))
        ->when($this->data_ciencia, fn($query) => $query->whereDate('data_ciencia', $this->data_ciencia))
        ->when($this->data_multa, fn($query) => $query->whereDate('data_multa', $this->data_multa))
        ->when($this->data_limite, fn($query) => $query->whereDate('data_limite', $this->data_limite))
        ->when($this->responsavel, fn($query) => $query->where('responsavel', 'LIKE', "%{$this->responsavel}%"))
        ->when($this->propriedade, fn($query) => $query->where('propriedade', $this->propriedade))
        ->when($this->local, fn($query) => $query->where('propriedade', $this->local))
        ->when($this->auto_infracao, fn($query) => $query->where('auto_infracao', 'LIKE', "%{$this->auto_infracao}%"))
        ->when($this->condutor, fn($query) => $query->where('condutor', 'LIKE', "%{$this->condutor}%"))
        ->when($this->data_identificacao, fn($query) => $query->whereDate('data_identificacao', $this->data_identificacao))
        ->when($this->data_identificacao_detran, fn($query) => $query->whereDate('data_identificacao_detran', $this->data_identificacao_detran))
        ->when($this->status, fn($query) => $query->where('status', $this->status))
        ->when($this->status_final, fn($query) => $query->where('status_final', $this->status_final));

    return [
        'multas' => $multas->paginate(15),
    ];
});

$inativarMulta = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('admin.users.delete')) {
        return $this->error('Sem permissão para excluir multa.');
    }

    try {
        Multa::find($id)->update([
            'deleted_at' => Carbon::now(),
            'deleted_by' => Ad::username(),
            'status' => 5
        ]);

        return $this->success('Multa excluída com sucesso');
    } catch (Exception $e) {
        return $this->error('Não foi possível excluir multa.');
    }
};

$ident_Interna = function () {
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

    $this->all_data = array_merge($this->all_data, $data);

    try {
        $this->multa->update([
            'condutor' => $this->all_data['condutor'],
            'data_identificacao' => $this->all_data['data_identificacao'],
            'status' => 2,
            'updated_by' => Ad::username(),
        ]);

        $this->success('Identificação interna realizada com sucesso!');

        $this->reset([
            'condutor', 'data_identificacao'
        ]);

        return redirect(route('dashboard'));
    } catch (Exception $e) {
        return $this->error('Não foi possível concluir a identificação interna.');
    }
};

$ident_Detran = function () {
    $data = $this->validate(
        [
            'data_identificacao_detran' => ['required', 'date'],
        ],
        [
            'data_identificacao_detran.required' => 'Selecione a data da identificação.',
        ]
    );

    $this->all_data = array_merge($this->all_data, $data);

    try {
        $this->multa->update([
            'data_identificacao_detran' => $this->all_data['data_identificacao_detran'],
            'status' => 3,
            'updated_by' => Ad::username(),
        ]);

        $this->success('Identificação Detran realizada com sucesso!');

        $this->reset([
            'condutor', 'data_identificacao_detran'
        ]);

        return redirect(route('dashboard'));
    } catch (Exception $e) {
        return $this->error('Não foi possível concluir a identificação Detran.');
    }
};

$openModalMulta = function () {
  $this->modal_multa = true;
};

$openModalIdentInterna  = function ($id) {
    $this->multa = Multa::find($id);
    $this->modal_ident_interna= true;
};

$openModalIdentDetran = function ($id){
    $this->multa = Multa::find($id);

    if ($this->multa) {
        $this->multa_id = $this->multa->id;
        $this->condutor = $this->multa->condutor;
        $this->data_identificacao = $this->multa->data_identificacao;
    }

    $this->modal_ident_detran = true;
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Multas Cadastradas</h1>
            <x-button class="btn-sm btn-success" label="ADICIONAR MULTA" icon="o-plus" link="" wire:click="openModalMulta"/>
        </div>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mt-2">
            <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="py-2 px-4 border-b">Unidade</th>
                <th class="py-2 px-4 border-b">Data Multa</th>
                <th class="py-2 px-4 border-b">Data Limite</th>
                <th class="py-2 px-4 border-b">Status</th>
                <th class="py-2 px-4 border-b">Responsável</th>
                <th class="py-2 px-4 border-b">Propriedade/Local</th>
                <th class="py-2 px-4 border-b">N° Auto Infração</th>
                <th class="py-2 px-4 border-b">Condutor</th>
                <th class="py-2 px-4 border-b">Etapas</th>
                <th class="py-2 px-4 border-b">Ações</th>

            </tr>
            </thead>
            <tbody class="text-gray-800">
            @forelse ($multas as $multa)
                <tr class="hover:bg-slate-50">
                    <td class="py-2 px-4 border-b text-center">
                    @switch($multa->unidade)
                        @case(1)
                            Maringá
                        @break

                        @case(3)
                            Guarapuava
                        @break

                        @case(7)
                            Ponta Grossa
                        @break

                        @case(10)
                            Norte Pioneiro
                        @break
                    @endswitch
                    </td>
                    <td class="py-2 px-4 border-b text-center">{{ Carbon::parse($multa->data_ciencia)->format('d/m/Y') }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ Carbon::parse($multa->data_limite)->format('d/m/Y') }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $multa->status_model->status_name ?? 'N/A' }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $multa->responsavel }}</td>
                    <td class="py-2 px-4 border-b text-center">{{$multa->propriedade_model->local}} - {{ $multa->local_model->local }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $multa->auto_infracao }}</td>
                    <td class="py-2 px-4 border-b text-center">
                        @if ($multa->condutor)
                            {{ $multa->condutor }}
                        @else
                            <span class="text-orange-500 ">Pendente</span>
                        @endif
                    </td>
                    {{--                    <td class="py-2 px-4 border-b text-center">{{ $multa->status_final_model->status_final_name }}</td>--}}

                    <td class="py-2 px-4 border-b text-center">
                        <x-button class="btn-outline btn-sm" tooltip="Identificação Interna" icon="o-clipboard-document-list" link=""
                                  wire:click="openModalIdentInterna({{$multa->id}})"/>

                        <x-button class="btn-outline btn-sm" tooltip="Identificação Detran" icon="o-building-library"
                                  wire:click="openModalIdentDetran({{$multa->id}})" />

                        <x-button class="btn-outline btn-success btn-sm" tooltip="Finalizar multa" icon="o-clipboard-document-check"
                                  />
                    </td>

                    <td class="py-2 px-4 border-b text-center">
                        <x-button class="btn-sm" tooltip="Editar multa." icon="o-pencil"
                                  />

                        <x-button tooltip="Excluir multa." icon="o-trash" class="btn-error btn-sm text-white"
                                  wire:confirm="Deseja realmente inativar essa multa?"
                                  wire:click="inativarMulta({{ $multa->id }})"/>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-slate-50">
                    <td class="py-2 px-4 border-b text-center ">Não há multas cadastradas.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $multas->links() }}
    </div>

    <x-modal wire:model.live="modal_multa" class="backdrop-blur">
        <livewire:multas.create/>
    </x-modal>

    <x-modal wire:model.live="modal_ident_interna" class="backdrop-blur">
        <div>
            <h1 class="font-bold uppercase text-center text-gray-700 underline">IDENTIFICAÇÃO INTERNA</h1>
            <form wire:submit.prevent="ident_Interna">
                <div class="flex flex-col gap-2">
                    <x-input label="Condutor:" placeholder="Informe o condutor..." wire:model.live="condutor" icon="o-building-office-2"/>
                    <x-datetime label="Data da identificação:" wire:model="data_identificacao" icon="o-calendar" type="datetime-local"/>
                    <div class="flex flex-row justify-evenly items-center mt-2">
                        <x-button class="btn-sm " label="FECHAR" wire:click="$set('modal_ident_interna', false)"/>
                        <x-button class="btn-sm btn-success text-white" label="SALVAR" icon="o-check" wire:click="ident_Interna"/>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal wire:model.live="modal_ident_detran" class="backdrop-blur">
        <div>
            <h1 class="font-bold uppercase text-center text-gray-700 underline">IDENTIFICAÇÃO DETRAN</h1>
            <form wire:submit.prevent="ident_Detran">
                <div class="flex flex-col gap-2">
                    <x-input readonly label="Condutor:" placeholder="Informe o condutor..." wire:model.live="condutor" icon="o-building-office-2"/>
                    <x-datetime label="Data da identificação no Detran:" wire:model="data_identificacao_detran" icon="o-calendar" type="datetime-local"/>
                    <div class="flex flex-row justify-evenly items-center mt-2">
                        <x-button class="btn-sm " label="FECHAR" wire:click="$set('modal_ident_detran', false)"/>
                        <x-button class="btn-sm btn-success text-white" label="SALVAR" icon="o-check" wire:click="ident_Detran"/>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>
</div>
