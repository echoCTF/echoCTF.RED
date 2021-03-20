# Sysconfig Keys

## Flags (0 false, 1 true)
* `event_active`: Enable/Disable current event
* `player_profile`: Enable/Disable player profiles
* `dashboard_is_home`: Enable/Disable dashboard as default home page for players
* `teams`: Enable/Disable teams support
* (optional) `team_required`: Enable/Disable requirement for teams
* `approved_avatar`: Enable/Disable automatic avatar approval


Not activated by default on current code-base but are going to

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
* `footer_logos`: HTML code to display logos for the event on the footer of the page. (PENDING)
* `site_description`: Text to be displayed on meta description and social media tags about the site
* `twitter_account`: The twitter account to link tweets to
* `twitter_hashtags`: The twitter hashtags to include in the tweets
* `default_homepage`: Default page to redirect the users after login
* (optional) `members_per_team`: How many members are allowed per team
* (optional) `event_start`/`event_end`: When the event starts and stops (timestamp)
* (optional) `registrations_start`/`registrations_end`: When the registrations starts and stops (timestamp)
* `profile_visibility`: Set the default player profile visibility (users can still change settings)
* (optional) `discord_invite_url`: discord server invite url on show on left sidebar

## mail configuration
* `mail_from` Email address used to send registration and password reset mails from
* `mail_fromName` The name appeared on the email send for registration and password resets
* `mail_host` The mail server host to send mails through
* `mail_port` The mail server port to connect
* `mail_username` The username to authenticate to the mail server
* `mail_password` The password to authenticate to the mail server

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
