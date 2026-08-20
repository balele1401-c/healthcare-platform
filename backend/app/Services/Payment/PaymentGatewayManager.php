<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\KomerceGateway;
use App\Services\Payment\Gateways\MayarGateway;
use App\Services\Payment\Gateways\MockSandboxGateway;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

class PaymentGatewayManager
{
    protected Application $app;
    protected array $gateways = [];
    protected array $customCreators = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get a payment gateway instance by name or default.
     */
    public function driver(string $name = null): PaymentGatewayInterface
    {
        $name = $name ?: config('payment.default', 'sandbox');

        if (! isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createDriver($name);
        }

        return $this->gateways[$name];
    }

    /**
     * Create a driver instance.
     */
    protected function createDriver(string $name): PaymentGatewayInterface
    {
        if (isset($this->customCreators[$name])) {
            return $this->customCreators[$name]($this->app);
        }

        $config = config("payment.providers.{$name}", []);

        return match ($name) {
            'sandbox', 'mock', 'test' => new MockSandboxGateway($config),
            'mayar' => new MayarGateway($config),
            'komerce' => new KomerceGateway($config),
            default => throw new InvalidArgumentException("Unsupported payment driver [{$name}]."),
        };
    }

    /**
     * Register a custom gateway creator.
     */
    public function extend(string $driver, \Closure $callback): self
    {
        $this->customCreators[$driver] = $callback;
        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     */
    public function __call(string $method, array $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
