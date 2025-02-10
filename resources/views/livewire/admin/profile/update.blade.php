<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Mary\Traits\Toast;

use App\Models\User;
use App\Models\Profile;
use App\Models\Permission;

use function Livewire\Volt\{state, layout, mount, uses, rules, updated};

uses([Toast::class]);

state(['id'])->url();
state(['profile', 'profile_name']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('admin.profile.view')) {
        return redirect(route('admin.profile.index'));
    }

    $this->profile = Profile::withTrashed()->find($this->id);

    if (!$this->profile) {
        return redirect(route('admin.profile.index'));
    }

    $this->profile_name = $this->profile->name;
});

rules([
    'profile_name' => ['required'],
])->messages(['profile_name.required' => 'Insira o nome do perfil.']);

$update = function () {
    if (!Gate::forUser(Auth::user())->allows('admin.profile.update')) {
        return $this->error('Sem permissão para editar');
    }

    try {
        $data = $this->validate();

        $this->profile->update(['name' => $data['profile_name']]);

        $this->success('Perfil atualizado com sucesso!');

        return redirect(route('admin.profile.index'));
    } catch (Exception $e) {
        return $this->error('Não foi possível editar.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Editar Perfil - {{ $this->profile_name }}</h1>
        </div>
        <form wire:submit.prevent="update">
            @csrf
            <div class="flex flex-col gap-2 bg-white mt-2 p-4 shadow rounded">
                <x-input label="Nome do Perfil:" wire:model="profile_name" icon="o-pencil"
                    placeholder="Nome do perfil..." />
            </div>

            <x-button class="btn-sm btn-success mt-2 w-full" label="SALVAR" icon="o-check" type="submit" />
        </form>
    </div>
</div>
