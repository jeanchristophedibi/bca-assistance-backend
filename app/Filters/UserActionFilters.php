<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class UserActionFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [];

    /* Filtres des chiffres clés du journal (connexions, modifications de comptes…).
       `user_action_type` accepte les CODES de type (« login_user,logout_user ») :
       il n'existe pas de route qui expose les identifiants des types. */
    public function user_action_type($value): void
    {
        $codes = array_filter(explode(',', (string) $value));
        $this->builder->whereIn('user_actions.user_action_type_id', \App\Models\UserActionType::whereIn('code', $codes)->select('id'));
    }

    public function user_action_type_id($value): void
    {
        $this->builder->whereIn('user_actions.user_action_type_id', array_filter(explode(',', (string) $value)));
    }
}
