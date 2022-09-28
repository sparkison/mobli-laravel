<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Mail\WelcomeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        return Inertia::render('Users/Index', [
            'filters' => request(['search', 'trashed', 'order']),
            'users' => User::query()
                // ->where('id', '!=', $user->id) // uncomment to exclude self
                ->filter(request(['search', 'trashed', 'order']))
                ->paginate()
                ->through(function(User $user) {
                    return [
                        'id' => $user->id,
                        'created_at' => $user->created_at,
                        'name' => $user->name,
                        'profile_photo_url' => $user->profile_photo_url,
                        'email' => $user->email,
                        'is_admin' => $user->is_admin,
                        'edit_url' => URL::route('user.edit', $user),
                        'show_url' => URL::route('user.show', $user),
                        'impersonate_url' => URL::route('impersonate', $user)
                    ];
                }),
            'create_url' => URL::route('user.create'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        return Inertia::render('Users/Create', [
            // ...
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @throws \Throwable
     */
    public function store(UserRequest $request)
    {
        // Get a new password
        $password = $this->randomPassword();

        // Wrap in transaction
        DB::beginTransaction();
        try {
            // Attempt to create user
            $user = User::query()->create([
                'name' => $request->name,
                'surname' => $request->surname,
                'phone' => $request->phone,
                'address' => $request->address,
                'is_admin' => $request->is_admin,
                'is_pro' => $request->is_pro,
                'geo_coordinates' => $request->geo_coordinates,
                'year_home_built' => $request->year_home_built,
                'additional_fields' => $request->additional_fields,
                'password' => Hash::make($password),
                'email_verified_at' => Carbon::now(), // user will need to access their email to get login details, assume they have a valid email address
            ]);
            if ($user) {
                /** @var User $user */

                // Send welcome message
                \Mail::to($user->email)
                    ->send(new WelcomeMail($user, $password));

                // Commit and return index
                DB::commit();
                return Redirect::route('user.index')
                    ->with('success', 'User created.');
            }
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
        }

        // Unable to create user, rollback and return error
        DB::rollBack();
        return Redirect::back()->with('error', 'Error creating user.');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function show(Request $request, User $user)
    {
        // $user->load(['eagerLoad']); // any eager loads?
        return Inertia::render('Users/Show',
            array_merge([
                // ...
            ], $this->userModelParam($user))
        );
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Inertia\Response
     */
    public function edit(User $user)
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        // If attempting to edit self, redirect to profile
        if ($authUser->id === $user->id) {
            return \redirect(route('profile.show'));
        }

        // $user->load(['eagerLoad']); // any eager loads?
        return Inertia::render('Users/Edit',
            $this->userModelParam($user)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        // Wrap in transaction
        DB::beginTransaction();
        try {
            // Update user
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'is_admin' => $request->is_admin,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Unable to update user: ' . $e->getMessage());
        }
        DB::commit();
        return Redirect::back()->with('success', 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        DB::beginTransaction();
        try {
            // Delete user (teams will "cascade on delete")
            $user->forceDelete();
        } catch (\Exception $e) {
            // Roll it back and return error
            DB::rollBack();
            return Redirect::back()
                ->with('error', 'We ran into an error attempting to delete the user: ' . $e->getMessage());
        }

        // Commit changes
        DB::commit();

        // Return to index
        return Redirect::route('user.index')
            ->with('success', 'User deleted.');
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

    /**
     * @param User $user
     * @return array[]
     */
    private function userModelParam(User $user): array
    {
        return [
            'model' => [
                'id' => $user->id,
                'created_at' => $user->created_at,
                'name' => $user->name,
                'profile_photo_url' => $user->profile_photo_url,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'impersonate_url' => URL::route('impersonate', $user),
            ]
        ];
    }
}
