<?php

use Carbon\Carbon;
use App\Classes\Ad;
use Illuminate\Support\Facades\Gate;
use Mary\Traits\Toast;

use App\Models\User;
use App\Models\Profile;

use function Livewire\Volt\{state, layout, mount, uses, rules};

uses([Toast::class]);

state(['name', 'nome_completo', 'profile']);
state(['profiles' => []]);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('admin.users.create')) {
        return redirect(route('errors.403'));
    }

    $this->profiles = Profile::get()->map(fn($e) => ['id' => $e->id, 'name' => $e->id . ' - ' . $e->name]);
});

rules([
    'name' => ['required', 'unique:users,name'],
    'nome_completo' => ['required'],
    'profile' => ['required'],
])->messages([
    'name.required' => 'Insira o usuário do AD.',
    'nome_completo.required' => 'Insira o nome do usuário',
    'name.unique' => 'Usuário já cadastrado.',
    'profile.required' => 'Selecione um perfil.',
]);

$save = function () {
    $data = $this->validate();

    try {
        User::create([
            'name' => $data['name'],
            'nome_completo' => $data['nome_completo'],
            'profile_id' => $data['profile'],
            'created_by' => Ad::username(),
            'updated_by' => Ad::username(),
        ]);

        $this->success('Usuário criado com sucesso!');

        return redirect(route('admin.users.index'));
    } catch (Exception $e) {
        return $this->error('Não foi possível adicionar usuário.');
    }
};

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Cadastro de Usuários</h1>
        </div>
        <form wire:submit.prevent="save">
            @csrf
            <div class="flex flex-col gap-2 bg-white mt-2 p-4 shadow rounded">
                <x-input label="Usuário AD:" wire:model="name" icon="o-user" placeholder="nome.sobrenome"/>

                <x-input label="Nome do Usuário:" wire:model="nome_completo" icon="o-user" placeholder="Ex: João da Silva"/>

                <x-select label="Perfil:" wire:model="profile" icon="o-cog-6-tooth" :options="$this->profiles"
                          placeholder="Selecione um perfil..." placeholder-value="0"/>
            </div>

            <x-button class="btn-sm btn-success mt-2 w-full" label="SALVAR" icon="o-check" type="submit"/>
        </form>
    </div>
</div>
