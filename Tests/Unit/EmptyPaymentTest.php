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

namespace EmptyPayment\Tests\Unit;

use EmptyPayment\Service\OrderStatusAfterPayment;
use EmptyPayment\Service\OrderStatusLookup;
use EmptyPayment\Service\OrderStatusSetting;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\Order;

final class EmptyPaymentTest extends TestCase
{
    #[Test]
    public function theOrderIsMovedToTheConfiguredStatus(): void
    {
        $dispatcher = new EventDispatcher();
        $statuses = [];
        $dispatcher->addListener(TheliaEvents::ORDER_UPDATE_STATUS, static function (OrderEvent $event) use (&$statuses): void {
            $statuses[] = $event->getStatus();
        });

        (new OrderStatusAfterPayment($this->lookup(['paid' => 2, 'order_form' => 7]), $dispatcher, new NullLogger()))
            ->apply(new Order(), 'order_form');

        self::assertSame([7], $statuses);
    }

    #[Test]
    public function aStatusDeletedSinceLeavesTheOrderAlone(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(TheliaEvents::ORDER_UPDATE_STATUS, static fn () => self::fail('No status to move the order to.'));

        (new OrderStatusAfterPayment($this->lookup(['paid' => 2]), $dispatcher, new NullLogger()))
            ->apply(new Order(), 'order_form');

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function aStatusTheShopDoesNotHaveIsRefusedAtTheSetting(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The order status "order_form" does not exist.');

        (new OrderStatusSetting($this->lookup(['paid' => 2])))->save('order_form');
    }

    /**
     * @param array<string, int> $idsByCode
     */
    private function lookup(array $idsByCode): OrderStatusLookup
    {
        return new readonly class($idsByCode) implements OrderStatusLookup {
            /**
             * @param array<string, int> $idsByCode
             */
            public function __construct(private array $idsByCode)
            {
            }

            public function idOf(string $code): ?int
            {
                return $this->idsByCode[$code] ?? null;
            }
        };
    }
}
