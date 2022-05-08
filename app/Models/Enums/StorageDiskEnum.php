<?php

namespace App\Models\Enums;

/**
 * @method static PUBLIC_S3()
 * @method static LOCAL()
 */
class StorageDiskEnum extends Enumerate
{
    const PUBLIC_S3 = 's3';
    const LOCAL = 'local';
}
