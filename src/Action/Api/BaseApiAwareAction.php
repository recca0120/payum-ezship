<?php

namespace PayumTW\Ezship\Action\Api;

use PayumTW\Ezship\Api;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\UnsupportedApiException;

abstract class BaseApiAwareAction implements ActionInterface, ApiAwareInterface
{
    /**
     * @var \Payum\Collect\Api
     */
    protected $api;

    /**
     * {@inheritdoc}
     */
    public function setApi($api)
    {
        if (false === $api instanceof Api) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }
}
