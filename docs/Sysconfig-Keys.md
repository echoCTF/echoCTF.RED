# Sysconfig Keys

## Flags (0 false, 1 true)
* `event_active` Is the event currently active?
* `player_profile` Enable player profiles
* `dashboard_is_home` Default home page for the users is the dashboard

Not implemented on current code-base but are going to
* _`teams`_ Competition supports teams
* _`require_activation`_ Whether it is required for users to activate their accounts
* _`disable_registration`_ Whether online registrations are allowed

## String and numeric key/val pairs
* `event_name` A name for your event
* `frontpage_scenario` The event scenario displayed at the `frontend/`, landing page for guests.
* `offense_scenario` A scenario displayed to the users once they have signed into the frontend.
* `spins_per_day` Limit allowed restarts per day per player
* `online_timeout` Timeout in seconds that a user is no longer considered online on the platform
* `challenge_home` Web accessible folder that the challenges can be downloaded from. (default: `uploads/`)
* `offense_registered_tag` PF tag used for registered offense users
* `defense_registered_tag` PF tag used for registered defense users
* _`footer_logos`_: HTML code to display logos for the event on the footer of the page. (PENDING)

## mail configuration
* `mail_from` Email address used to send registration and password reset mails from
* `mail_fromName` The name appeared on the email send for registration and password resets
* `mail_host`
* `mail_port`


## VPN specific keys
* `CA.csr` The CA CSR
* `CA.crt` The CA certificate
* `CA.key` The CA private key
* `CA.txt.crt` The text version of the CA certificate
* `vpn-ta.key` The OpenVPN TLS Auth key
* `vpngw` The VPN gateway IP or FQDN, that participants of the competition will have to connect to be able to access the targeted infrastructure.


## Application specific
* `platform_codename`
* `platform_version`
