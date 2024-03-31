# sym

## Project

Public publicator using Symfony 7.0. <img src="https://img.shields.io/badge/symfony-7-0" alt="symfony 7.0">

## Requirements

- Php-8.2 <img src="https://img.shields.io/badge/php-8.2-%23777BB4?logo=php" alt="php banner">, usin <a href="https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller">MVC</a>, in respect of <a href="https://fr.wikipedia.org/wiki/SOLID_(informatique)">SOLID principles</a>, and standard <a href="https://www.php-fig.org/psr/psr-12/">Psr-12</a>.

## Components

- <a href="https://www.dotenv.org/docs/languages/php.html">Dotenv</a>
- <a href="https://twig.symfony.com/doc/3.x/tags/extends.html">Twig</a>
- <a href="https://bootswatch.com/5/darkly/bootstrap.min.css/startbootstrap-freelancer/">Bootstrap</a>, https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css.

Composer.json:

    "php": ">=8.2",
    "ext-ctype": "*",
    "ext-iconv": "*",
    "fakerphp/faker": "*",
    "symfony/console": "7.0.*",
    "symfony/debug-bundle": "7.0.*",
    "symfony/dotenv": "7.0.*",
    "symfony/filesystem": "7.0.*",
    "symfony/finder": "7.0.*",
    "symfony/flex": "^2",
    "symfony/framework-bundle": "7.0.*",
    "symfony/mailer": "7.0.*",
    "symfony/runtime": "7.0.*",
    "symfony/security-bundle": "7.0.*",
    "symfony/sendgrid-mailer": "7.0.*",
    "symfony/string": "7.0.*",
    "symfony/validator": "7.0.*",
    "symfony/webapp-pack": "*",
    "symfony/yaml": "7.0.*",
    "symfonycasts/reset-password-bundle": "*",
    "symfonycasts/verify-email-bundle": "*",
    "vich/uploader-bundle": "*"

## Requirements

- Webserver Apache2
- Php-8.2+
- MariaDb

### Installation

- Git clone from `/home`. That will create the directory `sym`.
- Do a `adduser sym` and obtain it's ftp codes.
- Set your virtualhost, following the instructions from: <a href="https://symfony.com/doc/current/setup/web_server_configuration.html">Symfony Docs</a>. You don't need the options of `SetHandler proxy:unix` as it is said.

    git clone https://github.com/FractalFramework/sym.git
    composer audit
    composer install

Create your own .env.local, and connect your database:

    DATABASE_URL="mysql://{dbname-root}:{password}@127.0.0.1:3306/sym"

And the mailer: 

    MAILER_DSN=mailgun+smtp://USERNAME:PASSWORD@default?region=eu

Create the database:

    php bin/console doctrine:database:create

Migrate the schema of the databases for Symfony:

    php bin/console make:migration

Persist this schema on the database (that create the tables) :

    php bin/console doctrine:migration:migrate

Your site is now ready. To perform tests we can write a set of false datas:

    php bin/console doctrine:fixtures:load

In localhosting only, you can using the server of Symfony:

    symfony server:start -d

A very good tutorial for clone a repository on a webserver, tested and it works, is : <a href="http://david-robert.fr/articles/view/deployer-symfony-vps"></a>. You can follow it step by step.

## Usage

The user can :
- create new Posts
- import and use images
- set a serie of Tags for his Post
- set the Home prevalent image for the Post
- publish some trackarieson all Posts
- edit or unpublish a Post
- publish or unpublish some tracks
- choose an avatar for his profile
- change his password
- recover his account

### Roles

In this configuration, two roles are set hierarchically, `ROLE_EDIT` and `ROLE_ADMIN`.
User can add, edit and delet his own articles.
Admin can edit everything.
