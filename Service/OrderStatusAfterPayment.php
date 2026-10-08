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

use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\Order;

/**
 * Moves a placed order to the status the shop chose. A status deleted since it was
 * chosen leaves the order not paid, and says so in the log: the order exists, refusing
 * it here would not take it back.
 */
final readonly class OrderStatusAfterPayment
{
    public function __construct(
        private OrderStatusLookup $orderStatusLookup,
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface $logger,
    ) {
    }

    public function apply(Order $order, string $orderStatusCode): void
    {
        $orderStatusId = $this->orderStatusLookup->idOf($orderStatusCode);

        if (null === $orderStatusId) {
            $this->logger->error(\sprintf(
                'EmptyPayment: order %s left as it is, the order status "%s" set in the module configuration does not exist.',
                $order->getRef(),
                $orderStatusCode,
            ));

            return;
        }

        $this->dispatcher->dispatch((new OrderEvent($order))->setStatus($orderStatusId), TheliaEvents::ORDER_UPDATE_STATUS);
    }
}
