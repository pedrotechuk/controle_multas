<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;

use App\Models\User;

use function Livewire\Volt\{state, layout, mount, uses, with, usesPagination};

usesPagination();

uses([Toast::class]);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('admin.users.view-any')) {
        return redirect(route('errors.403'));
    }
});

with(function () {
    $users = User::query()->withTrashed()->orderBy('profile_id', 'asc');

    return [
        'users' => $users->paginate(15),
    ];
});


$inactiveUser = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('admin.users.delete')) {
        return $this->error('Sem permissão para inativar usuário.');
    }

    try {
        User::find($id)->update([
            'deleted_at' => Carbon::now(),
            'deleted_by' => Ad::username(),
        ]);

        return $this->success('Usuário inativado com sucesso');
    } catch (Exception $e) {
        return $this->error('Não foi possível inativar usuário.');
    }
};

$restoreUser = function ($id) {
    if (!Gate::forUser(Auth::user())->allows('admin.users.restore')) {
        return $this->error('Sem permissão para reativar usuário.');
    }

    try {
        User::withTrashed()->find($id)->restore();

        return $this->success('Usuário reativado com sucesso');
    } catch (Exception $e) {
        return $this->error('Não foi possível reativar usuário.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Usuários Cadastrados</h1>
            <x-button class="btn-sm btn-success" label="ADICIONAR USUÁRIO" icon="o-plus"
                link="{{ route('admin.users.create') }}" />
        </div>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden mt-2">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="py-2 px-4 border-b">Nome</th>
                    <th class="py-2 px-4 border-b">Usuário</th>
                    <th class="py-2 px-4 border-b">Perfil</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Ações</th>
                </tr>
            </thead>
            <tbody class="text-gray-800">
                @forelse ($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="py-2 px-4 border-b text-center">{{ $user->nome_completo }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $user->name }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ $user->profile->name }}</td>
                        <td class="py-2 px-4 border-b text-center">{{ !$user->deleted_at ? 'Ativo' : 'Inativo' }}</td>
                        <td class="py-2 px-4 border-b text-center">
                            <x-button class="btn-outline btn-sm" tooltip="Editar Usuário." icon="o-pencil"
                                link="{{ route('admin.users.update', ['id' => $user->id]) }}" />

                            @if (!$user->deleted_at)
                                <x-button tooltip="Inativar Usuário." icon="o-trash" class="btn-error btn-sm text-white"
                                    wire:confirm="Deseja realmente inativar esse usuário?"
                                    wire:click="inactiveUser({{ $user->id }})" />
                            @else
                                <x-button class="btn-sm btn-success" icon="o-check" tooltip="Reativar Usuário."
                                    wire:confirm="Deseja realmente reativar esse usuário?"
                                    wire:click="restoreUser({{ $user->id }})" />
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-slate-50">
                        <td class="py-2 px-4 border-b text-center ">Não há usuários cadastrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $users->links() }}
    </div>
</div>
