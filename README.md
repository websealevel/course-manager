# Course-manager

Une application CLI [basée sur le composant Console de Symfony](https://symfony.com/doc/current/components/console.html), à destination des formateur·trices et enseignant·es, pour gérer les supports pédagogiques et leurs publications. 

>Write once, reuse and publish everywhere !

- [Course-manager](#course-manager)
  - [Dépendances tierces](#dépendances-tierces)
  - [Installation avec Composer](#installation-avec-composer)
  - [Mettre à jour](#mettre-à-jour)
  - [Philosophie](#philosophie)
  - [Modèle](#modèle)
  - [Guide de démarrage](#guide-de-démarrage)
    - [Créer un nouveau projet](#créer-un-nouveau-projet)
    - [Ajouter un nouveau cours](#ajouter-un-nouveau-cours)
    - [Ajouter un nouveau module à un cours](#ajouter-un-nouveau-module-à-un-cours)
    - [Publier un cours](#publier-un-cours)
  - [Liste des commandes disponibles](#liste-des-commandes-disponibles)
  - [Manuel général des outils CLI: arguments, options et syntaxe utilisée dans la documentation](#manuel-général-des-outils-cli-arguments-options-et-syntaxe-utilisée-dans-la-documentation)
    - [Argument d'une commande](#argument-dune-commande)
    - [Options d'une commande](#options-dune-commande)
  - [Backlog (idées de commandes à ajouter)](#backlog-idées-de-commandes-à-ajouter)
  - [phpDocumentor](#phpdocumentor)
  - [phpStan](#phpstan)
  - [Ressources](#ressources)
    - [Utilisateur·rice](#utilisateurrice)
    - [Développeur·se](#développeurse)

## Dépendances tierces

Pour pouvoir utiliser le programme, assurez-vous d'installer les programmes suivants:

- [PHP](https://www.php.net/downloads) (>8.2.*)
- [Marp](https://marp.app/)
- [Pandoc](https://pandoc.org/)

## Installation avec Composer

Installer [Composer](https://getcomposer.org/download/).

Téléchargez-le [code source](https://github.com/websealevel/course-manager).

~~~bash
composer install
~~~

Rendre le script `cm` exécutable

~~~bash
chmod +x cm
# Tester et lister toutes les commandes
./cm
~~~

Pour rendre le programme globalement accessible sur votre machine, ajouter-le à votre `PATH`:

~~~bash
#Sous Debian/Ubuntu
export PATH="/path/to/course-manager:$PATH"
~~~

Tester votre installation en tapant la commande `cm`.

## Mettre à jour

Pour mettre à jour le programme, rendez-vous dans le dossier contenant le dépôt cloné. Assurez-vous d'être sur la branche `main` puis `git pull` pour récupérer le code-source à jour, et `composer update` pour mettre à jour les dépendances.

~~~bash
#Dans le repertoire du projet cloné sur github
git pull && composer update
~~~

## Philosophie

>Écrivez une fois, publiez plusieurs fois dans différents formats.

Ce gestionnaire de cours est basé sur le principe de la *stricte et nécessaire séparation du contenu et de la forme*. Vous écrivez, mettez à jour votre contenu **en un endroit**, puis vous le publiez **vers autant de formats que nécessaire** (HTML, PDF, etc.).

Un projet de gestion de cours se compose des éléments suivants:

- `sources/` : les fichiers sources de vos cours (Markdown, assets, images). *La source de vérité*
- `templates/`: les styles globaux pour la publication de vos cours (CSS, XSLT, etc.)
- `public/` : les cours publiés au format HTML et/ou PDF, distribuable, les notes n'y apparaissent pas
- `index.html`: un fichier pour naviguer facilement dans vos cours via votre navigateur web

Vous écrivez vos contenus de cours en Markdown. Le programme vous convertit tous vos supports aux formats HTML et PDF dans le dossier `public`, prêts à être imprimés ou distribués à vos étudiant·es.

~~~
#Un exemple de l'architecture d'un projet de gestion de cours
#Sources des cours
sources/
  - mon-ecole/
    - moncours/
      - bibliographie/
      - module00-presentation/
      - module01-mon-module/
        - cours/
          #cours source: presentation et notes à l'intention du formateur·rice
          - slides.md
        - exercices/
          - td-01.md
      - tp/
#Templates contenant la mise en page (style) globale des cours
templates/
  - css (html,pdf)
  - xslt (pdf)
#Cours publiés (format HTML/PDF)
public/
  - mon-ecole/
    - moncours/
      - module00-presentation-slides.pdf
      - module01-mon-module-slides.pdf
      - module01-td-01.pdf
      - etc.
~~~

Le programme vous oblige uniquement à 

- définir un projet courant (un dossier de travail)
- créer un dossier par cours

Vous organisez ensuite comme vous le désirez vos supports de cours à l'intérieur de ces dossiers. Des règles de publication sont définies automatiquement ou sont configurables.

## Modèle

Un *projet* est un *système de gestion de cours*, c'est votre répertoire de travail. Vous pouvez avoir plusieurs projets sur la même machine. 

Un *cours* est défini par des *métadonnées*, peut être assigné à un *vendor*, et se compose de *modules*. Chaque *module* contient vos supports de cours. Les métadonnées servent à lister, filtrer et interroger vos cours.

## Guide de démarrage

### Créer un nouveau projet

Créer un nouveau projet de gestion de cours 

~~~bash
cm add:project mes-cours
~~~

> Lors de votre première création de projet, course-manager crée un fichier de configuration global dans votre répertoire home (`$HOME/.create-manager`).


Si c'est votre premier projet alors, par défaut, `mes-cours` est votre projet *courant*, inutile donc de vous y déplacer pour l'administrer.

> Le projet *courant* est défini sous la clé `MAIN` dans le fichier de configuration global (voir $HOME/.create-manager). Vous pouvez changer de projet courant avec la commande `change:current-project` ou en vous rendant directement dans le répertoire d'un autre projet. 

### Ajouter un nouveau cours

Créer un nouveau cours sur le programme `course-manager`, pour le présenter à des apprenant·es de l'établissement `mon-ecole`

~~~bash
cm add:course course-manager mon-ecole
# Ou avec les options: renseigner des niveaux (L1) et des mot-clefs (cli et gestion)
cm add:course course-manager mon-ecole --level=L1 --keywords=cli,gestion
~~~

> Le `vendor` est un paramètre *optionnel*. Si vous ne renseignez pas de `vendor`, le cours sera enregistré dans le vendor global. Par exemple, `cm add:course course-manager` créera le cours `course-manager` directement dans le dossier `sources`, et celui-ci ne sera rattaché à aucun `vendor` en particulier.

Par défaut, le programme vous crée un dossier `mon-ecole/course-manager` contenant les fichiers/dossiers suivants:

 - `bibliographie`: pour stocker les documents bibliographiques du cours (ebook, fichiers PDF, images, etc.)
 - `module00-presentation`: module de présentation du cours
   - `cours`: contient le fichier de cours `cours.md`. Placer ici les supports de cours.
   - `exercices`: placer ici les fiches d'exercices (énoncés, corrections)
 - `README.md`: description brève du cours (durée, contenu, ressources, niveau des apprenants (`--level`), remarques). Contient le plan, les objectifs, le planning, etc.
 - `index.html`: permet de naviguer dans le cours au format HTML.
 - `.metadata`: un fichier caché contenant des métadonnées sur le cours. Généré automatiquement. Permet de filtrer les cours via le programme.

> Vous pouvez modifier le contenu par défaut d'un cours selon vos besoins.

### Ajouter un nouveau module à un cours

À venir...

### Publier un cours

À venir...

## Liste des commandes disponibles

Pour obtenir des informations sur les commandes disponibles

~~~bash
cm list
#ou simplement
cm
~~~

Pour obtenir des informations sur chaque commande (description, arguments et options) 

~~~bash
cm <nom de la commande> --help
#ou
cm <nom de la commande> -h
~~~

<!-- ### Créer un nouveau projet de gestion de cours `add:project` 

~~~bash
cm add:project <nom du système de gestion de cours>
~~~

### Ajouter un nouveau cours au système courant `add:course`

~~~bash
cm add:course <vendor> <nom du cours>
~~~ -->

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

<!-- crée le cours `l2-php` dans le dossier `cours/etablissement1`. Le dossier `l2-php` contiens un dossier de biblio et un module de présentation par défaut `module-00-presentation` contenant une présentation en Markdown initialisée. -->

<!-- ### Ajouter un module à un cours existant `cm add:module` -->

<!-- ~~~
cm-add-module [vendor] [niveau] {coursename} {modulename}
~~~

Ajoute un module `modulename` au cours `coursename`. Son numéro est défini comme le dernier module + 1. Par exemple

~~~bash
~~~

Ajoute le module `module-01-tableaux` au cours `etablissement1/l2-php`. Comme il n'y a pas d'ambiguïté sur le cours, il n'est pas utile de préciser le `niveau` ni le `vendor`. L'identifiant du module est 1 car le seul module présent est le module de présentation ayant pour identifiant 0.  -->

<!-- ### Publier un module (à venir...)

### Publier un cours (à venir...)

Publier un cours revient à publier l'intégralité de ses modules. 

#### Contenus de type *présentation*: format HTML et PDF

#### Contenus de type *document*: format HTML et PDF

### Inspecter les cours du système courant (à venir...) -->

<!-- ### Exporter toutes les références biblio dans un fichier par cours -->




## Manuel général des outils CLI: arguments, options et syntaxe utilisée dans la documentation

>Ce programme en ligne de commande suit les conventions généralement adoptées sur l'utilisation des commandes, arguments et options. 

Les *commandes* peuvent prendre des *arguments* ou des *options* en paramètre. Pour lister les arguments et les options disponibles de chaque commande, taper `cm <nom de la commande> --help`. 

>Les chaînes de caractères entre chevrons `<>` doivent être remplacées par un nom de commande, d'argument ou d'option.

### Argument d'une commande

Un argument est une chaîne de caractère ajouté à la suite de la commande pour modifier son comportement. Les arguments sont séparés par des espaces. Un argument peut-être obligatoire ou optionnel. 

Il s'écrit sous la forme `cm <nom de la commande> <nom de l'argument>`. Par exemple, `cm add:project mes-cours`. Attention, **les arguments sont ordonnés**, ils doivent être écrits dans l'ordre attendu par la commande. 

>Un argument optionnel est représenté *entre crochets* `cm <nom de la commande> [<nom de l'argument optionnel>]`.

### Options d'une commande

Une option **est par définition optionnelle**. Elle a un nom et peut être placée n'importe où à la suite du nom de la commande (contrairement aux arguments). Elles sont préfixées par deux dashes `--`. Par exemple l'option `help` s'écrit `cm add:project --help`. 

Les options ont également un *alias*, un nom plus court. Un alias est préfixé par un dash `-`. Par exemple, `cm add:project --help` est équivalent à `cm add:project -h`; `-h` est l'alias de `--help`. Si l'option prend une valeur, on la passera comme suit: `--option=foo`, ou en version abrégée `-ofoo` (en supposant ici que l'*alias* de `option` est `o`).


<!-- ## Sous le capot: génération des documents au format HTML ou PDF avec marp et pandoc

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
pandoc {file.md} -t html5 -o {file.pdf} --css style.css --pdf-engine-opt=--enable-local-file-access -->
<!-- ~~~ -->

## Backlog (idées de commandes à ajouter)

- `add:module`, ajouter un module a un cours existant
- publication et formats
- dans le `config.ini` ajouter un éditeur par défaut pour ouvrir le dossier d'un cours ou d'un module (par exemple vscode, emacs, vim, etc.). Ensuite utiliser `open:course <nom_du_cours>` et il ouvrira le dossier du cours avec l'éditeur défini.
- filtrer les cours par niveau, sujets
- ajouter une option à `add:course --prompt` pour proposer de guider l'utilisateur dans la création de cours:
  - vendor
  - liste de modules à ajouter des le debut
- ajouter une commande qui installer un projet demo avec un cours demo sur course-manager
- ajouter une commande pour publier sur un repo distant le contenu de sources et le contenu de public (possiblement séparés)
- ajouter des abstractions Abstract Factory pour laisser l'utilisateur implementer ses projets, modules et cours (dossiers et fichiers par défaut, structure personnalisée)

## phpDocumentor

La documentation du code est générée par [phpDocumentor](https://docs.phpdoc.org/3.0/) et se trouve dans le dossier `docs`. Elle est [consultable dans le navigateur depuis ce fichier](./docs/index.html).

Régénérer la documentation du projet à partir des sources

~~~
#installer docker pour pouvoir générér la documentation de cette manière
docker run --rm -v "$(pwd):/data" "phpdoc/phpdoc:3" run -dsrc -tdocs
~~~

## phpStan

Pour analyser le code source statiquement ([au level 6](https://phpstan.org/user-guide/rule-levels)) avec [phpStan](https://phpstan.org/) et corriger les erreurs

~~~
#Analyser le code au level 6
vendor/bin/phpstan analyse src -l6
~~~

## Ressources

### Utilisateur·rice

- [Site officiel du projet Markdown](https://daringfireball.net/projects/markdown/)
- [Pandoc](https://pandoc.org/index.html), un convertisseur de document universel et éprouvé
- [Marp](https://marp.app/)
- [poc-marp](https://github.com/websealevel/poc-marp), un dépôt qui récapitule les possibilités essentielles de l'écosystème marp

### Développeur·se

- [The Console Component (Symfony)](https://symfony.com/doc/current/components/console.html)
- [Console Commands](https://symfony.com/doc/current/console.html)
- [Commands Lifecycle](https://symfony.com/doc/current/console.html#command-lifecycle)
- [Console Input (Arguments & Options)](https://symfony.com/doc/current/console/input.html)
- [Learn more (on Symfony Console)](https://symfony.com/doc/current/components/console.html#learn-more)
- [PhpDocumentor (installation)](https://docs.phpdoc.org/3.0/guide/getting-started/installing.html#system-requirements)