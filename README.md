![echoCTF RED logo](https://echoctf.red/images/logo.png)
# What is echoCTF.RED
echoCTF.RED codename Mycenae is the first iteration of our online service, based on our unique framework, for setting up and executing cyber security training, exercises and Capture the Flag events.

It is a free online service that offers a controlled environment, based on real-life systems and services, to train and sharpen your offensive and defensive security skills. Scan, brute-force and do whatever it takes to attack the systems and solve the real-life security scenarios to gain points. More information about our offline competitions visit echoCTF.com

This project is currently under HEAVY restructure and development and may not be in a _usable_ state. However, get in touch with us and we will let you know of the best way to proceed if you're interested in testing the platform.

## Project Structure
 * `ansible` => Ansible playbooks for setting up and updating the infrastructure
 * `backend` => Admin interface and commands
 * `contrib` => Various contributed materials
 * `frontend` => User interface (what you see at https://echoctf.red/)
 * `nodejs` => Various nodejs scripts for bots and live feeds
 * `schemas` => database schema
 * `themes` => Various theme, images and layout sources

## Pre-requisites
* Install MySQL/MariaDB
* Install memcached
* Install https://github.com/echoCTF/memcached_functions_mysql
* Install composer
* Install PHP>=7.0
  - `php-memcache` (not `php-memcached`)
* Install NGiNX (other web servers may also work)

# Live demo?
Register at our online platform at https://echoctf.red/ :)
