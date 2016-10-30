# PHPMACHINE

Introduction
============

A demo prepared for the talk given in: [**PHPMad Maquinas de estado y flujos de trabajo en nuestros proyectos PHP por Daniel Abad**][1]

[1]: http://www.meetup.com/es-ES/PHPMad/events/234585802/

Please do not take into account the quality of the code, is about understand the concepts and the architecture around, without much "magic".

Instalation
===========

Execute following commands in order to run de demo:

    composer install

    php bin/console doctrine:database:create

    php bin/console doctrine:schema:create

    php bin/console doctrine:fixtures:load
    
To restart the application, i recommend to run:

    php bin/console doctrine:schema:drop -f
    
    php bin/console doctrine:Schema:create
    
    php bin/console doctrine:fixtures:load
    
Enjoy!