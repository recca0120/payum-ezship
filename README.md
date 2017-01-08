# ezShip

[![StyleCI](https://styleci.io/repos/78288815/shield?style=flat)](https://styleci.io/repos/78288815)
[![Build Status](https://travis-ci.org/recca0120/payum-ezship.svg)](https://travis-ci.org/recca0120/payum-ezship)
[![Total Downloads](https://poser.pugx.org/payum-tw/ezship/d/total.svg)](https://packagist.org/packages/payum-tw/ezship)
[![Latest Stable Version](https://poser.pugx.org/payum-tw/ezship/v/stable.svg)](https://packagist.org/packages/payum-tw/ezship)
[![Latest Unstable Version](https://poser.pugx.org/payum-tw/ezship/v/unstable.svg)](https://packagist.org/packages/payum-tw/ezship)
[![License](https://poser.pugx.org/payum-tw/ezship/license.svg)](https://packagist.org/packages/payum-tw/ezship)
[![Monthly Downloads](https://poser.pugx.org/payum-tw/ezship/d/monthly)](https://packagist.org/packages/payum-tw/ezship)
[![Daily Downloads](https://poser.pugx.org/payum-tw/ezship/d/daily)](https://packagist.org/packages/payum-tw/ezship)

The Payum extension to rapidly build new extensions.

1. Create new project

```bash
$ composer create-project payum-tw/ezship
```

2. Replace all occurrences of `payum` with your vendor name. It may be your github name, for now let's say you choose: `acme`.
3. Replace all occurrences of `ezship` with a payment gateway name. For example Stripe, Paypal etc. For now let's say you choose: `ezship`.
4. Register a gateway factory to the payum's builder and create a gateway:

```php
<?php

use Payum\Core\PayumBuilder;
use Payum\Core\GatewayFactoryInterface;

$defaultConfig = [];

$payum = (new PayumBuilder)
    ->addGatewayFactory('ezship', function(array $config, GatewayFactoryInterface $coreGatewayFactory) {
        return new \PayumTW\Collect\CollectGatewayFactory($config, $coreGatewayFactory);
    })

    ->addGateway('ezship', [
        'factory' => 'ezship',
        'suID' => null,
    ])

    ->getPayum()
;
```

5. While using the gateway implement all method where you get `Not implemented` exception:

```php
<?php

use Payum\Core\Request\Capture;

$ezship = $payum->getGateway('ezship');

$model = new \ArrayObject([
  // ...
]);

$ezship->execute(new Capture($model));
```

## Resources

* [Documentation](https://github.com/Payum/Payum/blob/master/src/Payum/Core/Resources/docs/index.md)
* [Questions](http://stackoverflow.com/questions/tagged/payum)
* [Issue Tracker](https://github.com/Payum/Payum/issues)
* [Twitter](https://twitter.com/payumphp)

## License

Skeleton is released under the [MIT License](LICENSE).
