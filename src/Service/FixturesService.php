<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use DateInterval;
use Faker\Factory;
use Faker\Generator;

class FixturesService
{
    public Generator $faker;
    private $month = 1;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    private string $adminName = 'd';
    private string $adminMail = 'd@d.d';
    private string $password = 'd';
    private int $numberOfPosts = 1;
    private int $numberOfUsers = 1;
    public array $users = [];
    public array $posts = [];

    private array $tags =
        [
            ['Lexique' => ['guerre', 'paix', 'propagande', 'sanctions', 'occupation', 'génocide', 'Droits de l\'homme', 'stratégie']],
            ['Auteurs' => ['Dav']],
            ['Pays' => ['Russie', 'France', 'Chine', 'Iran', 'Allemagne']],
            ['Personnalités' => ['Poutine', 'Lavrov']],
            ['Consortium' => ['Otan', 'Brics', 'Cia']],
            ['Tails' => ['Nose slide', 'Tail slide']],
            ['Type d\'article' => ['enquête', 'entretien', 'vidéo', 'étude', 'rapport']],
            ['Niveau' => ['Constat', 'Fait', 'Analyse', 'Articulation historique']],
            ['Catégories' => ['Economie', 'Politique', 'Confrontations', 'Médias', 'Phlosophie', 'Science', 'Ecologie', 'Cyberespace', 'Alternatives', 'Public']],
            ['Temporité' => ['Actualité', 'Projection', 'Histoire', 'Avenir']],
            ['Continent' => ['Amérique du nord', 'Amérique du Sud', 'Europe', 'Afrique', 'Asie', 'Océanie']]
        ];

    public function generateDateInPast(): DateTime
    {
        $this->month = mt_rand(1, 24);
        $now = new DateTime();
        $dist = DateInterval::createFromDateString($this->month . ' months');
        $now->sub($dist);
        $now->format('Y-m-d H:i:s');
        return $now;
    }

    public function generateRandomDateFrom(): DateTime
    {
        $now = new DateTime();
        $dist = DateInterval::createFromDateString(mt_rand(1, $this->month) . ' months');
        $now->sub($dist);
        $now->format('Y-m-d H:i:s');
        return $now;
    }

    public function tags()
    {
        return $this->tags;
    }

    public function numberOfPosts()
    {
        return $this->numberOfPosts;
    }

    public function numberOfUsers()
    {
        return $this->numberOfUsers;
    }

    public function adminName()
    {
        return $this->adminName;
    }

    public function adminMail()
    {
        return $this->adminMail;
    }

    public function generalPassword()
    {
        return $this->password;
    }

}
