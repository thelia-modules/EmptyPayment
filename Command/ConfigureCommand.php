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

namespace EmptyPayment\Command;

use EmptyPayment\Service\OrderStatusSetting;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The setting of the configuration screen, for a deployment that has to set it without
 * clicking: `empty-payment:configure --status=order_form`. Without an option, prints it.
 */
#[AsCommand(name: 'empty-payment:configure', description: 'Show or set the order status an order paid with EmptyPayment is moved to')]
final class ConfigureCommand extends Command
{
    public function __construct(
        private readonly OrderStatusSetting $orderStatusSetting,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('status', null, InputOption::VALUE_REQUIRED, 'Code of an existing order status (default: paid)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orderStatusCode = $input->getOption('status');

        if (null !== $orderStatusCode) {
            try {
                $this->orderStatusSetting->save((string) $orderStatusCode);
            } catch (\InvalidArgumentException $exception) {
                $output->writeln(\sprintf('<error>%s</error>', $exception->getMessage()));

                return Command::INVALID;
            }
        }

        $output->writeln(\sprintf('Order status once the order is placed: %s.', $this->orderStatusSetting->read()));

        return Command::SUCCESS;
    }
}
