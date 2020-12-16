# frontend development information

## Base Controller

## Dashboard Controller
* actionIndex
```
'dashboard' => 'dashboard/index',
```

## Legal Controller
* actionPrivacyPolicy
```
'privacy_policy'=>'legal/privacy-policy',
'legal/privacy-policy'=>'legal/privacy-policy',
```

* actionTermsAndConditions
```
'terms_and_conditions'=>'legal/terms-and-conditions',
'legal/terms-and-conditions'=>'legal/terms-and-conditions',
```

## Profile Controller
* actionIndex
```
'profile/<id:\d+>' => 'profile/index',
'p/<id:\d+>' => 'profile/index',
```

* actionNotifications
```
'profile/notifications'=>'profile/notifications',
```

* actionHints
```
'profile/hints'=>'profile/hints',
```

* actionMe
```
'profile/me'=>'profile/me',
```

* actionBadge
```
'profile/<id:\d+>/badge' => 'profile/badge',
'p/<id:\d+>/badge' => 'profile/badge',
```

* actionOvpn
```
'profile/ovpn'=>'profile/ovpn',
```

* actionSettings
```
'profile/settings'=>'profile/settings',
```

## Site Controller
* actionIndex
```
'' => 'site/index',
'/' => 'site/index',
```

* actionLogin & actionLogout
```
'login' => 'site/login',
'logout' => 'site/logout',
```

* actionError

* actionCaptcha
```
'site/captcha'=>'site/captcha',
```

* actionRegister
```
'register'=>'site/register',
```

* actionRequestPasswordReset
```
'request-password-reset'=>'site/request-password-reset',
```

* actionResetPassword
```
'reset-password' => 'site/reset-password',
```

* actionVerifyEmail
```
'verify-email'=>'site/verify-email',
```

* actionResendVerificationEmail
```
'resend-verification-email'=>'site/resend-verification-email',
```

* actionChangelog
```
'changelog' => 'site/changelog',
```

# Modules

## Challenge
### DefaultController
* actionIndex
```
'challenges' => 'challenge/default/index',
```

* actionView
```
'challenge/<id:\d+>' => 'challenge/default/view',
```

* actionDownload
```
'challenge/<id:\d+>/download' => 'challenge/default/download',
```

## Game
### DefaultController
* actionIndex
* actionRate

### LeaderboardsController
* actionIndex
```
'leaderboards' => 'game/leaderboards/index',
````


## Help

### FaqController
* actionIndex
```
'help/faq' => 'help/faq/index',
'faq' => 'help/faq/index',
```

### InstructionController
* actionIndex
```
'help/instructions' => 'help/instruction/index',
'instructions' => 'help/instruction/index',
```

### RuleController
* actionIndex
```
'rules' => 'help/rule/index',
```

## Network
### DefaultController
* actionIndex
```
'networks' => 'network/default/index',
```

* actionView
```
'network/<id:\d+>' => 'network/default/view',
```

## Target

'target/<id:\d+>/rate' => 'game/default/rate',

### DefaultController
* spin (`SpinRestAction`)
```
'target/<id:\d+>/spin'=>'target/default/spin',
```

* actionVersus
```
'target/<id:\d+>/versus/<profile_id:\d+>'=>'target/default/versus',
'target/<id:\d+>/vs/<profile_id:\d+>'=>'target/default/versus',
```

* actionIndex
```
'target/<id:\d+>' => 'target/default/index',
```

* actionClaim
```
'claim'=>'target/default/claim',
```

* actionBadge
```
'target/<id:\d+>/badge' => 'target/default/badge',
```

### WriteupController
* actionEnable
```
'target/<id:\d+>/writeups/enable' => 'target/writeup/enable',
```

* actionView
```
'target/<id:\d+>/writeup/view' => 'target/writeup/view',
```

* actionSubmit
```
'target/<id:\d+>/writeup/submit' => 'target/writeup/submit',
```

* actionUpdate
```
'target/<id:\d+>/writeup/update' => 'target/writeup/update',
```

## Team
### DefaultController
* actionIndex
```
'team' => 'team/default/index',
```

* actionCreate
```
'team/create' => 'team/default/create',
```

* actionJoin
```
'team/join/<token>' => 'team/default/join',
```

* actionUpdate
```
'team/update' => 'team/default/update',
```

* actionApprove
```
'team/approve/<id:\d+>' => 'team/default/approve',
```

* actionReject
```
'team/reject/<id:\d+>' => 'team/default/reject',
```

* actionInvite
```
'team/invite/<token>' => 'team/default/invite',
```


## Tutorial
### DefaultController
* actionIndex
```
'tutorials' => 'tutorial/default/index',
```

* actionView
```
'tutorial/<id:\d+>' => 'tutorial/default/view',
```
