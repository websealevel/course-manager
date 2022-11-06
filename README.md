# course-manager

Une application CLI en PHP pour gérer les fichiers de cours.


## Run

~~~bash
./create-course [option]
~~~

## Environnement

`ABSPATH`: le dossier racine des cours.

A la racine un fichier markdown et index.html qui contient la liste de tous les cours et permet de naviguer dans tous les cours dans Public au format html.

La bibliothèque est découpée en `courses`, eux mêmes divisés en `modules`. Un module contient un cours, des exercices et de TP.

Une présentation est généré par `module`. Cette présentation fait référence aux exercices et tps présents dans le module. Un fichier `index.html` local permet de passer d'une présentation à l'autre facilement.

Architecture:

~~~bash
ABSPATH/

    #fichiers sources, privés
    vendor/
        cours-coursA-/module01-presentation/
        cours-coursB-/module01-presentation/
    #fichiers générés à partir des sources publiques, partageable avec les étudiants, presentations sans notes
    Public/
        cours-A/
            module01-presentation/.html, .pdf (presentation sans les notes de présentation, notes de cours)
            module02-intro-a-la-poo/.html, .pdf
        index.html
   #fichiers générés à partir des sources privés, identique à public mais avec les présentations+notes
    Privé/
        cours-A/
    index.html
    index.md
    README.md
~~~

- les noms de cours et de module sont au format slug (lowercase, sans espace, alphanumeriques)
- les numeros de module sont au format %d%d
- chaque cours a obligatoirement un module-01-presentation

## Scripts

- script `create-course {vendor} {niveau} {nom du cours}`, crée la structure suivante:
    - `Bibliographie`: stocke la biblio (fichiers PDF et autres, livres)
    - `README.md`: description breve du cours (durée, contenu, ressources, niveau des apprenants, remarques). Contient le plan et le planning initial.
    - `index.html`: permet de naviguer dans le cours au format HTML

- script `course-ls {nom du cours}`: liste le contenu du cours (modules présents)
- 
- script `create-module {nom du cours} {numero} {nom du module}`, genere un module du cours nom du cours (analyse des dossiers présents en slug) genere un module avec le nom et un numero (different de 0 qui est déjà pris par presentation). Cree le contenu suivant
    - `{nom du cours}/{nom du module}/cours/{numero}-{nom module}-{nom du cours}.md` : le fichier contenant le cours
    - `{nom du cours}/{nom du module}/Exercices`
    -` {nom du cours}/{nom du module}/TPS`
    - `{nom du cours}/{nom du module}/Exams`
    - `/Public/{nom-du-cours}/{nom-du-module}/ : contiendra tout le contenu généré à partir des fichiers markdown pour le module (cours, exercice, tp, exams) au format PDF et HTML (sans les commentaires cad mes notes de cours). Ce sera un dossier que je pourrai partager sans soucis avec les étudiants (aucune info privée).

- script `course-export {nom du cours} {opt nom du module}`: genere les fichiers html et pdf du cours et fait une copie dans le dossier `Public` et Privé (presentation avec notes). Met à jour l'index.html local au cours et l'index.html global.

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

### Au format `PPTX` (powerpoint) **lecture seule**

La conversion opère juste une conversion en images au format `pptx`. Le document peut être ouvert dans powerpoint mais **non éditable.**

~~~bash
marp  --html --allow-local-files --theme assets/css/main.css --pptx slides.md
~~~

### Au format `PPTX` (powerpoint) **éditable**

Pour convertir la présentation Markdown en `pptx` éditable, on peut 

- passer [par le format PDF](#au-format-pdf) puis utiliser un outil comme [PDF to Powerpoint Converter](https://pdf.online/pdf-to-powerpoint-converter) ou [Adobe](https://www.adobe.com/acrobat/online/pdf-to-ppt.html)


## Génération des documents au format HTML ou PDF

### Générer un fichier HTML+CSS a partir d'un fichier markdown (`marp`)

~~~bash
marp --html --allow-local-files {file.md}
~~~

### Générer la présentation au format PDF a partir d'un fichier markdown (`marp`)

~~~bash
marp --pdf --allow-local-files {file.md}
~~~

### Générer un PDF à partir du markdown en passant par HTML/CSS (`pandoc`)

~~~
pandoc {file.md} -t html5 -o {file.pdf} --css style.css --pdf-engine-opt=--enable-local-file-access
~~~

## Ressources

- [Site officiel du projet Markdown](https://daringfireball.net/projects/markdown/)
- [Marp écosystème](https://marp.app/)
- [Doc officielle Marp CLI](https://github.com/marp-team/marp-cli)
- [Marp for VS Code (extension)](https://marketplace.visualstudio.com/items?itemName=marp-team.marp-vscode)
- [marpit API : theme CSS](https://marpit.marp.app/theme-css)
- [Produire un powerpoint editable dans le workflow de Marp](https://github.com/marp-team/marp/discussions/82), l'export vers pptx de marp ne cree pas une présentation éditable mais une version images. Pour produire un ppt éditable il faut passer par le format pdf avant
- [Pandoc](https://pandoc.org/index.html), un convertisseur de document universel et éprouvé
- [Convertisseur pdf vers powerpoint](https://pdf.online/pdf-to-powerpoint-converter), un outil de conversion en ligne gratuit permettant de convertir un fichier PDF vers un fichier pptx **éditable**


