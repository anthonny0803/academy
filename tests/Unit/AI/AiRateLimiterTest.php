<?php

namespace Tests\Unit\AI;

use App\Domains\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AiRateLimiterTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_a_tighter_tenant_scoped_quota(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $limit = (RateLimiter::limiter('ai'))($request);

        $this->assertSame("{$user->tenant_id}:{$user->id}", $limit->key);
        $this->assertSame(10, $limit->maxAttempts);
    }
}
