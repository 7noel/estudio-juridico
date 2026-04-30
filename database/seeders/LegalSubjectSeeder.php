<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalSpecialty;
use App\Models\LegalSubject;

class LegalSubjectSeeder extends Seeder
{
    public function run(): void
    {

        $data = [

            'Familia' => [
                'Divorcio',
                'Pensión de alimentos',
                'Tenencia',
                'Régimen de visitas',
                'Tutela',
            ],

            'Civil' => [
                'Desalojo',
                'Obligación de dar suma de dinero',
                'Prescripción adquisitiva',
                'Interdictos',
            ],

            'Penal' => [
                'Denuncia penal',
                'Defensa penal',
                'Querella',
            ],

            'Laboral' => [
                'Despido arbitrario',
                'Beneficios sociales',
            ],

            'Notarial' => [
                'Carta poder',
                'Declaración jurada',
                'Contrato privado',
            ],

            'Administrativo' => [
                'Procedimientos administrativos',
                'Recursos administrativos',
            ],

        ];

        foreach ($data as $specialty => $subjects) {

            $specialtyModel = LegalSpecialty::where('name', $specialty)->first();

            foreach ($subjects as $subject) {

                LegalSubject::create([
                    'legal_specialty_id' => $specialtyModel->id,
                    'name' => $subject,
                ]);

            }

        }

    }
}