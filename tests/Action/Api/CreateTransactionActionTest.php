<?php

namespace PayumTW\Ezship\Tests\Action\Api;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpPostRedirect;
use PayumTW\Ezship\Request\Api\CreateTransaction;
use PayumTW\Ezship\Action\Api\CreateTransactionAction;

class CreateTransactionActionTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testExecute()
    {
        $action = new CreateTransactionAction();
        $request = new CreateTransaction(new ArrayObject([
            'order_amount' => 100,
        ]));

        $action->setApi(
            $api = m::mock('PayumTW\Ezship\Api')
        );

        $api->shouldReceive('getApiEndpoint')->once('capture')->andReturn($apiEndpoint = 'foo');
        $api->shouldReceive('createTransaction')->once()->with((array) $request->getModel())->andReturn($params = ['foo' => 'bar']);

        try {
            $action->execute($request);
        } catch (HttpPostRedirect $e) {
            $this->assertSame($apiEndpoint, $e->getUrl());
            $this->assertSame($params, $e->getFields());
        }
    }

    public function testExecuteCVS()
    {
        $action = new CreateTransactionAction();
        $request = new CreateTransaction(new ArrayObject([
            'order_amount' => 0,
        ]));

        $action->setApi(
            $api = m::mock('PayumTW\Ezship\Api')
        );

        $api->shouldReceive('getApiEndpoint')->once('cvs')->andReturn($apiEndpoint = 'foo');
        $api->shouldReceive('createCvsMapTransaction')->once()->with((array) $request->getModel())->andReturn($params = ['foo' => 'bar']);

        try {
            $action->execute($request);
        } catch (HttpPostRedirect $e) {
            $this->assertSame($apiEndpoint, $e->getUrl());
            $this->assertSame($params, $e->getFields());
        }
    }
}
