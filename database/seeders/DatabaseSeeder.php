<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('profiles')->insert([
            [
                'name' => 'Administrador',
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Padrao',
                'active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);



        DB::table('permissions')->insert([
            // ---------------------------------------
            [
                'permission' => 'admin.view-any',
                'profile_id' => 1, 'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()],

            [
                'permission' => 'admin.users.view-any',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.users.create',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.users.update',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.users.view',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.users.delete',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.users.restore',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // ---------------------------------------
            // permissao adm - alterar perfis
            [
                'permission' => 'admin.profile.view-any',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.profile.create',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.profile.update',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.profile.view',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.profile.delete',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.profile.restore',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // ---------------------------------------
            // permissao adm - alterar permissoes
            [
                'permission' => 'admin.permission.view-any',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.permission.create',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.permission.update',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.permission.view',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.permission.delete',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'admin.permission.restore',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'permission' => 'apps.view-any',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.create',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.update',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.view',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.delete',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.restore',
                'profile_id' => 1,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],


            // Padrao Permissions
            [
                'permission' => 'apps.view-any',
                'profile_id' => 2,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.create',
                'profile_id' => 2,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.update',
                'profile_id' => 2,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.view',
                'profile_id' => 2,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.delete',
                'profile_id' => 2,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'permission' => 'apps.restore',
                'profile_id' => 2,
                'value' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

        ]);

        DB::table('users')->insert([
            [
                'name' => 'pedro.techuk',
                'nome_completo' => 'Pedro Techuk',
                'profile_id' => 1,
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'ph',
                'nome_completo' => 'Pedro Pazini',
                'profile_id' => 1,
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

        DB::table('statuses')->insert([

            [
                'status_name' => 'Ciente',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'status_name' => 'Identificada Interna',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'status_name' => 'Identificada Detran',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'status_name' => 'Finalizada',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'status_name' => 'Excluída',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]

        ]);

        DB::table('status_finals')->insert([

            [   'status_final_name' => 'Identificado e Descontado',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'status_final_name' => 'Identificado e Não Descontado',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'status_final_name' => 'Não Identificado e Descontado',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'status_final_name' => 'Não Identificado e Não Descontado',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

        DB::table('propriedades')->insert([

            [
                'local' => 'Virginia Guarapuava ',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'local' => 'Localiza',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'local' => 'Virginia Maringá',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

            [
                'local' => 'Virginia NP',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'local' => 'Virginia Ponta Grossa',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'local' => 'Stellantis',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'local' => 'Varejo Maringá',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'local' => 'Varejo Apucarana',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'local' => 'Varejo Umuarama',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'local' => 'Varejo Ponta Grossa',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

        DB::table('nao_identificados')->insert([

            [
                'justificativa' => 'Perda de prazo',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'justificativa' => 'Desligado',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'justificativa' => 'Autorização Superior',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'justificativa' => 'Multa não recebida a tempo',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

        DB::table('nao_descontados')->insert([

            [
                'justificativa' => 'Desligado',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'justificativa' => 'Responsábilidade da empresa',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'justificativa' => 'Autorização Superior',
                'created_by' => 'importacao',
                'updated_by' => 'importacao',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);

    }
}
