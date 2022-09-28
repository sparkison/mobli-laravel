<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\MailTemplates\TemplateMailable;

class WelcomeMail extends TemplateMailable implements ShouldQueue
{
    /** @var string */
    public $name;

    /** @var string */
    public $password;

    /** @var string */
    public $appName;

    /**
     * @param User $user
     * @param $password
     */
    public function __construct(User $user, $password)
    {
        $this->name = $user->name;
        $this->password = $password;
        $this->appName = env('APP_NAME');
    }

    public function getHtmlLayout(): string
    {
        $greeting = null;
        $introLines = [
            $this->html
        ];
        $actionUrl = url('login');
        $actionText = 'Login';
        $outroLines = [];
        $salutation = null;
        return view('emails.default', compact(
            'greeting',
            'introLines',
            'actionText',
            'actionUrl',
            'outroLines',
            'salutation'
        ))->render();
    }
}
