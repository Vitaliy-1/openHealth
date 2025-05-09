<?php

namespace Database\Factories;
use App\Models\Declaration;
use App\Models\Employee\Employee;
use App\Models\LegalEntity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Declaration>
 */
class DeclarationFactory extends Factory
{
    protected $model = Declaration::class;


    protected  $employee = Employee::class;

    protected $legalEntity = LegalEntity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $this->employee = Employee::where('employee_type', 'DOCTOR')->inRandomOrder()->first() ?? $this->employee;
        $this->legalEntity = LegalEntity::inRandomOrder()->first() ??$this->legalEntity;
        return [
            'uuid' => $this->faker->uuid,
            'declaration_number' => strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
            'start_date' => $this->faker->date,
            'end_date' => $this->faker->date,
            'signed_at' => $this->faker->dateTime,
            'person' => [
                'id' => $this->faker->uuid,
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'second_name' => $this->faker->middleName,
                'birth_date' => $this->faker->date,
                'gender' => $this->faker->randomElement(['MALE', 'FEMALE']),
                'tax_id' => $this->faker->numerify('##########'),
                'phones' => [
                    [
                        'type' => 'MOBILE',
                        'number' => $this->faker->phoneNumber
                    ]
                ],
                'birth_settlement' => $this->faker->city,
                'birth_country' => 'Україна',
                'emergency_contact' => [
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'second_name' => $this->faker->middleName,
                    'phones' => [
                        [
                            'type' => 'MOBILE',
                            'number' => $this->faker->phoneNumber
                        ]
                    ]
                ],
                'confidant_person' => [
                    [
                        'relation_type' => 'PRIMARY',
                        'first_name' => $this->faker->firstName,
                        'last_name' => $this->faker->lastName,
                        'second_name' => $this->faker->middleName,
                        'birth_date' => $this->faker->date,
                        'birth_country' => 'Україна',
                        'birth_settlement' => $this->faker->city,
                        'gender' => $this->faker->randomElement(['MALE', 'FEMALE']),
                        'email' => $this->faker->email,
                        'tax_id' => $this->faker->numerify('##########'),
                        'secret' => 'secret',
                        'unzr' => $this->faker->bothify('########-#####'),
                        'preferred_way_communication' => 'email',
                        'documents_person' => [
                            [
                                'type' => 'PASSPORT',
                                'number' => strtoupper($this->faker->bothify('??######')),
                                'expiration_date' => $this->faker->date,
                                'issued_by' => $this->faker->company,
                                'issued_at' => $this->faker->date
                            ]
                        ],
                        'documents_relationship' => [
                            [
                                'type' => 'BIRTH_CERTIFICATE',
                                'number' => strtoupper($this->faker->bothify('??######')),
                                'issued_by' => $this->faker->company,
                                'issued_at' => $this->faker->date
                            ]
                        ],
                        'phones' => [
                            [
                                'type' => 'MOBILE',
                                'number' => $this->faker->phoneNumber
                            ]
                        ]
                    ]
                ]
            ],
            'employee' => [
                'id' => $this->faker->uuid,
                'position' =>$this->employee->position ??  $this->faker->randomElement(['P1', 'P2', 'P3']),
                'employee_type' => 'DOCTOR',
                'status' => 'APPROVED',
                'start_date' =>$this->employee->start_date ?? $this->faker->dateTime,
                'end_date' => $this->employee->end_date ?? $this->faker->dateTime,
                'party' => [
                    'id' => $this->employee->party['id'],
                    'first_name' => $this->employee->party['first_name'],
                    'last_name' => $this->employee->party['last_name'],
                    'second_name' => $this->employee->party['second_name'],
                ],
                'division_id' => $this->faker->uuid,
                'legal_entity_id' => $this->legalEntity->uuid,
            ],
//            'person_id'=> $this->faker->numberBetween([1, 1000000]),
            'status' => $this->faker->randomElement(['NEW', 'APPROVED', 'REJECTED','SIGNED']),
            'scope' => 'family_doctor',
            'legal_entity' => [
                'id' => $this->legalEntity->uuid ?? $this->faker->uuid,
                'name' => $this->legalEntity->edr['public_name'] ?? 'Legal Entity',
                'short_name' => $this->legalEntity->edr['public_name'] ?? 'Legal Entity',
                'legal_form' => $this->legalEntity->edr['legal_form'] ?? 140,
                'public_name' => $this->legalEntity->edr['public_name'] ?? 'Legal Entity',
                'edrpou' => $this->faker->numerify('##########'),
                'status' => $this->legalEntity->status,
                'email' =>  $this->legalEntity->email,
                'phones' => [
                    [
                        'type' => 'MOBILE',
                        'number' => $this->legalEntity->phone['number'] ?? '380971234567',
                    ]
                ]
            ],
            'employee_id' => $this->employee->id ?? $this->faker->numberBetween([1, 1000000]),
            'declaration_request_id' => $this->faker->uuid,

        ];
    }
}
