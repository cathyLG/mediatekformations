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
    private const PAGEADMINFORMATIONS = "admin/admin.formations.html.twig";

    /**
     *
     * @var FormationRepository
     */
    private $repositoryF;

    /**
     * @var NiveauRepository
     */
    private $repositoryN;

    /**
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * 
     * @param FormationRepository $repositoryF
     */
    public function __construct(FormationRepository $repositoryF, NiveauRepository $repositoryN, EntityManagerInterface $em) {
        $this->repositoryF = $repositoryF;
        $this->repositoryN = $repositoryN;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.formations")
     * @return Response
     */
    public function index(): Response {
        $formations = $this->repositoryF->findAll();
        $niveaux = $this->repositoryN->findAll();
        return $this->render(self::PAGEADMINFORMATIONS, [
                    'formations' => $formations,
                    'niveaux' => $niveaux
        ]);
    }

    /**
     * @Route("/admin/formations/tri/{champ}/{ordre}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response {
        $formations = $this->repositoryF->findAllOrderBy($champ, $ordre);
        $niveaux = $this->repositoryN->findAll();
        return $this->render(self::PAGEADMINFORMATIONS, [
                    'formations' => $formations,
                    'niveaux' => $niveaux
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
            $formations = $this->repositoryF->findByContainValue($champ, $valeur);
            $niveaux = $this->repositoryN->findAll();
            return $this->render(self::PAGEADMINFORMATIONS, [
                        'formations' => $formations,
                        'niveaux' => $niveaux
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
        $formation = $this->repositoryF->find($id);
        $niveaux = $this->repositoryN->findAll();
        return $this->render("pages/formation.html.twig", [
                    'formation' => $formation,
                    'niveaux' => $niveaux
        ]);
    }

    /**
     * @Route("/admin/tri/{champ}/{valeur}", name="admin.formations.findallequal")
     * @param type $champ
     * @param Request $request
     * @return Response
     */
    public function findAllEqual($champ, $valeur): Response {
        $formations = $this->repositoryF->findByEqualValue($champ, $valeur);
        $niveaux = $this->repositoryN->findAll();
        return $this->render(self::PAGEADMINFORMATIONS, [
                    'formations' => $formations,
                    'niveaux' => $niveaux
        ]);
    }

    /**
     * @Route("/admin/suppr/{id}", name="admin.formation.suppr")
     * @param Formation $formation
     * @return Response
     */
    public function suppr(Formation $formation): Response {
        $this->em->remove($formation);
        $this->em->flush();
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
            $this->em->flush();
            return $this->redirectToRoute('admin.formations');
        }

        return $this->render("admin/admin.formation.edit.html.twig", [
                    'formation' => $formation,
                    'formFormation' => $formFormation->createView()
        ]);
    }

    /**
     * @Route("/admin/formation/ajout", name="admin.formation.ajout")
     * @param Formation $formation
     * @param Request $request
     * @return Response
     */
    public function ajout(Request $request): Response {
        $formation = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);

        $formFormation->handleRequest($request);
        if ($formFormation->isSubmitted() && $formFormation->isValid()) {
            $this->em->persist($formation);
            $this->em->flush();
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/admin.formation.ajout.html.twig", [
                    'formation' => $formation,
                    'formFormation' => $formFormation->createView()
        ]);
    }

}
