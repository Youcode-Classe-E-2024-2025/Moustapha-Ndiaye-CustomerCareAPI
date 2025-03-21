# CustomerCareAPI

## Description
Le projet CustomerCareAPI consiste à développer une API avancée en Laravel pour la gestion d’un service client. L’API devra gérer les tickets d’assistance, permettre l’attribution de demandes aux agents, suivre l’état des requêtes et fournir un historique des interactions. L’objectif est de concevoir une API REST robuste en respectant les bonnes pratiques de développement et d’architecture, puis de la consommer via n’importe quel framework JS (Vue.js, React, Angular, etc.).


## Technologies utilisées
- **Backend** : Laravel
- **Frontend** : AlpineJs
- **Base de données** : Postgres
- **Tests** : PHPUnit
- **Authentification** : Laravel Sanctum

## Installation et Prérequis

### Prérequis
Avant de commencer, assurez-vous d'avoir installé les outils suivants :
- PHP 78
- Composer
- Node.js et npm
- Postgres


### Étapes d'installation
1. Clonez le dépôt :
   ```bash
   git clone https://github.com/Youcode-Classe-E-2024-2025/Moustapha-Ndiaye-CustomerCareAPI.git
   ```

2. Installez les dépendances backend avec Composer :
   ```bash
   cd Moustapha-Ndiaye-CustomerCareAPI
   composer install
   ```

3. Installez les dépendances frontend avec npm :
   ```bash
   npm install
   ```

4. Configurez votre fichier `.env` avec les informations appropriées, telles que les informations de connexion à la base de données.

5. Créez la base de données et appliquez les migrations :
   ```bash
   php artisan migrate
   ```

6. Lancez le serveur local :
   ```bash
   php artisan serve
   ```

7. Accédez à l'application à l'adresse suivante : `http://localhost:8000`

