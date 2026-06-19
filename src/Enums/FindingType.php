<?php

declare(strict_types=1);

namespace PrivacyScanner\Enums;

enum FindingType: string
{
    case Secret = 'SECRET';
    case PersonalData = 'PII';
}
