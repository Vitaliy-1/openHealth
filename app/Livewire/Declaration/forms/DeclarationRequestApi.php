<?php

namespace App\Livewire\Declaration\forms;

use App\Classes\eHealth\Api\DeclarationApi;

class DeclarationRequestApi extends DeclarationApi
{


    public static function getListDeclaration($data): array
    {
//        return self::_getList($data);

        return self::_testArray();
    }


    public static function _testArray(): array
    {

        return [
            "meta"   => [
                "code"       => 200,
                "url"        => "https://example.com/resource",
                "type"       => "object",
                "request_id" => "6617aeec-15e2-4d6f-b9bd-53559c358f97#17810"
            ],
            "data"   => [
                [
                    "id"                     => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                    "declaration_number"     => "0000-12H4-245D",
                    "employee"               => [
                        "id"            => "d290f1ee-6c54-4b01-90e6-d701748f0851",
                        "position"      => "P6",
                        "employee_type" => "doctor"
                    ],
                    "division"               => [
                        "id"   => "asSbcy12sYs8c",
                        "name" => "Пединовка"
                    ],
                    "start_date"             => "2017-03-02",
                    "end_date"               => "2017-03-02",
                    "reason"                 => "manual_employee",
                    "reason_description"     => "Згідно постанови 1 від 10.01.20171111",
                    "person"                 => [
                        "id"                  => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "first_name"          => "Петро",
                        "last_name"           => "Іванов",
                        "second_name"         => "Миколайович",
                        "birth_date"          => "1991",
                        "verification_status" => "NOT_VERIFIED"
                    ],
                    "legal_entity"           => [
                        "short_name" => "Ноунейм",
                        "name"       => "Клініка Ноунейм",
                        "id"         => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "edrpou"     => "5432345432"
                    ],
                    "declaration_request_id" => "74a6fae6-4207-4e03-a136-f2e70c6b0c02",
                    "inserted_at"            => "2017-04-20T19:14:13Z",
                    "updated_at"             => "2017-04-20T19:14:13Z"
                ],
                [
                    "id"                     => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                    "declaration_number"     => "0000-12H4-245D",
                    "employee"               => [
                        "id"            => "d290f1ee-6c54-4b01-90e6-d701748f0851",
                        "position"      => "P6",
                        "employee_type" => "doctor"
                    ],
                    "division"               => [
                        "id"   => "asSbcy12sYs8c",
                        "name" => "Пединовка"
                    ],
                    "start_date"             => "2017-03-02",
                    "end_date"               => "2017-03-02",
                    "reason"                 => "manual_employee",
                    "reason_description"     => "Згідно постанови 1 від 10.01.20171111",
                    "person"                 => [
                        "id"                  => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "first_name"          => "Петро",
                        "last_name"           => "Іванов",
                        "second_name"         => "Миколайович",
                        "birth_date"          => "1991",
                        "verification_status" => "NOT_VERIFIED"
                    ],
                    "legal_entity"           => [
                        "short_name" => "Ноунейм",
                        "name"       => "Клініка Ноунейм",
                        "id"         => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "edrpou"     => "5432345432"
                    ],
                    "declaration_request_id" => "74a6fae6-4207-4e03-a136-f2e70c6b0c02",
                    "inserted_at"            => "2017-04-20T19:14:13Z",
                    "updated_at"             => "2017-04-20T19:14:13Z"
                ],
                [
                    "id"                     => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                    "declaration_number"     => "0000-12H4-245D",
                    "employee"               => [
                        "id"            => "d290f1ee-6c54-4b01-90e6-d701748f0851",
                        "position"      => "P6",
                        "employee_type" => "doctor"
                    ],
                    "division"               => [
                        "id"   => "asSbcy12sYs8c",
                        "name" => "Пединовка"
                    ],
                    "start_date"             => "2017-03-02",
                    "end_date"               => "2017-03-02",
                    "reason"                 => "manual_employee",
                    "reason_description"     => "Згідно постанови 1 від 10.01.20171111",
                    "person"                 => [
                        "id"                  => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "first_name"          => "Петро",
                        "last_name"           => "Іванов",
                        "second_name"         => "Миколайович",
                        "birth_date"          => "1991",
                        "verification_status" => "NOT_VERIFIED"
                    ],
                    "legal_entity"           => [
                        "short_name" => "Ноунейм",
                        "name"       => "Клініка Ноунейм",
                        "id"         => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "edrpou"     => "5432345432"
                    ],
                    "declaration_request_id" => "74a6fae6-4207-4e03-a136-f2e70c6b0c02",
                    "inserted_at"            => "2017-04-20T19:14:13Z",
                    "updated_at"             => "2017-04-20T19:14:13Z"
                ],
                [
                    "id"                     => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                    "declaration_number"     => "0000-12H4-245D",
                    "employee"               => [
                        "id"            => "d290f1ee-6c54-4b01-90e6-d701748f0851",
                        "position"      => "P6",
                        "employee_type" => "doctor"
                    ],
                    "division"               => [
                        "id"   => "asSbcy12sYs8c",
                        "name" => "Пединовка"
                    ],
                    "start_date"             => "2017-03-02",
                    "end_date"               => "2017-03-02",
                    "reason"                 => "manual_employee",
                    "reason_description"     => "Згідно постанови 1 від 10.01.20171111",
                    "person"                 => [
                        "id"                  => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "first_name"          => "Петро",
                        "last_name"           => "Іванов",
                        "second_name"         => "Миколайович",
                        "birth_date"          => "1991",
                        "verification_status" => "NOT_VERIFIED"
                    ],
                    "legal_entity"           => [
                        "short_name" => "Ноунейм",
                        "name"       => "Клініка Ноунейм",
                        "id"         => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "edrpou"     => "5432345432"
                    ],
                    "declaration_request_id" => "74a6fae6-4207-4e03-a136-f2e70c6b0c02",
                    "inserted_at"            => "2017-04-20T19:14:13Z",
                    "updated_at"             => "2017-04-20T19:14:13Z"
                ],
                [
                    "id"                     => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                    "declaration_number"     => "0000-12H4-245D",
                    "employee"               => [
                        "id"            => "d290f1ee-6c54-4b01-90e6-d701748f0851",
                        "position"      => "P6",
                        "employee_type" => "doctor"
                    ],
                    "division"               => [
                        "id"   => "asSbcy12sYs8c",
                        "name" => "Пединовка"
                    ],
                    "start_date"             => "2017-03-02",
                    "end_date"               => "2017-03-02",
                    "reason"                 => "manual_employee",
                    "reason_description"     => "Згідно постанови 1 від 10.01.20171111",
                    "person"                 => [
                        "id"                  => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "first_name"          => "Петро",
                        "last_name"           => "Іванов",
                        "second_name"         => "Миколайович",
                        "birth_date"          => "1991",
                        "verification_status" => "NOT_VERIFIED"
                    ],
                    "legal_entity"           => [
                        "short_name" => "Ноунейм",
                        "name"       => "Клініка Ноунейм",
                        "id"         => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "edrpou"     => "5432345432"
                    ],
                    "declaration_request_id" => "74a6fae6-4207-4e03-a136-f2e70c6b0c02",
                    "inserted_at"            => "2017-04-20T19:14:13Z",
                    "updated_at"             => "2017-04-20T19:14:13Z"
                ],
                [
                    "id"                     => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                    "declaration_number"     => "0000-12H4-245D",
                    "employee"               => [
                        "id"            => "d290f1ee-6c54-4b01-90e6-d701748f0851",
                        "position"      => "P6",
                        "employee_type" => "doctor"
                    ],
                    "division"               => [
                        "id"   => "asSbcy12sYs8c",
                        "name" => "Пединовка"
                    ],
                    "start_date"             => "2017-03-02",
                    "end_date"               => "2017-03-02",
                    "reason"                 => "manual_employee",
                    "reason_description"     => "Згідно постанови 1 від 10.01.20171111",
                    "person"                 => [
                        "id"                  => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "first_name"          => "Петро",
                        "last_name"           => "Іванов",
                        "second_name"         => "Миколайович",
                        "birth_date"          => "1991",
                        "verification_status" => "NOT_VERIFIED"
                    ],
                    "legal_entity"           => [
                        "short_name" => "Ноунейм",
                        "name"       => "Клініка Ноунейм",
                        "id"         => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "edrpou"     => "5432345432"
                    ],
                    "declaration_request_id" => "74a6fae6-4207-4e03-a136-f2e70c6b0c02",
                    "inserted_at"            => "2017-04-20T19:14:13Z",
                    "updated_at"             => "2017-04-20T19:14:13Z"
                ],
                [
                    "id"                     => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                    "declaration_number"     => "0000-12H4-245D",
                    "employee"               => [
                        "id"            => "d290f1ee-6c54-4b01-90e6-d701748f0851",
                        "position"      => "P6",
                        "employee_type" => "doctor"
                    ],
                    "division"               => [
                        "id"   => "asSbcy12sYs8c",
                        "name" => "Пединовка"
                    ],
                    "start_date"             => "2017-03-02",
                    "end_date"               => "2017-03-02",
                    "reason"                 => "manual_employee",
                    "reason_description"     => "Згідно постанови 1 від 10.01.20171111",
                    "person"                 => [
                        "id"                  => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "first_name"          => "Петро",
                        "last_name"           => "Іванов",
                        "second_name"         => "Миколайович",
                        "birth_date"          => "1991",
                        "verification_status" => "NOT_VERIFIED"
                    ],
                    "legal_entity"           => [
                        "short_name" => "Ноунейм",
                        "name"       => "Клініка Ноунейм",
                        "id"         => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "edrpou"     => "5432345432"
                    ],
                    "declaration_request_id" => "74a6fae6-4207-4e03-a136-f2e70c6b0c02",
                    "inserted_at"            => "2017-04-20T19:14:13Z",
                    "updated_at"             => "2017-04-20T19:14:13Z"
                ],
                [
                    "id"                     => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                    "declaration_number"     => "0000-12H4-245D",
                    "employee"               => [
                        "id"            => "d290f1ee-6c54-4b01-90e6-d701748f0851",
                        "position"      => "P6",
                        "employee_type" => "doctor"
                    ],
                    "division"               => [
                        "id"   => "asSbcy12sYs8c",
                        "name" => "Пединовка"
                    ],
                    "start_date"             => "2017-03-02",
                    "end_date"               => "2017-03-02",
                    "reason"                 => "manual_employee",
                    "reason_description"     => "Згідно постанови 1 від 10.01.20171111",
                    "person"                 => [
                        "id"                  => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "first_name"          => "Петро",
                        "last_name"           => "Іванов",
                        "second_name"         => "Миколайович",
                        "birth_date"          => "1991",
                        "verification_status" => "NOT_VERIFIED"
                    ],
                    "legal_entity"           => [
                        "short_name" => "Ноунейм",
                        "name"       => "Клініка Ноунейм",
                        "id"         => "b075f148-7f93-4fc2-b2ec-2d81b19a9b7b",
                        "edrpou"     => "5432345432"
                    ],
                    "declaration_request_id" => "74a6fae6-4207-4e03-a136-f2e70c6b0c02",
                    "inserted_at"            => "2017-04-20T19:14:13Z",
                    "updated_at"             => "2017-04-20T19:14:13Z"
                ],
            ],
            "paging" => [
                "page_number"   => 2,
                "page_size"     => 50,
                "total_entries" => 1000,
                "total_pages"   => 23
            ]
        ];


    }

}
