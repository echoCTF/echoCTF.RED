# Instructions for events
1. Create an empty github repo
2. Clone the repo locally
3. Clone echoctf.red repo into a temporary location
4. move all files from echoctf.red to the empty repo except `.git/`, `.github/`,
   `.mkdocs.yml`, `.readthedocs.yml`, `README.md`, `FAQ.md`,
   `docker-compose.yml`, `docker-compose-novpn.yml`, `docker-compose-novpn-macvlan.yml`,
   `CREDITS.md`, `CONTRIBUTING.md`
5. `mkdir migrations`
6. edit `contrib/init.sh` and modify the database name on top of the file as well as and the **`sysconfig`** values
7. run `./init.sh settings services sql sysconfig init services tmuxs` (notice that `services` need to given twice)

In order to:
* attach to the existing development server run `tmux -L DATABASENAME attach`
* stop the development server run `tmux -L DATABASENAME kill-server`

## Modifying the participant interfaces
1. replace the following frontend images with ones of your liking
   * `frontend/images/logo-small.png` small logo used on top of the right menu pane (200x40)
   * `frontend/images/logo.png` same logo but larger (919x184)
   * `frontend/web/images/twnew-target.png` logo used to create target social images (1200x628)
   * `frontend/web/images/badge.tpl.png` Template image used to generate the player badges (800x220)
