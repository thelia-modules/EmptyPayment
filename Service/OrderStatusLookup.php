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

interface OrderStatusLookup
{
    /**
     * The id of the order status of that code, or null when the shop has none.
     */
    public function idOf(string $code): ?int;
}
