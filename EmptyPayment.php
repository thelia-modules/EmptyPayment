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

namespace EmptyPayment;

use EmptyPayment\Service\OrderStatusAfterPayment;
use EmptyPayment\Service\PropelOrderStatusLookup;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Log\Tlog;
use Thelia\Model\Order;
use Thelia\Model\OrderStatus;
use Thelia\Module\AbstractPaymentModule;

/**
 * A payment that asks for nothing: the order is placed and moved to the status the
 * shop chose (paid by default), for a customer paying on account. Always valid: which
 * customer may see it is the business of a payment rule module, not of this one.
 */
class EmptyPayment extends AbstractPaymentModule
{
    public const DOMAIN_NAME = 'emptypayment';

    public const BACK_OFFICE_DOMAIN_NAME = 'emptypayment.bo.default-twig';

    public const CONFIG_ORDER_STATUS_CODE = 'order_status_code';

    public function isValidPayment(): bool
    {
        return true;
    }

    public function pay(Order $order): ?Response
    {
        (new OrderStatusAfterPayment(new PropelOrderStatusLookup(), $this->getDispatcher(), Tlog::getInstance()))
            ->apply($order, self::orderStatusCode());

        return null;
    }

    /**
     * The stock follows the status the order is moved to, as for any order whose
     * payment module leaves it alone at creation.
     */
    public function manageStockOnCreation(): bool
    {
        return false;
    }

    public static function orderStatusCode(): string
    {
        return self::getConfigValue(self::CONFIG_ORDER_STATUS_CODE) ?: OrderStatus::CODE_PAID;
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([
                __DIR__.'/I18n/*',
                __DIR__.'/Config/**/*.php',
                __DIR__.'/Tests/*',
                __DIR__.'/EmptyPayment.php',
            ])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
