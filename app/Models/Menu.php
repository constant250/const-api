<?php

namespace App\Models;

use App\Interfaces\ImageableInterface;
use App\Models\Enums\StorageDiskEnum;
use App\Models\Services\Factories\FileServiceFactory;
use App\Traits\ImageableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model implements ImageableInterface
{
    use HasFactory;
    use ImageableTrait;

    public function getRootDestinationPath(string $dir = null): string
    {
        $rootPath = "/menus/{$this->id}";

        if ($dir) {
            $rootPath .= '/' . trim($dir, '/');
        }

        return $rootPath;
    }

    public function FileServiceFactory(string $dir = null)
    {
        return FileServiceFactory::resolve($this, StorageDiskEnum::LOCAL(), $dir);
    }
}
