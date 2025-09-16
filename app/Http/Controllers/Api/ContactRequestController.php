<?php

namespace App\Http\Controllers\Api;

use App\Models\EmailStatus;
use App\Models\ContactRequest;
use App\Services\Recaptcha\RecaptchaService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Services\Email\MailValidator;
use App\Traits\Response\HasApiResponse;
use App\Http\Requests\Api\ContactUsRequest;
use Symfony\Component\HttpFoundation\Response;

class ContactRequestController extends Controller
{
    use HasApiResponse;

    // public function store(ContactUsRequest $request)
    // {
    //     $hasContactToday = ContactRequest::where(fn($q) => $q->where('email', $request->email)->orWhere('ip', $request->ip()))
    //         ->whereDate('created_at', today())
    //         ->exists();

    //     if ($hasContactToday || MailValidator::validate($request->email) != EmailStatus::DELIVERABLE) {
    //         return $this->send(
    //             message: __('messages.contact-request.invalid_or_spam_email'),
    //             statusCode: Response::HTTP_BAD_REQUEST
    //         );
    //     }

    //     ContactRequest::create($request->getSanitized());

    //     return $this->send(
    //         message: __('messages.contact-request.sent')
    //     );
    // }

    protected $recaptchaService;

    /**
     * Inject the RecaptchaService via constructor.
     *
     * @param RecaptchaService $recaptchaService
     */
    public function __construct(RecaptchaService $recaptchaService)
    {
        $this->recaptchaService = $recaptchaService;
    }

    public function store(ContactUsRequest $request)
    {
        // Verify the reCAPTCHA token using the service
//        $recaptchaData = $this->recaptchaService->verify($request->recaptcha_token, $request->ip());
//
//        if (!$recaptchaData['success'] || $recaptchaData['score'] < 0.5) {
//            return $this->send(
//                message: __('messages.contact-request.failed_captcha'),
//                statusCode: Response::HTTP_BAD_REQUEST
//            );
//        }

        // Check for duplicate submissions
        $hasContactToday = ContactRequest::where(fn($q) => $q->where('email', $request->email)->orWhere('ip', $request->ip()))
            ->whereDate('created_at', today())
            ->exists();

        if ($hasContactToday || MailValidator::validate($request->email) != EmailStatus::DELIVERABLE) {
            return $this->send(
                message: __('messages.contact-request.invalid_or_spam_email'),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }

        ContactRequest::create($request->getSanitized());

        return $this->send(
            message: __('messages.contact-request.sent')
        );
    }
}
