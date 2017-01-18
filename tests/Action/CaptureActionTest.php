<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\Action\CaptureAction;

class CaptureActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_redirect_to_gateway()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $api = m::spy('PayumTW\Ezship\Api');
        $request = m::spy('Payum\Core\Request\Capture');
        $gateway = m::spy('Payum\Core\GatewayInterface');
        $token = m::spy('Payum\Core\Model\TokenInterface');
        $notifyToken = m::spy('Payum\Core\Model\TokenInterface');

        $details = new ArrayObject([
            'orderID' => '20140318154002',
            'orderStatus' => 'A01',
            'orderType' => '1',
            'orderAmount' => '1680',
            'rvName' => '謝無忌',
            'rvEmail' => '123@ezship.com.tw',
            'rvMobile' => '0987654321',
            'stCode' => 'TFM0038',
            'webPara' => '20140318154002-xxx',
        ]);

        $targetUrl = 'http://localhost/payment/capture/FEDHD1o-fvtpZqM6QvtNsy_qoLX_8x4QXvfyE94mIZc';

        $gatewayName = 'foo.gateway';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details)
            ->shouldReceive('getToken')->andReturn($token);

        $token
            ->shouldReceive('getTargetUrl')->andReturn($targetUrl);

        $action = new CaptureAction();
        $action->setApi($api);
        $action->setGateway($gateway);
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $request->shouldHaveReceived('getModel')->twice();
        $gateway->shouldHaveReceived('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->once();
        $request->shouldHaveReceived('getToken')->once();
        $token->shouldHaveReceived('getTargetUrl')->once();
        $gateway->shouldHaveReceived('execute')->with(m::type('PayumTW\Ezship\Request\Api\CreateTransaction'))->once();
    }

    public function test_capture_success()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $api = m::spy('PayumTW\Ezship\Api');
        $request = m::spy('Payum\Core\Request\Capture');
        $gateway = m::spy('Payum\Core\GatewayInterface');

        $details = new ArrayObject([
            'orderID' => '20140318154002',
            'orderStatus' => 'A01',
            'orderType' => '1',
            'orderAmount' => '1680',
            'rvName' => '謝無忌',
            'rvEmail' => '123@ezship.com.tw',
            'rvMobile' => '0987654321',
            'stCode' => 'TFM0038',
            'webPara' => '20140318154002-xxx',
        ]);

        $response = [
            'orderID' => '20140318154002',
            'snID' => '20140318154002',
            'order_status' => 'S01',
            'webPara' => '20140318154002-xxx',
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $gateway
            ->shouldReceive('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->andReturnUsing(function ($httpRquest) use ($response) {
                $httpRquest->request = $response;

                return $httpRquest;
            });

        $api
            ->shouldReceive('verifyHash')->with($response, $details)->andReturn(true);

        $action = new CaptureAction();
        $action->setApi($api);
        $action->setGateway($gateway);
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'orderID' => '20140318154002',
            'orderStatus' => 'A01',
            'orderType' => '1',
            'orderAmount' => '1680',
            'rvName' => '謝無忌',
            'rvEmail' => '123@ezship.com.tw',
            'rvMobile' => '0987654321',
            'stCode' => 'TFM0038',
            'webPara' => '20140318154002-xxx',
            'snID' => '20140318154002',
            'order_status' => 'S01',
            'webPara' => '20140318154002-xxx',
        ], $details->toUnsafeArray());

        $request->shouldHaveReceived('getModel')->twice();
        $gateway->shouldHaveReceived('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->once();
        $api->shouldHaveReceived('verifyHash')->with($response, $details)->once();
    }

    public function test_capture_fail()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $api = m::spy('PayumTW\Ezship\Api');
        $request = m::spy('Payum\Core\Request\Capture');
        $gateway = m::spy('Payum\Core\GatewayInterface');

        $details = new ArrayObject([
            'orderID' => '20140318154002',
            'orderStatus' => 'A01',
            'orderType' => '1',
            'orderAmount' => '1680',
            'rvName' => '謝無忌',
            'rvEmail' => '123@ezship.com.tw',
            'rvMobile' => '0987654321',
            'stCode' => 'TFM0038',
            'webPara' => '20140318154002-xxx',
        ]);

        $response = [
            'orderID' => '20140318154002',
            'snID' => '20140318154002',
            'order_status' => 'S01',
            'webPara' => '20140318154002-xxx',
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $gateway
            ->shouldReceive('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->andReturnUsing(function ($httpRquest) use ($response) {
                $httpRquest->request = $response;

                return $httpRquest;
            });

        $api
            ->shouldReceive('verifyHash')->with($response, $details)->andReturn(false);

        $action = new CaptureAction();
        $action->setApi($api);
        $action->setGateway($gateway);
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'orderID' => '20140318154002',
            'orderStatus' => 'A01',
            'orderType' => '1',
            'orderAmount' => '1680',
            'rvName' => '謝無忌',
            'rvEmail' => '123@ezship.com.tw',
            'rvMobile' => '0987654321',
            'stCode' => 'TFM0038',
            'webPara' => '20140318154002-xxx',
            'snID' => '20140318154002',
            'order_status' => 'E99',
            'webPara' => '20140318154002-xxx',
        ], $details->toUnsafeArray());

        $request->shouldHaveReceived('getModel')->twice();
        $gateway->shouldHaveReceived('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->once();
        $api->shouldHaveReceived('verifyHash')->with($response, $details)->once();
    }

    public function test_cvs_capture_success()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $api = m::spy('PayumTW\Ezship\Api');
        $request = m::spy('Payum\Core\Request\Capture');
        $gateway = m::spy('Payum\Core\GatewayInterface');

        $details = new ArrayObject([
            'suID' => '20140318154002',
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'rtURL' => 'http://yourdomain.domain/direct/program.php',
            'webPara' => '20140318154002-xxx',
        ]);

        $response = [
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'stName' => '門市名稱',
            'stAddr' => '門市地址',
            'stTel' => '門市電話',
            'webPara' => '20140318154002-xxx',
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $gateway
            ->shouldReceive('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->andReturnUsing(function ($httpRquest) use ($response) {
                $httpRquest->request = $response;

                return $httpRquest;
            });

        $api
            ->shouldReceive('verifyHash')->with($response, $details)->andReturn(true);

        $action = new CaptureAction();
        $action->setApi($api);
        $action->setGateway($gateway);
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'suID' => '20140318154002',
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'rtURL' => 'http://yourdomain.domain/direct/program.php',
            'webPara' => '20140318154002-xxx',
            // response
            'stName' => '門市名稱',
            'stAddr' => '門市地址',
            'stTel' => '門市電話',
        ], $details->toUnsafeArray());

        $request->shouldHaveReceived('getModel')->twice();
        $gateway->shouldHaveReceived('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->once();
        $api->shouldHaveReceived('verifyHash')->with($response, $details)->once();
    }

    public function test_cvs_capture_fail()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $api = m::spy('PayumTW\Ezship\Api');
        $request = m::spy('Payum\Core\Request\Capture');
        $gateway = m::spy('Payum\Core\GatewayInterface');

        $details = new ArrayObject([
            'suID' => '20140318154002',
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'rtURL' => 'http://yourdomain.domain/direct/program.php',
            'webPara' => '20140318154002-xxx',
        ]);

        $response = [
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'stName' => '門市名稱',
            'stAddr' => '門市地址',
            'stTel' => '門市電話',
            'webPara' => '20140318154002-xxx',
        ];

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $gateway
            ->shouldReceive('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->andReturnUsing(function ($httpRquest) use ($response) {
                $httpRquest->request = $response;

                return $httpRquest;
            });

        $api
            ->shouldReceive('verifyHash')->with($response, $details)->andReturn(false);

        $action = new CaptureAction();
        $action->setApi($api);
        $action->setGateway($gateway);
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertSame([
            'suID' => '20140318154002',
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'rtURL' => 'http://yourdomain.domain/direct/program.php',
            'webPara' => '20140318154002-xxx',
            // response
            'stName' => '門市名稱',
            'stAddr' => '門市地址',
            'stTel' => '門市電話',
            'order_status' => 'E99',
        ], $details->toUnsafeArray());

        $request->shouldHaveReceived('getModel')->twice();
        $gateway->shouldHaveReceived('execute')->with(m::type('Payum\Core\Request\GetHttpRequest'))->once();
        $api->shouldHaveReceived('verifyHash')->with($response, $details)->once();
    }
}
