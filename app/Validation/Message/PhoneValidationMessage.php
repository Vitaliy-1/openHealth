<?php

namespace App\Validation\Message;

class PhoneValidationMessage
{
    public static function getMessages(string $attribute = ''): array
    {
        return [
            $attribute . 'phones.required' => 'Поле с номерами телефонов является обязательным.',
            $attribute . 'phones.array' => 'Поле с номерами телефонов должно быть массивом.',
            $attribute . 'phones.*.number.required' => 'Номер телефона является обязательным.',
            $attribute . 'phones.*.number.regex' => 'Номер телефона должен содержать 12 цифр с возможным знаком "+".',
            $attribute . 'phones.*.type.required' => 'Тип телефона является обязательным.',
            $attribute . 'phones.*.type.in' => 'Тип телефона должен быть "MOBILE" или "LAND_LINE".',
        ];
    }
}
