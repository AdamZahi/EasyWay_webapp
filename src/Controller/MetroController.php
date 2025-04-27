<?php

namespace App\Controller;

use App\Repository\MetroRepository;

use App\Entity\Metro;
use App\Entity\Vehicule;
use App\Repository\ConducteurRepository;
use App\Repository\LigneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\SmsService;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;


class MetroController extends AbstractController
{
    #[Route('/metro/create', name: 'create_metro')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ConducteurRepository $conducteurRepo,
        LigneRepository $trajetRepo
    ): Response {
        $errors = [];
    
        if ($request->isMethod('POST')) {
            $immatriculation = trim($request->request->get('immatriculation'));
            $capacite = (int) $request->request->get('capacite');
            $etat = $request->request->get('etat');
            $depart = trim($request->request->get('depart'));
            $arret = trim($request->request->get('arret'));
            $nomConducteur = trim($request->request->get('nomConducteur'));
            $prenomConducteur = trim($request->request->get('prenomConducteur'));
            $longueurReseau = (float) $request->request->get('longueurReseau');
            $nombreLignes = (int) $request->request->get('nombreLignes');
            $nombreRames = (int) $request->request->get('nombreRames');
            $proprietaire = trim($request->request->get('proprietaire'));
    
            // Validation manuelle
            if (empty($immatriculation)) $errors[] = "L'immatriculation est obligatoire.";
            if ($capacite <= 0) $errors[] = "La capacité doit être supérieure à 0.";
            if (!in_array($etat, ['EN_SERVICE', 'HORS_SERVICE', 'EN_MAINTENANCE'])) $errors[] = "État invalide.";
            if (empty($depart) || empty($arret)) $errors[] = "Le départ et l'arrivée sont obligatoires.";
            if (empty($nomConducteur) || empty($prenomConducteur)) $errors[] = "Le nom et le prénom du conducteur sont obligatoires.";
            if ($longueurReseau <= 0) $errors[] = "La longueur du réseau doit être positive.";
            if ($nombreLignes < 1) $errors[] = "Il faut au moins une ligne.";
            if ($nombreRames < 1) $errors[] = "Il faut au moins une rame.";
            if (empty($proprietaire)) $errors[] = "Le propriétaire est obligatoire.";
    
            $conducteur = $conducteurRepo->findOneBy([
                'nom' => $nomConducteur,
                'prenom' => $prenomConducteur,
            ]);
    
            if (!$conducteur) $errors[] = "Conducteur non trouvé.";
    
            $trajet = $trajetRepo->findTrajetByDepartAndArrivee($depart, $arret);
            if (!$trajet) $errors[] = "Trajet non trouvé.";
    
            // Si pas d'erreurs => enregistrement
            if (empty($errors)) {
                $vehicule = new Vehicule();
                $vehicule->setImmatriculation($immatriculation);
                $vehicule->setCapacite($capacite);
                $vehicule->setEtat($etat);
                $vehicule->setTypeVehicule('METRO');
                $vehicule->setId_conducteur($conducteur->getIdConducteur());
                $vehicule->setIdLigne($trajet->getId());
    
                $em->persist($vehicule);
                $em->flush();
    
                $metro = new Metro();
                $metro->setId($vehicule);
                $metro->setLongueurReseau($longueurReseau);
                $metro->setNombreLignes($nombreLignes);
                $metro->setNombreRames($nombreRames);
                $metro->setProprietaire($proprietaire);
    
                $em->persist($metro);
                $em->flush();
    
                return $this->redirectToRoute('metro_success');
            }
        }
    
        return $this->render('back-office/vehicules/metro/metroForm.html.twig', [
            'errors' => $errors,
        ]);
    }
    #[Route('/metro/success', name: 'metro_success')]
    public function success(): Response
    {
        return $this->render('back-office/vehicules/metro/success.html.twig');
    }

    


    #[Route('/admin/metro', name: 'admin_metro')]
public function index(
    MetroRepository $metroRepository,
    ConducteurRepository $conducteurRepository,
    LigneRepository $trajetRepository,
    PaginatorInterface $paginator,
    Request $request
): Response {
    // Requête de base sans jointures
    $query = $metroRepository->createQueryBuilder('m')
        ->getQuery();

    // Configuration spéciale pour la pagination
    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        2,
        [
            'wrap-queries' => true,
            'distinct' => false
        ]
    );

    

    // Hydratation manuelle des relations
    foreach ($pagination as $metro) {
        $vehicule = $metro->getId(); // Accès au véhicule via la relation existante
        if ($vehicule) {
            $metro->conducteur = $conducteurRepository->find($vehicule->getIdConducteur());
            $metro->trajet = $trajetRepository->find($vehicule->getIdLigne());
        }
    }

    return $this->render('back-office/vehicules/metro/metroCards.html.twig', [
        'pagination' => $pagination,
    ]);
}

#[Route('/metro/edit/{id}', name: 'edit_metro')]
public function edit(
    int $id,
    Request $request,
    EntityManagerInterface $em,
    MetroRepository $metroRepo,
    ConducteurRepository $conducteurRepo,
    LigneRepository $trajetRepo,
    SmsService $smsService
): Response {
    $metro = $metroRepo->find($id);

    if (!$metro) {
        return new Response("Métro non trouvé", 404);
    }

    $vehicule = $metro->getId();
    $conducteur = $conducteurRepo->find($vehicule->getIdConducteur());
    $trajet = $trajetRepo->find($vehicule->getIdLigne());

    // Sauvegarder l'état avant modification
    $oldEtat = $vehicule->getEtat();

    if ($request->isMethod('POST')) {
        $errors = [];

        $immatriculation = $request->request->get('immatriculation');
        $capacite = $request->request->get('capacite');
        $etat = $request->request->get('etat');
        $depart = $request->request->get('depart');
        $arret = $request->request->get('arret');
        $nomConducteur = $request->request->get('nomConducteur');
        $prenomConducteur = $request->request->get('prenomConducteur');
        $nombreRames = $request->request->get('nombreRames');
        $nombreLignes = $request->request->get('nombreLignes');
        $longueurReseau = $request->request->get('longueurReseau');
        $proprietaire = $request->request->get('proprietaire');

        // Validation
        if (empty($immatriculation)) $errors[] = "L'immatriculation est requise.";
        if (!is_numeric($capacite) || $capacite <= 0) $errors[] = "La capacité doit être un nombre positif.";
        if (!in_array($etat, ['EN_SERVICE', 'HORS_SERVICE', 'EN_MAINTENANCE'])) $errors[] = "État invalide.";
        if (empty($depart)) $errors[] = "Le départ est requis.";
        if (empty($arret)) $errors[] = "L'arrêt est requis.";
        if (empty($nomConducteur)) $errors[] = "Le nom du conducteur est requis.";
        if (empty($prenomConducteur)) $errors[] = "Le prénom du conducteur est requis.";
        if (!is_numeric($nombreRames) || $nombreRames <= 0) $errors[] = "Le nombre de rames doit être un nombre positif.";
        if (!is_numeric($nombreLignes) || $nombreLignes <= 0) $errors[] = "Le nombre de lignes doit être un nombre positif.";
        if (!is_numeric($longueurReseau) || $longueurReseau <= 0) $errors[] = "La longueur du réseau doit être un nombre positif.";
        if (empty($proprietaire)) $errors[] = "Le propriétaire est requis.";

        if (!empty($errors)) {
            return $this->render('back-office/vehicules/metro/metroEdit.html.twig', [
                'metro' => $metro,
                'vehicule' => $vehicule,
                'depart' => $depart,
                'arret' => $arret,
                'conducteur' => $conducteur,
                'errors' => $errors,
            ]);
        }

        // Mise à jour du véhicule
        $vehicule->setImmatriculation($immatriculation);
        $vehicule->setCapacite($capacite);
        $vehicule->setEtat($etat);

        // Mise à jour du trajet
        $trajet = $trajetRepo->findOneBy(['depart' => $depart, 'arret' => $arret]);

        if (!$trajet) {
            $trajet = new \App\Entity\Trajet();
            $trajet->setDepart($depart);
            $trajet->setArret($arret);
            $em->persist($trajet);
            $em->flush();
        }

        $vehicule->setIdLigne($trajet->getId());

        // Mise à jour du conducteur
        $conducteur = $conducteurRepo->findOneBy(['nom' => $nomConducteur, 'prenom' => $prenomConducteur]);

        if (!$conducteur) {
            $conducteur = new \App\Entity\Conducteur();
            $conducteur->setNom($nomConducteur);
            $conducteur->setPrenom($prenomConducteur);
            $em->persist($conducteur);
            $em->flush();
        }

        $vehicule->setIdConducteur($conducteur->getIdConducteur());

        // Mise à jour du métro
        $metro->setNombreRames($nombreRames);
        $metro->setNombreLignes($nombreLignes);
        $metro->setLongueurReseau($longueurReseau);
        $metro->setProprietaire($proprietaire);

        $em->flush();

        // Envoi du SMS si l'état a changé
        if ($oldEtat !== $etat) {
            $phoneNumber = $conducteur->getTelephonne();
            if (!empty($phoneNumber)) {
                if (strpos($phoneNumber, '+216') !== 0) {
                    $phoneNumber = '+216' . ltrim($phoneNumber, '0');
                }

                $message = "Bonjour {$conducteur->getPrenom()} {$conducteur->getNom()}, "
                         . "votre métro ({$vehicule->getImmatriculation()}) est maintenant en état : $etat.";

                $smsService->sendSms($phoneNumber, $message);
            }
        }

        return $this->redirectToRoute('admin_metro');
    }

    return $this->render('back-office/vehicules/metro/metroEdit.html.twig', [
        'metro' => $metro,
        'vehicule' => $vehicule,
        'depart' => $trajet ? $trajet->getDepart() : '',
        'arret' => $trajet ? $trajet->getArret() : '',
        'conducteur' => $conducteur,
    ]);
}



#[Route('/metro/delete/{id}', name: 'delete_metro', methods: ['POST'])]
public function deleteMetro(
    int $id,
    Request $request,
    EntityManagerInterface $em,
    MetroRepository $metroRepo
): Response {
    
    $metro = $metroRepo->find($id);

    if (!$metro) {

        throw $this->createNotFoundException('Métro non trouvé');
    }

    
    if ($this->isCsrfTokenValid('delete' . $metro->getId()->getId(), $request->request->get('_token'))) {
        
        $em->remove($metro);

       
        $em->flush();

       
        $this->addFlash('success', 'Métro supprimé avec succès');
    } else {
        
    }

    
    return $this->redirectToRoute('admin_metro');
}


}
