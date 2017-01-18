<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\Action\Api\CreateTransactionAction;

class CreateTransactionActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException   \Payum\Core\Reply\HttpPostRedirect
     */
    public function test_execute()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $request = m::spy('PayumTW\Ezship\Request\Api\CreateTransaction, ArrayAccess');
        $api = m::spy('PayumTW\Ezship\Api');
        $details = new ArrayObject([
            'su_id' => 'service@ezship.com.tw',
            'method' => 'HttpRequest',
            'order_id' => '20140318154002',
            'order_status' => 'A05',
            'order_type' => '1',
            'order_amount' => '1680',
            'rv_name' => '謝無忌',
            'rv_email' => '123@ezship.com.tw',
            'rv_mobile' => '0987654321',
            'rv_addr' => '台北市大安區xx路xx段xx號',
            'rv_zip' => '106',
            'rtn_url' => 'http://yourdomain.domain/direct/program.php',
            'web_para' => '20140318154002-xxx',
        ]);

        $endpoint = 'foo.endpoint';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $api
            ->shouldReceive('getApiEndpoint')->with('capture')->andReturn($endpoint)
            ->shouldReceive('createTransaction')->with($details->toUnsafeArray())->andReturn($details->toUnsafeArray());

        $action = new CreateTransactionAction();
        $action->setApi($api);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $action->execute($request);
        $request->shouldHaveReceived('getModel')->twice();
        $api->shouldHaveReceived('getApiEndpoint')->with('capture')->once();
        $api->shouldHaveReceived('createTransaction')->with($details->toUnsafeArray())->once();
    }

    /**
     * @expectedException   \Payum\Core\Reply\HttpPostRedirect
     */
    public function test_cvs_map_execute()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $request = m::spy('PayumTW\Ezship\Request\Api\CreateTransaction, ArrayAccess');
        $api = m::spy('PayumTW\Ezship\Api');
        $details = new ArrayObject([
            'su_id' => 'service@ezship.com.tw',
            'method' => 'XML',
            'order_amount' => '',
            'process_id' => '20140318154002',
            'st_cate' => 'A01',
            'st_code' => '1',
            'rtn_url' => 'http://yourdomain.domain/direct/program.php',
            'web_para' => '20140318154002-xxx',
        ]);

        $endpoint = 'foo.endpoint';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('getModel')->andReturn($details);

        $api
            ->shouldReceive('getApiEndpoint')->with('cvs')->andReturn($endpoint)
            ->shouldReceive('createCvsMapTransaction')->with($details->toUnsafeArray())->andReturn($details->toUnsafeArray());

        $action = new CreateTransactionAction();
        $action->setApi($api);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $action->execute($request);
        $request->shouldHaveReceived('getModel')->twice();
        $api->shouldHaveReceived('getApiEndpoint')->with('capture')->once();
        $api->shouldHaveReceived('createCvsMapTransaction')->with($details->toUnsafeArray())->once();
    }
}
