<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class VehicleFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ['license_plate'];

    /* Filtre des chiffres clés de la page Véhicules (genre : particulier, utilitaire, taxi…). */
    public function vehicle_genre_id($value): void
    {
        $this->builder->whereIn('vehicles.vehicle_genre_id', array_filter(explode(',', (string) $value)));
    }
}
