<?php

namespace App;

use App\Entity\Theme;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

class ThemeList{
    private $em;
        public function __construct(EntityManagerInterface  $em){
            $this->em = $em;
        }
        public function home()
        {
            $theme=$this->em->getRepository(Theme::class);
            $listeTheme=$theme->findAll();
            return  $listeTheme;
            
        }

    }



?>