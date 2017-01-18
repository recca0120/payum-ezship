<?php

use Mockery as m;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\Action\StatusAction;

class StatusActionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_mark_new()
    {
        $this->validate([
            'order_id' => '20140318154002',
            'order_status' => 'A01',
            'order_type' => '1',
            'order_amount' => '1680',
            'rv_name' => '謝無忌',
            'rv_email' => '123@ezship.com.tw',
            'rv_mobile' => '0987654321',
            'st_code' => 'TFM0038',
            'rtn_url' => 'http://yourdomain.domain/direct/program.php',
            'web_para' => '20140318154002-xxx',
            'details' => [
                [
                    'prod_item' => '1',
                    'prod_no' => 'A2769-1',
                    'prod_name' => '格子口袋襯衫',
                    'prod_price' => '860',
                    'prod_qty' => '1',
                    'prod_spec' => '白',
                ],
                [
                    'prod_item' => '2',
                    'prod_no' => 'A2770-2',
                    'prod_name' => '格子口袋襯衫',
                    'prod_price' => '820',
                    'prod_qty' => '1',
                    'prod_spec' => '水藍',
                ],
            ],
        ], 'markNew');
    }

    public function test_mark_captured()
    {
        $this->validate([
            'order_type' => '1',
            'order_amount' => '1680',
            'rv_name' => '謝無忌',
            'rv_email' => '123@ezship.com.tw',
            'rv_mobile' => '0987654321',
            'st_code' => 'TFM0038',
            'rtn_url' => 'http://yourdomain.domain/direct/program.php',
            'web_para' => '20140318154002-xxx',
            'details' => [
                [
                    'prod_item' => '1',
                    'prod_no' => 'A2769-1',
                    'prod_name' => '格子口袋襯衫',
                    'prod_price' => '860',
                    'prod_qty' => '1',
                    'prod_spec' => '白',
                ],
                [
                    'prod_item' => '2',
                    'prod_no' => 'A2770-2',
                    'prod_name' => '格子口袋襯衫',
                    'prod_price' => '820',
                    'prod_qty' => '1',
                    'prod_spec' => '水藍',
                ],
            ],
            // response
            'order_id' => '20140318154002',
            'sn_id' => '20140318154002',
            'order_status' => 'S01',
            'webPara' => '20140318154002-xxx',
        ], 'markCaptured');
    }

    public function test_mark_failed()
    {
        $this->validate([
            'order_type' => '1',
            'order_amount' => '1680',
            'rv_name' => '謝無忌',
            'rv_email' => '123@ezship.com.tw',
            'rv_mobile' => '0987654321',
            'st_code' => 'TFM0038',
            'rtn_url' => 'http://yourdomain.domain/direct/program.php',
            'web_para' => '20140318154002-xxx',
            'details' => [
                [
                    'prod_item' => '1',
                    'prod_no' => 'A2769-1',
                    'prod_name' => '格子口袋襯衫',
                    'prod_price' => '860',
                    'prod_qty' => '1',
                    'prod_spec' => '白',
                ],
                [
                    'prod_item' => '2',
                    'prod_no' => 'A2770-2',
                    'prod_name' => '格子口袋襯衫',
                    'prod_price' => '820',
                    'prod_qty' => '1',
                    'prod_spec' => '水藍',
                ],
            ],
            // response
            'order_id' => '20140318154002',
            'sn_id' => '20140318154002',
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
            'suID' => '20140318154002',
            'processID' => '20140318154002',
            'stCate' => 'TFM',
            'stCode' => '0038',
            'rtURL' => 'http://yourdomain.domain/direct/program.php',
            'webPara' => '20140318154002-xxx',
            // response
            'stCate' => 'TFM',
            'stCode' => '0038',
            'stName' => '門市名稱',
            'stAddr' => '門市地址',
            'stTel' => '門市電話',
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
