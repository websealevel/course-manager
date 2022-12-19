# `cm`: course-manager (en cours de développement)

Une application CLI en PHP pour gérer les fichiers de cours, à destination des formateur·trices et enseignant·es. Tous les supports de cours sont écrits en Markdown puis publiés vers des formats HTML (présentations interactives) et PDF (distribution, exercices, sujet d'examens, etc.).


- [`cm`: course-manager (en cours de développement)](#cm-course-manager-en-cours-de-développement)
  - [Installation locale avec Composer](#installation-locale-avec-composer)
  - [Installation globale](#installation-globale)
  - [Dépendances](#dépendances)
  - [Specs](#specs)
  - [Manuel général d'outils en ligne de commande: arguments, options et syntaxe utilisée dans la doc](#manuel-général-doutils-en-ligne-de-commande-arguments-options-et-syntaxe-utilisée-dans-la-doc)
    - [Argument d'une commande](#argument-dune-commande)
    - [Options d'une commande](#options-dune-commande)
  - [Guide de démarrage](#guide-de-démarrage)
  - [Liste des commandes](#liste-des-commandes)
    - [Initialiser un nouveau projet de gestion de cours : `cm init`](#initialiser-un-nouveau-projet-de-gestion-de-cours--cm-init)
    - [Ajouter un cours `cm add:course`](#ajouter-un-cours-cm-addcourse)
    - [Ajouter un module à un cours existant `cm add:module`](#ajouter-un-module-à-un-cours-existant-cm-addmodule)
    - [Publier un module (à venir...)](#publier-un-module-à-venir)
    - [Publier un cours (à venir...)](#publier-un-cours-à-venir)
    - [Inspecter les cours (à venir...)](#inspecter-les-cours-à-venir)
  - [Génération des présentations avec marp](#génération-des-présentations-avec-marp)
    - [macOS](#macos)
    - [Windows](#windows)
    - [Debian/Ubuntu](#debianubuntu)
      - [Installation via les binaires](#installation-via-les-binaires)
      - [Installation via `node` et `npm`](#installation-via-node-et-npm)
    - [Au format `HTML`](#au-format-html)
    - [Au format `PDF`](#au-format-pdf)
    - [Au format `PPTX` (PowerPoint) **lecture seule**](#au-format-pptx-powerpoint-lecture-seule)
    - [Au format `PPTX` (PowerPoint) **éditable**](#au-format-pptx-powerpoint-éditable)
  - [Génération des documents au format HTML ou PDF](#génération-des-documents-au-format-html-ou-pdf)
    - [Générer un fichier HTML+CSS à partir d'un fichier Markdown (avec `marp`)](#générer-un-fichier-htmlcss-à-partir-dun-fichier-markdown-avec-marp)
    - [Générer la présentation au format PDF à partir d'un fichier Markdown (avec `marp`)](#générer-la-présentation-au-format-pdf-à-partir-dun-fichier-markdown-avec-marp)
    - [Générer un PDF à partir du Markdown en passant par HTML/CSS (avec `pandoc`)](#générer-un-pdf-à-partir-du-markdown-en-passant-par-htmlcss-avec-pandoc)
  - [Ressources](#ressources)

## Installation locale avec Composer

Installer php (>v8.2.*).

Installer Composer.

Télécharger le [code source](https://github.com/websealevel/course-manager).

~~~bash
composer install
~~~

Créer un fichier `conf.ini` à la racine de votre dossier

~~~ini
path_courses=/chemin/vers/la/ou/vous/voulez/stocker/les/cours
~~~

Rendre le script `cm` exécutable

~~~
chmod +x cm
//Lister toutes les commandes
./cm
~~~

Ajouter l'éxecutable sur votre PATH.

Initialiser un votre premier système de gestion de cours avec `cm init`.

## Installation globale

À venir...

## Dépendances

- PHP (8.2.*)
- Marp
- Pandoc

## Specs

Un dossier `cours` (par défaut) est créé à l'initialisation et sert de racine au projet. Il contient les fichiers suivants: 

- `sources/` : les fichiers sources de vos cours (markdown, assets, images)
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

## Manuel général d'outils en ligne de commande: arguments, options et syntaxe utilisée dans la doc

Les *commandes* peuvent prendre des *arguments* ou des *options* en paramètre. Pour lister les arguments et les options disponibles de chaque commande, taper `cm <nom de la commande> --help`. 

>Les chaines de caractères entre chevrons `<>` doivent être remplacés par un nom de commande, d'argument ou d'option.

### Argument d'une commande

Un argument est une chaine de caractère ajouté à la suite de la commande pour modifier son comportement. Les arguments sont séparés par des esapces. Un argument peut-être obligatoire ou optionnel. 

Il s'écrit sous la forme `cm <nom de la commande> <nom de l'argument>`. Par exemple, `cm init mes-cours`. Attention, **les arguments sont ordonnés**, ils doivent être écrits dans l'ordre attendu par la commande. 

>Un argument optionnel s'écrit entre crochets `cm <nom de la commande> |[<nom de l'argument optionnel>]`.

### Options d'une commande

Une option **est par définition optionnelle**. Elle a un nom et peut être placée n'importe où à la suite du nom de la commande (contrairement aux arguments). Elle sont préfixées par deux dashes `--`. Par exemple, `cm init --help`. Les options ont également un alias, un nom plus court. Un alias est préfixé par un dash `-`. Par exemple, `cm init --help` est équivalent à `cm init -h`; `-h` est l'alias de `--help`.


## Guide de démarrage

- créer un nouveau projet de gestion de cours avec `cm init [<root_dir>]`. L'argument optionnel root_dir vous permet de préciser le nom du dossier.

## Liste des commandes

Pour obtenir la liste des commandes disponibles `cm list`.

Pour obtenir des informations sur chaque commande (description, arguments, options) `cm <nom de la commande> --help` ou `cm <nom de la commande> -h`.

### Initialiser un nouveau projet de gestion de cours : `cm init`

### Ajouter un cours `cm add:course`

~~~bash
~~~

Crée un dossier `cours/{vendor}/{niveau}-{nom du cours}` avec le contenu par défaut suivant

 - `bibliographie`: stocke la bibliographie du cours (livres, fichiers PDF, etc.)
 - `module-00-presentation`: module de présentation du cours, avec ses sous-dossiers et son fichier deck de slides Markdown
 - `README.md`: description brève du cours (durée, contenu, ressources, niveau des apprenants, remarques). Contient le plan, les objectifs et le planning.
 - `index.html`: permet de naviguer dans le cours au format HTML

Par exemple

~~~bash
cm-create etablissement1 l2 php
~~~

crée le cours `l2-php` dans le dossier `cours/etablissement1`. Le dossier `l2-php` contient un dossier de biblio et un module de présentation par défaut `module-00-presentation` contenant une présentation en Markdown initialisée.

### Ajouter un module à un cours existant `cm add:module`

~~~
cm-add-module [vendor] [niveau] {coursename} {modulename}
~~~

Ajoute un module `modulename` au cours `coursename`. Son numéro est défini comme le dernier module + 1. Par exemple

~~~bash
~~~

Ajoute le module `module-01-tableaux` au cours `etablissement1/l2-php`. Comme il n'y a pas d'ambiguïté sur le cours, il n'est pas utile de préciser le `niveau` ni le `vendor`. L'identifiant du module est 1 car le seul module présent est le module de présentation ayant pour identifiant 0. 

### Publier un module (à venir...)

### Publier un cours (à venir...)

### Inspecter les cours (à venir...)

<!-- - script `course-ls {nom du cours}`: liste le contenu du cours (modules présents)


- script `course-export {nom du cours} {opt nom du module}`: génère les fichiers HTML et PDF du cours et fais une copie dans le dossier `Public` et Privé (presentation avec notes). Met à jour l'index.html local au cours et l'index.html global. -->




## Génération des présentations avec [marp](https://marp.app)

Toutes les possibilités d'installation de l'application sont listées [sur le dépôt](https://github.com/marp-team/marp-cli).

### macOS

Installer marp via le gestionnaire de paquets [Homebrew](https://brew.sh/index_fr)
~~~bash
brew install marp-cli
~~~
### Windows

Installer marp via le gestionnaire de paquets [Scoop](https://scoop.sh/)
~~~bash
scoop install marp
~~~

### Debian/Ubuntu

#### Installation via les binaires

Télécharger l'archive contenant les binaires [depuis son dépôt GitHub](https://github.com/marp-team/marp-cli/releases).
Extraire l'archive
~~~bash
tar xvzf marp-cli-v{derniere version}-linux-tar.gz
~~~
Copiez l'exécutable présent dans l'archive dans un répertoire présent sur le `PATH`, par exemple
~~~bash
sudo cp marp /usr/local/bin
~~~
Vérifier que marp est bien installé
~~~bash
marp -h
~~~
#### Installation via `node` et `npm`

Installer [node](https://packages.debian.org/fr/sid/nodejs) et [npm](le gestionnaire de paquets de node), puis installer `marp-cli` globalement (option `-g`)

~~~bash
sudo apt-get update
sudo apt-get install nodejs npm
npm install -g @marp-team/marp-cli
~~~

### Au format `HTML`

~~~bash
marp --html --allow-local-files --theme assets/css/main.css slides.md
~~~

### Au format `PDF`

~~~bash
marp --theme assets/css/main.css --pdf slides.md
~~~

### Au format `PPTX` (PowerPoint) **lecture seule**

La conversion opère juste une conversion en images au format `pptx`. Le document peut être ouvert dans PowerPoint, mais **non éditable.**

~~~bash
marp  --html --allow-local-files --theme assets/css/main.css --pptx slides.md
~~~

### Au format `PPTX` (PowerPoint) **éditable**

Pour convertir la présentation Markdown en `pptx` éditable, on peut 

- passer [par le format PDF](#au-format-pdf) puis utiliser un outil comme [PDF to Powerpoint Converter](https://pdf.online/pdf-to-powerpoint-converter) ou [Adobe](https://www.adobe.com/acrobat/online/pdf-to-ppt.html)


## Génération des documents au format HTML ou PDF

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
- [Minicli 3](https://github.com/minicli/minicli), un projet open source d'application CLI inspirant
- [poc-marp](https://github.com/websealevel/poc-marp), un dépôt qui récapitule les possibilités essentielles de l'écosystème marp