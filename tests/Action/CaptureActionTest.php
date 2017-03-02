<?php

namespace PayumTW\Ezship\Tests\Action;

use Mockery as m;
use Payum\Core\Request\Capture;
use PHPUnit\Framework\TestCase;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetHttpRequest;
use PayumTW\Ezship\Action\CaptureAction;

class CaptureActionTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testExecute()
    {
        $action = new CaptureAction();
        $request = m::mock(new Capture(new ArrayObject([])));

        $action->setGateway(
            $gateway = m::mock('Payum\Core\GatewayInterface')
        );

        $gateway->shouldReceive('execute')->once()->with(m::on(function ($getHttpRequest) {
            return $getHttpRequest instanceof GetHttpRequest;
        }));

        $request->shouldReceive('getToken')->once()->andReturn(
            $token = m::mock('Payum\Core\Model\TokenInterface')
        );

        $token->shouldReceive('getTargetUrl')->once()->andReturn($targetUrl = 'http://dev/payum/ezship/');

        $gateway->shouldReceive('execute')->once()->with(m::type('PayumTW\Ezship\Request\Api\CreateTransaction'));

        $action->execute($request);

        $this->assertSame([
            'rtn_url' => $targetUrl,
        ], (array) $request->getModel());
    }

    public function testCapture()
    {
        $action = new CaptureAction();
        $request = m::mock(new Capture(new ArrayObject([])));

        $action->setGateway(
            $gateway = m::mock('Payum\Core\GatewayInterface')
        );

        $response = [
            'order_status' => 'foo',
        ];
        $gateway->shouldReceive('execute')->once()->with(m::on(function ($getHttpRequest) use ($response) {
            $getHttpRequest->request = $response;

            return $getHttpRequest instanceof GetHttpRequest;
        }));

        $action->setApi(
            $api = m::mock('PayumTW\Ezship\Api')
        );

        $api->shouldReceive('verifyHash')->once()->with($response, (array) $request->getModel())->andReturn(false);

        $action->execute($request);

        $this->assertSame([
            'order_status' => 'E99',
        ], (array) $request->getModel());
    }

    public function testCaptureCVS()
    {
        $action = new CaptureAction();
        $request = m::mock(new Capture(new ArrayObject([])));

        $action->setGateway(
            $gateway = m::mock('Payum\Core\GatewayInterface')
        );

        $response = [
            'processID' => 'foo',
        ];
        $gateway->shouldReceive('execute')->once()->with(m::on(function ($getHttpRequest) use ($response) {
            $getHttpRequest->request = $response;

            return $getHttpRequest instanceof GetHttpRequest;
        }));

        $action->setApi(
            $api = m::mock('PayumTW\Ezship\Api')
        );

        $api->shouldReceive('verifyHash')->once()->with($response, (array) $request->getModel())->andReturn(false);

        $action->execute($request);

        $this->assertSame([
            'processID' => 'foo',
            'order_status' => 'E99',
        ], (array) $request->getModel());
    }
}
