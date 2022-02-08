# DCKyiv project
Purpose of this project - website development for DrupalCampKyiv conference.
If you want to help us - feel free to take a ticket that you like from
the project board (https://github.com/drupal-ukraine/dcamp-site/projects/1) and
propose your changes using Pull Request here.


## Get Started

### Development stack

* We are using Docksal as developer local env.
* Make sure you have latest Docksal installed: https://docksal.io/installation
* About Docksal + documentation: https://docs.docksal.io/
* Git clone into Docksal project directory. Specified during installation or in
`DOCKSAL_NFS_PATH` variable in `~/.Docksal/docksal.env` configuration file
* Your docksal projects should be under docksal NFS path.

### We use:
- Drupal 8
- Docksal (for develepment)
- VirtualBox (you need if you work on WIndows or MacOS)
- Gulp (for compile js, icons and scss files in the our custom theme)
- SCSS (for css styles)
- Composer (Drupal Core, Modules, Libraries packet management and patches)
- Circle CI (We are creating build for every pull request that you create
  at gitbub for better code review)
- CI Agent (for create builds via Docksal)
- IDE C9 (for write and edit code, with support xDebug) - use it if you forgot
  your laptop with installed PHPStorm :)

### How to start your development?
- Clone repository to your local:
```
git clone https://github.com/drupal-ukraine/dcamp-site.git
```

- Install Docksal (if needed):
```bash
# Install Docksal
curl -fsSL https://get.docksal.io | bash

# Install VirtualBox (if you use MacOS or Windows):
# https://docksal.io/installation/#macos-virtualbox

# Up start project containers
# If your Docksal VM is not yet started it will ask you.
fin project start

# Rebuild your local instance with last content and changes.
fin rebuild

# Wait till finish. First time it takes 10-15 minutes for downloading of the database.
```
#### If everything is 'OK' AWESOME!, your project would be available at [http://dckyiv.docksal](http://dckyiv.docksal)

#### ENJOY YOUR WORK AND CONTRIBUTION!

### Files clone
We use stage_file_proxy to proxy files from staging to local.

### Drush usage

`fin drush (command)`

**Examples:**

* Cache Rebuild -         `$ fin drush cr`
* Configuration export -  `$ fin drush cex`
* Configuration import -  `$ fin drush cim`
* Deployment -            `$ fin drush deploy`

### Gulp usage

`fin gulp (command)`

**If you have problems with build of gulp, delete package-json.lock file and
run `fin gulp build`**

**Examples:**

* Build scss -         `$ fin gulp build`
* Run watch -          `$ fin gulp watch`

# How to develop?

After you run the "fin rebuild" and have your environment ready you need to do
few things.

- Create a fork of [https://github.com/drupal-ukraine/dcamp-site](https://github.com/drupal-ukraine/dcamp-site).
- In project folder edit `.git/config` file. Replace repo URL (remote origin)
  to your newly created fork.
- Then you can create a branch in your repo, push some code and create a pull
  request back to `drupal-ukraine/dcamp-site` repo.

### If you don't have PHPStorm or IDE
- Open `.docksal/docksal.env` file
- Find `IDE_ENABLED` and change value to `1`
- Run `fin p restart`
- In the your browser open [http://ide.dckyiv.docksal](http://ide.dckyiv.docksal) and enjoy your work and contribution!

### Access to local Database
In the your browser open [http://pma.dckyiv.docksal](http://pma.dckyiv.docksal) and enjoy your work and contribution!

### Share your local to the world
Run `fin share` command if you want to share your local. Use it if you want
to show your local changes to your friends if you want test you local on
different devices.
