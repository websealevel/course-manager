# Course-manager

Une application CLI, [basée sur le composant Console de Symfony](https://symfony.com/doc/current/components/console.html), pour gérer les fichiers de cours (contenu, publication), à destination des formateur·trices et enseignant·es. Tous les supports de cours sont écrits en Markdown *en un endroit*, puis publiés vers différents formats: HTML (présentations interactives), PDF (impression, exercices, sujet d'examens, etc.).


- [Course-manager](#course-manager)
  - [Dépendances tierces](#dépendances-tierces)
  - [Installation avec Composer](#installation-avec-composer)
  - [Mettre à jour `course-manager`](#mettre-à-jour-course-manager)
  - [Philosophie](#philosophie)
  - [Guide de démarrage](#guide-de-démarrage)
  - [Liste des commandes](#liste-des-commandes)
    - [Initialiser un nouveau projet de gestion de cours `add:project`](#initialiser-un-nouveau-projet-de-gestion-de-cours-addproject)
    - [Ajouter un nouveau cours au système courant `add:course`](#ajouter-un-nouveau-cours-au-système-courant-addcourse)
    - [Ajouter un module à un cours existant `cm add:module`](#ajouter-un-module-à-un-cours-existant-cm-addmodule)
    - [(Re)Publier un module (à venir...)](#republier-un-module-à-venir)
    - [(Re)Publier un cours (à venir...)](#republier-un-cours-à-venir)
      - [Contenus de type *présentation*: format HTML et PDF](#contenus-de-type-présentation-format-html-et-pdf)
      - [Contenus de type *document*: format HTML et PDF](#contenus-de-type-document-format-html-et-pdf)
    - [Inspecter les cours (à venir...)](#inspecter-les-cours-à-venir)
  - [Manuel général d'outils en ligne de commande: arguments, options et syntaxe utilisée dans la doc](#manuel-général-doutils-en-ligne-de-commande-arguments-options-et-syntaxe-utilisée-dans-la-doc)
    - [Argument d'une commande](#argument-dune-commande)
    - [Options d'une commande](#options-dune-commande)
  - [Sous le capôt: génération des documents au format HTML ou PDF avec marp et pandoc](#sous-le-capôt-génération-des-documents-au-format-html-ou-pdf-avec-marp-et-pandoc)
    - [Générer un fichier HTML+CSS à partir d'un fichier Markdown (avec `marp`)](#générer-un-fichier-htmlcss-à-partir-dun-fichier-markdown-avec-marp)
    - [Générer la présentation au format PDF à partir d'un fichier Markdown (avec `marp`)](#générer-la-présentation-au-format-pdf-à-partir-dun-fichier-markdown-avec-marp)
    - [Générer un PDF à partir du Markdown en passant par HTML/CSS (avec `pandoc`)](#générer-un-pdf-à-partir-du-markdown-en-passant-par-htmlcss-avec-pandoc)
  - [Ressources](#ressources)

## Dépendances tierces

Pour pouvoir utiliser le programme, assurez vous d'installer les programmes suivants:

- [PHP](https://www.php.net/downloads) (>8.2.*)
- [Marp](https://marp.app/)
- [Pandoc](https://pandoc.org/)

## Installation avec Composer

Installer [Composer](https://getcomposer.org/download/).

Télécharger le [code source](https://github.com/websealevel/course-manager).

~~~bash
composer install
~~~

Rendre le script `cm` exécutable

~~~
chmod +x cm
# Tester et lister toutes les commandes
./cm
~~~

Pour rendre le programme globalement accessible sur votre machine, ajouter-le à votre `PATH`:

~~~
#Sous Debian/Ubuntu
export PATH="/path/to/course-manager:$PATH"
~~~

Tester votre installation en tapant la commande `cm`.

## Mettre à jour `course-manager`

Pour mettre à jour course-manager, rendez-vous dans le dossier contenant le dépôt cloné. Assurez-vous d'être sur la branche `main` puis `git pull` pour récupérer le code-source à jour, et `composer update` pour mettre à jour les dépendances.

## Philosophie

>Écrivez une fois, publiez plusieurs fois dans différents formats.

Ce gestionnaire de cours est basé sur le principe de la stricte et nécessaire séparation du contenu et de la forme. Vous écrivez, mettez à jour votre contenu en un endroit, puis vous le publier vers autant de formats que nécessaire (HTML,PDF,etc.).

Un dossier `cours` (par défaut) est créé à l'initialisation et sert de racine au projet. Il contient les fichiers suivants: 

- `sources/` : les fichiers sources de vos cours (markdown, assets, images). *La source de vérité*
- `templates/`: les styles globaux pour la publication de vos cours (CSS, XSLT, etc.)
- `public/` : les cours publiés au format HTML et/ou PDF, distribuables, les notes n'y apparaissent pas
- `index.html`: un fichier pour naviguer facilement dans vos cours via votre navigateur web

Chaque `cours` est défini dans un `vendor` (un organisme, une école, etc.), et se divise en `modules`. Un module contient une partie du cours, des exercices et des tps.

Une présentation est générée par `module` au moment de la publication. Chaque présentation est écrite en Markdown, et générée ensuite en HTML et PDF avec marpit. 

~~~
#Exemple de l'architecture du système de gestion de cours
#Sources des cours
sources/
  - *vendorA*
    - coursA
      - bibliographie
      - module1
        - cours
          #cours source: contenu du cours en markdown, commentaire et notes à l'intention du formateur·rice
          - cours.md
        - exercices
        - tp
        - index.html
        - README.md
      - module2
    - coursB
  - *vendorB*

#Templates contenant la mise en page (style) globale des cours
templates/
  - css (html,pdf)
  - xslt (pdf)

#Cours publiés (format HTML/PDF)
public/
  - *vendorA*
    - coursA
      - cours
        #cours publié: contenu en HTML/PDF, pas de commentaire ni de notes visibles
        - cours.html
        - cours.pdf
~~~



## Guide de démarrage

Créer un nouveau projet de gestion de cours 

~~~bash
cm add:project mes-cours
~~~

> Lors de votre première création de projet, course-manager crée un fichier de configuration global dans votre répertoire home (`$HOME/.create-manager`). Ce fichier de configuration globale défini le projet de gestion de cours principal (`MAIN`) qui est utilisé si vous n'êtes pas placé à la racine d'un projet lors de l'utilisation de `create-manager`.

Rentrer dans le dossier crée afin de pouvoir gérer votre projet

~~~bash
cd mes-cours
~~~

> Si c'est votre projet MAIN (voir $HOME/.create-manager), vous n'êtes pas obligé de vous rendre dans le repertoire courant pour travailler sur le projet.

Créer un nouveau cours sur cet outil pour le présenter à des étudiant·es `cm-manager` de l'établissement (de manière général appelé *vendor*) `foo`

~~~bash
cm add:course foo cm-manager
# Avec un niveau (ici L1 et L2)
cm add:course foo cm-manager --level=L1,L2
~~~

> Vous pouvez également ajouter un niveau du cours avec l'option `--level`. Par exemple `--level=L1` ou `--level=M1,M2`.

## Liste des commandes

Pour obtenir la liste des commandes disponibles `cm list`.

Pour obtenir des informations sur chaque commande (description, arguments, options) `cm <nom de la commande> --help` ou `cm <nom de la commande> -h`.

### Initialiser un nouveau projet de gestion de cours `add:project` 

~~~bash
cm add:project <nom du système de gestion de cours>
~~~

### Ajouter un nouveau cours au système courant `add:course`

~~~bash
cm add:course <vendor> <niveau> <nom du cours>
~~~

<!-- ~~~bash
~~~

Crée un dossier `cours/{vendor}/{niveau}-{nom du cours}` avec le contenu par défaut suivant

 - `bibliographie`: stocke la bibliographie du cours (livres, fichiers PDF, etc.)
 - `module-00-presentation`: module de présentation du cours, avec ses sous-dossiers et son fichier deck de slides Markdown
 - `README.md`: description brève du cours (durée, contenu, ressources, niveau des apprenants, remarques). Contient le plan, les objectifs et le planning.
 - `index.html`: permet de naviguer dans le cours au format HTML

Par exemple

~~~bash
cm-create etablissement1 l2 php
~~~ -->

<!-- crée le cours `l2-php` dans le dossier `cours/etablissement1`. Le dossier `l2-php` contient un dossier de biblio et un module de présentation par défaut `module-00-presentation` contenant une présentation en Markdown initialisée. -->

### Ajouter un module à un cours existant `cm add:module`

<!-- ~~~
cm-add-module [vendor] [niveau] {coursename} {modulename}
~~~

Ajoute un module `modulename` au cours `coursename`. Son numéro est défini comme le dernier module + 1. Par exemple

~~~bash
~~~

Ajoute le module `module-01-tableaux` au cours `etablissement1/l2-php`. Comme il n'y a pas d'ambiguïté sur le cours, il n'est pas utile de préciser le `niveau` ni le `vendor`. L'identifiant du module est 1 car le seul module présent est le module de présentation ayant pour identifiant 0.  -->

### (Re)Publier un module (à venir...)

### (Re)Publier un cours (à venir...)

Publier un cours revient à publier l'intégralité de ses modules. 

#### Contenus de type *présentation*: format HTML et PDF

#### Contenus de type *document*: format HTML et PDF

### Inspecter les cours (à venir...)

<!-- - script `course-ls {nom du cours}`: liste le contenu du cours (modules présents)

- script `course-export {nom du cours} {opt nom du module}`: génère les fichiers HTML et PDF du cours et fais une copie dans le dossier `Public` et Privé (presentation avec notes). Met à jour l'index.html local au cours et l'index.html global. -->


## Manuel général d'outils en ligne de commande: arguments, options et syntaxe utilisée dans la doc

Les *commandes* peuvent prendre des *arguments* ou des *options* en paramètre. Pour lister les arguments et les options disponibles de chaque commande, taper `cm <nom de la commande> --help`. 

>Les chaines de caractères entre chevrons `<>` doivent être remplacés par un nom de commande, d'argument ou d'option.

### Argument d'une commande

Un argument est une chaine de caractère ajouté à la suite de la commande pour modifier son comportement. Les arguments sont séparés par des esapces. Un argument peut-être obligatoire ou optionnel. 

Il s'écrit sous la forme `cm <nom de la commande> <nom de l'argument>`. Par exemple, `cm add:project mes-cours`. Attention, **les arguments sont ordonnés**, ils doivent être écrits dans l'ordre attendu par la commande. 

>Un argument optionnel s'écrit entre crochets `cm <nom de la commande> |[<nom de l'argument optionnel>]`.

### Options d'une commande

Une option **est par définition optionnelle**. Elle a un nom et peut être placée n'importe où à la suite du nom de la commande (contrairement aux arguments). Elle sont préfixées par deux dashes `--`. Par exemple, `cm add:project --help`. Les options ont également un alias, un nom plus court. Un alias est préfixé par un dash `-`. Par exemple, `cm add:project --help` est équivalent à `cm add:project -h`; `-h` est l'alias de `--help`.



## Sous le capôt: génération des documents au format HTML ou PDF avec marp et pandoc

### Générer un fichier HTML+CSS à partir d'un fichier Markdown (avec `marp`)

~~~bash
marp --html --allow-local-files {file.md}
~~~

### Générer la présentation au format PDF à partir d'un fichier Markdown (avec `marp`)

~~~bash
marp --pdf --allow-local-files {file.md}
~~~

### Générer un PDF à partir du Markdown en passant par HTML/CSS (avec `pandoc`)

~~~
pandoc {file.md} -t html5 -o {file.pdf} --css style.css --pdf-engine-opt=--enable-local-file-access
~~~

## Ressources

- [Site officiel du projet Markdown](https://daringfireball.net/projects/markdown/)
- [Marp écosystème](https://marp.app/)
- [Pandoc](https://pandoc.org/index.html), un convertisseur de document universel et éprouvé
- [poc-marp](https://github.com/websealevel/poc-marp), un dépôt qui récapitule les possibilités essentielles de l'écosystème marp
- [The Console Component (Symfony)](https://symfony.com/doc/current/components/console.html)