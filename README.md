# `cm`: course-manager

Une application CLI en PHP pour gérer les fichiers de cours, à destination des formateur·trices et enseignant·es. Tous les supports de cours sont écrits en Markdown puis publiés vers des formats HTML (présentations interactives) et PDF (distribution, exercices, sujet d'examens, etc.).


- [`cm`: course-manager](#cm-course-manager)
  - [Installation locale avec Composer](#installation-locale-avec-composer)
  - [Installation globale](#installation-globale)
  - [Dépendances](#dépendances)
  - [Specs](#specs)
  - [Scripts](#scripts)
    - [Ajouter un cours `cm-create`](#ajouter-un-cours-cm-create)
    - [Ajouter un module à un cours existant `cm-add-module`](#ajouter-un-module-à-un-cours-existant-cm-add-module)
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

Installer php.

Installer Composer.

Télécharger le [code source](https://github.com/websealevel/course-manager).

~~~bash
composer update
~~~

Créer un fichier `conf.ini` à la racine de votre dossier

~~~ini
path_courses=/chemin/vers/la/ou/vous/voulez/stocker/les/cours
~~~

Créer votre premier cours avec [cm-create](#ajouter-un-cours-cm-create).

## Installation globale

À venir...

## Dépendances

- php (8.1)
- marp
- pandoc

## Specs

Un dossier `cours` est créé à la création du premier cours et sert de racine au projet. 

À la racine, un fichier `README.md` et `index.html` qui contient la liste de tous les cours et permet de naviguer dans tous les cours au format HTML.

Chaque `cours` est défini dans un `vendor` (un organisme, une école), eux-mêmes divisés en `modules`. Un module contient une partie du cours, des exercices et des tps.

Une présentation est générée par `module` dans le dossier `module/cours`. Chaque présentation est écrite en Markdown, et générée ensuite en HTML et PDF avec marpit. 

Un fichier `index.html` local permet de passer d'une présentation à l'autre facilement. Les exercices, sujets de TP ou d'examen et les autres documents Markdown sont générés en PDF avec pandoc.

Enfin, un dossier `cours/Public`  contient les cours publiés et distribués (fichiers générés au format PDF, HTML, etc. uniquement sans les notes, *aucun fichier source Markdown*). Il reflète la structure de `cours` (même arborescence). Tout le contenu de ce dossier est distribuable (publication).

## Scripts

> Les arguments définis entre accolades sont obligatoires, les arguments définis entre crochets sont optionnels.

### Ajouter un cours `cm-create`

~~~bash
cm-create {vendor} {niveau} {nom du cours}
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

### Ajouter un module à un cours existant `cm-add-module`

~~~
cm-add-module [vendor] [niveau] {coursename} {modulename}
~~~

Ajoute un module `modulename` au cours `coursename`. Son numéro est défini comme le dernier module + 1. Par exemple

~~~bash
cm-add-module php tableaux
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