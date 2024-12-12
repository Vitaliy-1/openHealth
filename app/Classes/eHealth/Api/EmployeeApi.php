<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class EmployeeApi
{

    public const URL_REQUEST = '/api/employee_requests';
    public const URL_REQUEST_MIS = '/api/mis/employee_requests';
    public const URL_REQUEST_V2 = '/api/v2/employee_requests';

    public const URL = '/api/employees';

    public static function _get($params): array
    {
        return (new Request('GET', self::URL, $params))->sendRequest();
    }

    public static function _create($params = []): array
    {
        return (new Request('POST', self::URL_REQUEST_V2, $params))->sendRequest();
    }


    public static function _dismissed($id): array
    {
        return (new Request('POST', self::URL.'/'.$id.'/actions/deactivate', []))->sendRequest();
    }

    public static function _getById($id)
    {
        return (new Request('GET', self::URL.'/'.$id, []))->sendRequest();
    }

    public static function _getRequestList($data): array
    {
        return (new Request('GET', self::URL_REQUEST, $data))->sendRequest();
    }

    public static function _getRequestById($id): array
    {
        return (new Request('GET', self::URL_REQUEST.'/'.$id, []))->sendRequest();
    }

    public static function _getRequestByIdMis($id): array
    {
        return (new Request('GET', self::URL_REQUEST_MIS.'/'.$id, []))->sendRequest();
    }


    public static function schemaRequest(): string
    {
        return <<<'JSON'
        {
          "$schema": "http://json-schema.org/draft-04/schema#",
          "definitions": {
            "phone": {
              "type": "object",
              "properties": {
                "type": {
                  "type": "string",
                  "enum": [
                    "MOBILE",
                    "LANDLINE"
                  ]
                },
                "number": {
                  "type": "string",
                  "pattern": "^\\+38[0-9]{10}$"
                }
              },
              "required": [
                "type",
                "number"
              ],
              "additionalProperties": false
            },
            "document": {
              "type": "object",
              "properties": {
                "type": {
                  "type": "string",
                  "enum": [
                    "PASSPORT",
                    "NATIONAL_ID",
                    "BIRTH_CERTIFICATE",
                    "TEMPORARY_CERTIFICATE"
                  ]
                },
                "number": {
                  "type": "string"
                }
              },
              "required": [
                "type",
                "number"
              ],
              "additionalProperties": false
            },
            "education": {
              "type": "object",
              "properties": {
                "country": {
                  "type": "string",
                  "enum": [
                    "UA"
                  ]
                },
                "city": {
                  "type": "string"
                },
                "institution_name": {
                  "type": "string"
                },
                "issued_date": {
                  "type": "string"
                },
                "diploma_number": {
                  "type": "string"
                },
                "degree": {
                  "type": "string",
                  "enum": [
                    "Молодший спеціаліст",
                    "Бакалавр",
                    "Спеціаліст",
                    "Магістр"
                  ]
                },
                "speciality": {
                  "type": "string"
                }
              },
              "required": [
                "country",
                "city",
                "institution_name",
                "diploma_number",
                "degree",
                "speciality"
              ],
              "additionalProperties": false
            },
            "qualification": {
              "type": "object",
              "properties": {
                "type": {
                  "type": "string",
                  "enum": [
                    "Інтернатура",
                    "Спеціалізація",
                    "Передатестаційний цикл",
                    "Тематичне вдосконалення",
                    "Курси інформації",
                    "Стажування"
                  ]
                },
                "institution_name": {
                  "type": "string"
                },
                "speciality": {
                  "type": "string"
                },
                "issued_date": {
                  "type": "string",
                  "format": "date"
                },
                "certificate_number": {
                  "type": "string",
                  "format": "date"
                }
              },
              "required": [
                "type",
                "institution_name",
                "speciality"
              ],
              "additionalProperties": false
            },
            "speciality": {
              "type": "object",
              "properties": {
                "speciality": {
                  "type": "string",
                  "enum": [
                    "Терапевт",
                    "Педіатр",
                    "Сімейний лікар"
                  ]
                },
                "speciality_officio": {
                  "type": "boolean"
                },
                "level": {
                  "type": "string",
                  "enum": [
                    "Друга категорія",
                    "Перша категорія",
                    "Вища категорія"
                  ]
                },
                "qualification_type": {
                  "type": "string",
                  "enum": [
                    "Присвоєння",
                    "Підтвердження"
                  ]
                },
                "attestation_name": {
                  "type": "string"
                },
                "attestation_date": {
                  "type": "string",
                  "format": "date"
                },
                "valid_to_date": {
                  "type": "string",
                  "format": "date"
                },
                "certificate_number": {
                  "type": "string"
                }
              },
              "required": [
                "speciality",
                "speciality_officio",
                "level",
                "qualification_type",
                "attestation_name",
                "certificate_number"
              ],
              "additionalProperties": false
            },
            "science_degree": {
              "type": "object",
              "properties": {
                "country": {
                  "type": "string",
                  "enum": [
                    "UA"
                  ]
                },
                "city": {
                  "type": "string"
                },
                "degree": {
                  "type": "string",
                  "enum": [
                    "Доктор філософії",
                    "Кандидат наук",
                    "Доктор наук"
                  ]
                },
                "institution_name": {
                  "type": "string"
                },
                "diploma_number": {
                  "type": "string"
                },
                "speciality": {
                  "type": "string",
                  "enum": [
                    "Терапевт",
                    "Педіатр",
                    "Сімейний лікар"
                  ]
                },
                "issued_date": {
                  "type": "string",
                  "format": "date"
                }
              },
              "required": [
                "country",
                "city",
                "degree",
                "institution_name",
                "diploma_number",
                "speciality"
              ],
              "additionalProperties": false
            },
            "party": {
              "type": "object",
              "properties": {
                "first_name": {
                  "type": "string"
                },
                "last_name": {
                  "type": "string"
                },
                "second_name": {
                  "type": "string"
                },
                "birth_date": {
                  "type": "string",
                  "format": "date"
                },
                "gender": {
                  "type": "string",
                  "enum": [
                    "MALE",
                    "FEMALE"
                  ]
                },
                "tax_id": {
                  "type": "string",
                  "pattern": "^[1-9]([0-9]{7}|[0-9]{9})$"
                },
                "email": {
                  "type": "string",
                  "format": "email"
                },
                "documents": {
                  "type": "array",
                  "items": {
                    "$ref": "#/definitions/document"
                  }
                },
                "phones": {
                  "type": "array",
                  "items": {
                    "$ref": "#/definitions/phone"
                  }
                }
              },
              "required": [
                "first_name",
                "last_name",
                "birth_date",
                "gender",
                "tax_id",
                "email",
                "documents",
                "phones"
              ],
              "additionalProperties": false
            }
          },
          "type": "object",
          "properties": {
            "employee_request": {
              "type": "object",
              "properties": {
                "legal_entity_id": {
                  "type": "string",
                  "pattern": "^[0-9a-f]{8}(-?)[0-9a-f]{4}(-?)[0-9a-f]{4}(-?)[0-9a-f]{4}(-?)[0-9a-f]{12}$"
                },
                "division_id": {
                  "type": "string",
                  "pattern": "^[0-9a-f]{8}(-?)[0-9a-f]{4}(-?)[0-9a-f]{4}(-?)[0-9a-f]{4}(-?)[0-9a-f]{12}$"
                },
                "employee_id": {
                  "type": "string",
                  "pattern": "^[0-9a-f]{8}(-?)[0-9a-f]{4}(-?)[0-9a-f]{4}(-?)[0-9a-f]{4}(-?)[0-9a-f]{12}$"
                },
                "position": {
                  "type": "string"
                },
                "start_date": {
                  "type": "string",
                  "format": "date"
                },
                "end_date": {
                  "type": "string",
                  "format": "date"
                },
                "status": {
                  "type": "string",
                  "enum": [
                    "NEW"
                  ]
                },
                "employee_type": {
                  "type": "string",
                  "enum": [
                    "DOCTOR",
                    "HR",
                    "ADMIN",
                    "OWNER"
                  ]
                },
                "party": {
                  "type": "object",
                  "properties": {
                    "items": {
                      "$ref": "#/definitions/party"
                    }
                  }
                },
                "doctor": {
                  "type": "object",
                  "properties": {
                    "educations": {
                      "type": "array",
                      "items": {
                        "$ref": "#/definitions/education"
                      }
                    },
                    "qualifications": {
                      "type": "array",
                      "items": {
                        "$ref": "#/definitions/qualification"
                      }
                    },
                    "specialities": {
                      "type": "array",
                      "items": {
                        "$ref": "#/definitions/speciality"
                      }
                    },
                    "science_degree": {
                      "type": "object",
                      "items": {
                        "$ref": "#/definitions/science_degree"
                      }
                    }
                  },
                  "required": [
                    "educations",
                    "specialities"
                  ]
                }
              },
              "required": [
                "legal_entity_id",
                "position",
                "start_date",
                "status",
                "employee_type",
                "party"
              ]
            }
          },
          "required": [
            "employee_request"
          ]
        }

    JSON;
    }


}
