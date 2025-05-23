# EasyWay_webapp

# 🚗 Easy Way – Web App de Gestion de Transport Intelligent

## 🧭 Aperçu

Easy Way est une application web développée avec **Symfony** et **MySQL** (via **XAMPP**) dans le cadre du projet universitaire **PIDEV 3A** à *Esprit School of Engineering*. Elle vise à digitaliser et optimiser la gestion du transport collectif, incluant la gestion des trajets, des véhicules, des utilisateurs, des réclamations, du covoiturage et des événements liés aux lignes de transport (grèves, incidents, retards).

---

## 📑 Table des Matières

- [Fonctionnalités](#-fonctionnalités)
- [Architecture Technique](#-architecture-technique)
- [Installation](#-installation)
- [Utilisation](#-utilisation)
- [Contribuer](#-contribuer)
- [Licence](#-licence)
- [Crédits](#-crédits)

---

## ✅ Fonctionnalités

### 🔐 Gestion des utilisateurs

- Hashage sécurisé des mots de passe
- Authentification via Google
- Validation d’email
- Réinitialisation de mot de passe
- Protection via reCAPTCHA

### 🗺️ Gestion des trajets

- Carte dynamique avec **OpenStreetMap API**
- Suivi en temps réel des trajets
- Réservation avec intégration **Stripe**
- Gestion des lignes par les administrateurs

### 🚗 Gestion des véhicules

- CRUD véhicules (ajout, modification, suppression)
- Association véhicule–ligne de transport

### 📨 Gestion des réclamations

- Système de soumission et réponse aux réclamations
- Envoi de notifications par email
- Statistiques détaillées
- Chatbot pour guider les utilisateurs dans la soumission

### 🚘 Gestion du covoiturage

- Réservation de places
- Confirmation de réservation par email
- Paiement via Stripe

### 📢 Gestion des événements

- Notifications d’événements (grève, incident, retard) par email
- Statistiques des événements
- Recherche dynamique et filtrée

---

## 🧰 Architecture Technique

- **Backend** : Symfony (PHP 8.x)
- **Base de données** : MySQL via XAMPP
- **Frontend** : Twig + HTML/CSS/JS
- **API externes** :
  - OpenStreetMap API
  - reCAPTCHA Google
  - Stripe (paiement)
  - SMTP pour emails
  - Google OAuth2

---

## ⚙️ Installation

1. **Cloner le projet :**
   ```bash
   git clone https://https://github.com/AdamZahi/EasyWay_webapp
   cd EasyWay_webapp
   ```

2. **Configurer la base de données MySQL :**
   - Lancer XAMPP, activer Apache et MySQL.
   - Créer une base de données `easy_way` dans phpMyAdmin.

3. **Installer les dépendances PHP :**
   ```bash
   composer install
   ```

4. **Configurer le fichier `.env` :**
   - Renseigner les informations MySQL, Stripe, reCAPTCHA, etc.

5. **Lancer le serveur Symfony :**
   ```bash
   symfony server:start
   ```

---

## ▶️ Utilisation

- Accéder à l'application via `http://localhost:8000`
- Inscription et connexion avec ou sans Google
- Utiliser les modules via l’interface admin ou utilisateur

---

## 🤝 Contribuer

Les contributions sont les bienvenues ! Voici comment démarrer :

1. Fork le repo
2. Crée ta branche : `git checkout -b feature/ma-feature`
3. Commit : `git commit -m "Ajoute ma fonctionnalité"`
4. Push : `git push origin feature/ma-feature`
5. Crée une Pull Request

---

## 📜 Licence

Ce projet est sous licence **MIT** – voir le fichier [LICENSE](LICENSE) pour plus de détails.

---

## 🙏 Crédits

Développé dans le cadre du module **PIDEV 3A** à [Esprit School of Engineering](https://esprit.tn)  
Encadré par : Mme Linda Ouerfelli - Mme Yassmine Maazoun
