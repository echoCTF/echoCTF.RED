# Preparing Stripe
## 1. Create Stripe Account

## 2. Create products

* Go to Products -> Create <https://dashboard.stripe.com/test/products/create>
* Fill in the following details
  * Name / Description
  * Additional Options -> Add metadata
    * `network_ids`: Coma seperated list of network IDs that this subscription will provide access to
    * `badge_id`: ID of the badge to assign to user
    * `spins`: Number of spins the user will get
    * `shortcode`: Network shortcode
    * `weight`: users by ordering
    * `perks`: raw html of the perks the subscription provides
    * `api_bearer_enable`: Wheather the subscription includes enables API access to the frontend for the players
    * `standalone_perk`: (optional) 0/1 if this is a stand-alone perk (non-recurring)
    * `days`: If this is a stand-alone product but needs to stay active for a number of days (non-recurring)
    * `htmlOptions`: jsonEncoded Options to be used by the UI
      * `title`: title to show on frontend
      * `class`: css classes to add to the product card
* Create prices for the products
* Choose if the payment will bn reccuring (eg. subscription based) or flat rate
* Optionally add metadata for non recurring one-time payments
  * `days`: number of days this will be active
  * `perk`: 0/1 if this is a perk
  * `spins`: 0 reset, any other number increase player daily limit by number
  * `notification_body`: Notification details to be send to the user (overloads product details if present)
  * `notification_title`: Notification details to be send to the user (overloads product details if present)
  * `notification_type`: Notification details to be send to the user (overloads product details if present)
  * `machines`: Number of machines this involves

## 3. Configure customer portal

* Go to Settings -> Customer portal <https://dashboard.stripe.com/test/settings/billing/portal>
* Set the following options:
  * Allow customers to view their invoice history
  * Allow customer to update the following billing information
    * Email, Billing Address, Tax ID
  * Allow customers to update their payment methods
  * Allow customers to switch to a different pricing plan
  * Allow customers to update subscription quantities
  * Prorate subscription updates: Issue invoice immediately
  * Products: add existing products and add restrictions to _Limit quantity to 1_
  * Business Information details

## 4. Configure automatic emails on purchases

* Go to <https://dashboard.stripe.com/settings/emails>
* Set the following details on **Customer emails**:
  * Email customers about… enable
    * Successful payments
    * Refunds


## 5. Configure local modules

* Copy modules and edit config.php to match your stripe settings


