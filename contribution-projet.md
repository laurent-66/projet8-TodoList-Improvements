# Document explicatif pour la contribution au projet

## Procédure pour apporter des modifications au projet

### Prérequis: 

* Avoir installé git sur son desktop
* Avoir créer son compte github

### Pour information dans ce tuto :

* Le projet original est laurent-66/projet8-TodoList-Improvements
* Le projet forker est lesageduweb/projet8-TodoList-Improvements

### Forker le répertoire

Se rendre sur le compte github du répertoire du projet d’origine, cliquez sur le bouton Fork en haut à gauche du projet sur lequel vous souhaitez participer. Ceci aura pour effet de créer une copie de ce répertoire dans votre compte github. Cette bifurcation du répertoire vous permet d'expérimenter librement des changements sans affecter le projet original.

<p align="center"><img src="public\img\contribution-projet\buttonFork.png"></p>

 Comme indiqué, la copie sera sur la branche master. cliquez sur le bouton ‘Create fork’

 <p align="center"><img src="public\img\contribution-projet\buttoncreatefork.png"></p>

 Le répertoire à bien été forker sur le compte github du contributeur lesageduweb.

 <p align="center"><img src="public\img\contribution-projet\repositoryforkergithub.png"></p>

### Cloner le répertoire

Le répertoire du projet étant maintenant sur votre compte github, il va falloir le cloner pour récupérer le code en local sur votre machine. Cliquez sur le bouton “code” est copier l’url du dépôt github.

 <p align="center"><img src="public\img\contribution-projet\copiegitclone.png"></p>

Coller le lien url précédé de “git clone” dans le répertoire local de votre machine.
Par exemple, dans votre terminale, votre répertoire aura un dossier “contributions-github”. A l’intérieure tapez:  “git clone < url >”.
<br>
<p align="center"><img src="public\img\contribution-projet\gitclonecli.png"></p>

### Réinitialisation du dossier git du projet sur sa machine

Réinitialiser le dossier de configuration git pour notre projet. Pour cela, tapez dans la commande ‘git init’. 

### Connecter son projet local avec le projet distant sur github
Normalement le fait de réinitialiser le dossier git conserve le pointage vers le remote " origin https://github.com/lesageduweb/projet8-TodoList-Improvements.git "
on peut vérifier que le pointage vers le projet en remote sur github est exact en tapant la commande :    ‘ git remote -v ’
<br>
Si c’est bon, on obtient:

<p align="center"><img src="public\img\contribution-projet\gitRemote.png"></p>
<br>
Sinon le pointage devra être établie en tapant la commande:
<br>
<br>

```bash
git remote add origin https://github.com/lesageduweb/projet8-TodoList-Improvements.git
```

Remarque : cette commande est à taper sur la même ligne.
<br>
Puis refaire la commande git remote -v pour vérifier que le pointage a été pris en compte.

### Créer une nouvelle branche

Pour cela nous allons dans notre IDE préféré, ici je prendrai visual studio code.<br>
Ouvrons notre projet qui se trouve dans le dossier “contributions-github”.<br>
Dans le terminale de VScode, tapez cette commande qui créera une nouvelle branche  depuis  la branche master. Le basculement de master vers la nouvelle branche se fera automatiquement. 

```bash
git switch -c branch-add-file-contribution
```
### Faire les changements nécessaires et faire un commit de ces changements

L’objectif ici sera de créer un fichier markdown(.md) intitulé ‘contribution-projet’ à la racine de l’arborescence du projet.<br>
On y écrira le titre et un peu de texte. Ce travail sera préparatoire pour mettre ensuite le reste du présent contenu de ce document dans le fichier.<br>

Les étapes sont les suivantes:

* Création du fichier  ‘contribution-projet’  à la racine de l’arborescence du projet.
* indexation des modifications avec la commande : <br>

```bash
 git add contribution-projet
```
* Créer un commit avec message: <br>

```bash
git commit -m “création fichier contribution-projet ”
```

### Pousser les changements sur son compte Github

Attention:<br>
Depuis visual studio code l’utilisateur devra s’être connecté à son compte github.<br>
Tapez la commande:<br>

```bash
git push origin -u  branch-add-file-contribution
```
### Soumettre vos changements pour la révision

Si vous allez sur votre répertoire github, vous verrez un bouton “Compare & pull request”. Cliquez sur ce bouton.

<p align="center"><img src="public\img\contribution-projet\compareAndPullRequest.png"></p>

A noter:  Vous avez été automatiquement redirigé sur la page du projet d’origine laurent-66/projet8-TodoList-Improvements pour créer la pull request.<br>
Ensuite soumettez la pull request en cliquant sur “create pull request”.<br>

<p align="center"><img src="public\img\contribution-projet\createthepullrequest.png"></p>
<br>
La pull request est bien transmise au compte github du répertoire projet d’origine.<br>
<p align="center"><img src="public\img\contribution-projet\confirmpullrequest.png"></p>
<br>
L’auteur (laurent-66/projet8-TodoList-Improvements) du projet d’origine pourra analyser votre proposition au travers de la pull request que vous lui avait envoyé et va ensuite fusionner toutes vos modifications dans la branche principale de son projet. Vous recevrez un courriel de notification une fois que les changements auront été fusionnés.<br>
<p align="center"><img src="public\img\contribution-projet\ConfirmMailMergedintoMaster.png"></p>
<br>