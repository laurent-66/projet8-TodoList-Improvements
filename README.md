# Application web To do list

## A propos

Cette application web permet d'écrire des tâches, elles peuvent être indiquées comme validées ou à faire. Chaque tâche à un intitulé et un contenue.
Chaque utlisateur connecté pourra effectué des actions de création, modification et de suppression de ses propres tâches.

## Versions utilisées
* Version utilisé avec symfony pour ce projet PHP 8.1.11
* Version de symfony 5.4.11 LTS
* Symfony CLI version v5.4.14

## Mise en place de l'environnement de travail pour une installation locale

* Installez le gestionnaire de versions de fichiers GIT  https://git-scm.com/downloads
* Installez l'environnement de développement pour PHP et MySQL sur votre ordinateur avec XAMPP https://www.apachefriends.org/fr/index.html
* Installez le gestionnaire de dépendances de PHP : composer https://getcomposer.org/download/
* Installez NodeJS et NPM https://nodejs.org/en/
* Installez l'interpréteur de commandes symfony (CLI Symfony)

### Testez votre configuration

1. Ouvrez	votre terminal
2. Tapez	la commande **git** et	assurez vous qu'il n'y a pas de message d'erreur particulier
3. Tapez	la commande **php	-v** et	assurez vous que vous avez la version 7.4.9 au minimum
4. Tapez	la commande **composer	-v** et	assurez vous qu'il n'y a pas de message d'erreur particulier
5. Tapez	la commande **node	-v** et	assurez vous qu'il n'y a pas de message d'erreur particulier
6. Tapez	la commande **npm	-v** et	assurez vous qu'il n'y a pas de message d'erreur particulier


## Installation projet

### Cloner le dépôt git distant en local
Dans votre terminal, positionnez vous dans le bon répertoire est cloner le dépot git en local 

```bash
git clone https://github.com/laurent-66/projet8-TodoList-Improvements.git
```

### Installer les dépendances
Installer les dépendances avec composer à partir du fichier composer.lock en tapant la commande :

```bash
composer install
```
### Paramétrer les variables d'environnement pour une utilisation en local

* Dupliquer le fichier .env et renomé le .env.local
* Dans l'aborescence du projet rendez vous dans le fichier .env.local
* les réglages qui vont y être fait seront pour une configuration en local:
* Utilisation de xampp comme serveur pour la base de donnée en SQL avec utilisation de phpmyadmin
* L'adresse de l'application sera http://127.0.0.1:8000
* l'adresse du serveur pour la base de données http://127.0.0.1:3306

Dans le fichier .env.local penser à commenté la ligne concernant le postgresql et décommenté la ligne mysql au dessus

Sur la ligne MySQL rentrer les informations de la manière suivante

DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"

* db_user : entrée un identifiant pour l'accés à la base de donnée (par exemple: root)
* db_password : entrée mot de passe (par exemple: vide)
* db_name : entrée le nom de la base de donnée par exemple todolist_improvements

Exemple complet :

```bash
DATABASE_URL="mysql://root:@127.0.0.1:3306/todolist_improvements?serverVersion=10.4.24-MariaDB&charset=utf8mb4"
```

### création de la base de donnée

1-Lancer l'application Xampp démarrer les modules Apach et MySQL
2-sur xampp ouvrer la page de phpmyadmin en cliquant sur admin

Dans votre terminal
```bash
symfony console doctrine:database:create
```
Cette commande va créer la base de donnée en récupérant le nom que nous avons donnés dans le fichier .env.local
Rafraîchir la page de phpmyadmin todolist_improvements doit apparaître dans l'aborescence

### Jouer les migrations pour alimenter la base de données

Tapez la commande dans votre terminal
```bash
symfony console doctrine:migrations:migrate
```
A la question "Êtes-vous sûr de vouloir continuer d'éxecuté la migration dans la base de données "todolist_improvements" ? répondre oui

Rafraichir la page de phpmyadmin, la liste des tables doit apparaître dans la base de donnée.

### Charger les fixtures

Dans votre terminal

```bash
symfony console doctrine:fixtures:load
```
Cela aura pour effet de créer un jeu de fausses données.
A la question répondre oui.
Rafraichir la page de phpmyadmin, les fausses données doivent apparaître.

### Chargement de l'application

1-Lancer le serveur

Dans votre terminal

```bash
symfony server:start
```
2- tapez dans la barre d'url de votre navigateur

http://127.0.0.1:8000 ou localhost:8000

3- Pour arrêter le serveur

```bash
symfony server:stop
```
### Rappel

Avant le lancement de l'application n'oublié pas au préalable de lancer les modules de xampp.

