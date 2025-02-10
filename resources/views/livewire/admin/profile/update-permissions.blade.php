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
state(['permission', 'permissions' => [], 'allowed_permissions' => []]);
state(['array_users' => [], 'array_profile' => [], 'array_permission' => [], 'array_apps' => [], 'array_power_users' => []]);
state(['profile']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('admin.profile.view')) {
        return redirect(route('admin.profile.index'));
    }

    $this->profile = Profile::withTrashed()->find($this->id);

    if (!$this->profile) {
        return redirect(route('admin.profile.index'));
    }

    $this->permissions = Permission::select('id', 'permission')->distinct()->get();

    $this->allowed_permissions = Permission::where('profile_id', $this->profile->id)
        ->pluck('permission')
        ->toArray();
});

layout('layouts.app');

?>

<div>
    <div>
        <div class="flex flex-row justify-between items-center bg-gray-100 p-4 shadow rounded">
            <h1 class="font-bold text-gray-700">Editar Permissões - {{ $this->profile->name }}</h1>
        </div>

        <div class="grid grid-cols-5 gap-2 mt-2">
            @foreach ($this->permissions as $permission)
                <div
                    class="p-2 rounded shadow {{ in_array($permission->permission, $this->allowed_permissions) ? 'bg-green-100' : '' }}">
                    <x-checkbox label="{{ $permission->permission }}" wire:model="allowed_permissions"
                        value="{{ $permission->permission }}" :checked="in_array($permission->permission, $this->allowed_permissions)" />
                </div>
            @endforeach
        </div>
    </div>
</div>
