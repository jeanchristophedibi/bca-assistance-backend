<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class OfferFilters extends QueryFilters
{
    // `status_id` est un CODE de statut (« pending »…), traité par OfferController::index :
    // le laisser ici comparerait aussi la colonne numérique à ce code et ferait échouer la requête.
    protected array $allowedFilters = [
        'ids',
    ];

    protected array $allowedIncludes = [
        'comparison',
        'repairer',
        'status',
        'offerShocks',
        'createdBy',
        'updatedBy',
        'deletedBy',
    ];

    protected array $columnSearch = [];
}
