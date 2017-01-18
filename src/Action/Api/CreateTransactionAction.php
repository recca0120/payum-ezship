<?php

namespace PayumTW\Ezship\Action\Api;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpPostRedirect;
use PayumTW\Ezship\Request\Api\CreateTransaction;
use Payum\Core\Exception\RequestNotSupportedException;

class CreateTransactionAction extends BaseApiAwareAction
{
    /**
     * {@inheritdoc}
     *
     * @param $request CreateTransaction
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (empty($details['order_amount']) === true) {
            throw new HttpPostRedirect(
                $this->api->getApiEndpoint('cvs'),
                $this->api->createCvsMapTransaction((array) $details)
            );
        }

        throw new HttpPostRedirect(
            $this->api->getApiEndpoint('capture'),
            $this->api->createTransaction((array) $details)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof CreateTransaction &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
