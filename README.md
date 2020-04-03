![echoCTF RED logo](https://echoctf.red/images/logo-red-small.png)

# What is echoCTF.RED
echoCTF.RED <small>codename Mycenae</small>, is the first iteration of our online service,
based on our unique framework, for setting up and executing cyber security
training, exercises and Capture the Flag events.

It is a free online service that offers a controlled environment, based on
real-life systems and services, to train and sharpen your offensive and
defensive security skills. Scan, brute-force and do whatever it takes to
attack the systems and solve the real-life security scenarios to gain points.

For more information about our competitions visit [https://echoCTF.com/](https://echoCTF.com/) or if
you'd rather see a live example of our platform feel free to visit
[https://echoCTF.RED/](https://echoCTF.RED/)

This project is currently under HEAVY restructure and development and may not
be in a _usable_ state.

Our main goals for echoCTF include:
* **Completeness** - Provide a complete set of tools and applications to develop, deploy and maintain competitions
* **Modularity** - Each component has a unique and clear role
* **Expandability** - echoCTF's components are designed to permit expansion

Also keep in mind that the system comes up with absolutely no data. This means
that it is up to you to create targets, challenges, rules, instructions and
any other details you require.

## Project Structure
 * `ansible` => Ansible playbooks for setting up and updating the infrastructure
 * `backend` => Admin interface and commands
 * `contrib` => Various contributed materials
 * `docs` => Documentation folder
 * `frontend` => User interface (what you see at https://echoctf.red/)
 * `nodejs` => Various nodejs scripts for bots and live feeds
 * `schemas` => database schema
 * `themes` => Various theme, images and layout sources

# Disclaimer
The documents and guides in this repository are only examples and are not meant
to be used in production.

The project is far from plug-n-play at the moment and you are required to have
a fairly good understanding of the services involved.

Apply common logic when copy pasting command and files :)

echoCTF is software that comes with absolutely no warranties whatsoever. By
using echoCTF, you take full responsibility for any and all outcomes that
result.
