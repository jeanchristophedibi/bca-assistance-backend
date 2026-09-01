<?php

namespace App\Enums;

use Essa\APIToolKit\Enum\Enum;
use App\Concerns\UsefulEnums;

enum ExpertiseTypeEnum: string
{
    use UsefulEnums;

    case STANDARD = 'standard';
    case JUDICIAL = 'judicial';
    case AGAINST_EXPERTISE = 'against_expertise'; 
    case VEHICLE_VOL = 'vehicle_vol';
    case EVALUATION = 'evaluation';
    case RISK_DIVERSE = 'risk_diverse';
}

