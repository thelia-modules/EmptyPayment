<?php

declare(strict_types=1);

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EmptyPayment\Service;

use EmptyPayment\EmptyPayment;

/**
 * The status an order is moved to once placed. Only a status the shop has may be
 * chosen: a code typed for another shop would leave every order not paid.
 */
final readonly class OrderStatusSetting
{
    public function __construct(
        private OrderStatusLookup $orderStatusLookup,
    ) {
    }

    public function read(): string
    {
        return EmptyPayment::orderStatusCode();
    }

    /**
     * @throws \InvalidArgumentException when the shop has no order status of that code
     */
    public function save(string $orderStatusCode): void
    {
        if (null === $this->orderStatusLookup->idOf($orderStatusCode)) {
            throw new \InvalidArgumentException(\sprintf('The order status "%s" does not exist.', $orderStatusCode));
        }

        EmptyPayment::setConfigValue(EmptyPayment::CONFIG_ORDER_STATUS_CODE, $orderStatusCode);
    }
}
