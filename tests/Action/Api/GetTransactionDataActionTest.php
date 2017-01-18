<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\Action\Api\GetTransactionDataAction;

class GetTransactionDataActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_get_transaction_data()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $api = m::spy('PayumTW\Ezship\Api');
        $request = m::spy('PayumTW\Ezship\Request\Api\GetTransactionData');
        $details = m::mock(new ArrayObject([]));

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $api
            ->shouldReceive('getTransactionData')->andReturn($details->toUnsafeArray());

        $action = new GetTransactionDataAction();
        $action->setApi($api);
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $request->shouldHaveReceived('getModel')->twice();
        $api->shouldHaveReceived('getTransactionData')->with($details->toUnsafeArray())->once();
        $details->shouldHaveReceived('replace')->with($details->toUnsafeArray())->once();
    }
}
