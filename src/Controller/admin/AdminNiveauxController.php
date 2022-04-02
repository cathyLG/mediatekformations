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

/**
 * contrôleur admin pour les niveaux
 */
class AdminNiveauxController extends AbstractController {

    private const PAGEADMINNIVEAUX = "admin/admin.niveaux.html.twig";

    /**
     *
     * @var NiveauRepository
     */
    private $repositoryN;

    /**
     *
     * @var FormationRepository
     */
    private $repositoryF;

    /**
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * constructeur
     * @param NiveauRepository $repositoryN
     * @param EntityManagerInterface $em
     * @param FormationRepository $repositoryF
     */
    public function __construct(NiveauRepository $repositoryN, EntityManagerInterface $em, FormationRepository $repositoryF) {
        $this->repositoryN = $repositoryN;
        $this->em = $em;
        $this->repositoryF = $repositoryF;
    }

    /**
     * @Route("/admin/niveaux", name="admin.niveaux")
     * @return Response
     */
    public function index(Request $request): Response {
        $niveaux = $this->repositoryN->findAll();
        $niveau = new Niveau();
        $formNiveau = $this->createForm(NiveauType::class, $niveau);
        // en cas d'enregistrement d'un nouveau niveau
        $formNiveau->handleRequest($request);
        if ($formNiveau->isSubmitted() && $formNiveau->isValid()) {
            $this->em->persist($niveau);
            $this->em->flush();
            return $this->redirectToRoute('admin.niveaux');
        }
        return $this->render(self::PAGEADMINNIVEAUX, [
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
            $this->addFlash('alert', $message);
        } else {
            // suppression
            $this->em->remove($niveau);
            $this->em->flush();
        }
        return $this->redirectToRoute('admin.niveaux');
    }

}
