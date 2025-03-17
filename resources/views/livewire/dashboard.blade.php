<?php

use App\Models\User;
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
state(['modal_corresponsavel' => false]);

state(['unidades' => [], 'propriedades' => [], 'statuses' => [], 'status_finals' => [], 'usuarios' => []]);
state(['filters', 'unidade', 'multa', 'data_ciencia', 'data_multa', 'data_limite', 'responsavel', 'corresponsavel', 'propriedade', 'auto_infracao', 'condutor', 'condutor_modal', 'data_identificacao', 'identificador_interno', 'data_identificacao_detran', 'identificador_detran', 'status', 'status_final', 'corresponsavel']);


mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::with(['infracao', 'responsavel_model'])->find($this->id);

    $this->usuarios = User::whereNull('deleted_at')->orderBy('nome_completo', 'asc')->get()->map(fn($usuario) => ['id' => $usuario->name, 'name' => $usuario->nome_completo]);

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('id', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->status_finals = statusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);

    $filters = Session::get('filters', []);

    $this->unidade = $filters['unidade'] ?? [];
    $this->data_multa = $filters['data_multa'] ?? null;
    $this->data_limite = $filters['data_limite'] ?? null;
    $this->status = $filters['status'] ?? null;
    $this->responsavel = $filters['responsavel'] ?? null;
    $this->propriedade = $filters['propriedade'] ?? null;
    $this->auto_infracao = $filters['auto_infracao'] ?? null;
    $this->condutor = $filters['condutor'] ?? null;

});

with(function () {
    $multas = Multa::query()->orderBy('data_finalizada', 'asc')
        ->when($this->unidade, fn($query) => $query->where('unidade', $this->unidade))
        ->when($this->data_ciencia, fn($query) => $query->whereDate('data_ciencia', $this->data_ciencia))
        ->when($this->data_multa, fn($query) => $query->whereDate('data_multa', $this->data_multa))
        ->when($this->data_limite, fn($query) => $query->whereDate('data_limite', $this->data_limite))
        ->when($this->responsavel, fn($query) => $query->where('responsavel', 'LIKE', "%{$this->responsavel}%"))
        ->when($this->corresponsavel, fn($query) => $query->where('corresponsavel', 'LIKE', "%{$this->corresponsavel}%"))
        ->when($this->propriedade, fn($query) => $query->where('propriedade', $this->propriedade))
        ->when($this->auto_infracao, fn($query) => $query->where('auto_infracao', 'LIKE', "%{$this->auto_infracao}%"))
        ->when($this->condutor, fn($query) => $query->where('condutor', 'LIKE', "%{$this->condutor}%"))
        ->when($this->data_identificacao, fn($query) => $query->whereDate('data_identificacao', $this->data_identificacao))
        ->when($this->data_identificacao_detran, fn($query) => $query->whereDate('data_identificacao_detran', $this->data_identificacao_detran))
        ->when($this->status, fn($query) => $query->where('status', $this->status))
        ->when($this->status_final, fn($query) => $query->where('status_final', $this->status_final))
        ->whereNot('status', 4);

    return [
        'multas' => $multas->paginate(7),
    ];
});

$filtrar = function () {
    Session::put('filters', [
        'unidade' => $this->unidade,
        'data_multa' => $this->data_multa,
        'data_limite' => $this->data_limite,
        'status' => $this->status,
        'responsavel' => $this->responsavel,
        'propriedade' => $this->propriedade,
        'auto_infracao' => $this->auto_infracao,
        'condutor' => $this->condutor,

    ]);
};

$resetarFiltros = function () {
    Session::forget('filters');

    return $this->reset(['unidade', 'data_multa', 'data_limite', 'status', 'responsavel', 'propriedade', 'auto_infracao', 'condutor']);
};

$inativarMulta = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
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
            'condutor_modal' => ['nullable', 'string'],
            'data_identificacao' => ['required', 'date'],
        ],
        [
            'data_identificacao.required' => 'Selecione a data da identificação.',
        ]
    );

    $this->all_data = array_merge($this->all_data, $data);

    try {
        $this->multa->update([
            'condutor' => $this->all_data['condutor_modal'] ?: 'Não Identificado',
            'data_identificacao' => $this->all_data['data_identificacao'],
            'status' => 2,
            'identificador_interno' => Ad::username(),
        ]);

        $this->success('Identificação interna realizada com sucesso!');

        $this->reset(['condutor_modal', 'data_identificacao']);

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
            'identificador_detran' => Ad::username(),
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

$saveCorresponsavel = function () {
    $data = $this->validate(
        [
            'corresponsavel' => ['required'],
        ]
    );

    $this->all_data = array_merge($this->all_data, $data);

    try {
        $this->multa->update([
            'corresponsavel' => $this->all_data['corresponsavel'],
        ]);

        $this->success('Corresponsável indicado com sucesso!');

        $this->reset([
            'corresponsavel'
        ]);
        return redirect(route('dashboard'));
    } catch (Exception $e) {
        return $this->error('Não foi possível indicar o responsável.');
    }
};

$getButtonClass = function ($dateStart, $daysThreshold, $warningDays, $errorDays) {
    if (!$dateStart) return 'btn-sm btn-outline';

    $diffDays = Carbon::parse($dateStart)->diffInDays(Carbon::now());

    if ($diffDays > $errorDays) {
        return 'btn-sm btn-error btn-outline';
    } elseif ($diffDays > $warningDays) {
        return 'btn-sm btn-warning btn-outline';
    }

    return 'btn-sm btn-outline';
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

$openModalCorresponsavel = function ($id) {
    $this->multa = Multa::find($id);
    $this->corresponsavel = $this->multa->corresponsavel;
    $this->modal_corresponsavel = true;
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Multas Cadastradas</h1>
            <x-button class="btn-sm btn-success" label="ADICIONAR MULTA" icon="o-plus" link=""
                      wire:click="openModalMulta"/>
        </div>

        <div class="grid grid-cols-5 gap-4 bg-gray-100 p-4 shadow rounded mt-2">
            <x-select label="Filtrar por unidade:" :options="$this->unidades" wire:model="unidade"
                      placeholder="Selecione uma unidade..." placeholder-value="0"/>
            <x-datetime label="Filtrar por Data da Multa:" wire:model="data_multa" placeholder="Data Multa"/>
            <x-datetime label="Filtrar por Data Limite:" wire:model="data_limite" placeholder="Data Limite"/>
            <x-select label="Filtrar por status:"
                      :options="$this->statuses->filter(fn($status) => !in_array($status['id'], [4, 5]))"
                      wire:model="status" placeholder="Selecione um status..." placeholder-value="0" option-label="name"
                      option-value="id"/>
            <x-input label="Filtrar por responsável:" placeholder="Nome do responsável..." wire:model="responsavel"/>
            <x-select label="Filtrar por Propriedade/ Local:" :options="$this->propriedades" wire:model="propriedade"
                      placeholder="Selecione a propriedade/local" placeholder-value="0"/>
            <x-input label="Filtrar por N° Auto Infração:" placeholder="N° Auto Infração" wire:model="auto_infracao"/>
            <x-input label="Filtrar por condutor:" placeholder="Nome do condutor..." wire:model.lazy="condutor"/>
            <x-button class="btn-outline mt-7" icon="o-x-circle" label="LIMPAR FILTROS" wire:click="resetarFiltros"/>
            <x-button class="btn-outline mt-7" icon="o-adjustments-horizontal" label="FILTRAR"
                      wire:click="filtrar"/>
        </div>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mt-2">
            <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="py-2 px-4 border-b">Unidade</th>
                <th class="py-2 px-4 border-b">Data Ciência</th>
                <th class="py-2 px-4 border-b">Data Multa</th>
                <th class="py-2 px-4 border-b">Data Limite</th>
                <th class="py-2 px-4 border-b">Dias Restantes</th>
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
                    <td class="py-2 px-4 border-b text-center">{{ Carbon::parse($multa->data_multa)->format('d/m/Y') }}</td>
                    <td class="py-2 px-4 border-b text-center
                        @if ($multa->status_model->status_id != 4 && now()->greaterThan(Carbon::parse($multa->data_limite)))
                            text-red-500 font-semibold
                        @else
                            text-green-600 font-semibold
                        @endif">
                        {{ Carbon::parse($multa->data_limite)->format('d/m/Y') }}
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        @php
                            $dataLimite = Carbon::parse($multa->data_limite);
                            $diasRestantes = now()->diffInDays($dataLimite, false);
                        @endphp

                        @if ($diasRestantes < 0)
                            <x-button icon="o-exclamation-triangle" class="btn-ghost text-error rounded-full "
                                      tooltip="Multa em atraso!"/>
                        @elseif ($diasRestantes < 5)
                            <span class="text-red-600 font-semibold">{{ $diasRestantes }}</span>
                        @elseif ($diasRestantes < 10)
                            <span class="text-orange-500 font-semibold">{{ $diasRestantes }}</span>
                        @else
                            <span class="text-green-600 font-semibold">{{ $diasRestantes }}</span>
                        @endif
                    </td>

                    <td class="py-2 px-4 border-b text-center">{{ $multa->status_model->status_name ?? 'N/A' }}</td>
                    <td class="py-2 px-4 border-b text-center">{{ $multa->responsavel_model->nome_completo }}</td>
                    <td class="py-2 px-4 border-b text-center">{{$multa->propriedade_model->local}}</td>
                    <td class="py-2 px-4 border-b text-center relative group cursor-pointer">
                        <a href="{{ route('multas.info', ['id' => $multa->id]) }}">{{ $multa->auto_infracao }}</a>
                        <x-button icon="o-information-circle"
                                  class="btn-ghost btn-sm rounded-full -ms-1"
                                  link="{{route('multas.info', ['id' => $multa->id])}}"/>
                        {{--                        <div class="absolute left-1/2 transform -translate-x-1/2  translate-y-2  bottom-full--}}
                        {{--                            hidden group-hover:block bg-gray-100 text-gray-800 font-semibold text-sm--}}
                        {{--                            px-2 py-1 rounded shadow-lg">--}}
                        {{--                            <a href="{{ route('multas.info', ['id' => $multa->id]) }}">Ver detalhes</a>--}}
                        {{--                        </div>--}}
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        @if ($multa->condutor)
                            {{ $multa->condutor }}
                        @else
                            <span class="text-orange-500 font-semibold">Pendente</span>
                        @endif
                    </td>

                    <td class="py-2 px-4 border-b text-center">
                        <x-button
                            class="btn-outline btn-sm"
                            tooltip="Identificação Interna"
                            icon="o-clipboard-document-list"
                            link=""
                            wire:click="openModalIdentInterna({{ $multa->id }})"
                            :class="$this->getButtonClass($multa->data_ciencia, 1, 7, 10)"
                        />

                        <x-button
                            class="btn-outline btn-sm"
                            tooltip="Identificação Detran"
                            icon="o-building-library"
                            wire:click="openModalIdentDetran({{ $multa->id }})"
                            :disabled="!$multa->condutor || !$multa->data_identificacao"
                            :class="$this->getButtonClass($multa->data_identificacao, 1, 2, 5)"
                        />

                        <x-button
                            class="btn-outline btn-success btn-sm"
                            tooltip="Finalizar multa"
                            icon="o-clipboard-document-check"
                            link="{{ route('multas.finalize', ['id' => $multa->id]) }}"
                            :disabled="!$multa->data_identificacao_detran"
                            :class="$this->getButtonClass($multa->data_identificacao_detran, 1, 4, 7)"
                        />
                    </td>

                    <td class="py-2 px-4 border-b text-center">

                        <x-button class="btn-sm" tooltip="Anexar." icon="o-paper-clip"
                                  link="{{route('multas.anexo', ['id' => $multa->id])}}"/>

                        <x-button class="btn-sm" tooltip="Corresponsável." icon="o-user"
                                  :class="$multa->corresponsavel ? 'btn-sm bg-blue-600 text-white' : 'btn-sm'"
                                  wire:click="openModalCorresponsavel({{ $multa->id }})"/>

                        <x-button class="btn-sm" tooltip="Editar multa." icon="o-pencil"
                                  link="{{route('multas.update', ['id' => $multa->id])}}"/>

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
            <h1 class=" mb-4 font-bold uppercase text-center text-gray-700 underline">IDENTIFICAÇÃO INTERNA</h1>
            <form wire:submit.prevent="ident_Interna">
                <div class="flex flex-col gap-2">
                    <x-input label="Condutor: (Caso não identificado deixe em branco)"
                             placeholder="Informe o condutor..." wire:model="condutor_modal"
                             icon="o-building-office-2"/>
                    <x-datetime label="Data da identificação:" wire:model="data_identificacao" icon="o-calendar"
                                type="datetime-local"/>
                    <div class="flex flex-row justify-evenly items-center mt-2">
                        <x-button class="btn-sm " label="FECHAR" wire:click="$set('modal_ident_interna', false)"/>
                        <x-button class="btn-sm btn-success text-white" label="SALVAR" icon="o-check"
                                  wire:click="ident_Interna"/>
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
                    <x-input readonly label="Condutor:" placeholder="Informe o condutor..." wire:model.live="condutor"
                             icon="o-building-office-2"/>
                    <x-datetime label="Data da identificação no Detran:" wire:model="data_identificacao_detran"
                                icon="o-calendar" type="datetime-local"/>
                    <div class="flex flex-row justify-evenly items-center mt-2">
                        <x-button class="btn-sm " label="FECHAR" wire:click="$set('modal_ident_detran', false)"/>
                        <x-button class="btn-sm btn-success text-white" label="SALVAR" icon="o-check"
                                  wire:click="ident_Detran"/>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal wire:model.live="modal_corresponsavel" class="backdrop-blur">
        <div>
            <h1 class=" mb-4 font-bold uppercase text-center text-gray-900 underline">INDICAR CORRESPONSÁVEL</h1>
            <form wire:submit.prevent="saveCorresponsavel">
                <div class="flex flex-col gap-2">
                    <x-input placeholder="Informe o corresponsável..." wire:model.live="corresponsavel" icon="o-user"/>
                    <div class="flex flex-row justify-evenly items-center mt-2">
                        <x-button class="btn-sm " label="FECHAR" wire:click="$set('modal_corresponsavel', false)"/>
                        <x-button class="btn-sm btn-success text-white" label="SALVAR" icon="o-check"
                                  wire:click="saveCorresponsavel"/>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>
</div>
