<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Illuminate\Support\Facades\Session;
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
state(['filters', 'unidade', 'multa', 'data_ciencia', 'data_multa', 'data_limite', 'responsavel', 'propriedade', 'local', 'auto_infracao', 'condutor', 'data_identificacao', 'data_identificacao_detran', 'status', 'status_final']);


mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::find($this->id);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->locais = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->status_finals = statusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);

    $filters = Session::get('filters', []);

    $this->unidade = $filters['unidade'] ?? [];
    $this->data_multa = $filters['data_multa'] ?? null;
    $this->data_limite = $filters['data_limite'] ?? null;
    $this->status_final = $filters['status_final'] ?? null;
    $this->responsavel = $filters['responsavel'] ?? null;
    $this->propriedade = $filters['propriedade'] ?? null;
    $this->auto_infracao = $filters['auto_infracao'] ?? null;
    $this->condutor = $filters['condutor'] ?? null;
});

with(function () {
    $multas = Multa::query()->orderBy('data_finalizada', 'desc')
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
        ->when($this->status_final, fn($query) => $query->where('status_final', $this->status_final))
        ->where('status', 4);

    return [
        'multas' => $multas->paginate(15),
    ];
});

$filtrar = function () {
    Session::put('filters', [
        'unidade' => $this->unidade,
        'data_multa' => $this->data_multa,
        'data_limite' => $this->data_limite,
        'status_final' => $this->status_final,
        'responsavel' => $this->responsavel,
        'propriedade' => $this->propriedade,
        'auto_infracao' => $this->auto_infracao,
        'condutor' => $this->condutor,

    ]);
};

$resetarFiltros = function() {
    Session::forget('filters');

    return $this->reset(['unidade', 'data_multa', 'data_limite', 'status_final', 'responsavel', 'propriedade', 'auto_infracao', 'condutor']);

};

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
            'condutor' => ['nullable', 'string'],
            'data_identificacao' => ['required', 'date'],
        ],
        [
            'data_identificacao.required' => 'Selecione a data da identificação.',
        ]
    );

    $this->all_data = array_merge($this->all_data, $data);

    try {
        $this->multa->update([
            'condutor' => $this->all_data['condutor'] ?: 'Não Identificado',
            'data_identificacao' => $this->all_data['data_identificacao'],
            'status' => 2,
            'updated_by' => Ad::username(),
        ]);

        $this->success('Identificação interna realizada com sucesso!');

        $this->reset(['condutor', 'data_identificacao']);

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

$openModalIdentInterna = function ($id) {
    $this->multa = Multa::find($id);
    $this->modal_ident_interna = true;
};

$openModalIdentDetran = function ($id) {
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
            <h1 class="font-bold text-gray-700">Multas Finalizadas</h1>
            <x-button class="btn-sm btn-outline" label="VER MULTAS EM ANDAMENTO" icon="o-document-text"
                      link="{{route('dashboard')}}"/>
        </div>

        <div class="grid grid-cols-5 gap-4 bg-gray-100 p-4 shadow rounded mt-2">
            <x-select label="Filtrar por unidade:" :options="$this->unidades" wire:model="unidade"
                      placeholder="Selecione uma unidade..." placeholder-value="0" />
            <x-datetime label="Filtrar por Data da Multa:" wire:model="data_multa" placeholder="Data Multa" />
            <x-datetime label="Filtrar por Data Limite:" wire:model="data_limite" placeholder="Data Limite" />
            <x-select label="Filtrar por status:" :options="$this->statuses->filter(fn($status) => !in_array($status['id'], [4, 5]))"
                      wire:model="status" placeholder="Selecione um status..." placeholder-value="0" option-label="name" option-value="id"/>
            <x-input label="Filtrar por responsável:" placeholder="Nome do responsável..." wire:model="responsavel"/>
            <x-select label="Filtrar por Propriedade/ Local:" :options="$this->propriedades" wire:model="propriedade"
                      placeholder="Selecione a propriedade/local" placeholder-value="0" />
            <x-input label="Filtrar por N° Auto Infração:" placeholder="N° Auto Infração" wire:model="auto_infracao"/>
            <x-input label="Filtrar por condutor:" placeholder="Nome do condutor..." wire:model.lazy="condutor" />
            <x-button class="btn-outline mt-7" icon="o-x-circle" label="LIMPAR FILTROS" wire:click="resetarFiltros" />
            <x-button class="btn-outline mt-7" icon="o-adjustments-horizontal" label="FILTRAR"
                      wire:click="filtrar" />
        </div>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mt-2">
            <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="py-2 px-4 border-b">Unidade</th>
                <th class="py-2 px-4 border-b">Data Multa</th>
                <th class="py-2 px-4 border-b">Data Finalizada</th>
                <th class="py-2 px-4 border-b">Status Final</th>
                <th class="py-2 px-4 border-b">Responsável</th>
                <th class="py-2 px-4 border-b">Propriedade/Local</th>
                <th class="py-2 px-4 border-b">N° Auto Infração</th>
                <th class="py-2 px-4 border-b">Condutor</th>
                {{--                <th class="py-2 px-4 border-b">Etapas</th>--}}
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
                    <td class="py-2 px-4 border-b text-center">{{ Carbon::parse($multa->data_finalizada)->format('d/m/Y') }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $multa->status_final_model->status_final_name ?? 'N/A' }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $multa->responsavel }}</td>
                    <td class="py-2 px-4 border-b text-center">{{$multa->propriedade_model->local}}</td>
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
                        <x-button tooltip="Info. Multa" icon="o-information-circle" class="btn-outline btn-sm "
                                  link="{{route('consultas.info', ['id' => $multa->id])}}"/>
                        <x-button class="btn-sm btn-outline" tooltip="Anexar." icon="o-paper-clip"
                                  link="{{route('multas.anexo', ['id' => $multa->id])}}"/>
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

</div>
