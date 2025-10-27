# Sysconfig Keys

## Flags (0 false, 1 true)

* `event_active`: Enable/Disable current event
* `player_profile`: Enable/Disable player profiles
* `player_require_approval` If player activation requires moderator approval first
* `player_require_identification` Whether players need to provide proof of ID during registration
* `all_players_vip`: Allow all players to have VIP features enabled
* `dashboard_is_home`: Enable/Disable dashboard as default home page for players
* `dashboard_graph_visible`: Enable/Disable dashboard activity graph for last 10 days
* `stream_record_limit`: Number of records to limit stream entries. A value of `0` disables limit.
* `teams`: Enable/Disable teams support
* `team_required` (optional): Enable/Disable requirement for teams
* `approved_avatar`: Enable/Disable automatic avatar approval
* `leaderboard_show_zero`: Show zero points on leaderboard
* `leaderboard_visible_after_event_end`: Show leaderboard after event end
* `leaderboard_visible_before_event_start`: Show leaderboard before event start
* `target_guest_view_deny`: Deny guests to target/view and target/versus
* `target_hide_inactive`: Hide inactive targets from the frontend listings. This includes upcoming powerups
* `network_view_guest`: Allow networks to be viewed by guests
* `force_findings_to_claim`: Enable the enforcement of players needing to have discovered the findings before claiming flags
* `maintenance`: Enable site-wide maintenance mode
* `maintenance_notification`: Send maintenance notification to everyone connected to the frontend interface. The popup can be dismissed but it always comes back. No other notifications are delivered.
* `disable_mailer`: Whether to disable or not the platform mailing operations
* `require_activation` Whether it is required for users to activate their accounts
* `disable_registration` Whether online registrations are allowed
* `team_visible_instances` Whether or not player instances are visible to the rest of the team by default otherwise the per-instance field `team_allowed` takes priority
* `guest_visible_leaderboards` Whether or not the leaderboard will be visible to guest users (this still respects the event start/end restrictions)
* `hide_timezone` Whether or not the Timezone information should be visible
* `disable_mail_validation`: Whether or not mail validation should be disabled
* `disable_ondemand_operations`: Whether or not to disable ondemand operations all together
* `profile_discord`: Whether the field will be visible under the player profile page. This is different than `profile_settings_fields`
* `profile_echoctf`: Whether the field will be visible under the player profile page. This is different than `profile_settings_fields`
* `profile_twitter`: Whether the field will be visible under the player profile page. This is different than `profile_settings_fields`
* `profile_github`: Whether the field will be visible under the player profile page. This is different than `profile_settings_fields`
* `profile_htb`: Whether the field will be visible under the player profile page. This is different than `profile_settings_fields`
* `profile_twitch`: Whether the field will be visible under the player profile page. This is different than `profile_settings_fields`
* `profile_youtube`: Whether the field will be visible under the player profile page. This is different than `profile_settings_fields`
* `team_only_leaderboards`: When enabled, only show team based leaderboards. All other leaderboards are hidden (including player profile ones)
* `writeup_rankings`: Whether or not writeup rankings will be visible on leaderboard
* `country_rankings`: Whether or not country rankings will be visible on leaderboard
* `player_point_rankings`: Whether or not player point rankings will be visible on leaderboard
* `player_monthly_rankings`: Whether or not player monthly rankings will be visible on leaderboard
* `module_smartcity_disabled`: Whether or not the smartcity module is disabled (hides the menu from backend)
* `module_speedprogramming_enabled`: Whether or not the Speed Programming module is enabled (hides the menu from backend also)

* `log_failed_claims`: Log failed claim attempts?
* `team_encrypted_claims_allowed`: Should we allow claims of flags across teams?

## String and numeric key/val pairs

* `writeup_rules` Your rules for writeup submissions
* `treasure_secret_key`: Encrypt flags per player?
* `event_name` A name for your event
* `frontpage_scenario` The event scenario displayed at the `frontend/`, landing page for guests.
* `offense_scenario` A scenario displayed to the users once they have signed into the frontend.
* `defense_scenario` A scenario displayed to the users once they have signed into the frontend.
* `spins_per_day` Limit allowed restarts per day per player
* `online_timeout` Timeout in seconds that a user is no longer considered online on the platform
* `challenge_home` Full path to folder that the challenges will be uploaded to. (default: `@web/uploads/`)
* `challenge_root` Web accessible folder that the challenges can be downloaded from. (default: `/uploads/`)
* `offense_registered_tag` PF tag used for registered offense users
* `defense_registered_tag` PF tag used for registered defense users
* `footer_logos`: HTML code to display logos for the event on the footer of the page. (PENDING)
* `site_description`: Text to be displayed on meta description and social media tags about the site
* `twitter_account`: The twitter account to link tweets to
* `twitter_hashtags`: The twitter coma separated hashtags for tweets
* `default_homepage`: Default page to redirect the users after login
* `bannedIPS`: Coma separated list of IPs and IP patterns to be denied access to the interface
* `members_per_team` (optional): How many members are allowed per team
* `event_start`/`event_end` (optional): When the event starts and stops (timestamp)
* `registrations_start`/`registrations_end` (optional): When the registrations starts and stops (timestamp)
* `profile_visibility`: Set the default player profile visibility (users can still change settings)
* `profile_settings_fields`: Comma separated list of field names that the users are allowed to change (avatar,bio,country,discord,echoctf,email,fullname,github,htb,pending_progress,twitch,twitter,username,visibility,youtube)
* `profile_card_disabled_actions`: Comma separated list of disabled actions. Values include: badge, edit, profileurl, inviteurl, generate-token, copy-token, revoke, disconnect, delete.
* `admin_ids` (optional): Comma separated list of admin player IDs
* `admin_player:<PLAYER_ID>` (optional): Set a specific player ID as admin
* `target_new_days`: How many days is target considered as `new` on the frontend after creation
* `target_updated_days`: How many days the target is considered as `updated` on the frontend after update
* `discord_news_webhook`: A discord webhook url to send news and announcements to
* `pf_state_limits`: The content to be appended to the pass rules that enforces limits (default: `(max 10000, source-track rule, max-src-nodes 5, max-src-states 2000, max-src-conn 50)`)
* `force_https_urls`: Force URL generation to always be https (sets `_SERVER['HTTPS']=on`)
* `menu_items`: JSON encoded string of items to append to the frontend menu
* `event_end_notification_title`: Title to be used for a notification when the event ends
* `event_end_notification_body`: The body that will be used to send a notification to all players when the event ends
* `plus_writeups`: Number to add to the headshots to allow for writeup activations (eg. a value of `2` means that the player can have `player_headshots+2` writeups active at most). A value of `0` means that the player can have only as many writeups active as its own number of headshots.
* `avatar_generator`: If set to `Identicon` it will use that instead of Robohash
* `avatar_robohash_set`: Choose the set for when robohash is configured
* `mail_verification_token_validity`: How long will the mail verification tokens be active for. Can take intervals supported by php and `INTERVAL`, eg. 10 day, meaning 10 days from now
* `password_reset_token_validity`: How long will the password reset tokens be active for. Can take intervals supported by php and `INTERVAL`, eg. 10 day, meaning 10 days from now
* `pflog_min`/`pflog_max`: min/max number of pflog interfaces to use for the match findings. Allows for splitting the findings load into multiple processes.


* `player_delete_inactive_after`: Delete players with status=9 (inactive) after X days
* `player_delete_deleted_after`: Delete players with status=0 (deleted) after X days
* `player_changed_to_deleted_after`: Update players with status=8 (changed) into status=0 (deleted) after X days
* `player_delete_rejected_after`: Delete players that their registration was rejected (status=9 and approval=4) after X days

## Frontend API
* `api_bearer_enable` Enable Bearer authorizations API operations
* `api_claim_timeout` set the rate limit for the api claim. One request per `api_claim_timeout`+1 seconds
* `api_target_instances_timeout` set the rate limit for the target instances endpoint. One request per `api_target_instances_timeout`+1 seconds
* `api_target_spin_timeout` set the rate limit for the given target operation endpoints. One request per `api_target_spin_timeout`+1 seconds
* `api_target_spawn_timeout` set the rate limit for the given target operation endpoints. One request per `api_target_spawn_timeout`+1 seconds

## Rate limit
* `rate_limit_requests`: Rate limit number of requests
* `rate_limit_window`: Rate limit window in seconds for the requests above


## mail configuration

* `mail_from` Email address used to send registration and password reset mails from
* `mail_fromName` The name appeared on the email send for registration and password resets
* `dsn` A symphony mailer compatible DSN
* `mail_useFileTransport` Do not actually send mails, just save them in a file

Alternatively, instead of DSN the following keys can be used

* `mail_host` The mail server host to send mails through
* `mail_port` The mail server port to connect
* `mail_username` The username to authenticate to the mail server
* `mail_password` The password to authenticate to the mail server
* `local_domain` Set the EHLO mail used when sending mail
* `verify_peer_name` Verify the SSL peer name of the remote server when sending email
* `verify_peer` Verify the remote peer certificate when sending mail


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


## Subscriptions Specific

* `subscriptions_emergency_suspend` Temporary suspend subscriptions
* `subscriptions_menu_show` Show subscriptions left side menu item to logged in users
* `stripe_apiKey` Stripe API key
* `stripe_publicApiKey` Stripe public API Key
* `stripe_webhookSecret` Stripe webhook secret
* `stripe_automatic_tax_enabled` Enable Stripe automatic TAX
* `stripe_webhook_ips` List of IP's that are allowed to access the stripe webhook endpoint
* `stripe_webhookLocalEndpoint`: The local endpoint url for the receive of Stripe webhooks. Make it something unique and random so that its not easy to be hit by anyone.

## Player Specific

* `academic_Nlong` Name for academic value `N` (starting at 0)
* `academic_Nshort` Short name for academic value `N` (starting at 0)
* `academic_Nicon` An icon name associated with this academic grouping (starting at 0)
* `academic_grouping` The number for the supported groups or (0) to Disable support for academic grouping of player activity

**example:**

```sh
backend/yii sysconfig/set academic_grouping 2
backend/yii sysconfig/set academic_0 "SuperSite.com"
backend/yii sysconfig/set academic_1 "AnotherSite.com"
backend/yii sysconfig/set academic_0short "supersite"
backend/yii sysconfig/set academic_1short "anothersite"
backend/yii sysconfig/set academic_0icon "supersite.png"
backend/yii sysconfig/set academic_1icon "anothersite.png"
```


## Validator Configuration Keys

* `verification_resend_ip` A number of attempts an IP will stop being able to request verification resend email. `0` disables the verification completely
* `verification_resend_ip_timeout` the timeout for the verification resend_ip counter
* `verification_resend_email` A number of attempts a verification resend email can be requested per email. `0` disables the verification completely
* `verification_resend_email_timeout` the timeout for the verification resend_email counter
* `password_reset_ip` A number of attempts an IP will stop being able to request password resets. `0` disables the verification completely
* `password_reset_ip_timeout` The timeout for the reset_ip counter
* `password_reset_email` A number of attempts a password reset email can be requested per email. `0` disables the verification completely
* `password_reset_email_timeout` The timeout for the reset_email counter
* `signup_TotalRegistrationsValidator` Number of total registrations allowed per single IP overall on the platform. `0` Disables the check completely
* `signup_HourRegistrationValidator` Number of total registrations per IP allowed. `0` Disables the check completely
* `signup_StopForumSpamValidator` Percentage of confidence required before we mark an email offensive from StopForumSpam (eg `80`). `0` Disables the check completely
* `signup_VerifymailValidator` Enable or disable verifymail.io validator
* `verifymail_key` The API key for verifymail.io
* `signup_MXServersValidator` Enable/Disable validating `MX` and `IN A` DNS records for given domains. `0` Disables the check completely
* `failed_login_ip` A number of failed logins are allowed per IP. `0` Disables the check completely
* `failed_login_ip_timeout` timeout of failed login ip counter expires
* `failed_login_username` A number of failed logins are allowed per username. `0` Disables the check completely
* `failed_login_username_timeout` the timeout for the failed login_username counter.
* `username_length_min` min length for a username
* `username_length_max` max length for a username


## Dynamic Keys
* `player_type:PLAYER_ID`: The type of the player `offense`/`defense`
* `player:PLAYER_ID`:
* `team_player:PLAYER_ID`:
* `ovpn:PLAYER_ID`: Local IP of player in dotted octet notation
* `ovpn:LOCAL_IP`: The player ID of the vpn assigned local IP `LOCAL_IP`
* `ovpn_remote:PLAYER_ID`: The remote IP of the player