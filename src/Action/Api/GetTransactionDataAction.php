<?php

namespace PayumTW\EzShip\Action\Api;

use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\EzShip\Request\Api\GetTransactionData;
use Payum\Core\Exception\RequestNotSupportedException;

class GetTransactionDataAction extends BaseApiAwareAction
{
    /**
     * {@inheritdoc}
     *
     * @param $request GetTransactionData
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $result = $this->api->getTransactionData((array) $details);

        if (isset($result['order_status']) === false) {
            throw new HttpPostRedirect(
                $this->api->getApiEndpoint('query'),
                $result
            );
        }

        $details->replace($result);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetTransactionData &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
