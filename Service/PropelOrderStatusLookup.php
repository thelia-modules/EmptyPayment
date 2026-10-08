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

use Thelia\Model\OrderStatusQuery;

final readonly class PropelOrderStatusLookup implements OrderStatusLookup
{
    public function idOf(string $code): ?int
    {
        return OrderStatusQuery::create()->findOneByCode($code)?->getId();
    }
}
