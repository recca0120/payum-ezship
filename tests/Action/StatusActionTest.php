<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\EzShip\Action\StatusAction;

class StatusActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_mark_new()
    {
        $this->validate([
            'orderID' => '20140318154002',
            'orderStatus' => 'A01',
            'orderType' => '1',
            'orderAmount' => '1680',
            'rvName' => '謝無忌',
            'rvEmail' => '123@ezship.com.tw',
            'rvMobile' => '0987654321',
            'stCode' => 'TFM0038',
            'rtURL' => 'http://yourdomain.domain/direct/program.php',
            'webPara' => '20140318154002-xxx',
            'details' => [
                [
                    'prodItem' => '1',
                    'prodNo' => 'A2769-1',
                    'prodName' => '格子口袋襯衫',
                    'prodPrice' => '860',
                    'prodQty' => '1',
                    'prodSpec' => '白',
                ],
                [
                    'prodItem' => '2',
                    'prodNo' => 'A2770-2',
                    'prodName' => '格子口袋襯衫',
                    'prodPrice' => '820',
                    'prodQty' => '1',
                    'prodSpec' => '水藍',
                ],
            ],
        ], 'markNew');
    }

    public function test_mark_captured()
    {
        $this->validate([
            'orderID' => '20140318154002',
            'snID' => '20140318154002',
            'order_status' => 'S01',
            'webPara' => '20140318154002-xxx',
        ], 'markCaptured');
    }

    public function test_mark_failed()
    {
        $this->validate([
            'orderID' => '20140318154002',
            'snID' => '20140318154002',
            'order_status' => 'E00',
            'webPara' => '20140318154002-xxx',
        ], 'markFailed');
    }

    public function test_cvs_mark_new()
    {
        $this->validate([
            'suID' => '20140318154002',
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'rtURL' => 'http://yourdomain.domain/direct/program.php',
            'webPara' => '20140318154002-xxx',
        ], 'markNew');
    }

    public function test_cvs_mark_captured()
    {
        $this->validate([
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'stName' => '門市名稱',
            'stAddr' => '門市地址',
            'stTel' => '門市電話',
            'webPara' => '20140318154002-xxx',
        ], 'markCaptured');
    }

    public function test_get_transaction_data_mark_new()
    {
        $this->validate([
            'su_id' => '20140318154002',
            'sn_id' => '20140318154002',
            'rtn_url' => 'http://yourdomain.domain/direct/program.php',
            'web_para' => '20140318154002-xxx',
        ], 'markNew');
    }

    public function test_get_transaction_data_mark_unknown()
    {
        foreach (['S01', 'S02', 'S03', 'S04', 'S05', 'S06'] as $status) {
            $this->validate([
                'sn_id' => '20140318154002',
                'order_status' => $status,
                'web_para' => '20140318154002-xxx',
            ], 'markUnknown');
        }
    }

    public function test_get_transaction_data_mark_failed()
    {
        foreach (['E00', 'E01', 'E02', 'E03', 'E04', 'E99'] as $status) {
            $this->validate([
                'sn_id' => '20140318154002',
                'order_status' => $status,
                'web_para' => '20140318154002-xxx',
            ], 'markFailed');
        }
    }

    protected function validate($input, $type)
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $request = m::spy('Payum\Core\Request\GetStatusInterface');
        $details = new ArrayObject($input);

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request->shouldReceive('getModel')->andReturn($details);

        $action = new StatusAction();
        $action->execute($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $request->shouldHaveReceived('getModel')->twice();
        $request->shouldHaveReceived($type)->once();
    }
}
