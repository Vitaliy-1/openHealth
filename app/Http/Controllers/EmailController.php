<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Services\EmailService;
use App\DTO\EmailDTO;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send an email.
     *
     * @param  \App\Http\Requests\SendEmailRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(SendEmailRequest $request): JsonResponse
    {
        // Create an instance of the DTO
        $emailDTO = new EmailDTO($request->input('name'), trim($request->input('phone')));

        // Send email using the service
        $result = $this->emailService->sendEmail($emailDTO);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message']
        ], $result['success'] ? 200 : 400);
    }
}

