<?php

namespace App\Models;

use App\Filters\ExpertiseCarriedOutFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ExpertiseCarriedOut extends Model
{
    use HasFactory, Filterable;

    protected string $default_filters = ExpertiseCarriedOutFilters::class;

    /**
     * Mass-assignable attributes.
     *
     * @var array
     */
    protected $guarded = [];


}
