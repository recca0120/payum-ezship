<?php

namespace PayumTW\Ezship\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\Request\Api\GetTransactionData;
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

        $details->replace($this->api->getTransactionData((array) $details));
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
