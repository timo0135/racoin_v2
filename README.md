## Racoin

Racoin est une application de vente en ligne entre particulier.

## Installation
Il suffit de lancer le container, installer les dépandances et lancer les scripts de création de la base de données.

```bash
docker compose run --rm php composer install
docker compose run --rm php php sql/initdb.php
```
