<?php

namespace PayumTW\Ezship;

use Payum\Core\GatewayFactory;
use PayumTW\Ezship\Action\SyncAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use PayumTW\Ezship\Action\StatusAction;
use PayumTW\Ezship\Action\CaptureAction;
use PayumTW\Ezship\Action\ConvertPaymentAction;
use PayumTW\Ezship\Action\Api\CreateTransactionAction;
use PayumTW\Ezship\Action\Api\GetTransactionDataAction;

class EzshipGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'ezship',
            'payum.factory_title' => 'Ezship',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.sync' => new SyncAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),

            'payum.action.api.create_transaction' => new CreateTransactionAction(),
            'payum.action.api.get_transaction_data' => new GetTransactionDataAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'su_id' => null,
                'method' => 'XML',
                'sandbox' => false,
            ];

            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['su_id'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
