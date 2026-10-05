<?php

namespace Tests\Feature;

use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Tests\TestCase;

class TrustProxiesTest extends TestCase
{
    /** @test */
    public function ip_klien_diambil_dari_x_forwarded_for_jika_proxy_dipercaya(): void
    {
        config(['app.trusted_proxies' => ['103.21.244.0/22']]);

        $request = $this->requestFrom('103.21.244.5', '1.2.3.4');
        (new TrustProxies())->handle($request, fn ($r) => response('ok'));

        $this->assertSame('1.2.3.4', $request->ip());
    }

    /** @test */
    public function x_forwarded_for_palsuan_dari_luar_proxy_dipercaya_diabaikan(): void
    {
        config(['app.trusted_proxies' => ['103.21.244.0/22']]);

        $request = $this->requestFrom('198.51.100.7', '1.2.3.4');
        (new TrustProxies())->handle($request, fn ($r) => response('ok'));

        $this->assertSame('198.51.100.7', $request->ip());
    }

    /** @test */
    public function tanpa_konfigurasi_tidak_ada_proxy_yang_dipercaya(): void
    {
        config(['app.trusted_proxies' => []]);

        $request = $this->requestFrom('103.21.244.5', '1.2.3.4');
        (new TrustProxies())->handle($request, fn ($r) => response('ok'));

        $this->assertSame('103.21.244.5', $request->ip());
    }

    private function requestFrom(string $remoteAddr, string $forwardedFor): Request
    {
        return Request::create('/', 'GET', [], [], [], [
            'REMOTE_ADDR'          => $remoteAddr,
            'HTTP_X_FORWARDED_FOR' => $forwardedFor,
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);
    }
}
