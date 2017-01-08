<?php

namespace PayumTW\EzShip\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Exception\RequestNotSupportedException;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (isset($details['processID']) === true && isset($details['stName']) === true) {
            $request->markCaptured();

            return;
        }

        if (isset($details['order_id']) === true && isset($details['sn_id']) === true) {
            if ($details['order_status'] === 'S01') {
                $request->markCaptured();

                return;
            }

            $request->markFailed();

            return;
        }

        if (isset($details['sn_id']) === true && isset($details['order_status']) === true) {
            $status = [
                // 尚未寄件或尚未收到超商總公司提供的寄件訊息
                'S01' => 'markUnknown',
                // 運往取件門市途中
                'S02' => 'markUnknown',
                // 已送達取件門市
                'S03' => 'markUnknown',
                // 已完成取貨
                'S04' => 'markUnknown',
                // 退貨 (包含：已退回物流中心 / 再寄一次給取件人 / 退回給寄件人)
                'S05' => 'markUnknown',
                // 配送異常 (包含：刪單 / 門市閉店 / 貨故)
                'S06' => 'markUnknown',
                // 參數傳遞內容有誤或欄位短缺
                'E00' => 'markFailed',
                // <su_id>帳號不存在
                'E01' => 'markFailed',
                // <su_id>帳號無網站串接權限
                'E02' => 'markFailed',
                // <sn_id>店到店編號有誤
                'E03' => 'markFailed',
                // <su_id>帳號與<sn_id>店到店編號無法對應
                'E04' => 'markFailed',
                // 系統錯誤
                'E99' => 'markFailed',
            ];

            call_user_func([$request, $status[$details['order_status']]]);

            return;
        }

        $request->markNew();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
