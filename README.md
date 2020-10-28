# news.ly

## Introduction

This project was developed for FEUP's LBAW course by
 * João N. Matos (@joaonmatos)
 * Joana Ferreira (@joanaferreira0011)
 * Gonçalo Oliveira (@Goncalo101)
 * João Monteiro (@UnlimitedSpeed)

It uses PostgreSQL as a backend, a full Laravel stack and Bootstrap on the frontend.

## Installing the Software Dependencies

To prepare you computer for development you need to install some software, namely PHP and the PHP package manager composer.

We recommend using an __Ubuntu__ distribution that ships PHP 7.2 (e.g Ubuntu 18.04LTS).
You may install the required software with:

    sudo apt-get install git composer php7.2 php7.2-mbstring php7.2-xml php7.2-pgsql


## Installing Docker and Docker Compose

Firstly, you'll need to have __Docker__ and __Docker Compose__ installed on your PC.
The official instructions are in [Install Docker](https://docs.docker.com/install/) and in [Install Docker Compose](https://docs.docker.com/compose/install/#install-compose).
It resumes to executing the commands:

    # install docker-ce
    sudo apt-get update
    sudo apt-get install apt-transport-https ca-certificates curl software-properties-common
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
    sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable"
    sudo apt-get update
    sudo apt-get install docker-ce
    docker run hello-world # make sure that the installation worked

    # optionally, add your user to the docker group by using a terminal to run:
    # sudo usermod -aG docker $USER
    # Sign out and back in again so this setting takes effect.

    # install docker-compose
    sudo curl -L "https://github.com/docker/compose/releases/download/1.25.3/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
    docker-compose --version # verify that you have Docker Compose installed.


## Installing local PHP dependencies

After the steps above you will have updated your repository with the required Laravel structure from this repository.
Afterwards, the command bellow will install all local dependencies, required for development.

    composer install


## Working with PostgreSQL

We've created a development-only _docker-compose_ file that sets up __PostgreSQL9.4__ and __pgAdmin4__ to run as local Docker containers.

From the project root issue the following command:

    docker-compose up

This will start the database and the pgAdmin tool.
The database's username is _postgres_ and the password is _pg!lol!2020_.

You can hit http://localhost:5050 to access __pgAdmin4__ and manage your database.
On the first usage you will need to add the connection to the database using the following attributes:

    hostname: postgres
    username: postgres
    password: pg!lol!2020

Hostname is _postgres_ instead of _localhost_ since _Docker composer_ creates an internal DNS entry to facilitate the connection between linked containers.
