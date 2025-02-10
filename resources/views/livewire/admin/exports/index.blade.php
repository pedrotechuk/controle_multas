<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Mary\Traits\Toast;
use function Livewire\Volt\{state, layout, mount, uses};

state(['columns' => [], 'powerUsersData' => []]);

uses([Toast::class]);

mount(function () {

    if (!Gate::forUser(Auth::user())->allows('admin.view-any')) {
        return redirect(route('errors.403'));
    }

    $allColumns = Schema::getColumnListing('power_users');

    $excludedColumns = [
        'created_by', 'updated_by', 'deleted_by',
        'created_at', 'updated_at', 'deleted_at'
    ];

    $this->columns = array_diff($allColumns, $excludedColumns);

    // Busca os dados com apenas as colunas desejadas
    $this->powerUsersData = DB::table('power_users')
        ->select($this->columns)
        ->get();
});


$export = function () {
    $fileName = 'power_users_' . date('dmY') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$fileName",
    ];

    $callback = function () {
        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xef) . chr(0xbb) . chr(0xbf));

        fputcsv($file, $this->columns, ';');

        foreach ($this->powerUsersData as $row) {
            $rowData = [];
            foreach ($this->columns as $column) {
                $rowData[] = $row->$column ?? '';
            }
            fputcsv($file, $rowData, ';');
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
};

layout('layouts.app');

?>

<div class="mb-6">
    <div class="flex justify-between items-center bg-[#2f4d93] text-white p-4 rounded-t-lg shadow-lg mb-2">
        <h1 class="font-semibold text-lg">Exportar Base de Power Users</h1>
    </div>

    <div class="flex flex-col bg-white rounded-lg p-6 border border-blue-200 shadow-md hover:shadow-lg transition duration-300">
        <h2 class="font-semibold text-[#2f4d93] mb-4">Exportar Power Users</h2>
        <p class="text-gray-700 mb-4">Exportar a lista de todos os usuários com as colunas disponíveis no momento.</p>
        <div class="flex justify-end">
            <x-button class="btn-sm bg-[#2f4d93] text-white hover:bg-[#4c6fc0]" label="EXPORTAR"
                      wire:click="export" />
        </div>
    </div>
</div>
