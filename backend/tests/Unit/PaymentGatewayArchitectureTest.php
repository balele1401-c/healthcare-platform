<?php

namespace Tests\Unit;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Payment\Gateways\KomerceGateway;
use App\Services\Payment\Gateways\MayarGateway;
use App\Services\Payment\Gateways\MockSandboxGateway;
use App\Services\Payment\PaymentGatewayManager;
use Tests\TestCase;

class PaymentGatewayArchitectureTest extends TestCase
{
    public function test_payment_gateway_manager_resolves_configured_drivers(): void
    {
        $manager = app(PaymentGatewayManager::class);

        $this->assertInstanceOf(MockSandboxGateway::class, $manager->driver('sandbox'));
        $this->assertInstanceOf(MayarGateway::class, $manager->driver('mayar'));
        $this->assertInstanceOf(KomerceGateway::class, $manager->driver('komerce'));
    }

    public function test_mayar_gateway_reports_unavailable_when_credentials_not_configured(): void
    {
        $gateway = new MayarGateway(['api_key' => null]);
        $this->assertFalse($gateway->isAvailable());

        $payment = new Payment([
            'payment_reference' => 'PAY-TEST-MOCK',
            'amount' => 100.00,
            'currency' => 'USD',
        ]);

        $result = $gateway->createPayment($payment);
        $this->assertFalse($result->success);
        $this->assertEquals('Payment service is currently unavailable.', $result->errorMessage);
    }

    public function test_komerce_gateway_reports_unavailable_when_credentials_not_configured(): void
    {
        $gateway = new KomerceGateway(['api_key' => null]);
        $this->assertFalse($gateway->isAvailable());

        $payment = new Payment([
            'payment_reference' => 'PAY-TEST-MOCK',
            'amount' => 100.00,
            'currency' => 'USD',
        ]);

        $result = $gateway->createPayment($payment);
        $this->assertFalse($result->success);
        $this->assertEquals('Payment service is currently unavailable.', $result->errorMessage);
    }

    public function test_mock_sandbox_gateway_is_always_available_and_returns_checkout_details(): void
    {
        $gateway = new MockSandboxGateway();
        $this->assertTrue($gateway->isAvailable());

        $payment = new Payment([
            'id' => 42,
            'payment_reference' => 'PAY-SANDBOX-42',
            'amount' => 150.00,
            'currency' => 'USD',
            'status' => PaymentStatus::PENDING,
        ]);

        $result = $gateway->createPayment($payment);
        $this->assertTrue($result->success);
        $this->assertNotNull($result->providerPaymentId);
        $this->assertNotNull($result->checkoutUrl);
        $this->assertNotNull($result->qrString);
        $this->assertNotNull($result->virtualAccount);
    }

    public function test_payment_status_normalization_and_helper_methods(): void
    {
        $this->assertEquals('pending', PaymentStatus::PENDING->value);
        $this->assertEquals('processing', PaymentStatus::PROCESSING->value);
        $this->assertEquals('paid', PaymentStatus::PAID->value);
        $this->assertEquals('failed', PaymentStatus::FAILED->value);
        $this->assertEquals('expired', PaymentStatus::EXPIRED->value);
        $this->assertEquals('cancelled', PaymentStatus::CANCELLED->value);
        $this->assertEquals('refunded', PaymentStatus::REFUNDED->value);

        $this->assertTrue(PaymentStatus::PAID->isFinalized());
        $this->assertTrue(PaymentStatus::REFUNDED->isFinalized());
        $this->assertFalse(PaymentStatus::PENDING->isFinalized());
        $this->assertFalse(PaymentStatus::PROCESSING->isFinalized());
    }
}
