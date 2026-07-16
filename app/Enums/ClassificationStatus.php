<?php

namespace App\Enums;

enum ClassificationStatus: string
{
    case Classified = 'classified';
    case NoRule = 'no_rule';
}
