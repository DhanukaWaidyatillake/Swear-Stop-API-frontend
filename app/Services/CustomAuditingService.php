<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use OwenIt\Auditing\Events\AuditCustom;

class CustomAuditingService
{

    public function createCustomAudit(User $user, string $auditEvent, array $auditData): void
    {
        $user->auditEvent = $auditEvent;
        $user->isCustomEvent = true;
        $user->auditCustomOld = $auditData;
        $user->auditCustomNew = $auditData;
        Event::dispatch(AuditCustom::class, [$user]);
    }
}
