<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Project;
use App\Policies\ProjectPolicy;


class AuthServiceProvider extends ServiceProvider
{   

    protected $policies = [
        Project::class => ProjectPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }

}
