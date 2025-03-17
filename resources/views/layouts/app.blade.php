<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/styles/choices.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.7/inputmask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.7/inputmask.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/robsontenorio/mary@0.44.2/libs/currency/currency.js">
    </script>
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <style>
        .toast {
            z-index: 99999 !important
        }

        .toggle-container {
            display: inline-flex;
            align-items: center;
        }

        .toggle-input {
            opacity: 0;
            position: absolute;
        }

        .toggle-label {
            position: relative;
            width: 50px;
            height: 26px;
            background-color: #ccc;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .toggle-label::after {
            content: "";
            position: absolute;
            top: 2px;
            left: 2px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background-color: white;
            transition: transform 0.3s ease;
        }

        .toggle-input:checked+.toggle-label {
            background-color: #4CAF50;
        }

        .toggle-input:checked+.toggle-label::after {
            transform: translateX(24px);
        }


        .ts-control {
            border: none !important;
            display: flex !important;
            align-items: center !important;
            font-size: 16px !important;
        }

        .ts-dropdown-content {
            font-size: 16px !important;
        }

        input:where(:not([type])),
        input:where(:not([type])):focus,
        choices,
        choices:focus {
            box-shadow: none;
            --tw-ring-shadow: none;
            border: none;
            width: 99%;
        }

        .select input,
        .select input:active,
        .select input:focus {
            border: none;
            box-shadow: none;
            width: 99%;
        }

        .dark\:bg-gray-800 {
            --tw-bg-opacity: 1;
            background-color: #fff !important;
        }

        .dark\:text-gray-400 {
            --tw-text-opacity: 1;
            color: #2E2E2E !important;
        }

        @media (min-width: 1024px) {
            .lg\:h-\[calc\(100vh-73px\)\] {
                height: 100dvh !important;
            }
        }

        #btnSalvar {
            padding: 8px 16px;
            background-color: #28a745; /* Cor de sucesso (verde) */
            color: white;
            border: none;
            border-radius: 6px;
            margin-top: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        #btnSalvar:hover {
            background-color: #218838;


    </style>

    @livewireStyles

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased">
    <x-nav sticky class="lg:hidden bg-white text-dark">
        <x-slot:brand>
            <label for="main-drawer" class="lg:hidden mr-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
            Gestão de Multas
        </x-slot:brand>
    </x-nav>

    <x-main with-nav full-width collapsed>
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-white text-dark shadow-xl mr-1" collapsed>
            <div class="hidden-when-collapsed text-center  mx-4 mt-4 font-black text-3xl text-[#003CA2]">Gestão de Multas</div>

            <div class="display-when-collapsed mx-4 mt-3 font-black text-2xl text-[#003CA2]">GM</div>

            <div class="hidden-when-collapsed mx-4 mt-3 font-black text-sm">
                <div class="mb-4">
                    <div class="flex flex-col items-center mb-1 gap-2">
                        <div class="bg-gray-200 w-16 h-16 rounded-full flex justify-center items-center">
                            <x-heroicon-o-user class="h-10 w-10 text-gray-400" />
                        </div>
                        <p>
                            @auth
                                {{ Auth::user()->getName() }}
                            @else
                                <script>window.location = "{{ route('login') }}";</script>
                            @endauth
                        </p>
                        {{-- @if (request()->session()->has('vgportal_perfil'))
                            <div class="flex justify-center p-2 mb-4 min-w-[80px]">
                                {{ request()->session()->get('vgportal_perfil')->nome }}
                            </div>
                        @endif --}}
                        <div x-data="{}">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-button label="SAIR" class="btn-sm w-[96px]" type="submit" />
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <x-menu activate-by-route>
                @if (Gate::forUser(Auth::user())->allows('admin.view-any'))
                    <x-menu-sub title="Administrador" icon="o-cog-6-tooth">
                        <x-menu-item title="Usuários" icon="o-users" link="{{ route('admin.users.index') }}" />

                        <x-menu-item title="Exportar Dados" icon="o-arrow-up-on-square-stack"
                            link="{{ route('admin.exports.index') }}" />
                    </x-menu-sub>
                @endif

                <x-menu-item title="Painel de Multas" icon="o-clipboard-document-check" link="{{ route('dashboard') }}" />

                <x-menu-item title="Consultar Finalizadas" icon="o-document-magnifying-glass" link="{{ route('consultas.index') }}" />

            </x-menu>
        </x-slot:sidebar>

        <x-slot:content>
            <x-toast />
            {{ $slot }}
        </x-slot:content>
    </x-main>

    @livewireScripts
    @livewireScriptConfig
</body>


</html>
