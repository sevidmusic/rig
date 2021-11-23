<?php

/**
 * Components.php
 *
 * This file is used to configure an AppBuilder for the App.
 *
 * An AppBuilder is responsible for building an App for a domain
 * when an App's Components.php is executed via php.
 *
 *     For example:
 *
 *     php Apps/APPNAME/Components.php
 *
 * Unless you modify this file, the domain the App is built for 
 * will be determined as follows:
 *
 *     1. If a domain is specified via $argv[1] the App will be 
 *        built for that domain.
 *
 *        For example:
 *
 *            php Apps/APPNAME/Components.php \
 *                'https://roady.tech'
 *
 *        Would build the App for the domain:
 *
 *            https://roady.tech
 *
 *     2. If a domain is not specified via $argv[1], then 
 *        the App will be built for the hard coded domain 
 *        supplied as the $domain parameter to this files 
 *        call to the AppBuilder::getAppsAppComponentsFactory() 
 *        method.
 *
 *     3. If a domain is not specified via $argv[1], and the 
 *        hard-coded default $domain is empty, then the App 
 *        will be built for the domain:
 *
 *            http://localhost:8080
 *
 * WARNING: There is a bug that causes build issues if the 
 * domain supplied to Components.php contains an ending
 * forward slash.
 *
 * For example, the following hypothetical examples may fail to 
 * build the App:
 *
 *    php Apps/APPNAME/Components.php 
 *        'https://domain.with.ending.forward.slash/'
 *
 *    php Apps/APPNAME/Components.php 
 *        'http://localhost:8989/'
 *
 * @see https://github.com/sevidmusic/roady/issues/193
 */

use roady\classes\utility\AppBuilder;

ini_set('display_errors', 'true');

require(
    strval(
        realpath(
            str_replace(
                'Apps' . DIRECTORY_SEPARATOR . strval(
                    basename(__DIR__)
                ),
                'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
                __DIR__
            )
        )
    )
);

AppBuilder::buildApp(
    AppBuilder::getAppsAppComponentsFactory(
        /**
         * @param string $appName The App's name should match the 
         *                        App's directory name.
         *
         * WARNING: If you modify this file, it is 
         * recommended that escapeshellarg() is still
         * used to filter the value supplied to $appName
         * parameter.
         */
        escapeshellarg(strval(basename(__DIR__))),
        (
            /**
             * @param string $domain The domain to build the App 
             *                       for.
             *                       
             *                       The App will be built for 
             *                       the domain specified via 
             *                       $argv[1] if $argv[1] is 
             *                       supplied.
             *
             *                       If $argv[1] is not supplied, 
             *                       and the $domain parameter is 
             *                       hard coded, the App will be
             *                       built for the hard coded
             *                       domain. 
             *
             *                       If $argv[1] is not specified,
             *                       and the hard coded $domain 
             *                       parameter is empty, then the 
             *                       AppBuilder will build the 
             *                       App for the domain:
             *
             *                       http://localhost:8080
             *
             * WARNING: If you modify this file, it is 
             * recommended that escapeshellarg() is still
             * used to filter the value supplied to $domain
             * parameter.
             */
            escapeshellarg($argv[1] ?? '_DOMAIN_')
        )
    )
);
