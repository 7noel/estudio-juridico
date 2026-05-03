<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\CaseFile;
use App\Models\Consultation;
use App\Models\Client;
use App\Models\Document;
use App\Models\Payment;
use App\Models\LegalActivity;
use App\Models\AgendaEvent;
use App\Models\Communication;
use App\Models\Employee;
use App\Models\Establishment;
use Spatie\Permission\Models\Role;

use App\Policies\CaseFilePolicy;
use App\Policies\ConsultationPolicy;
use App\Policies\ClientPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\LegalActivityPolicy;
use App\Policies\AgendaEventPolicy;
use App\Policies\CommunicationPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\EstablishmentPolicy;
use App\Policies\RolePolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(CaseFile::class, CaseFilePolicy::class);
        Gate::policy(Consultation::class, ConsultationPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Document::class, DocumentPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(LegalActivity::class, LegalActivityPolicy::class);
        Gate::policy(AgendaEvent::class, AgendaEventPolicy::class);
        Gate::policy(Communication::class, CommunicationPolicy::class);
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(Establishment::class, EstablishmentPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
    }
}