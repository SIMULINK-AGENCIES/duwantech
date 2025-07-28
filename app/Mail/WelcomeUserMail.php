<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $verificationUrl = null)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
        
        // Set queue connection for welcome emails
        $this->onQueue('emails');
        $this->delay(now()->addMinutes(2)); // Small delay for better registration flow
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [$this->user->email],
            subject: 'Welcome to ' . config('app.name') . '!',
            tags: ['welcome', 'user-registration'],
            metadata: [
                'user_id' => $this->user->id,
                'registration_date' => $this->user->created_at->toISOString(),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.users.welcome',
            with: [
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'verificationUrl' => $this->verificationUrl,
                'registrationDate' => $this->user->created_at,
                'storeName' => config('app.name'),
                'storeUrl' => config('app.url'),
                'loginUrl' => route('login'),
                'profileUrl' => route('profile.edit'),
                'supportEmail' => config('mail.from.address'),
                'supportPhone' => config('app.support_phone', '+254 700 000 000'),
                'features' => [
                    'Browse thousands of quality products',
                    'Secure M-Pesa payment integration',
                    'Fast and reliable delivery',
                    'Order tracking and history',
                    'Customer support and assistance',
                ],
                'benefits' => [
                    'Exclusive member discounts',
                    'Early access to new products',
                    'Personalized recommendations',
                    'Priority customer support',
                ],
                'socialLinks' => [
                    'facebook' => config('app.social.facebook'),
                    'twitter' => config('app.social.twitter'),
                    'instagram' => config('app.social.instagram'),
                ],
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Determine if the message should be sent.
     */
    public function shouldSend(): bool
    {
        // Check if welcome emails are enabled and user hasn't opted out
        return config('app.welcome_emails', true) && 
               ($this->user->email_notifications ?? true);
    }
}
