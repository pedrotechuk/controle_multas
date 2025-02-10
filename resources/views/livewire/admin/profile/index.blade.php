<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;

use App\Models\User;
use App\Models\Profile;
use App\Models\Permission;

use function Livewire\Volt\{state, layout, mount, uses, rules, with, usesPagination};

usesPagination();

uses([Toast::class]);

state(['profile', 'permission']);
state(['modal_permission' => false, 'modal_profile' => false]);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('admin.permission.view-any')) {
        return redirect(route('errors.403'));
    }
});

with(function () {
    $profiles = Profile::query();
    $permissions = Permission::query();

    return [
        'profiles' => $profiles->paginate(15),
        'permissions' => $permissions->paginate(15),
    ];
});

$openModal = function ($modal, $id) {
    if ($modal == 'modal_profile') {
        if (!Gate::forUser(Auth::user())->allows('admin.profile.view')) {
            return $this->error('Sem permissão para editar');
        }
    } elseif ($modal == 'modal_permission') {
        if (!Gate::forUser(Auth::user())->allows('admin.permission.view')) {
            return $this->error('Sem permissão para editar');
        }
    }

    $this->reset(['profile', 'permission']);

    $this->profile = Profile::withTrashed()->find($id);

    $this->$modal = true;
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Perfis & Permissões</h1>
            {{-- <x-button class="btn-sm btn-success" label="NOVO VALOR DE FRETE" icon="o-plus"
                link="{{ route('admin.trip-value.create') }}" /> --}}
        </div>

        <div class="grid grid-cols-2 mt-2 gap-2">
            @foreach ($profiles as $profile)
                <x-card title="{{ $profile->name }}" shadow separator class="bg-gray-100 rounded ">
                    Edite as permissões e configurações avançadas desse perfil.

                    <x-slot:menu>
                        <p class="font-bold">{{ count(User::where('profile_id', $profile->id)->get()) }} usuários com
                            essa permissão.</p>
                    </x-slot:menu>
                    <x-slot:actions>
                        <x-button icon="o-pencil" tooltip="Editar Perfil." class="btn-outline btn-sm"
                            link="{{ route('admin.profile.update', ['id' => $profile->id]) }}" />
                        <x-button icon="o-squares-2x2" tooltip="Editar Permissões." class="btn-outline btn-sm"
                            link="{{ route('admin.profile.update-permissions', ['id' => $profile->id]) }}" />
                    </x-slot:actions>
                </x-card>
            @endforeach
        </div>
    </div>

    <div class="mt-2">
        {{ $profiles->links() }}
    </div>
</div>
