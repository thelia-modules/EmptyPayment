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

namespace EmptyPayment\Form;

use EmptyPayment\EmptyPayment;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\OrderStatusQuery;

class ConfigurationForm extends BaseForm
{
    public static function getName(): string
    {
        return 'empty_payment_configuration';
    }

    protected function buildForm(): void
    {
        $translator = Translator::getInstance();
        $choices = [];

        foreach (OrderStatusQuery::create()->orderByPosition()->find() as $orderStatus) {
            $orderStatus->setLocale($translator->getLocale());
            $choices[\sprintf('%s (%s)', $orderStatus->getTitle(), $orderStatus->getCode())] = $orderStatus->getCode();
        }

        $this->formBuilder->add('order_status_code', ChoiceType::class, [
            'data' => EmptyPayment::orderStatusCode(),
            'label' => $translator->trans('Order status once the order is placed', [], EmptyPayment::DOMAIN_NAME),
            'choices' => $choices,
            'constraints' => [new NotBlank()],
        ]);
    }
}
