<?php

namespace App\Traits;


trait HasPersonalAttributes
{

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            $this->party['first_name'] ?? '',
            $this->party['last_name'] ?? '',
            $this->party['second_name']
        ]));
    }

    public function getPhoneAttribute(): string
    {
        return $this->party['phones'][0]['number'] ?? '';
    }

    public function getBirthDateAttribute($property = 'party'): string
    {
        return humanFormatDate($this->party['birth_date'] ?? '');
    }


    public function getEmailAttribute($property = 'party'): string
    {
        return $this->party['email'] ?? '';
    }

}
