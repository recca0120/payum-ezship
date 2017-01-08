<?php

use Mockery as m;
use PayumTW\EzShip\Action\SyncAction;
use Payum\Core\Bridge\Spl\ArrayObject;

class SyncActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_sync()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $request = m::spy('Payum\Core\Request\Sync');
        $gateway = m::spy('Payum\Core\GatewayInterface');
        $token = m::spy('Payum\Core\Model\TokenInterface');

        $details = new ArrayObject([
            'orderID' => '20140318154002',
            'snID' => '20140318154002',
            'order_status' => 'S01',
            'webPara' => '20140318154002-xxx',
        ]);

        $targetUrl = 'http://localhost/payment/capture/FEDHD1o-fvtpZqM6QvtNsy_qoLX_8x4QXvfyE94mIZc';

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

        $action = new SyncAction();
        $action->setGateway($gateway);
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $request->shouldHaveReceived('getModel')->twice();
        $request->shouldHaveReceived('getToken')->once();
        $token->shouldHaveReceived('getTargetUrl')->once();
        $gateway->shouldHaveReceived('execute')->with(m::type('PayumTW\EzShip\Request\Api\GetTransactionData'))->once();
    }
}
