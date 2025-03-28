Voici la version complète mise à jour de la documentation pour ton projet **CustomerCareAPI** avec toutes les sections correctement détaillées, y compris l'intégration de Laravel pour le backend et Alpine.js pour le frontend.

---

# CustomerCareAPI

## Description
Le projet **CustomerCareAPI** consiste à développer une API avancée en **Laravel** pour la gestion d’un service client. L’API devra gérer les tickets d’assistance, permettre l’attribution de demandes aux agents, suivre l’état des requêtes et fournir un historique des interactions. L’objectif est de concevoir une API REST robuste en respectant les bonnes pratiques de développement et d’architecture.

Pour le frontend, **Alpine.js** est utilisé pour simplifier l'interactivité côté client tout en restant léger, permettant une gestion fluide des tickets côté utilisateur, tout en interagissant avec l’API backend.

---

## Technologies utilisées

- **Backend** : Laravel
- **Frontend** : Alpine.js
- **Base de données** : Postgres
- **Tests** : PHPUnit
- **Authentification** : Laravel Sanctum
- **Documentation API** : Swagger

---

## Installation et Prérequis

### Prérequis

Avant de commencer, assurez-vous d'avoir installé les outils suivants :

- PHP 7.8 ou plus récent
- Composer
- Node.js et npm
- Postgres

### Étapes d'installation

1. **Clonez le dépôt** :

   Clonez le dépôt GitHub du projet pour récupérer tous les fichiers nécessaires :
   ```bash
   git clone https://github.com/Youcode-Classe-E-2024-2025/Moustapha-Ndiaye-CustomerCareAPI.git
   ```

2. **Installez les dépendances backend avec Composer** :

   Une fois dans le répertoire du projet, installez les dépendances PHP avec Composer :
   ```bash
   cd Moustapha-Ndiaye-CustomerCareAPI
   composer install
   ```

3. **Installez les dépendances frontend avec npm (pour Alpine.js)** :

   Installez les dépendances nécessaires pour le frontend avec npm :
   ```bash
   npm install
   ```

4. **Configurer le fichier `.env`** :

   Configurez votre fichier `.env` avec les informations appropriées, telles que les informations de connexion à la base de données. Vous devrez aussi spécifier les clés d'API pour Laravel Sanctum pour la gestion de l'authentification.

5. **Créez la base de données et appliquez les migrations** :

   Créez la base de données dans Postgres, puis appliquez les migrations pour préparer la structure de la base de données :
   ```bash
   php artisan migrate
   ```

6. **Lancez le serveur local** :

   Une fois la configuration terminée, lancez le serveur local :
   ```bash
   php artisan serve
   ```

7. **Accédez à l'application** :

   Vous pouvez maintenant accéder à l'application via l'URL suivante :
   ```bash
   http://localhost:8000/login
   ```

---

## Documentation de l'API

La documentation de l'API est générée automatiquement via **Swagger**. Vous pouvez consulter cette documentation en accédant à l'URL suivante lorsque l'application est en cours d'exécution :

- [Documentation Swagger de l'API](http://localhost:8000/api/documentation)

### Endpoints principaux de l'API

Voici une liste des endpoints principaux que l'API expose :

1. **GET /api/tickets** : Liste tous les tickets d'assistance.
   - **Réponse** : Liste paginée des tickets.

2. **POST /api/tickets** : Crée un nouveau ticket d'assistance.
   - **Réponse** : Ticket créé avec les détails.

3. **PUT /api/tickets/{id}** : Met à jour un ticket existant.
   - **Réponse** : Ticket mis à jour.

4. **GET /api/tickets/{id}** : Récupère un ticket spécifique avec ses détails.
   - **Réponse** : Détails du ticket.

5. **POST /api/tickets/{id}/responses** : Ajoute une réponse à un ticket.
   - **Réponse** : Réponse ajoutée au ticket.

---

## Structure de l'application

### Modèles

1. **User** : Représente un utilisateur qui peut soumettre des tickets et interagir avec eux.
2. **Ticket** : Représente un ticket d'assistance, avec des informations comme le statut, la description, l'agent affecté et les réponses associées.
3. **Response** : Représente une réponse à un ticket, émise par un agent du service client.

### Contrôleurs

1. **TicketController** : Gère la logique pour les tickets (création, suivi, mise à jour).
2. **ResponseController** : Gère l'ajout de réponses aux tickets.
3. **AuthController** : Gère l'authentification des utilisateurs via Laravel Sanctum.

---

## Sécurité

Le projet utilise **Laravel Sanctum** pour l'authentification des utilisateurs. Cela permet de protéger les endpoints API, garantissant que seules les requêtes authentifiées peuvent accéder aux données sensibles.

### Middleware d'authentification

L'application utilise le middleware `auth:sanctum` pour protéger les endpoints nécessitant une authentification. Les utilisateurs doivent s'authentifier via une clé API (token) générée par Sanctum avant d'accéder aux tickets et de soumettre des réponses.

---

## Tests avec PHPUnit

Des tests unitaires sont ajoutés pour garantir le bon fonctionnement de l'API. Voici comment exécuter les tests :

1. **Exécuter les tests PHPUnit** :
   ```bash
   php artisan test
   ```

Les tests couvrent les principales fonctionnalités de l'API, notamment la validation des endpoints, la gestion des erreurs et la validation des données envoyées.

---

## Consommation de l'API avec Alpine.js

Le frontend de l'application est construit avec **Alpine.js**, qui permet une gestion légère et réactive des tickets côté client.

### Exemple d'intégration Alpine.js

Voici un exemple simple d'utilisation d'Alpine.js pour afficher les tickets récupérés via l'API :

```html
<div x-data="{
    tickets: [],
    loadTickets() {
        fetch('/api/tickets')
            .then(response => response.json())
            .then(data => {
                this.tickets = data.data;
            });
    }
}" x-init="loadTickets()">
    <template x-for="ticket in tickets" :key="ticket.id">
        <div class="ticket">
            <h3 x-text="ticket.title"></h3>
            <p x-text="ticket.status"></p>
        </div>
    </template>
</div>
```

---

## Gestion des erreurs API

Les erreurs sont gérées correctement dans l'API et les messages d'erreur sont renvoyés dans un format structuré avec des codes HTTP appropriés.

### Exemple de réponse d'erreur

```json
{
    "error": {
        "message": "Ticket not found",
        "code": 404
    }
}
```

---

## Gestion du projet

Le projet est géré via **GitHub** en utilisant une méthodologie **Agile**, avec un **backlog** et un **Kanban** sur **Trello** pour organiser les tâches.

---

## Conclusion

Ce projet vise à fournir une solution efficace et sécurisée pour la gestion des tickets d'assistance. L’utilisation de Laravel pour le backend garantit une architecture robuste, tandis qu'Alpine.js permet une gestion réactive et légère du frontend. Grâce à cette API, les entreprises peuvent facilement centraliser et gérer leurs requêtes clients, assurant ainsi un service client réactif et organisé.

---

