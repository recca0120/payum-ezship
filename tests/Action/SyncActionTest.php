<?php

namespace PayumTW\Ezship\Tests\Action;

use Mockery as m;
use Payum\Core\Request\Sync;
use PHPUnit\Framework\TestCase;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\Action\SyncAction;

class SyncActionTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testExecute()
    {
        $action = new SyncAction();
        $request = new Sync(new ArrayObject([]));

        $action->setGateway(
            $gateway = m::mock('Payum\Core\GatewayInterface')
        );

        $gateway->shouldReceive('execute')->once()->with('PayumTW\Ezship\Request\Api\getTransactionData');

        $action->execute($request);
    }
}
