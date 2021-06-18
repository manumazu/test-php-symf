# Projet Symfony servant de base au test technique Recyclivre

Un environnement de développement Docker est fourni avec le projet, pour le faire fonctionner vous aurez besoin de : 

    - Docker
    - Docker Compose
    - Makefile

Une fois ces prérequis remplis, vous pouvez lancer le container Doker : 

    make up

Puis lancer les tests pour vérifier que le projet fonctionne (attention la base est entièrement reconstruite au moment des tests) : 

    make test

Vous aurez alors accès : 

- Au bash du container pour exécuter les commandes symfony : 
    
        make bash
        ./bin/console

- A un accès web via : `http://localhost:8001`

- A une base de données : 
    - Host : `localhost`
    - Port : `3336`
    - User : `user`
    - Password : `password`
    - Base : `recyclivre`

Si vous ne souhaitez pas utiliser Docker, vous pouvez utiliser votre propre environnement de développement : 

    - Serveur web (Apache, NGinx, Caddy ...)
    - PHP >= 7.4
    - MySQL >= 8
    - Composer >= 2

Installer les dépendances :

    composer install

Lancer les tests (attention la base est entièrement reconstruite au moment des tests)

    ./vendor/bin/simple-phpunit