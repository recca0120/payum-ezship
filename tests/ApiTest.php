<?php

namespace PayumTW\Ezship\Tests\Action;

use Mockery as m;
use PayumTW\Ezship\Api;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testCreateCvsMapTransaction()
    {
        $api = new Api(
            $options = [
                'su_id' => 'service@ezship.com.tw',
                'method' => 'XML',
            ],
            $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            $messageFactory = m::mock('Http\Message\MessageFactory')
        );

        $params = [
            'order_amount' => '',
            'process_id' => '20140318154002',
            'st_cate' => 'A01',
            'st_code' => '1',
            'rtn_url' => 'http://yourdomain.domain/direct/program.php',
            'web_para' => '20140318154002-xxx',
        ];

        $this->assertSame([
            'suID' => $options['su_id'],
            'processID' => $params['process_id'],
            'stCate' => $params['st_cate'],
            'stCode' => $params['st_code'],
            'rtURL' => $params['rtn_url'],
            'webPara' => $params['web_para'],
        ], $api->createCvsMapTransaction($params));
    }

    public function testCreateTransactionXML()
    {
        $api = new Api(
            $options = [
                'su_id' => 'service@ezship.com.tw',
                'method' => 'XML',
            ],
            $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            $messageFactory = m::mock('Http\Message\MessageFactory')
        );

        $params = [
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
        ];

        $this->assertSame([
            'web_map_xml' => preg_replace('/[\n\s]+/', '',
                '<ORDER>
                  <suID>service@ezship.com.tw</suID>
                  <orderID>20140318154002</orderID>
                  <orderStatus>A01</orderStatus>
                  <orderType>1</orderType>
                  <orderAmount>1680</orderAmount>
                  <rvName><![CDATA[謝無忌]]></rvName>
                  <rvEmail>123@ezship.com.tw</rvEmail>
                  <rvMobile>0987654321</rvMobile>
                  <stCode>TFM0038</stCode>
                  <rtURL>http://yourdomain.domain/direct/program.php</rtURL>
                  <webPara>20140318154002-xxx</webPara>
                  <Detail>
                    <prodItem>1</prodItem>
                    <prodNo>A2769-1</prodNo>
                    <prodName><![CDATA[格子口袋襯衫]]></prodName>
                    <prodPrice>860</prodPrice>
                    <prodQty>1</prodQty>
                    <prodSpec><![CDATA[白]]></prodSpec>
                  </Detail>
                  <Detail>
                    <prodItem>2</prodItem>
                    <prodNo>A2770-2</prodNo>
                    <prodName><![CDATA[格子口袋襯衫]]></prodName>
                    <prodPrice>820</prodPrice>
                    <prodQty>1</prodQty>
                    <prodSpec><![CDATA[水藍]]></prodSpec>
                  </Detail>
            </ORDER>'),
        ], $api->createTransaction($params));
    }

    public function testCreateTransactionHomeXML()
    {
        $api = new Api(
            $options = [
                'su_id' => 'service@ezship.com.tw',
                'method' => 'XML',
            ],
            $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            $messageFactory = m::mock('Http\Message\MessageFactory')
        );

        $params = [
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
        ];

        $this->assertSame([
            'web_map_xml' => preg_replace('/[\n\s]+/', '',
                '<ORDER>
                   <suID>service@ezship.com.tw</suID>
                   <orderID>20140318154002</orderID>
                   <orderStatus>A05</orderStatus>
                   <orderType>1</orderType>
                   <orderAmount>1680</orderAmount>
                   <rvName><![CDATA[謝無忌]]></rvName>
                   <rvEmail>123@ezship.com.tw</rvEmail>
                   <rvMobile>0987654321</rvMobile>
                   <rvAddr><![CDATA[台北市大安區xx路xx段xx號]]></rvAddr>
                   <rvZip>106</rvZip>
                   <rtURL>http://yourdomain.domain/direct/program.php</rtURL>
                   <webPara>20140318154002-xxx</webPara>
                   <Detail>
                      <prodItem>1</prodItem>
                      <prodNo>A2769-1</prodNo>
                      <prodName><![CDATA[格子口袋襯衫]]></prodName>
                      <prodPrice>860</prodPrice>
                      <prodQty>1</prodQty>
                      <prodSpec><![CDATA[白]]></prodSpec>
                   </Detail>
                   <Detail>
                      <prodItem>2</prodItem>
                      <prodNo>A2770-2</prodNo>
                      <prodName><![CDATA[格子口袋襯衫]]></prodName>
                      <prodPrice>820</prodPrice>
                      <prodQty>1</prodQty>
                      <prodSpec><![CDATA[水藍]]></prodSpec>
                   </Detail>
                </ORDER>'),
        ], $api->createTransaction($params));
    }

    public function testCreateTransactionRequest()
    {
        $api = new Api(
            $options = [
                'su_id' => 'service@ezship.com.tw',
                'method' => 'HttpRequest',
            ],
            $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            $messageFactory = m::mock('Http\Message\MessageFactory')
        );

        $params = [
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
        ];

        $this->assertSame([
            'su_id' => 'service@ezship.com.tw',
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
        ], $api->createTransaction($params));
    }

    public function testGetTransactionData()
    {
        $api = new Api(
            $options = [
                'su_id' => 'service@ezship.com.tw',
                'method' => 'HttpRequest',
            ],
            $httpClient = m::mock('Payum\Core\HttpClientInterface'),
            $messageFactory = m::mock('Http\Message\MessageFactory')
        );

        $params = [
            'sn_id' => uniqid(),
            'rtn_url' => 'http://yourdomain.domain/direct/program.php',
            'web_para' => '20140318154002-xxx',
        ];

        $this->assertSame(array_merge([
            'su_id' => $options['su_id'],
        ], $params), $api->getTransactionData($params));
    }
}
