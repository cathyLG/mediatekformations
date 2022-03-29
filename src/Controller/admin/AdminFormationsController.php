<?php

namespace App\Controller\admin;

use App\Repository\FormationRepository;
use App\Entity\Formation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Niveau;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\FormationType;

/**
 * Description of AdminFormationsController
 *
 * @author Xiaoxiao LIU
 */
class AdminFormationsController extends AbstractController {

    /**
     * page à afficher
     */
    private const PAGEFORMATIONS = "admin/admin.formations.html.twig";

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
     * @var EntityManagerInterface
     */
    private $om;

    /**
     * 
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository, NiveauRepository $repositoryN, EntityManagerInterface $om) {
        $this->repository = $repository;
        $this->niveaux = $repositoryN->findAll();
        $this->om = $om;
    }

    /**
     * @Route("/admin", name="admin.formations")
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
     * @Route("/admin/formations/tri/{champ}/{ordre}", name="admin.formations.sort")
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
     * @Route("/admin/formations/recherche/{champ}", name="admin.formations.findallcontain")
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
        return $this->redirectToRoute("admin.formations");
    }

    /**
     * @Route("/admin/formations/formation/{id}", name="admin.formations.showone")
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

    /**
     * à modifier 
     * @Route("/admin/niveaux", name="admin.niveaux")
     * @return Response
     */
    public function gestionNiveaux(): Response {
        $formations = $this->repository->findAll();
        return $this->render(self::PAGEFORMATIONS, [
                    'formations' => $formations,
                    'niveaux' => $this->niveaux
        ]);
    }

    /**
     * @Route("/admin/tri/{champ}/{valeur}", name="admin.formations.findallequal")
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
     * @Route("/admin/suppr/{id}", name="admin.formation.suppr")
     * @param Formation $formation
     * @return Response
     */
    public function suppr(Formation $formation): Response {
        $this->om->remove($formation);
        $this->om->flush();
        return $this->redirectToRoute('admin.formations');
    }

    /**
     * @Route("/admin/edit/{id}", name="admin.formation.edit")
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */
    public function edit(Formation $formation, Request $request): Response {
        $formFormation = $this->createForm(FormationType::class, $formation);

        $formFormation->handleRequest($request);
        if ($formFormation->isSubmitted() && $formFormation->isValid()) {
            $this->om->flush();
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render("admin/admin.formation.edit.html.twig", [
                    'formation' => $formation,
                    'formFormation' => $formFormation->createView()
        ]);
    }

}
