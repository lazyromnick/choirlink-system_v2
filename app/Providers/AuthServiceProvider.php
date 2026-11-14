<?php

namespace App\Providers;

use App\Models\Nagsasanay;
use App\Policies\NagsasanayPolicy;
use App\Models\MangAawit;
use App\Models\MangAawitAccount;
use App\Models\MangAawitMembership;
use App\Models\MangAawitQrToken;
use App\Policies\MangAawitPolicy;
use App\Policies\MangAawitAccountPolicy;
use App\Policies\MangAawitMembershipPolicy;
use App\Policies\MangAawitQrTokenPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        MangAawit::class           => MangAawitPolicy::class,
        MangAawitAccount::class    => MangAawitAccountPolicy::class,
        MangAawitMembership::class => MangAawitMembershipPolicy::class,
        MangAawitQrToken::class    => MangAawitQrTokenPolicy::class,
        Nagsasanay::class => NagsasanayPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}