# DCKyiv project
This is template for Drupal Camp sites in Ukraine. 

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
- Composer (for menage Drupal Core, modules and Libraries)
- Circle CI (for testing and create builds)
- CI Agent (for create builds via Docksal)
- IDE C9 (for write and edit code, with support xDebug)

### Full install from scratch
- Create project folder (Ex.: `dckyiv`):
```
mkdir dckyiv
```
- Copy repository to your folder (copy project files):
```
cd dckyiv
git clone https://github.com/drupal-ukraine/dcamp-site.git ./
```

- In project root folder execute commands:
```bash
# Install Docksal
curl -fsSL https://get.docksal.io | bash

# If your Docksal VM is not yet started it will ask you.
# https://docksal.io/installation/#macos-virtualbox

# Up and run project
fin p start
# Start the project
fin rebuild
# Wait till finish. First time it takes 10-15 minutes for downloading of the database. 
# If everything is ok, your project would be available at http://dckyiv.docksal/ and enjoy you work!
```

Virtualhost: [http://dckyiv.docksal](http://dckyiv.docksal)

### Files clone
We use stage_file_proxy to proxy files from staging to local. 

### Drush usage

`fin drush (command)`

**Examples:**
 
* Cache Rebuild -         `$ fin drush cr`
* Configuration export -  `$ fin drush cex` 
* Configuration import -  `$ fin drush cim`

### Gulp usage

`fin gulp (command)`

**If you have problems with build of gulp, delete package-json.lock file and run `fin gulp build`**

**Examples:**
 
* Build scss -         `$ fin gulp build`
* Run watch -          `$ fin gulp watch` 

# How to develop?

After you run the "fin rebuild" and have your environment ready you need to do few things.

- Create a fork of [https://github.com/drupal-ukraine/dcamp-site](https://github.com/drupal-ukraine/dcamp-site).
- In project folder edit `.git/config` file. Replace repo URL (remote origin) to your newly created fork.
- Then you can create a branch in your repo, push some code and create a pull request back to `drupal-ukraine/dcamp-site` repo.

### If you don't have PHPStorm or IDE
- Open `.docksal/docksal.env` file
- Find `IDE_ENABLED` and change value to `1`
- Run `fin p restart`
- In the your browser open [http://ide.dckyiv.docksal](http://ide.dckyiv.docksal) and enjoy you work!

### Access to local Database
In the your browser open [http://pma.dckyiv.docksal](http://pma.dckyiv.docksal) and enjoy you work!
