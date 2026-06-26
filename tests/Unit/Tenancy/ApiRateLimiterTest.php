<?php

namespace Tests\Unit\Tenancy;

use App\Domains\Identity\Models\User;
use App\Domains\Tenancy\Models\Tenant;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiRateLimiterTest extends TestCase
{
    use RefreshDatabase;

    public function test_keys_authenticated_requests_by_tenant_and_user(): void
    {
        $user = User::factory()->create();
        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        $limit = $this->resolveApiLimit($request);

        $this->assertSame("{$user->tenant_id}:{$user->id}", $limit->key);
        $this->assertSame(60, $limit->maxAttempts);
    }

    public function test_falls_back_to_ip_for_unauthenticated_requests(): void
    {
        $request = Request::create('/', 'GET', server: ['REMOTE_ADDR' => '203.0.113.5']);

        $limit = $this->resolveApiLimit($request);

        $this->assertSame('203.0.113.5', $limit->key);
        $this->assertSame(60, $limit->maxAttempts);
    }

    public function test_isolates_the_keyspace_across_tenants(): void
    {
        $userA = User::factory()->create();

        $tenantB = Tenant::factory()->create();
        $userB = $this->withinTenant($tenantB, fn () => User::factory()->create());

        $keyA = $this->resolveApiLimit($this->requestFor($userA))->key;
        $keyB = $this->resolveApiLimit($this->requestFor($userB))->key;

        $this->assertNotSame($keyA, $keyB);
        $this->assertStringStartsWith("{$userA->tenant_id}:", $keyA);
        $this->assertStringStartsWith("{$tenantB->id}:", $keyB);
    }

    private function resolveApiLimit(Request $request): Limit
    {
        return (RateLimiter::limiter('api'))($request);
    }

    private function requestFor(User $user): Request
    {
        $request = Request::create('/');
        $request->setUserResolver(fn () => $user);

        return $request;
    }
}
