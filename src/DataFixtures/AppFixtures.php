<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Music;
use App\Entity\Artist;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    private $artistRepo;
    private $albumRepo;

    public function __construct(ArtistRepository $artistRepository, AlbumRepository $albumRepository)
    {
        $this->artistRepo = $artistRepository;
        $this->albumRepo = $albumRepository;
    }

    public function load(ObjectManager $manager): void
    {
        // Je crée les données liés à mes artistes
        $artists = [
            'Ed Sheeran' => [
                'name' => 'Ed Sheeran',
                'biography' => "Ed Sheeran, né le 17 février 1991 à Halifax dans le Yorkshire de l'Ouest, est un auteur-compositeur-interprète et guitariste anglais. Sa carrière professionnelle commence en 2011 avec la maison de disques Atlantic Records, qui signe son premier album, +, écoulé à 4 millions de copies1. Suivent les albums x (2014) et ÷ (2017), qui rencontrent un succès international encore plus grand2.",
                'photo' => 'sheeran.jpeg'
            ],
            'Lady Gaga' => [
                'name' => 'Lady Gaga',
                'biography' => "Stefani Germanotta, dite Lady Gaga, née le 28 mars 1986 dans l'arrondissement de Manhattan, à New York, est une autrice-compositrice-interprète et actrice américaine.",
                'photo' => 'gaga.jpeg'
            ],
            'Sting' => [
                'name' => 'Sting',
                'biography' => "Sting, de son vrai nom Gordon Matthew Thomas Sumner, né le 2 octobre 1951 à Wallsend, est un auteur-compositeur-interprète et musicien britannique, brièvement instituteur et occasionnellement acteur.",
                'photo' => 'sting.jpeg'
            ],
            'Calvin Harris' => [
                'name' => 'Calvin Harris',
                'biography' => 'Adam Richard Wiles, plus connu sous son nom de scène Calvin Harris, né le 17 janvier 1984 à Dumfries, est un disc jockey, chanteur et producteur de musique électronique britannique.',
                'photo' => 'harris.jpeg'
            ],
            'Bruno Mars' => [
                'name' => 'Bruno Mars',
                'biography' => 'Peter Gene Hernandez, dit Bruno Mars, est un auteur-compositeur-interprète, musicien, danseur-chorégraphe, directeur artistique, producteur, réalisateur, styliste et homme d’affaires américain, né le 8 octobre 1985 à Honolulu, dans l\'État de Hawaï.',
                'photo' => 'mars.jpeg'
            ]
        ];

        // Je crée les données liés à mes albums
        $albums = [
            'I Created Disco' => [
                'title' => 'I Created Disco',
                'published_at' => new \DateTime("2017-06-15"),
                'artists' => [$artists['Calvin Harris']]
            ],
            '+' => [
                'title' => '+',
                'published_at' => new \DateTime("2011-09-01"),
                'artists' => [$artists['Ed Sheeran']]
            ],
            'The Fame' => [
                'title' => 'The Fame',
                'published_at' => new \DateTime("2008-08-01"),
                'artists' => [$artists['Lady Gaga']]
            ],
            'Born This Way' => [
                'title' => 'Born This Way',
                'published_at' => new \DateTime("2011-05-01"),
                'artists' => [$artists['Lady Gaga']]
            ],
            'Chromatica' => [
                'title' => 'Chromatica',
                'published_at' => new \DateTime("2020-06-01"),
                'artists' => [$artists['Lady Gaga'], $artists['Ed Sheeran']]
            ],
        ];

        // Je crée les données liés à mes musiques
        $musics = [
            [
                'name' => 'Merrymaking at My Place',
                'duration' => '4:09',
                'album' => $albums['I Created Disco']
            ],
            [
                'name' => 'Colours',
                'duration' => '4:01',
                'album' => $albums['I Created Disco']
            ],
            [
                'name' => 'The A Team',
                'duration' => '4:18',
                'album' => $albums['+']
            ],
            [
                'name' => 'Drunk',
                'duration' => '3:20',
                'album' => $albums['+']
            ],
            [
                'name' => 'U. N. I.',
                'duration' => '3:48',
                'album' => $albums['+']
            ],
            [
                'name' => 'Just Dance',
                'duration' => '4:01',
                'album' => $albums['The Fame']
            ],
            [
                'name' => 'Paparazzi',
                'duration' => '3:28',
                'album' => $albums['The Fame']
            ],
            [
                'name' => 'Marry the night',
                'duration' => '4:25',
                'album' => $albums['Born This Way']
            ],
            [
                'name' => 'Govemment Hooker',
                'duration' => '4:14',
                'album' => $albums['Born This Way']
            ],
            [
                'name' => 'Stupid Love',
                'duration' => '3:14',
                'album' => $albums['Chromatica']
            ],
            [
                'name' => 'Free Woman',
                'duration' => '3:11',
                'album' => $albums['Chromatica']
            ],
        ];

        // Je fais une boucle pour récupérer 1 artiste à chaque fois.
        foreach ($artists as $artist) {
            
            // Je crée un objet Artist
            $a = new Artist();
            // J'hydrate cet objet
            // $a->setName($artist['name'])
            //     ->setBiography($artist['biography'])
            //     ->setPhoto($artist['photo']);

            // 2ème option pour hydrater automatiquement
            foreach ($artist as $key => $value) {
                // je crée un setter avec la concaténation de 'set' + la clé avec la première lettre en majuscule
                $setter = "set" . ucfirst($key);
                // j'utilise ce setter avec la valeur 
                $a->$setter($value);
            }

            // Je persit à chaque tour de boucle un artist
            $manager->persist($a);

        }
        $manager->flush();

        // Je fais une boucle pour récupérer 1 album à chaque fois
        foreach ($albums as $album) {
            // Je crée un objet de la classe Album et je l'hydrate
            $al = (new Album)
                ->setTitle($album['title'])
                ->setPublishedAt($album['published_at']);
            
            // Un album peut avoir plusieurs Artist et attend un objet de la classe artist
            foreach ($album['artists'] as $artist) {
                // Je dois donc aller récupérer en BDD les Artists correspondant à cet album
                $artistAlbum = $this->artistRepo->findOneBy(['name' => $artist['name']]);
                // Je n'ai pas de setter mais un addArtist() car la propriété attend une collection d'objet Artist
                $al->addArtist($artistAlbum);

            }

            $manager->persist($al);
        }
        $manager->flush();

        // Je fais une boucle pour récupérer 1 music à chaque fois
        foreach ($musics as $music) {
            // Je crée un objet de la classe Music et je l'hydrate
            $m = (new Music)
                ->setName($music['name'])
                ->setDuration($music['duration']);
            
            // Je récupére en BDD l'objet Album lié à ma music.
            $albumMusic = $this->albumRepo->findOneBy(['title' => $music['album']['title'] ]);
            // Je l'ajoute à mon objet music
            $m->setAlbum($albumMusic);

            $manager->persist($m);
        }
        $manager->flush();

    }
}
