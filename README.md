# PostBundle
Work still in progress
Will be a bundle to handle simple posts within a Symfony Desktop App.
Will be part of an AdminBundle.

(c) mesclics.fr 2018

___

# Collections
## Config
Afin de pouvoir utiliser les collections, il faut définir les types d'objets disponibles pour la constitution de nouvelles collections.
Pour cela, il suffit d'ajouter au fichier config.yml de l'app (app > Resources > config.yml) un paramètre admin.collections contenant un tableau associatif ('Label pour les objets': 'entité concernée').
### Exemple
```yaml
parameters:
    admin.collections: { 'Publication': 'post', 'Message': 'message', 'Collection': 'collection'}
```
Ici, on permettra la constitution de collections de publications, de messages et de collections.

