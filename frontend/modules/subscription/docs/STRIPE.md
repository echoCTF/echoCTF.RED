# Preparing Stripe
## 1. Create Stripe Account

## 2. Create products
* Go to Products -> Create https://dashboard.stripe.com/test/products/create
* Fill in the following details
 - Name / Description
 - Additional Options -> Add metadata
  - network_ids: Coma seperated list of network IDs that this subscription will provide access to
  - badge_id: ID of the badge to assign to user
  - spins: Number of spins the user will get
  - shortcode: Network shortcode
  - weight: users by ordering
  - perks: raw html of the perks the subscription provides
  - htmlOptions: jsonEncoded Options to be used by the UI
    - title: title to show
    - class: css classes

## 3. Configure customer portal
* Go to Settings -> Customer portal https://dashboard.stripe.com/test/settings/billing/portal
* Set the following options:
  - Allow customers to view their invoice history
  - Allow customer to update the following billing information
    - Email, Billing Address, Tax ID
  - Allow customers to update their payment methods
  - Allow customers to switch to a different pricing plan
  - Allow customers to update subscription quantities
  - Prorate subscription updates: Issue invoice immediately
  - Products: add existing products and add restrictions to _Limit quantity to 1_
  - Business Information details

## 4. Configure automatic emails on purchases
* Go to https://dashboard.stripe.com/settings/emails
* Set the following details on **Customer emails**:
  - Email customers about… enable
    - Successful payments
    - Refunds


## 4. Configure local modules
* Copy modules and edit config.php to match your stripe settings
