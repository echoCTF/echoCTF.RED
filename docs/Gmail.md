# Configure echoCTF.RED to use Google Mail
In order to configure echoCTF.RED to use a GMail account for sending emails you first have to configure the GMail account.

1. Go to https://myaccount.google.com/
2. Enable 2-step verification
3. Go to https://myaccount.google.com/apppasswords (NOTE: this do not work without 2-step verification)
4. Create an application ie `myplatform`
5. Copy the generated app password, it will look something like `blah blah blah blah`
6. Go to your echoCTF backend Settings => Configure
7. Scroll to the Mail Setting section and add something like the following
   ```
   gmail+smtp://YOUR_GOOGLE_MAIL:APP_PASSWORD@default?local_domain=YOUR_DOMAIN&verify_peer=0
   ```
   1. Replace `YOUR_GOOGLE_MAIL` with your gmail eg `blah.blah@gmail.com`
   2. Replace `APP_PASSWORD` with the application password you copied from step 5 but without spaces in between eg `blahblahblahblah`
   3. Replace `YOUR_DOMAIN` with your platform frontend domain eg `ctf.blah.io`
8. Press Save
9. Execute `composer require symfony/google-mailer` from your `frontend` and `backend` system folders
