# Adimeo Test Module

## Description
Le module **Adimeo Test** est conçu pour fournir un bloc personnalisé qui affiche des événements liés sur la page de détail d'un événement. De plus, ce module gère un `QueueWorker` pour dépublier les événements dont la date de fin est dépassée.

## Composants du Module

### 1. **Bloc personnalisé: EventRelatedBlock**
   - Fichier: `src/Plugin/Block/EventRelatedBlock.php`
   - Description: Ce bloc affiche trois autres événements du même type que l'événement courant, ordonnés par date de début (ascendant) et dont la date de fin n'est pas dépassée. Si moins de trois événements du même type sont trouvés, il complète avec un ou plusieurs événements d'autres types.

### 2. **QueueWorker: EventUnpublishQueueWorker**
   - Fichier: `src/Plugin/QueueWorker/EventUnpublishQueueWorker.php`
   - Description: Ce QueueWorker est responsable de dépublier les événements dont la date de fin est dépassée. Il est exécuté via une tâche cron.

### 3. **Configuration du Bloc**
   - Fichier: `config/install/block.block.relatedevents.yml`
   - Description: Ce fichier configure le bloc `related_events_block` pour être visible uniquement sur les pages de nœuds de type "event" et le place dans la région `content` du thème `test_drupal`.

### 4. **Fichier du Module Principal**
   - Fichier: `adimeo_test.module`
   - Description: Ce fichier contient la logique principale du module, y compris les hooks nécessaires à l'intégration du bloc personnalisé et du `QueueWorker`.

## Installation

1. Placez le dossier `adimeo_test` dans le répertoire `modules/custom` de votre installation Drupal.
2. Activez le module en utilisant la commande suivante :
   ```bash
   drush en adimeo_test
