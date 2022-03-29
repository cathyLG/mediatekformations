<?php

namespace App\Controller\admin;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Niveau;
use App\Repository\NiveauRepository;

/**
 * Description of AdminFormationsController
 *
 * @author Xiaoxiao LIU
 */
class AdminFormationsController extends AbstractController {

    /**
     *
     * @var FormationRepository
     */
    private $repository;

    /**
     * @var Niveau[]
     */
    private $niveaux;

    /**
     * 
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository, NiveauRepository $repositoryN) {
        $this->repository = $repository;
        $this->niveaux = $repositoryN->findAll();
    }

    /**
     * @Route("/admin", name="admin.formations")
     * @return Response
     */
    public function index(): Response {
        $formations = $this->repository->findAll();
        return $this->render("admin/admin.formations.html.twig", [
                    'formations' => $formations,
                    'niveaux' => $this->niveaux
        ]);
    }

    /**
     * à modifier 
     * @Route("/admin/niveaux", name="admin.niveaux")
     * @return Response
     */
    public function gestionNiveaux(): Response {
        $formations = $this->repository->findAll();
        return $this->render("admin/admin.formations.html.twig", [
                    'formations' => $formations,
                    'niveaux' => $this->niveaux
        ]);
    }

}
