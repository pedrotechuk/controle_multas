<?php

use App\Models\Anexo;
use App\Models\NaoDescontado;
use App\Models\NaoIdentificado;
use Carbon\Carbon;
use App\Classes\Ad;
use Illuminate\Support\Facades\Storage;
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

    $this->unidades = [['id' => 1, 'name' => 'Virginia Maringá'], ['id' => 3, 'name' => 'Virginia Guarapuava'], ['id' => 7, 'name' => 'Virginia Ponta Grossa'], ['id' => 10, 'name' => 'Virginia Norte Pioneiro']];

    $this->propriedades = Propriedade::whereNull('deleted_at')->orderBy('local', 'asc')->get()->map(fn($propriedade) => ['id' => $propriedade->id, 'name' => $propriedade->local]);

    $this->statuses = Status::whereNull('deleted_at')->orderBy('status_name', 'asc')->get()->map(fn($status) => ['id' => $status->id, 'name' => $status->status_name]);

    $this->nao_identificados = NaoIdentificado::whereNull('deleted_at')->orderBy('justificativa', 'asc')->get()->map(fn($nao_identificados) => ['id' => $nao_identificados->id, 'name' => $nao_identificados->justificativa]);

    $this->nao_descontos = NaoDescontado::whereNull('deleted_at')->orderBy('justificativa', 'asc')->get()->map(fn($nao_descontados) => ['id' => $nao_descontados->id, 'name' => $nao_descontados->justificativa]);

    $this->status_finals = StatusFinal::whereNull('deleted_at')->orderBy('status_final_name', 'asc')->get()->map(fn($status_final) => ['id' => $status_final->id, 'name' => $status_final->status_final_name]);
});


$salvarAnexo = function () {
    $data = $this->validate([
        'arquivo' => ['required', 'file', 'max:10048'],
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
        dd($e->getMessage());
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
    <x-card class="bg-white p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold">Anexos Multa N°{{ $this->id }}</h2>
                <p class="text-gray-600">Criada em: {{ Carbon::parse($this->multa->created_at)->format('d/m/Y') }}
                    por {{ $this->multa->created_by }}</p>
            </div>
            <x-button class="btn-sm btn-outline" label="VOLTAR" icon="o-arrow-uturn-left"
                      @click="window.history.back()"/>
        </div>

        <!-- Formulário de Upload -->
        <div class="mt-6 mb-6 border border-gray-300 rounded-md p-4">
            <form id="upload-form" enctype="multipart/form-data">
                <label class="block mb-2 font-semibold">Enviar Arquivo:</label>
                <input type="file" id="arquivo" class="block w-full border rounded-md p-2 mt-2 mb-2">
                <img id="preview" class="w-32 h-32 object-cover rounded-md shadow hidden" />

                <button type="button" id="btnSalvar" onclick="uploadArquivo()">SALVAR</button>
            </form>
        </div>
    </x-card>

    <script>
        document.getElementById('arquivo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewImg = document.getElementById('preview');
            if (file) {
                const reader = new FileReader();
                if (file.type === "application/pdf") {
                    previewImg.classList.add('hidden');
                    const pdfPreview = document.createElement('embed');
                    pdfPreview.src = URL.createObjectURL(file);
                    pdfPreview.type = "application/pdf";
                    pdfPreview.className = "w-full h-48 rounded-md shadow";
                    previewImg.parentNode.appendChild(pdfPreview);
                } else if (file.type.startsWith("image/")) {
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewImg.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        function uploadArquivo() {
            let formData = new FormData();
            let arquivo = document.getElementById('arquivo').files[0];

            if (!arquivo) {
                alert('Selecione um arquivo para enviar.');
                return;
            }

            formData.append('arquivo', arquivo);
            formData.append('multa_id', '{{ $this->id }}'); // Adicionando o ID

            fetch("{{ route('upload.anexo') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => console.error('Erro ao enviar:', error));
        }

    </script>

    <!-- Lista de Anexos -->
    <div>
        <h4 class="text-md font-semibold mt-4 mb-2">Lista de Anexos:</h4>

        @if ($this->multa && $this->multa->anexos->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach ($this->multa->anexos as $anexo)
                    <div class="bg-gray-100 p-3 rounded-lg shadow-md relative">
                        <!-- Exibir a imagem com opção de visualização -->
                        @if (Str::endsWith($anexo->arquivo, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                            <a href="{{ asset('storage/' . $anexo->arquivo) }}" target="_blank">
                                <img src="{{ asset('storage/' . $anexo->arquivo) }}"
                                     alt="Anexo"
                                     class="w-full h-32 object-cover rounded-md mb-2 cursor-pointer">
                            </a>
                        @elseif (Str::endsWith($anexo->arquivo, ['.pdf']))
                            <a href="{{ asset('storage/' . $anexo->arquivo) }}" target="_blank"
                               class="text-blue-500 hover:underline flex items-center">
                                <x-icon name="o-document" class="w-5 h-5 mr-2"/> Abrir PDF
                            </a>

                        @else
                            <div class="text-gray-700 text-sm italic">Arquivo não é uma imagem</div>
                        @endif

                        <p class="text-sm text-gray-700 truncate">{{ $anexo->nome_original }}</p>

                        <!-- Botão de Excluir -->
                        <x-button tooltip="Excluir anexo" icon="o-trash" class="btn-error btn-xs rounded-full text-white "
                                  wire:confirm="Deseja realmente remover este anexo?"
                                  wire:click="removerAnexo({{ $anexo->id }})"/>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 italic">Nenhum anexo encontrado.</p>
        @endif
    </div>

</div>
