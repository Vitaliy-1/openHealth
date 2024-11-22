<?php

namespace App\Traits;


trait HasPersonalAttributes
{

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            optional($this->party)->firstFname ?? '',
            optional($this->party)->lastName ?? '',
            optional($this->party)->secondName?? '',
        ]));
    }

    public function getPhoneAttribute(): string
    {
        return optional(optional($this->party)->phones)->first()->number ?? '';
    }

    public function getBirthDateAttribute(): string
    {
        return humanFormatDate(optional($this->party)->birth_date ?? '');
    }


    public function getEmailAttribute(): string
    {
        return optional($this->party)->email ?? '';
    }

}
