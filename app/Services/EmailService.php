<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Dto\EmailDTO;

class EmailService
{
    public function sendEmail(EmailDTO $emailDTO): array
    {
        $result = [];
        $result['success'] = false;

        try {
            $email = config('app.email');
            Mail::to($email)->send(new SendEmail($emailDTO));

            //* Email sent successfully
            $result['success'] = true;
            $result['message'] = trans('Повідомлення відправлено успішно');
        } catch (\Exception $e) {
            //! Email sending failed
            $result['message'] = $e->getMessage();
        }

        return $result;
    }
}
