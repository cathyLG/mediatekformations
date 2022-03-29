<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormationRepository;
use App\Repository\NiveauRepository;
use App\Entity\Niveau;

/**
 * Description of FormationsController
 *
 * @author emds
 */
class FormationsController extends AbstractController {

    private const PAGEFORMATIONS = "pages/formations.html.twig";

    /**
     *
     * @var FormationRepository
     */
    private $repository;

    /**
     *
     * @var Niveau[]
     */
    private $niveaux;

    /**
     * 
     * @param FormationRepository $repository
     */
    function __construct(FormationRepository $repository, NiveauRepository $repositoryN) {
        $this->repository = $repository;
        $this->niveaux = $repositoryN->findAll();
    }

    /**
     * @Route("/formations", name="formations")
     * @return Response
     */
    public function index(): Response {
        $formations = $this->repository->findAll();
        return $this->render(self::PAGEFORMATIONS, [
                    'formations' => $formations,
                    'niveaux' => $this->niveaux
        ]);
    }

    /**
     * @Route("/formations/tri/{champ}/{ordre}", name="formations.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response {
        $formations = $this->repository->findAllOrderBy($champ, $ordre);
        return $this->render(self::PAGEFORMATIONS, [
                    'formations' => $formations,
                    'niveaux' => $this->niveaux
        ]);
    }

    /**
     * @Route("/formations/recherche/{champ}", name="formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllContain($champ, Request $request): Response {
        if ($this->isCsrfTokenValid('filtre_' . $champ, $request->get('_token'))) {
            $valeur = $request->get("recherche");
            $formations = $this->repository->findByContainValue($champ, $valeur);
            return $this->render(self::PAGEFORMATIONS, [
                        'formations' => $formations,
                        'niveaux' => $this->niveaux
            ]);
        }
        return $this->redirectToRoute("formations");
    }

    /**
     * @Route("/formations/recherche/{champ}/{valeur}", name="formations.findallequal")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllEqual($champ, $valeur): Response {
        $formations = $this->repository->findByEqualValue($champ, $valeur);
        return $this->render(self::PAGEFORMATIONS, [
                    'formations' => $formations,
                    'niveaux' => $this->niveaux
        ]);
    }

    /**
     * @Route("/formations/formation/{id}", name="formations.showone")
     * @param type $id
     * @return Response
     */
    public function showOne($id): Response {
        $formation = $this->repository->find($id);
        return $this->render("pages/formation.html.twig", [
                    'formation' => $formation,
                    'niveaux' => $this->niveaux
        ]);
    }

}
