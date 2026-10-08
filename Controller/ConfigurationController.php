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

namespace EmptyPayment\Controller;

use EmptyPayment\EmptyPayment;
use EmptyPayment\Form\ConfigurationForm;
use EmptyPayment\Service\OrderStatusSetting;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

class ConfigurationController extends BaseAdminController
{
    #[Route('/admin/module/empty-payment/configuration', name: 'empty_payment.admin.configuration', methods: ['POST'])]
    public function saveAction(OrderStatusSetting $orderStatusSetting): Response
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], [EmptyPayment::getModuleCode()], AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm(ConfigurationForm::getName());

        try {
            $orderStatusSetting->save((string) $this->validateForm($form)->getData()['order_status_code']);
        } catch (FormValidationException|\InvalidArgumentException $exception) {
            $this->addFlash('danger', $exception->getMessage());

            return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/'.EmptyPayment::getModuleCode()));
        }

        $this->addFlash('success', $this->getTranslator()->trans('Settings saved.', [], EmptyPayment::DOMAIN_NAME));

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/'.EmptyPayment::getModuleCode()));
    }
}
