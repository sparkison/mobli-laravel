<?php

namespace App\Http\Controllers;

use App\Http\Requests\MailTemplateRequest;
use App\Mail\VerificationEmail;
use App\Mail\WelcomeMail;
use App\Models\User;
use Faker\Factory;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use ProtoneMedia\LaravelQueryBuilderInertiaJs\InertiaTable;
use Spatie\MailTemplates\Models\MailTemplate;
use Spatie\QueryBuilder\QueryBuilder;

class MailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Build the query
        $models = QueryBuilder::for(MailTemplate::class)
            ->defaultSort('name')
            ->paginate()
            ->withQueryString();

        // Render the view
        return Inertia::render('MailTemplates/Index', [
            'models' => $models,
            'sortable' => []
        ])->table(function(InertiaTable $table) {
            $table
                ->defaultSort('name')
                ->column(label: 'Actions', canBeHidden: false)
                ->column(key: 'name', searchable: false, sortable: true, canBeHidden: false)
                ->column(key: 'subject', searchable: false, sortable: true, canBeHidden: false)
                ->column(key: 'info', canBeHidden: false);
        });
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function show($id)
    {
        // Get the template
        $emailTemplate = MailTemplate::query()->findOrFail($id);

        // Get the mailable model
        $model = $emailTemplate->mailable;

        // Create a faker factory to build temp models for different template previews
        $faker = Factory::create();

        // Grab the logged in user and their team (needed for all notifs)
        /** @var User $user */
        $user = auth()->user();

        // Return view based on template model
        switch ($model) {
            case WelcomeMail::class:
                return new WelcomeMail($user, $this->randomPassword());
            case VerificationEmail::class:
                return new VerificationEmail($user, url(''));
        }

        // Model not found
        return "Mail template not found";
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function edit($id)
    {
        // Get the template
        $emailTemplate = MailTemplate::query()->findOrFail($id);

        // Return the view
        return Inertia::render('MailTemplates/Edit', [
            'template' => [
                'id' => $emailTemplate->id,
                'name' => $emailTemplate->name ?? explode('\\', $emailTemplate->mailable)[2],
                'info' => $emailTemplate->info,
                'subject' => $emailTemplate->subject,
                'html_template' => $emailTemplate->html_template,
                'text_template' => $emailTemplate->text_template,
                'preview_url' => URL::route('notifications.show', $emailTemplate),
                'variables' => $emailTemplate->getVariables()
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MailTemplateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(MailTemplateRequest $request, $id)
    {
        // Get the template
        $emailTemplate = MailTemplate::query()->findOrFail($id);
        $emailTemplate->update([
            'name' => $request->name,
            'info' => $request->info,
            'subject' => $request->subject,
            'html_template' => $request->html_template,
            'text_template' => $request->text_template,
        ]);
        return Redirect::back()->with('success', 'Template updated.');
    }

    /**
     * @param int $length
     * @return string
     * @throws \Exception
     */
    protected function randomPassword($length = 10)
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz=*+.&%$#@!ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = []; //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
