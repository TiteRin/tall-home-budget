# Dépenses mensuelles

## Objectifs

Permettre la saisie de dépenses mensuelles (e.g. courses, vétérinaires, etc.) et les inclure dans les charges que se
partagent les membres du foyer.

## Tâches

- [x] CRUD un onglet de dépenses (expenses_tab)
    - Nom
    - Plage mensuelle => e.g. du 5 au 5 du mois
    - Mode de répartition par défaut (nullable)
    - Membre du foyer responsable des dépenses par défaut (nullable)
- [x] Affichage des onglets
    - Affichage principal
    - Onglets de dépenses
- [x] CRUD dépenses (expenses)
- [ ] Affichage : affichage par mois
- [x] Calcul du total : par mois
- [x] Intégration dans les charges

## Draft

1. L’utilisateur va devoir paramétrer ses onglets de dépense : leur donner un nom, et sélectionner un jour de
   démarrage (e.g. le 5 du mois).
2. Il pourra aussi y paramétrer un compte par défaut, ainsi qu’une répartition par défaut. Sinon, la répartition sera
   égale à la répartition par défaut du foyer.
3. À tout moment, il peut modifier tout élément de ce paramétrage sans impacter les données existantes.
4. Une dépense est assez proche d’une facture : elle a un nom, un montant, un compte, un mode de répartition.
5. En plus de cela, la dépense est associée à un onglet de dépense, et possède une date d’effet.
6. Cette date d’effet permettra de calculer à quel mois la dépense appartient.
7. Chaque onglet de dépense fera apparaître un tableau avec l’ensemble des dépenses, le compte débiteur, le mode de
   répartition, la date d’effet, et le montant.
8. Le total de chaque onglet de dépenses indiquera : le montant total de la période et le montant total par compte.
9. Pour le moment, on ne calcule les totaux que sur le mois courant.
10. Dans une prochaine version, on permettra à l’utilisateur de passer sur les mois précédents.
11. Les totaux des onglets seront apparents dans la page de charge.
12. Un onglet de charge doit s’intégrer dans les calculs des mouvements mensuels, et doit donc donner aux dépenses du
    mois en cours (aux totaux ?)
13. Les onglets doivent s’afficher en bas de la page principale, comme des onglets de feuilles Excel
14. Les onglets sont par défaut ordonnés par date de création, mais l’utilisateur pourra les ordonner comme iel le
    souhaite (drag & drop, ordre à stocker en base)
15. Par défaut, toutes les dépenses sont affichées dans un onglet, avec une pagination. Cependant, les dépenses passées
    ou futures ont un style "disabled", et l’affichage/la pagination est centrée sur les dépenses du mois en cours
16. Il est possible de modifier une dépense à tout moment.
17. On ne stocke jamais le résultat des calculs, tout est calculé dynamiquement.

## UML
