<?php

namespace Database\Seeders;

use App\Mail\VerificationEmail;
use App\Mail\WelcomeMail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\MailTemplates\Models\MailTemplate;

class MailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the welcome email template (if not already present in DB)
        if (DB::table('mail_templates')->where('mailable', '=', WelcomeMail::class)->count() === 0) {
            MailTemplate::query()->create([
                'name' => 'Welcome email',
                'info' => 'Sent to user when registered manually by an admin.',
                'mailable' => WelcomeMail::class,
                'subject' => 'Welcome to {{ appName }}!',
                'html_template' => '<h1>Hello, {{ name }} 👋</h1><p>Welcome to {{ appName }}!</p><p>Please use the login button below with the following password: <b>{{ password }}</b> to login and start using the app now.</p>',
                'text_template' => null,
            ]);
        }

        // Create the email verification template (if not already present in DB)
        if (DB::table('mail_templates')->where('mailable', '=', VerificationEmail::class)->count() === 0) {
            MailTemplate::query()->create([
                'name' => 'Email verification',
                'info' => 'Sent to users who have self-registered to confirm their email address.',
                'mailable' => VerificationEmail::class,
                'subject' => 'Please verify your email',
                'html_template' => '<h1>Hello, {{ name }} 👋</h1><p>Please use the button below to verify your email address.</p><p>Didn\'t create an account on {{ appName }}? No problem, simply disregard this email.</p>',
                'text_template' => null,
            ]);
        }
    }
}
