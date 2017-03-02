<?php

namespace PayumTW\Ezship\Action;

use Payum\Core\Request\Capture;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetHttpRequest;
use PayumTW\Ezship\Action\Api\BaseApiAwareAction;
use PayumTW\Ezship\Request\Api\CreateTransaction;
use Payum\Core\Exception\RequestNotSupportedException;

class CaptureAction extends BaseApiAwareAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $httpRequest = new GetHttpRequest();
        $this->gateway->execute($httpRequest);

        if (isset($httpRequest->request['order_status']) === true ||
            isset($httpRequest->request['processID']) === true // CVS
        ) {
            if ($this->api->verifyHash($httpRequest->request, (array) $details) === false) {
                $httpRequest->request['order_status'] = 'E99';
            }
            $details->replace($httpRequest->request);

            return;
        }

        $token = $request->getToken();
        $targetUrl = $token->getTargetUrl();

        if (empty($details['rtn_url']) === true) {
            $details['rtn_url'] = $targetUrl;
        }

        $this->gateway->execute(new CreateTransaction($details));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
