<?php

namespace App\Controller\admin;

use App\Entity\Niveau;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminNiveauxController extends AbstractController {

    private const PAGENIVEAUX = "admin/admin.niveaux.html.twig";

    /**
     *
     * @var NiveauRepository
     */
    private $repository;

    /**
     * constructeur
     * @param NiveauRepository $repository
     * @param \App\Controller\admin\EntityManagerInterface $om
     */
    public function __construct(NiveauRepository $repository, EntityManagerInterface $om) {
        $this->repository = $repository;
        $this->om = $om;
    }

    /**
     * @Route("/admin/niveaux", name="admin.niveaux")
     * @return Response
     */
    public function index(): Response {
        $niveaux = $this->repository->findAll();
        return $this->render(self::PAGENIVEAUX, [
                    'niveaux' => $niveaux
        ]);
    }

    /**
     * @Route("/admin/niveau/suppr/{id}", name="admin.niveau.suppr")
     * @param Niveau $niveau
     * @return Response
     */
    public function suppr(Niveau $niveau): Response {
        $this->om->remove($niveau);
        $this->om->flush();
        return $this->redirectToRoute('admin.niveaux');
    }

}
