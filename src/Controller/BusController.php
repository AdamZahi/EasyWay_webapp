<?php

namespace App\Controller;

use App\Repository\BusRepository;

use App\Entity\Bus;
use App\Entity\Vehicule;
use App\Repository\ConducteurRepository;
use App\Repository\LigneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


class BusController extends AbstractController
{
    
#[Route('/bus/create', name: 'create_bus')]
public function create(
    Request $request,
    EntityManagerInterface $em,
    ConducteurRepository $conducteurRepo,
    LigneRepository $trajetRepo
): Response {
    $errors = [];

    if ($request->isMethod('POST')) {
        $immatriculation = $request->request->get('immatriculation');
        $capacite = $request->request->get('capacite');
        $etat = $request->request->get('etat');
        $depart = $request->request->get('depart');
        $arret = $request->request->get('arret');
        $nomConducteur = $request->request->get('nomConducteur');
        $prenomConducteur = $request->request->get('prenomConducteur');
        $nombrePortes = $request->request->get('nombrePortes');
        $typeService = $request->request->get('typeService');
        $nombreDePlaces = $request->request->get('nombreDePlaces');
        $compagnie = $request->request->get('compagnie');
        $climatisation = $request->request->get('climatisation');

        // === Contrôle de saisie ===
        if (empty($immatriculation)) $errors[] = "L'immatriculation est obligatoire.";
        if (!is_numeric($capacite) || $capacite < 0) $errors[] = "La capacité doit être un nombre positif.";
        if (!in_array($etat, ['EN_SERVICE', 'HORS_SERVICE', 'EN_MAINTENANCE'])) $errors[] = "État invalide.";
        if (empty($depart)) $errors[] = "Le champ départ est obligatoire.";
        if (empty($arret)) $errors[] = "Le champ arrivée est obligatoire.";
        if (empty($nomConducteur)) $errors[] = "Le nom du conducteur est obligatoire.";
        if (empty($prenomConducteur)) $errors[] = "Le prénom du conducteur est obligatoire.";
        if (!is_numeric($nombrePortes) || $nombrePortes < 1) $errors[] = "Le nombre de portes doit être supérieur à 0.";
        if (!in_array($typeService, ['URBAIN', 'INTERURBAIN'])) $errors[] = "Type de service invalide.";
        if (!is_numeric($nombreDePlaces) || $nombreDePlaces < 1) $errors[] = "Le nombre de places doit être supérieur à 0.";
        if (empty($compagnie)) $errors[] = "La compagnie est obligatoire.";
        if (!in_array($climatisation, ['0', '1'])) $errors[] = "Climatisation invalide.";

        // Vérifier si conducteur et trajet existent
        $conducteur = $conducteurRepo->findOneBy([
            'nom' => $nomConducteur,
            'prenom' => $prenomConducteur,
        ]);
        if (!$conducteur) {
            $errors[] = "Conducteur non trouvé.";
        }

        $trajet = $trajetRepo->findTrajetByDepartAndArrivee($depart, $arret);
        if (!$trajet) {
            $errors[] = "Trajet non trouvé.";
        }

        // === Si tout est OK ===
        if (empty($errors)) {
            $vehicule = new Vehicule();
            $vehicule->setImmatriculation($immatriculation);
            $vehicule->setCapacite($capacite);
            $vehicule->setEtat($etat);
            $vehicule->setTypeVehicule('BUS');
            $vehicule->setId_conducteur($conducteur->getIdConducteur());
            $vehicule->setIdLigne($trajet->getId());

            $em->persist($vehicule);
            $em->flush();

            $bus = new Bus();
            $bus->setVehicule($vehicule);
            $bus->setNombrePortes($nombrePortes);
            $bus->setTypeService($typeService);
            $bus->setNombreDePlaces($nombreDePlaces);
            $bus->setCompagnie($compagnie);
            $bus->setClimatisation((bool) $climatisation);

            $em->persist($bus);
            $em->flush();

            $this->addFlash('success', 'Bus ajouté avec succès.');
            return $this->redirectToRoute('bus_success');
        }
    }

    return $this->render('back-office/vehicules/bus/busForm.html.twig', [
        'errors' => $errors
    ]);
}
#[Route('/bus/success', name: 'bus_success')]
    public function success(): Response
    {
        return $this->render('back-office/vehicules/bus/success.html.twig');
    }

    #[Route('/admin/bus', name: 'admin_bus')]
    public function index(
        BusRepository $busRepository,
        ConducteurRepository $conducteurRepository,
        LigneRepository $trajetRepository
    ): Response {
        $buses = $busRepository->findAll();
    
        foreach ($buses as $bus) {
            $vehicule = $bus->getVehicule(); // C’est l’objet Vehicule
            $conducteur = $conducteurRepository->find($vehicule->getId_conducteur());
            $trajet = $trajetRepository->find($vehicule->getIdLigne());
    
            // On attache les objets manuellement
            $bus->conducteur = $conducteur;
            $bus->trajet = $trajet;
        }
    
        return $this->render('back-office/vehicules/bus/busCards.html.twig', [
            'buses' => $buses,
        ]);
    }

    

    #[Route('/bus/edit/{id}', name: 'edit_bus')]
public function edit(
    int $id,
    Request $request,
    EntityManagerInterface $em,
    BusRepository $busRepo,
    ConducteurRepository $conducteurRepo,
    LigneRepository $trajetRepo
): Response {
    $bus = $busRepo->find($id);

    if (!$bus) {
        return new Response("Bus non trouvé", 404);
    }

    $vehicule = $bus->getVehicule();
    $conducteur = $conducteurRepo->find($vehicule->getId_conducteur());
    $trajet = $trajetRepo->find($vehicule->getIdLigne());

    if ($request->isMethod('POST')) {
        
        $immatriculation = trim($request->request->get('immatriculation'));
        $capacite = (int) $request->request->get('capacite');
        $etat = $request->request->get('etat');
        $depart = trim($request->request->get('depart'));
        $arret = trim($request->request->get('arret'));
        $nomConducteur = trim($request->request->get('nomConducteur'));
        $prenomConducteur = trim($request->request->get('prenomConducteur'));
        $nombrePortes = (int) $request->request->get('nombrePortes');
        $typeService = $request->request->get('typeService');
        $nombreDePlaces = (int) $request->request->get('nombreDePlaces');
        $compagnie = trim($request->request->get('compagnie'));
        $climatisation = $request->request->get('climatisation');

        
        $errors = [];

        if (empty($immatriculation)) $errors[] = "L'immatriculation est requise.";
        if ($capacite <= 0) $errors[] = "La capacité doit être supérieure à zéro.";
        if (!in_array($etat, ['EN_SERVICE', 'HORS_SERVICE', 'EN_MAINTENANCE'])) $errors[] = "État invalide.";
        if (empty($depart)) $errors[] = "Le lieu de départ est requis.";
        if (empty($arret)) $errors[] = "Le lieu d'arrivée est requis.";
        if (empty($nomConducteur)) $errors[] = "Le nom du conducteur est requis.";
        if (empty($prenomConducteur)) $errors[] = "Le prénom du conducteur est requis.";
        if ($nombrePortes <= 0) $errors[] = "Le nombre de portes doit être supérieur à zéro.";
        if (!in_array($typeService, ['URBAIN', 'INTERURBAIN'])) $errors[] = "Type de service invalide.";
        if ($nombreDePlaces <= 0) $errors[] = "Le nombre de places doit être supérieur à zéro.";
        if (empty($compagnie)) $errors[] = "La compagnie est requise.";
        if (!in_array($climatisation, ['0', '1'])) $errors[] = "Valeur de climatisation invalide.";

        
        if (!empty($errors)) {
            return $this->render('back-office/vehicules/bus/busEdit.html.twig', [
                'bus' => $bus,
                'depart' => $depart,
                'arret' => $arret,
                'conducteur' => $conducteur,
                'errors' => $errors,
            ]);
        }

        
        $vehicule->setImmatriculation($immatriculation);
        $vehicule->setCapacite($capacite);
        $vehicule->setEtat($etat);

        
        $trajet = $trajetRepo->findOneBy(['depart' => $depart, 'arret' => $arret]);
        if (!$trajet) {
            $trajet = new Trajet();
            $trajet->setDepart($depart);
            $trajet->setArret($arret);
            $em->persist($trajet);
            $em->flush();
        }
        $vehicule->setIdLigne($trajet->getId());

        
        $conducteur = $conducteurRepo->findOneBy(['nom' => $nomConducteur, 'prenom' => $prenomConducteur]);
        if (!$conducteur) {
            $conducteur = new Conducteur();
            $conducteur->setNom($nomConducteur);
            $conducteur->setPrenom($prenomConducteur);
            $em->persist($conducteur);
            $em->flush();
        }
        $vehicule->setId_conducteur($conducteur->getIdConducteur());

        
        $bus->setNombrePortes($nombrePortes);
        $bus->setTypeService($typeService);
        $bus->setNombreDePlaces($nombreDePlaces);
        $bus->setCompagnie($compagnie);
        $bus->setClimatisation((bool)$climatisation);

        $em->flush();

        return $this->redirectToRoute('admin_bus');
    }

    return $this->render('back-office/vehicules/bus/busEdit.html.twig', [
        'bus' => $bus,
        'depart' => $trajet ? $trajet->getDepart() : '',
        'arret' => $trajet ? $trajet->getArret() : '',
        'conducteur' => $conducteur,
    ]);
}

    #[Route('/bus/delete/{id}', name: 'delete_bus', methods: ['POST'])]
public function delete(
    int $id,
    Request $request,
    EntityManagerInterface $em,
    BusRepository $busRepo
): Response {
    $bus = $busRepo->find($id);
    
    if (!$bus) {
        throw $this->createNotFoundException('Bus non trouvé');
    }

    
    if ($this->isCsrfTokenValid('delete'.$bus->getVehicule()->getId(), $request->request->get('_token'))) {
        $em->remove($bus);
        $em->remove($bus->getVehicule()); 
        $em->flush();

        $this->addFlash('success', 'Bus supprimé avec succès');
    }

    return $this->redirectToRoute('admin_bus');
}


    
    
}
