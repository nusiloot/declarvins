# Spécifications techniques de l'implémentation du service EDI sur le portail DeclarVins à destination des interprofessions

## Architecture technique de sécurité

### Authentification des utilisateurs

L'interface EDI n'est accessible qu'après authentification. L'authentification nécessite que l'utilisateur possède un compte sur la plateforme de télédéclaration DeclarVins. Une fois ce compte créé, l'utilisateur pourra s'identifier sur la plateforme EDI en fournissant son login et mot de passe via le protocole d'authentification HTTP (HTTP Authentication Basic [1]).

Les informations relatives aux identifiants/mots de passe, aux cookies ou aux authentifications HTTP seront transférées en HTTPS [2] comme tout le reste des informations.

### Protocole technique utilisé

L'EDI mis à disposition est accessible à travers le protocole HTTPS. Pour l'envoi d'information, la méthode POST x-www-form-urlencoded [3] doit être implémentée.

### Échange de données

Les données échangées en mode lecture ou écriture se font sous le format CSV [4]. La plateforme supporte indifféremment les séparateurs virgules (« , ») ou point-virgules (« ; »). En revanche, il est nécessaire qu'un seul type de séparateur soit utilisé  au sein d'un même document.

La plateforme de télédéclération est insensible à la casse et aux caractères accentués. Les chaines de caractères « Côte » ou « cote » seront donc traitées de manière identique.
Il faut noter toute fois, qu'en cas d'utilisation de caractères accentués, ces caractères devront être encodés en UTF-8 [5]. 

Débuter une ligne par le caractère « # » permet de définir des commentaires. Elles ne sont donc pas prises en compte par la plateforme.

Les nombres décimaux peuvent avoir pour séparateur décimal une virgule « , » ou un point « . ». Dans le cas ou la virgule « , » est choisi, bien faire attention qu'il n'y ait pas de confusion avec le séparateur du CSV.

### Sécurité des transferts

Toutes les connexions réalisées sur l'interface de saisie des DRM se feront via le protocole HTTPS [2].

### Domaine dédié à l'EDI

Le nom de domaine de pré-production est : https://edi-preprodv2.declarvins.net   

Le nom de domaine de production est : https://edi.declarvins.net

### Envoi des informations par EDI

Voici les détails téchnique pour accéder au webservice d'envoi EDI :

 - Protocole : HTTPS
 - Authentification : HTTP Authentication Basic
 - Encodage des caractères : UTF-8
 - Format des données à fournir en entrée : CSV
 - Format des données fournies en sortie : Aucun ou CSV
 - Type de requete : POST x-www-form-urlencoded
 - URL : *mis à disposition sur le portail DeclarVins*
 
## Interface EDI DRM

L'url de récupération des DRM pour une interprofession est : 

/edi.php/edi/drm/\<\<interpro\>\>/\<\<date\>\>

 * \<\<interpro\>\> : correspond à l'identifiant de l'interprofession
 * \<\<date\>\> : correspondant à la date au format ISO 8601 [6] à partir de laquelle les DRM ont été saisies (le format horaire 00h00m00 est aussi accepté)
 
La nomenclature du fichier CSV retourné est : 

DRM_\<\<date demandée\>\>_\<\<date de saisie de la dernière DRM retournée\>\>.csv 

Les dates de la nomenclature sont aux formats : 

aaaa-mm-jjT00h00m00 (en effet le spérateur ":" du format horaire ISO 8601 [6] n'est pas un caractère autorisé en nom de fichier). 

### Spécification complète du format d'export des DRM

*informations à venir*

## Interface EDI Contrat d'achat

L'url de récupération des contrats d'achat pour une interprofession est : 

/edi.php/edi/vrac/\<\<interpro\>\>/\<\<date\>\>

 * \<\<interpro\>\> : correspond à l'identifiant de l'interprofession
 * \<\<date\>\> : correspondant à la date au format ISO 8601 [6] à partir de laquelle les Contrats ont été saisies (le format horaire 00h00m00 est aussi accepté)
 
Cet export fournira la liste complète des contrats de vente visés dont les produits concernent l'interprofession désignée.

La nomenclature du fichier CSV retourné est : 

VRAC_\<\<date demandée\>\>_\<\<date de saisie du dernier contrat retourné\>\>.csv 

### Spécification complète du format d'export des contrats de vente

1. Numéro de VISA du contrat
2. Date de statistique : date (format ISO 8601 (AAAA-MM-JJTHH:MM:SS)) de validation/signature du contrat (signé par l'ensemble des acteurs au contrat) ou date renseigné par l'opérateur qui saisi le contrat
3. Date de signature :  date (format ISO 8601 (AAAA-MM-JJTHH:MM:SS)) à laquelle le contrat a été validé / signé par l'ensemble des acteurs au contrat
4. Identifiant DeclarVins Acheteur
5. CVI de l'Acheteur
6. SIRET de l'Acheteur
7. Nom de l'Acheteur
8. Identifiant DeclarVins du Vendeur
9. CVI du Vendeur
10. SIRET du Vendeur
11. Nom du Vendeur
12. Identifiant DeclarVins du Courtier
13. SIRET du Courtier
14. Nom du Courtier
15. Type de produit : vrac / raisin / mout
16. Le libellé de la certification
17. Le code de la certification
18. Le libellé du genre
19. Le code du genre
20. Le libellé de l'appellation
21. Le code de l'appellation
22. Le libellé du lieu
23. Le code du lieu
24. Le libellé de la couleur
25. Le code de la couleur
26. Le libellé du cepage
27. Le code du cepage
28. Millésime 
29. Millésime (historique : doublon avec 28)
30. Labels : Libellés des labels (Conventionnel, Bio, Bio en conversion, HVE 3, Autre ou Libellé précisé à la saisie), cumulables à l'aide du séparateur « | » (pipe)
31. Codes DeclarVins Labels : Codes des labels (conv, biol, bioc, hve, autre), cumulables à l'aide du séparateur « | » (pipe)
32. Mentions : Libellés des mentions (Domaine, autre terme règlementé, Primeur, Marque, Autre ou Libellé précisé à la saisie), cumulables à l'aide du séparateur « | » (pipe)
33. Codes DeclarVins Mentions : Codes des labels (chdo, prim, marque, autre), cumulables à l'aide du séparateur « | » (pipe)
34. Condition Particulière : aucune (Aucune) / union (Apport contractuel à une union) / interne (Contrat interne entre deux entreprises liées)
35. Première Mise en Marché : 0 ou 1
36. Annexes: 0 ou 1
37. Volume ou quantité total du contrat : en HL pour le vrac et mout, en KG pour le raisin
38. Prix Unitaire : Prix unitaire net HT hors cotisation en HL pour le vrac et mout, en KG pour le raisin
39. Type de Prix : definitif / objectif / acompte
40. Mode de détermination du prix : Texte libre détaillant le mode de détermination du prix dans le cas d'un prix d'objectif ou d'acompte
41. Expédition Export : 0 ou 1
42. Paiement : echeancier_paiement (Echéancier dérogatoire selon accords interprofessionnels) / cadre_reglementaire (Cadre Réglementaire Général)
43. Référence contrat pluriannuel : Référence du contrat dans le cas d'un contrat pluriannuel, null (vide) le cas échéant (contrat ponctuel)
44. Le Vin sera : livre / retire 
45. Date de début de Retiraison : date de début de retiraison prévue au contrat (AAAA-MM-JJ)
46. Date Limite de Retiraison : date limite de retiraison prévue au contrat (AAAA-MM-JJ)
47. Dates échéance : dates de paiement dans le cas d'un échéancier (AAAA-MM-JJ), cumulables à l'aide du séparateur « | » (pipe)
48. (historique) champs vide
49. Montant échéance : Montant en euros correspondant à chaque échéance, cumulables à l'aide du séparateur « | » (pipe)
50. Numéro de Lot
51. Numéros des Cuves : cumulables à l'aide du séparateur « | » (pipe)
52. Volume des Cuves : cumulables à l'aide du séparateur « | » (pipe)
53. Dates de retiraison des Cuves : cumulables à l'aide du séparateur « | » (pipe)
54. Assemblage dans le lot : 0 ou 1
55. Millésimes dans le lot : cumulables à l'aide du séparateur « | » (pipe)
56. Pourcentage des Millésimes dans le lot : cumulables à l'aide du séparateur « | » (pipe)
57. Degré du lot
58. Allergènes : 0 ou 1
59. Statut du contrat : NONSOLDE / SOLDE / ANNULE
60. Commentaires : texte saisi par l'operateur
61. Version du contrat : null (vide) si contrat non rectifié/modifié. Sinon indique M (contrat modificatif) ou R (contrat rectificatif) + numéro de version
62. Contrat référent : 0 ou 1
63. Mode de saisie : PAPIER si saisie par l'interprofession, DTI si saisie par le déclarant, EDI si échange informatique avec logiciel déclarant
64. Date de saisie  : date (format ISO 8601 (AAAA-MM-JJTHH:MM:SS)) à laquelle le contrat a été soumis
65. Date de validation/VISA  : date (format ISO 8601 (AAAA-MM-JJTHH:MM:SS)) de validation/signature du contrat (signé par l'ensemble des acteurs au contrat) ou date renseigné par l'opérateur qui saisi le contrat
66. Observations : texte saisi par le télédéclarant
67. Bailleur à fruit à métayer : 0 ou 1
68. Date de l'envoi à l'OCo : date (AAAA-MM-JJ) de mise à disposition des informations de transaction
69. Date de chargement par l'OCo : date (AAAA-MM-JJ) de mise à disposition des informations de transaction (historique : doublon avec 68)
70. Date de traitement par l'OCo : date (AAAA-MM-JJ) de traitement du contrat par l'organisme
71. Adresse de stockage SIRET
72. Adresse de stockage Nom commercial
73. Adresse de stockage Adresse
74. Adresse de stockage Code Postal
75. Adresse de stockage Commune
76. Adresse de stockage Pays
77. Adresse de stockage Présence : 0 ou 1
78. Numéro d'Accises de l'Acheteur
78. Numéro d'Accises du Vendeur
79. Date de détermination du prix : date (AAAA-MM-JJ) de détermination du prix dans le cas d'un prix d'objectif ou d'acompte
80. Campagne du contrat
81. Prix total du contrat
82. Mois de la date de statistique
83. Année de la date de statistique
84. Type de retiraison : vrac (Retiraison/Livraison en Vrac) / tire_bouche (Retiraison/Livraison en Tiré Bouché) / lattes (Retiraison/Livraison sur Lattes)
85. Delai de paiement : 60_jours / 45_jours / autre
86. Prix Unitaire HL : Prix unitaire net HT hors cotisation en HL estimé pour le raisin (= prix unitaire pour le vrac et mout)
87. Prix total du contrat en HL : Prix total du contrat en HL pour le raisin (= prix total du contrat pour le vrac et mout)


   [1]: https://fr.wikipedia.org/wiki/Authentification_HTTP
   [2]: https://tools.ietf.org/html/rfc2818
   [3]: http://www.w3.org/TR/html401/interact/forms.html#h-17.13.4.1
   [4]: https://fr.wikipedia.org/wiki/Comma-separated_values
   [5]: https://fr.wikipedia.org/wiki/UTF-8
   [6]: https://fr.wikipedia.org/wiki/ISO_8601
