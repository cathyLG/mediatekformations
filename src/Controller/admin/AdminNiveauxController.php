<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Entity\Niveau;
use App\Form\NiveauType;
use App\Repository\FormationRepository;
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
     *
     * @var FormationRepository
     */
    private $repositoryF;

    /**
     * constructeur
     * @param NiveauRepository $repository
     * @param \App\Controller\admin\EntityManagerInterface $om
     */
    public function __construct(NiveauRepository $repository, EntityManagerInterface $om, FormationRepository $repositoryF) {
        $this->repository = $repository;
        $this->om = $om;
        $this->repositoryF = $repositoryF;
    }

    /**
     * @Route("/admin/niveaux", name="admin.niveaux")
     * @return Response
     */
    public function index(Request $request): Response {
        $niveaux = $this->repository->findAll();
        $niveau = new Niveau();
        $formNiveau = $this->createForm(NiveauType::class, $niveau);
        // en cas d'enregistrement d'un nouveau niveau
        $formNiveau->handleRequest($request);
        if ($formNiveau->isSubmitted() && $formNiveau->isValid()) {
            $this->om->persist($niveau);
            $this->om->flush();
            return $this->redirectToRoute('admin.niveaux');
        }
        return $this->render(self::PAGENIVEAUX, [
                    'niveaux' => $niveaux,
                    'formNiveau' => $formNiveau->createView()
        ]);
    }

    /**
     * @Route("/admin/niveau/suppr/{id}", name="admin.niveau.suppr")
     * @param Niveau $niveau
     * @return Response
     */
    public function suppr(Niveau $niveau): Response {
        // vérifier si le niveau est déjà utilisé par une formation
        $formations = $this->repositoryF->findAll();
        $found = false;
        foreach ($formations as $formation) {
            if ($formation->getNiveau() == $niveau) {
                $found = true;
                break;
            }
        }
        if ($found) {
            $message = 'Suppression impossible : le niveau "' . $niveau->getNom() . '" est déjà utilisé par une formation';
            $this->addFlash('alert',$message);
        } else {
            // suppression
            $this->om->remove($niveau);
            $this->om->flush();
        }
        return $this->redirectToRoute('admin.niveaux');
    }

}
