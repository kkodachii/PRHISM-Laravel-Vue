<?php

namespace App\Http\Middleware;

use App\Models\Barangays;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    protected $routeMiddleware = [
        // Other middleware
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(
            parent::share($request),
            [
                'auth.user' => fn () => $request->user()
                    ? array_merge(
                        $request->user()->only('role_id', 'name', 'email', 'id', 'rhu_id', 'barangay_id', 'profile_picture', 'status'),
                        [
                            'barangay_name' => barangays::where('id', $request->user()->barangay_id)->value('barangay_name')
                        ]
                    )
                    : null,
                'toast' => session('toast'),
            ]
        );
    }

}
