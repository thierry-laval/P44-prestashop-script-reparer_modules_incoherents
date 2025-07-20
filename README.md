# ![left 100%](https://raw.githubusercontent.com/thierry-laval/archives/master/images/logo-portfolio.png "Un bien beau logo !")

## Auteur

👤 &nbsp; **Thierry LAVAL** [🇫🇷 Contactez-moi 🇬🇧](mailto:contact@thierrylaval.dev)

* Github: [@Thierry Laval](https://github.com/thierry-laval)  
* LinkedIn: [@Thierry Laval](https://www.linkedin.com/in/thierry-laval)  
* Visitez ==> 🏠 [Site Web](https://thierrylaval.dev)

***

### 📎 Projet : Script de correction des versions des modules PrestaShop

_`Début du projet le 20/07/2025`_

***

## Description

### 🧩 Script PHP pour corriger les incohérences de versions des modules PrestaShop

Ce script PHP permet de détecter et corriger les incohérences entre les versions des modules enregistrées en base de données PrestaShop et celles présentes physiquement dans le dossier `/modules` sur le serveur FTP.

#### 🔁 Fonctionnement

* Analyse les versions des modules en base et sur FTP  
* Identifie les différences de version ou modules absents  
* Met à jour automatiquement la version en base pour qu'elle corresponde à celle du FTP  
* Génère un rapport clair pour faciliter le suivi  

***

## Prérequis

* PHP 7.0+ avec PDO MySQL activé  
* Accès à la base de données PrestaShop  
* Accès au dossier `/modules` de PrestaShop sur le serveur  

***

## Installation

1. Clonez ce dépôt ou téléchargez les fichiers  
2. Placez le script `reparer_modules_incoherents.php.php` à la racine de votre installation PrestaShop (là où se trouve le dossier `/modules`)  
3. Modifiez les paramètres de connexion à la base de données dans le script 

```bash
// 🔧 Connexion base de données - Mettez vos données
$dbHost = 'localhost';
$dbName = 'NOM_BASE_DE_DONNEES';
$dbUser = 'NOM_UTILISATEUR';
$dbPass = 'VOTRE MOT DE PASSE';
```

***

## Usage

Lancez le script via un navigateur web ou en ligne de commande :

```bash
php reparer_modules_incoherents.php.php
```

***

## 📦 Technologies utilisées

| Langages / Techs | Description                            |
|------------------|----------------------------------------|
| PHP              | Script autonome pour PrestaShop        |
| SQL              | Requêtes pour lire et modifier la base |
| PrestaShop       | Système de gestion des modules         |

***

## Limitations

* Ne gère pas l'installation ou la désinstallation des modules.  
* À utiliser uniquement après sauvegarde complète.  
* Testé sur PrestaShop 1.7, 8.x et 9.x.  

***

## Contributions bienvenues

* Forkez le projet  
* Créez une branche pour vos modifications (`git checkout -b feature/ma-fonctionnalite`)  
* Commitez vos changements (`git commit -am 'Ajout d'une fonctionnalité'`)  
* Pushez sur votre branche (`git push origin feature/ma-fonctionnalite`)  
* Ouvrez une Pull Request  

***

## 📝 Licence

Ce projet est sous licence MIT.

Copyright © 2025 Thierry Laval

***

## €€€ Soutenez-moi

Si ce projet vous plaît, n’hésitez pas à me soutenir :

<a href="https://paypal.me/thierrylaval01?country.x=FR&locale.x=fr_FR" target="_blank">  
  <img src="https://www.paypalobjects.com/digitalassets/c/website/logo/full-text/pp_fc_hl.svg" alt="Soutiens-moi !" height="35" width="150">  
</a>

[Voir mon travail](https://github.com/thierry-laval)

***

### ♥ Love Markdown

Donnez une ⭐️ si ce projet vous plaît !

<span style="font-family:Papyrus; font-size:4em;">FAN DE GITHUB !</span>

<a href="#">  
  <img src="https://github.com/thierry-laval/P00-mes-archives/blob/master/images/octocat-oley.png" height="300">  
</a>

**[⬆ Retour en haut](#auteur)**  
