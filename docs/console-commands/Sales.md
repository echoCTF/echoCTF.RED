# Sales commands
Commands related to the paying features of the platform.

## ExpireSubscriptions
Find subscriptions that have expired for more than 4 hours and cause expirations.

Usage: `./backend/yii sales/expire-subscriptions`

## ImportStripe
Import Stripe details (subscriptions, customers, products).

Usage: `./backend/yii sales/import-stripe`

## StripeImportProducts
Import Stripe products and their prices.

Usage: `./backend/yii sales/stripe-import-products`

## Delete Inactive
Delete inactive subscriptions

Usage: `./backend/yii sales/delete-inactive`
