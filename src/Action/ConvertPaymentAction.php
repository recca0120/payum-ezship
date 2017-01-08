<?php

namespace PayumTW\EzShip\Action;

use Payum\Core\Request\Convert;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Exception\RequestNotSupportedException;

class ConvertPaymentAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $details['orderID'] = $payment->getNumber();
        $details['orderAmount'] = $payment->getTotalAmount();
        $details['rvEmail'] = $payment->getClientEmail();

        // CVS
        $details['processID'] = $details['orderID'];

        $request->setResult((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() == 'array';
    }
}
