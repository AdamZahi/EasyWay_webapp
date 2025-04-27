<?php

namespace App\Controller;

use App\Entity\Train;
use App\Entity\Vehicule;
use App\Repository\ConducteurRepository;
use App\Repository\LigneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\TrainRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use App\Service\SmsService;
class TrainController extends AbstractController
{
    #[Route('/train/create', name: 'create_train')]
public function create(
    Request $request,
    EntityManagerInterface $em,
    ConducteurRepository $conducteurRepo,
    LigneRepository $trajetRepo
): Response {
    $errors = [];

    if ($request->isMethod('POST')) {
        // Récupération des données du formulaire
        $immatriculation = trim($request->request->get('immatriculation'));
        $capacite = (int) $request->request->get('capacite');
        $etat = $request->request->get('etat');
        $depart = trim($request->request->get('depart'));
        $arret = trim($request->request->get('arret'));
        $nomConducteur = trim($request->request->get('nomConducteur'));
        $prenomConducteur = trim($request->request->get('prenomConducteur'));
        $longueurReseau = (float) $request->request->get('longueurReseau');
        $nombreLignes = (int) $request->request->get('nombreLignes');
        $nombreWagons = (int) $request->request->get('nombreWagons');
        $vitesseMaximale = (float) $request->request->get('vitesseMaximale');
        $proprietaire = trim($request->request->get('proprietaire'));

        // Validation
        if (empty($immatriculation)) $errors[] = "L'immatriculation est obligatoire.";
        if ($capacite <= 0) $errors[] = "La capacité doit être positive.";
        if (!in_array($etat, ['EN_SERVICE', 'HORS_SERVICE', 'EN_MAINTENANCE'])) $errors[] = "État invalide.";
        if (empty($depart) || empty($arret)) $errors[] = "Le départ et l'arrivée sont obligatoires.";
        if (empty($nomConducteur) || empty($prenomConducteur)) $errors[] = "Nom et prénom du conducteur requis.";
        if ($longueurReseau <= 0) $errors[] = "Longueur du réseau invalide.";
        if ($nombreLignes < 1) $errors[] = "Au moins une ligne est requise.";
        if ($nombreWagons < 1) $errors[] = "Nombre de wagons invalide.";
        if ($vitesseMaximale <= 0) $errors[] = "Vitesse maximale invalide.";
        if (empty($proprietaire)) $errors[] = "Propriétaire requis.";

        // Rechercher le conducteur
        $conducteur = $conducteurRepo->findOneBy([
            'nom' => $nomConducteur,
            'prenom' => $prenomConducteur,
        ]);
        if (!$conducteur) $errors[] = "Conducteur non trouvé.";

        // Rechercher le trajet
        $trajet = $trajetRepo->findTrajetByDepartAndArrivee($depart, $arret);
        if (!$trajet) $errors[] = "Trajet non trouvé.";

        if (empty($errors)) {
            // Création du véhicule
            $vehicule = new Vehicule();
            $vehicule->setImmatriculation($immatriculation);
            $vehicule->setCapacite($capacite);
            $vehicule->setEtat($etat);
            $vehicule->setTypeVehicule('TRAIN');
            $vehicule->setId_conducteur($conducteur->getId_conducteur());
            $vehicule->setIdTrajet($trajet->getId());

            $em->persist($vehicule);
            $em->flush();

            // Création du train
            $train = new Train();
            $train->setVehicule($vehicule);
            $train->setLongueurReseau($longueurReseau);
            $train->setNombreLignes($nombreLignes);
            $train->setNombreWagons($nombreWagons);
            $train->setVitesseMaximale($vitesseMaximale);
            $train->setProprietaire($proprietaire);

            $em->persist($train);
            $em->flush();

            return $this->redirectToRoute('train_success');
        }
    }

    return $this->render('back-office/vehicules/train/trainForm.html.twig', [
        'errors' => $errors,
    ]);
}


    #[Route('/train/success', name: 'train_success')]
    public function success(): Response
    {
        return $this->render('back-office/vehicules/train/success.html.twig');
    }

    #[Route('/admin/train', name: 'admin_train')]
public function index(
    TrainRepository $trainRepository,
    ConducteurRepository $conducteurRepository,
    LigneRepository $trajetRepository
): Response {
    $trains = $trainRepository->findAll();

   
    foreach ($trains as $train) {
        $vehicule = $train->getVehicule(); 
        $conducteur = $conducteurRepository->find($vehicule->getIdConducteur());
        $trajet = $trajetRepository->find($vehicule->getIdLigne());
    
        $train->conducteur = $conducteur;
        $train->trajet = $trajet;
    }

    return $this->render('back-office/vehicules/train/trainCards.html.twig', [
        'trains' => $trains,
    ]);
}
#[Route('/train/edit/{id}', name: 'edit_train')]
public function edit(
    int $id,
    Request $request,
    EntityManagerInterface $em,
    TrainRepository $trainRepo,
    ConducteurRepository $conducteurRepo,
    LigneRepository $trajetRepo,
    SmsService $smsService
): Response {
    $train = $trainRepo->find($id);

    if (!$train) {
        return new Response("Train non trouvé", 404);
    }

    $vehicule = $train->getVehicule();
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
        $longueurReseau = $request->request->get('longueurReseau');
        $nombreLignes = $request->request->get('nombreLignes');
        $nombreWagons = $request->request->get('nombreWagons');
        $vitesseMaximale = $request->request->get('vitesseMaximale');
        $proprietaire = $request->request->get('proprietaire');

        if (empty($immatriculation)) $errors[] = "L'immatriculation est requise.";
        if (!is_numeric($capacite) || $capacite <= 0) $errors[] = "La capacité doit être un nombre positif.";
        if (!in_array($etat, ['EN_SERVICE', 'HORS_SERVICE', 'EN_MAINTENANCE'])) $errors[] = "État invalide.";
        if (empty($depart)) $errors[] = "Le départ est requis.";
        if (empty($arret)) $errors[] = "L'arrêt est requis.";
        if (empty($nomConducteur)) $errors[] = "Le nom du conducteur est requis.";
        if (empty($prenomConducteur)) $errors[] = "Le prénom du conducteur est requis.";
        if (!is_numeric($longueurReseau) || $longueurReseau <= 0) $errors[] = "La longueur du réseau doit être un nombre positif.";
        if (!is_numeric($nombreLignes) || $nombreLignes <= 0) $errors[] = "Le nombre de lignes doit être un nombre positif.";
        if (!is_numeric($nombreWagons) || $nombreWagons <= 0) $errors[] = "Le nombre de wagons doit être un nombre positif.";
        if (!is_numeric($vitesseMaximale) || $vitesseMaximale <= 0) $errors[] = "La vitesse maximale doit être un nombre positif.";
        if (empty($proprietaire)) $errors[] = "Le propriétaire est requis.";

        if (!empty($errors)) {
            return $this->render('back-office/vehicules/train/trainEdit.html.twig', [
                'train' => $train,
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

        // Mise à jour du train
        $train->setLongueurReseau($longueurReseau);
        $train->setNombreLignes($nombreLignes);
        $train->setNombreWagons($nombreWagons);
        $train->setVitesseMaximale($vitesseMaximale);
        $train->setProprietaire($proprietaire);

        $em->flush();

        // Envoi du SMS si l'état a changé
        if ($oldEtat !== $etat) {
            $phoneNumber = $conducteur->getTelephonne();
            if (!empty($phoneNumber)) {
                if (strpos($phoneNumber, '+216') !== 0) {
                    $phoneNumber = '+216' . ltrim($phoneNumber, '0');
                }

                $message = "Bonjour {$conducteur->getPrenom()} {$conducteur->getNom()}, "
                         . "votre train ({$vehicule->getImmatriculation()}) est maintenant en état : $etat.";

                $smsService->sendSms($phoneNumber, $message);
            }
        }

        return $this->redirectToRoute('admin_train');
    }

    return $this->render('back-office/vehicules/train/trainEdit.html.twig', [
        'train' => $train,
        'depart' => $trajet ? $trajet->getDepart() : '',
        'arret' => $trajet ? $trajet->getArret() : '',
        'conducteur' => $conducteur,
    ]);
}



    #[Route('/train/delete/{id}', name: 'delete_train', methods: ['POST'])]
public function deleteTrain(
    int $id,
    Request $request,
    EntityManagerInterface $em,
    TrainRepository $trainRepo
): Response {
    
    $train = $trainRepo->find($id);

    if (!$train) {
        
        throw $this->createNotFoundException('Train non trouvé');
    }

    
    if ($this->isCsrfTokenValid('delete' . $train->getVehicule()->getId(), $request->request->get('_token'))) {
        
        $em->remove($train);

        
        $em->flush();

        
        $this->addFlash('success', 'Train supprimé avec succès');
    } else {
        
        $this->addFlash('error', 'Erreur de sécurité, suppression échouée');
    }

 
    return $this->redirectToRoute('admin_train');
}


}


