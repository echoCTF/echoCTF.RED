# Initializing a new event

The following document provides instructions on how to initialize a private project for your new event.

Its generally advised that you do not work directly on a clone of the official project, since the project may get updated in between which can cause your project to be corrupted.

1. Create an empty github repo (we dont clone since clones cant be private)
2. Clone your new project locally
3. Clone echoctf.red repo into a temporary location
4. move all files from echoctf.red to the empty repo, except: `.git/`, `.github/`,
   `.mkdocs.yml`, `.readthedocs.yml`, `README.md`, `FAQ.md`,
   `docker-compose.yml`, `docker-compose-novpn.yml`, `docker-compose-novpn-macvlan.yml`,
   `CREDITS.md`, `CONTRIBUTING.md`
5. Create a folder for your local migrations `mkdir migrations`
6. Copy the files from `contrib/sample-migrations/` into `migrations`
7. Edit the migration files with your own details, such as event name, durations etc

For local testing also do the following:

1. edit `contrib/init.sh` and modify the database name on top of the file as well as and the **`sysconfig`** values
2. run `./contrib/init.sh settings services sql migrate sysconfig init services tmuxs` (notice that `services` is given twice)
3. Edit `frontend/config/web.php` and change the cookie `secure` flag to `false`

In order to:

* attach to the existing development server run `tmux -L DATABASENAME attach`
* stop the development server run `tmux -L DATABASENAME kill-server`

## Modifying the participant interfaces

1. replace the following frontend images with ones of your liking
   * `frontend/images/logo-small.png` small logo used on top of the right menu pane (200x40)
   * `frontend/images/logo.png` same logo but larger (919x184)
   * `frontend/web/images/twnew-target.png` logo used to create target social images (1200x628)
   * `frontend/web/images/badge.tpl.png` Template image used to generate the player badges (800x220)

2. Certain image folders on the frontend are not tracked in git, so if you want to add images there you have to remove the `.gitignore` entries
   * `frontend/web/images/.gitignore` for flag images (you can use the ones you like as long as they follow the same format)
   * `frontend/web/images/targets/.gitignore` for your network images
   * `frontend/web/images/networks/.gitignore` for your network images
   * `frontend/web/images/avatars/.gitignore`, `frontend/web/images/avatars/badges/.gitignore`, `frontend/web/images/avatars/team/.gitignore` for player and team avatars as well as player badges.
   * `frontend/web/images/challenge/.gitignore` & `frontend/web/images/challenge/category/.gitignore` for specific challenges or categories
