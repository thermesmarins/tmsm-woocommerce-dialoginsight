TMSM WooCommerce DialogInsight
==============================

Requires:
* Dialog Insight active subscription
* Dialog Insight API access to: Projects API, Contacts API
* WooCommerce 3.4+

Features
--------

* Adds a checkbox to subscribe to newsletter on checkout page
* Syncs data only if checkbox is checked by user
* Checkbox is unchecked by default
* If checkbox is unchecked, does not unsubscribe the user if the user is already subscribed
* Sync the billing fields: billing_email, billing_first_name, billing_last_name
* Compatibility with TMSM WooCommerce Billing Fields (billing_birthday, billing_title fields)
* Select the optin field the users will subscribe

TODO
----
Map fields (currently fields are hardcoded)