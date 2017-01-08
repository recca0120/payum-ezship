<?php

namespace PayumTW\EzShip;

use Http\Message\MessageFactory;
use Payum\Core\HttpClientInterface;
use Payum\Core\Exception\Http\HttpException;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    protected function doRequest($method, $body, $type = 'cancel', $isJson = true)
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        if (is_array($body) === true) {
            $body = http_build_query($body);
        }

        $request = $this->messageFactory->createRequest($method, $this->getApiEndpoint($type), $headers, $body);

        $response = $this->client->send($request);

        if (false == ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
            throw HttpException::factory($request, $response);
        }

        $contents = $response->getBody()->getContents();

        return $isJson === true ? json_decode($contents, true) : $contents;
    }

    /**
     * @return string
     */
    public function getApiEndpoint($type = 'capture')
    {
        $endpoints = [
            'cvs' => ' http://map.ezship.com.tw/ezship_map_web.jsp',
            'capture' => 'https://www.ezship.com.tw/emap/ezship_xml_order_api.jsp',
            'query' => 'http://www.ezship.com.tw/emap/ezship_request_order_status_api.jsp',
        ];

        return $endpoints[$type];
    }

    public function createCvsTransaction(array $params)
    {
        $supportedParams = [
            // varchar 100 賣家登入ezShip帳號 需開通網站對接者
            'suID' => $this->options['suID'],
            // varchar 10 處理序號或訂單編號 由開通網站自行提供的編號(KEY值)。(非 ezShip 提供)
            'processID' => null,
            // varchar 3 取件門市通路代號
            'stCate' => null,
            // varchar 6 取件門市代號
            'stCode' => null,
            // varchar 100 回傳網址路徑及程式名稱 請輸入完整網站路徑網址。如 http://yourdomain.domain/direct/program.php
            'rtURL' => null,
            // varchar 100 網站所需額外判別資料 由開通網站自行提供，ezShip 將原值回傳
            'webPara' => null,
        ];

        $params = array_filter(array_replace(
            $supportedParams,
            array_intersect_key($params, $supportedParams)
        ));

        return $params;
    }

    /**
     * createTransaction.
     *
     * @param array $params
     *
     * @return array
     */
    public function createTransaction(array $params)
    {
        if (empty($params['orderAmount']) === true) {
            return $this->createCvsTransaction($params);
        }

        $orderStatus = empty($params['stCode']) === false ? 'A01' : 'A05';
        $details = '';
        if (isset($params['details']) === true) {
            $details = implode('', array_map(function ($detail) {
                return $this->createXml(['Detail' => $detail], ['prodName', 'prodSpec']);
            }, $params['details']));
        }

        $supportedParams = [
            // varchar 100 賣家登入ezShip帳號 需開通網站對接者，取貨付款訂單須帳號於合約期間內
            'suID' => $this->options['suID'],
            // varchar 10 訂單編號
            'orderID' => null,
            // varchar 3 訂單狀態
            // A01 超商取貨新訂單，不需在ezShip上確認訂單，可直接印單 (回覆snID)
            // A02 超商取貨新訂單，需在ezShip上確認訂單，確認後才可進行印單 (預設值, 回覆snID)
            // A03 超商取貨新訂單，使用 輕鬆袋或迷你袋 (不回覆snID，不需在ezShip上確認訂單，需登錄編號)
            // A04 超商取貨新訂單，使用 輕鬆袋或迷你袋 (不回覆snID，需在ezShip上確認訂單，需登錄編號)
            // A05 宅配新訂單，不需在ezShip上確認訂單，可直接印單 (回覆snID，10碼數字)
            // A06 宅配新訂單，需在ezShip上確認訂單，確認後才可進行印單 (回覆snID，10碼數字)
            'orderStatus' => $orderStatus,
            // varchar 1 訂單類別 1 取貨付款 3 取貨不付款
            'orderType' => '1',
            // 代收金額或訂單金額
            // 若<orderType>=1，為代收金額 10~6,000
            // 若<orderType>=3，為訂單金額 0~2,000
            'orderAmount' => null,
            // varchar 60 取件人姓名
            // 若<orderType>=1，建議為取件人身分證件上之真實姓名
            // 若<orderType>=3，須為取件人身分證件上之真實姓名
            'rvName' => null,
            // varchar 100 取件人電子郵件 發送取件通知信函
            'rvEmail' => null,
            // varchar 10 取件人行動電話 發送取件通知簡訊
            'rvMobile' => null,
            // varchar 9 取件門市
            // 通路別+門市代號 電子地圖回傳之 <stCate><stCode> orderStatus 為 A01、A02、A03、A04 時必須 ( 店到店資料 )
            'stCode' => null,
            // varchar 120 取件人收件地址 orderStatus 為 A05、A06 時必須 ( 宅配資料 )
            'rvAddr' => null,
            // varchar 10 取件人郵遞區號 orderStatus 為 A05、A06 時必須 ( 宅配資料 )
            'rvZip' => null,
            // varchar 100 回傳網址路徑及程式名稱 請輸入完整網站路徑網址。如 http://yourdomain.domain/direct/program.php
            'rtURL' => null,
            // varchar 100 網站所需額外判別資料 ezShip 將原值回傳，供網站判別用
            'webPara' => null,
        ];

        $params = array_filter(array_replace(
            $supportedParams,
            array_intersect_key($params, $supportedParams)
        ));

        return [
            'web_map_xml' => '<ORDER>'.$this->createXml($params, ['rvName', 'rvAddr']).$details.'</ORDER>',
        ];
    }

    /**
     * getTransactionData.
     *
     * @param mixed $params
     *
     * @return array
     */
    public function getTransactionData(array $params)
    {
        if (empty($params['response']) === false) {
            return $params['response'];
        }

        $supportedParams = [
            // varchar 100 賣家登入ezShip帳號 需開通網站對接者
            'su_id' => $this->options['suID'],
            // varchar 10 ezShip店到店編號 訂單成立後，ezShip回傳給網站的店到店編號
            'sn_id' => null,
            //  varchar 100 回傳網址路徑及程式名稱 請輸入完整網站路徑網址。如 http://yourdomain.domain/direct/program.php
            'rtn_url' => null,
            // varchar 100 網站所需額外判別資料 由開通網站自行提供，ezShip 將原值回傳。
            'web_para' => null,
            // // varchar 10 店到店編號 訂單成立後，ezShip回傳給網站的店到店編號
            // 'sn_id' => null,
            // // varchar 3 訂單狀態
            // // S01 尚未寄件或尚未收到超商總公司提供的寄件訊息
            // // S02 運往取件門市途中
            // // S03 已送達取件門市
            // // S04 已完成取貨
            // // S05 退貨 (包含：已退回物流中心 / 再寄一次給取件人 / 退回給寄件人)
            // // S06 配送異常 (包含：刪單 / 門市閉店 / 貨故)
            // // E00 參數傳遞內容有誤或欄位短缺
            // // E01 <su_id>帳號不存在
            // // E02 <su_id>帳號無網站串接權限
            // // E03 <sn_id>店到店編號有誤
            // // E04 <su_id>帳號與<sn_id>店到店編號無法對應
            // // E99 系統錯誤
            // 'order_status' => null,
            // // varchar 100 網站所需額外判別資料 ezShip 將原值回傳，供網站判別用
            // 'web_para' => null
        ];

        $params = array_filter(array_replace(
            $supportedParams,
            array_intersect_key($params, $supportedParams)
        ));

        return $this->doRequest('POST', $params, 'query', false);
    }

    protected function createXml($array, $cdataSections = [])
    {
        $xml = '';
        foreach ($array as $key => $value) {
            if (is_array($value) === true) {
                $xml .= sprintf('<%s>%s</%s>', $key, $this->createXml($value, $cdataSections), $key);
            } else {
                if (in_array($key, $cdataSections, true) === true || (bool) preg_match('/[<>&]/', $value) === true) {
                    $value = '<![CDATA['.$value.']]>';
                }
                $xml .= sprintf('<%s>%s</%s>', $key, $value, $key);
            }
        }

        return $xml;
    }
}
