# Workflow de Traitement des Dossiers

Ce document décrit le processus complet de traitement des dossiers (assignments) dans le système, depuis leur création jusqu'à leur clôture.

## Vue d'ensemble

Le workflow des dossiers suit un processus séquentiel avec plusieurs étapes de validation et de correction. Chaque étape correspond à un statut spécifique et nécessite des permissions appropriées.

## États du Dossier

### États Principaux

1. **PENDING_FOR_OPENING** - En attente d'ouverture
2. **OPENED** - Ouvert
3. **REALIZED** - Réalisé
4. **PENDING_FOR_REPAIRER_QUOTE** - En attente de devis du réparateur
5. **PENDING_FOR_REPAIRER_QUOTE_VALIDATION** - En attente de validation du devis
6. **IN_EDITING** - En rédaction
7. **IN_FIRST_CORRECTION** - En première correction
8. **IN_SECOND_CORRECTION** - En deuxième correction
9. **FOR_CORRECTION** - Pour correction
10. **PENDING_FOR_REPAIRER_VALIDATION** - En attente de validation réparateur
11. **PENDING_FOR_EXPERT_VALIDATION** - En attente de validation expert
12. **VALIDATED** - Validé
13. **PAID** - Payé
14. **CANCELLED** - Annulé
15. **CLASSIFIED_WITHOUT_FURTHER_ACTION** - Classé sans suite
16. **CLOSED** - Clôturé

## Workflow Détaillé

### 1. Création du Dossier

**Action** : `CREATE_ASSIGNMENT`  
**Rôle** : Admin, Expert Manager, Expert, Opener, Business Developer, Editor Manager, Accountant Manager, Accountant, Insurer Admin, Insurer Standard User  
**Statut initial** : `PENDING_FOR_OPENING`

**Description** :
- Un dossier est créé avec les informations de base (véhicule, assureur, réparateur, client, etc.)
- Le dossier est assigné à un ou plusieurs experts
- Le statut est défini sur "En attente d'ouverture"

**Données requises** :
- Type de dossier
- Type d'expertise
- Informations du véhicule
- Informations de l'assureur
- Informations du client
- Date d'expertise prévue
- Lieu d'expertise

---

### 2. Ouverture du Dossier

**Action** : `OPEN_ASSIGNMENT`  
**Rôle** : System Admin, Expert, Opener, Business Developer, Editor Manager, Validator  
**Statut précédent** : `PENDING_FOR_OPENING`  
**Statut suivant** : `OPENED`

**Description** :
- Le dossier est officiellement ouvert
- Une notification est envoyée
- Une fiche d'expertise PDF est générée automatiquement
- La date d'ouverture est enregistrée

**Conditions** :
- Le dossier doit être en statut "En attente d'ouverture"

---

### 3. Réalisation de l'Expertise

**Action** : `REALIZE_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Opener, Business Developer, Editor Manager  
**Statut précédent** : `OPENED`  
**Statut suivant** : `REALIZED`

**Description** :
- L'expert réalise l'expertise sur le terrain
- Les informations de l'expertise sont saisies (kilométrage, date, lieu, points notés)
- Le réparateur peut être assigné ou modifié
- Le directeur de l'expertise est enregistré
- Une nouvelle fiche d'expertise PDF est générée

**Données requises** :
- Kilométrage du véhicule
- Date d'expertise réelle
- Lieu d'expertise
- Points notés
- Directeur de l'expertise

**Conditions** :
- Le dossier doit être en statut "Ouvert"

---

### 4. Création de la Fiche de Travail (Worksheet)

**Action** : `CREATE_WORKSHEET_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert  
**Statut** : Peut être créée à différents moments du workflow

**Description** :
- Une fiche de travail détaillée est créée
- Elle contient les travaux à effectuer, les chocs, les points de choc, etc.

---

### 5. Validation de la Fiche de Travail

**Action** : `VALIDATE_WORK_SHEET_BY_EXPERT_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert  
**Statut** : Variable selon le workflow

**Description** :
- La fiche de travail est validée par l'expert
- Peut être dévalidée si nécessaire

---

### 6. Création du Devis (Quote)

**Action** : `CREATE_QUOTE_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Repairer Admin, Repairer Standard User  
**Statut précédent** : `REALIZED`  
**Statut suivant** : `PENDING_FOR_REPAIRER_QUOTE` ou `PENDING_FOR_REPAIRER_QUOTE_VALIDATION`

**Description** :
- Un devis est créé par le réparateur ou l'expert
- Le devis contient les travaux à effectuer, les pièces, les coûts
- Le statut passe en "En attente de devis du réparateur" ou "En attente de validation du devis"

---

### 7. Validation du Devis

#### 7.1. Validation par le Réparateur

**Action** : `VALIDATE_QUOTE_ASSIGNMENT` (par réparateur)  
**Rôle** : Repairer Admin, Repairer Standard User  
**Statut précédent** : `PENDING_FOR_REPAIRER_QUOTE`  
**Statut suivant** : `PENDING_FOR_REPAIRER_QUOTE_VALIDATION`

#### 7.2. Validation par l'Expert

**Action** : `VALIDATE_QUOTE_ASSIGNMENT` ou `VALIDATE_QUOTE_WITH_CONDITION_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert  
**Statut précédent** : `PENDING_FOR_REPAIRER_QUOTE_VALIDATION`  
**Statut suivant** : `IN_EDITING` ou reste en attente si validation conditionnelle

**Description** :
- L'expert valide le devis définitivement ou avec conditions
- Si validation conditionnelle, le dossier reste en attente jusqu'à validation définitive
- Si validation définitive, le dossier passe en rédaction

**Actions possibles** :
- Validation définitive → passe en rédaction
- Validation avec conditions → reste en attente
- Annulation du devis → retour en arrière

---

### 8. Rédaction du Rapport

**Action** : `EDIT_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Opener, Business Developer, Editor Manager, Editor
**Statut précédent** : `IN_EDITING` (après validation du devis)  
**Statut suivant** : `IN_EDITING`

**Description** :
- Le rapport d'expertise est rédigé
- Toutes les informations de l'expertise sont consignées
- Le rapport PDF est généré
- Le dossier passe directement en première correction après validation de la rédaction

**Conditions** :
- Le devis doit être validé définitivement (si applicable)

---

### 9. Validation de la rédaction du Rapport

**Action** : `VALIDATE_EDITION_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Opener, Business Developer, Editor Manager  
**Statut précédent** : `IN_EDITING` (après validation du devis)  
**Statut suivant** : `IN_FIRST_CORRECTION`

**Description** :
- Le rapport d'expertise est rédigé
- Toutes les informations de l'expertise sont consignées
- Le rapport PDF est généré
- Le dossier passe directement en première correction après validation de la rédaction

**Conditions** :
- Le devis doit être validé définitivement (si applicable)

---

### 10. Corrections

#### 10.1. Première Correction

**Action** : `VALIDATE_FIRST_CORRECTION`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Editor Manager  
**Statut précédent** : `IN_FIRST_CORRECTION`  
**Statut suivant** : `IN_SECOND_CORRECTION`

**Description** :
- La première correction est validée
- Le dossier passe en deuxième correction

#### 10.2. Deuxième Correction

**Action** : `VALIDATE_SECOND_CORRECTION`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Editor Manager  
**Statut précédent** : `IN_SECOND_CORRECTION`  
**Statut suivant** : `PENDING_FOR_REPAIRER_VALIDATION`

**Description** :
- La deuxième correction est validée
- Le dossier passe en attente de validation réparateur

#### 10.3. Retour pour Correction

**Action** : `UNVALIDATE_ASSIGNMENT` ou `UNVALIDATE_EDITED_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Editor Manager  
**Statut suivant** : `FOR_CORRECTION` ou `IN_EDITING`

**Description** :
- Le dossier est renvoyé pour correction
- Les raisons de la correction sont enregistrées
- Le dossier peut être modifié et renvoyé

---

### 11. Validation par le Réparateur

**Action** : `VALIDATE_BY_REPAIRER_ASSIGNMENT`  
**Rôle** : Repairer Admin, Repairer Standard User  
**Statut précédent** : `PENDING_FOR_REPAIRER_VALIDATION`  
**Statut suivant** : `PENDING_FOR_EXPERT_VALIDATION`

**Description** :
- Le réparateur valide le rapport d'expertise
- Le dossier passe en attente de validation expert

**Actions possibles** :
- Validation → passe en attente validation expert
- Dévalidation → retour pour correction

---

### 12. Validation par l'Expert

**Action** : `VALIDATE_BY_EXPERT_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert  
**Statut précédent** : `PENDING_FOR_EXPERT_VALIDATION`  
**Statut suivant** : `VALIDATED`

**Description** :
- L'expert valide définitivement le rapport
- Le dossier est marqué comme validé
- Le statut passe à "Validé"

**Actions possibles** :
- Validation → dossier validé
- Dévalidation → retour pour correction

---

### 13. Facturation et Paiement

**Action** : Création de facture et paiement  
**Rôle** : CEO, Accountant Manager, Accountant  
**Statut précédent** : `VALIDATED`  
**Statut suivant** : `PAID`

**Description** :
- Une facture est générée pour le dossier validé
- Le paiement est enregistré
- Le statut passe directement à "Payé"

---

### 14. Clôture du Dossier

**Action** : `CLOSE_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Editor Manager, Accountant Manager, Accountant  
**Statut précédent** : `VALIDATED`, `PAID`, ou autres selon le workflow  
**Statut suivant** : `CLOSED`

**Description** :
- Le dossier est clôturé
- La raison de clôture est enregistrée
- Le dossier est archivé

**Données requises** :
- Raison de clôture

---

### 15. Annulation du Dossier

**Action** : `CANCEL_ASSIGNMENT`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Opener, Business Developer, Editor Manager  
**Statut suivant** : `CANCELLED`

**Description** :
- Le dossier est annulé
- La raison d'annulation est enregistrée
- Le dossier ne peut plus être modifié

**Données requises** :
- Raison d'annulation

---

### 16. Classement sans Suite

**Action** : `CLASSIFY_WITHOUT_FURTHER_ACTION`  
**Rôle** : Admin, CEO, Expert Manager, Expert, Editor Manager  
**Statut suivant** : `CLASSIFIED_WITHOUT_FURTHER_ACTION`

**Description** :
- Le dossier est classé sans suite
- Aucune action supplémentaire n'est requise

---

## Diagramme de Workflow

```
CRÉATION
   ↓
PENDING_FOR_OPENING
   ↓
OPENED
   ↓
REALIZED
   ↓
PENDING_FOR_REPAIRER_QUOTE
   ↓
PENDING_FOR_REPAIRER_QUOTE_VALIDATION
   ↓
IN_EDITING
   ↓
IN_FIRST_CORRECTION
   ↓
IN_SECOND_CORRECTION
   ↓
PENDING_FOR_REPAIRER_VALIDATION
   ↓
PENDING_FOR_EXPERT_VALIDATION
   ↓
VALIDATED
   ↓
PAID
   ↓
CLOSED
```

## Points de Retour en Arrière

Le workflow permet plusieurs retours en arrière :

1. **Dévalidation du devis** → Retour à `PENDING_FOR_REPAIRER_QUOTE`
2. **Dévalidation de la rédaction** → Retour à `FOR_CORRECTION` ou `IN_EDITING`
3. **Dévalidation par réparateur** → Retour à `FOR_CORRECTION`
4. **Dévalidation par expert** → Retour à `FOR_CORRECTION`

## Permissions par Rôle

### System Admin
- Toutes les permissions de visualisation
- Génération de dossiers
- Ouverture de dossiers
- Gestion des configurations

### Admin
- Toutes les actions sur les dossiers
- Validation complète du workflow
- Annulation et clôture

### Expert Manager
- Gestion complète des dossiers
- Validation des étapes expert
- Gestion des experts

### Expert
- Création, réalisation, rédaction
- Validation des devis et rapports
- Validation expert

### Opener (Réceptionniste)
- Création de dossiers
- Ouverture de dossiers
- Réalisation de base

### Business Developer (Commercial)
- Création de dossiers
- Suivi des dossiers
- Génération de rapports

### Editor Manager
- Rédaction et validation
- Gestion des corrections
- Validation des rédactions

### Validator
- Validation des dossiers
- Validation des corrections

### Accountant Manager / Accountant
- Gestion de la facturation
- Gestion des paiements
- Clôture des dossiers

### Repairer Admin / Repairer Standard User
- Création et validation de devis
- Validation des rapports

### Insurer Admin / Insurer Standard User
- Création de dossiers
- Visualisation des dossiers

## Notes Importantes

1. **Ordre des étapes** : Certaines étapes doivent être suivies dans un ordre spécifique
2. **Validations conditionnelles** : Les validations avec conditions bloquent la progression jusqu'à validation définitive
3. **Corrections** : Les corrections peuvent être multiples (première, deuxième, etc.)
4. **Notifications** : Des notifications automatiques sont envoyées à certaines étapes
5. **Génération PDF** : Des PDFs sont générés automatiquement (fiche d'expertise, rapport)
6. **Traçabilité** : Toutes les actions sont tracées avec les utilisateurs et dates

## Exceptions et Cas Spéciaux

- Un dossier peut être annulé à tout moment (sauf s'il est déjà clôturé)
- Un dossier peut être classé sans suite à tout moment
- Les corrections peuvent être multiples
- Les validations peuvent être dévalidées pour permettre les corrections

