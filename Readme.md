# Empty payment

A payment module that asks for no payment: the order is placed and moved to the order
status the shop chose, "paid" by default. Meant for customers paying on account
(bank transfer, bill of exchange) once the order is placed. Thelia 3.

## Installation

```
composer require thelia/empty-payment-module:^2.0
php bin/console module:refresh
php bin/console module:activate EmptyPayment
```

Version 2 needs Thelia 3. Thelia 2 shops stay on 1.x.

## Settings

Back office, module configuration, or from the console:

```
php bin/console empty-payment:configure --status=order_form
php bin/console empty-payment:configure     # prints the current setting
```

Only an existing order status is accepted. If the status is deleted afterwards, an
order paid with this module stays "not paid" and an error is written to the log.

The stock is not handled when the order is created (`manageStockOnCreation()` is false):
it follows the status the order is moved to.

## Security

The module is valid for every cart and every customer. To offer it to some customers
only, use a payment rule module (for instance PaymentCondition).

The title shown to the buyer is the title of the module, editable in the back office.
