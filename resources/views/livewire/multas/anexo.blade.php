<?php

use App\Models\Anexo;
use App\Models\NaoDescontado;
use App\Models\NaoIdentificado;
use Carbon\Carbon;
use App\Classes\Ad;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use App\Models\Status;
use App\Models\StatusFinal;
use App\Models\Propriedade;
use App\Models\Multa;

use function Livewire\Volt\{state, layout, mount, uses};

uses([Toast::class]);

uses([WithFileUploads::class]);

state(['id'])->url();

state(['all_data' => []]);

state(['multa', 'arquivo', 'anexos']);

mount(function () {
    if (!Gate::forUser(Auth::user())->allows('apps.view-any')) {
        return redirect()->route('errors.403');
    }
    $this->multa = Multa::with('anexos')->find($this->id);

    $this->multa->anexos = $this->multa->anexos ?? [];

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virgini Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->nao_identificados = NaoIdentificado::whereNull('deleted_at')->orderBy('justificativa', 'asc')->get()->map(fn($nao_identificados) => ['id' => $nao_identificados->id, 'name' => $nao_identificados->justificativa]);

    $this->nao_descontos = NaoDescontado::whereNull('deleted_at')->orderBy('justificativa', 'asc')->get()->map(fn($nao_descontados) => ['id' => $nao_descontados->id, 'name' => $nao_descontados->justificativa]);

    $this->status_finals = StatusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);
});

$salvarAnexo = function () {
    $data = $this->validate([
        'arquivo' => ['required', 'file', 'max:10048'], // Limite de 2MB
    ]);

    try {
        $path = $this->arquivo->store('anexos', 'public');

        Anexo::create([
            'multa_id' => $this->multa->id,
            'arquivo' => $path,
            'nome_original' => $this->arquivo->getClientOriginalName(),
            'created_at' => Carbon::now(),
            'created_by' => Ad::username(),
        ]);

        $this->multa->refresh();
        $this->arquivo = null;
        return $this->success('Anexo enviado com sucesso!');
    } catch (Exception $e) {
        dd(Ad::username());
        return $this->error('Erro ao enviar o anexo.');
    }
};

$removerAnexo = function ($anexoId) {
    try {
        $anexo = Anexo::findOrFail($anexoId);
        Storage::disk('public')->delete($anexo->arquivo);
        $anexo->delete();

        $this->multa->refresh();
        return $this->success('Anexo removido com sucesso!');
    } catch (Exception $e) {
        return $this->error('Erro ao remover o anexo.');
    }
};
layout('layouts.app');
?>

<div>
    <div>
        <h3 class="font-bold mb-2">Anexos</h3>

        @if (session()->has('message'))
            <div class="text-green-600">{{ session('message') }}</div>
        @endif

        <form wire:submit.prevent="salvarAnexo" enctype="multipart/form-data" class="mb-4">
            <input type="file" wire:model="arquivo">
            @error('arquivo') <span class="text-red-600">{{ $message }}</span> @enderror
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-2">Enviar</button>
        </form>

        <ul>
            @if($this->multa && $this->multa->anexos)
                @foreach($this->multa->anexos as $anexo)
                    <p>{{ $anexo->arquivo }}</p>
                @endforeach
            @else
                <p>Nenhum anexo encontrado.</p>
            @endif
        </ul>
    </div>
</div>
