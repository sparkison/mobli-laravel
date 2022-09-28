<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\MailTemplates\TemplateMailable;

class VerificationEmail extends TemplateMailable implements ShouldQueue
{
    /** @var string */
    public $name;

    /** @var string */
    public $appName;

    /** @var string */
    protected $url;

    /**
     * @param User $user
     * @param $url
     */
    public function __construct(User $user, $url)
    {
        $this->name = $user->name;
        $this->url = $url;
        $this->appName = env('APP_NAME');
    }

    public function getHtmlLayout(): string
    {
        $greeting = null;
        $introLines = [
            $this->html
        ];
        $actionUrl = $this->url;
        $actionText = 'Confirm Email';
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
