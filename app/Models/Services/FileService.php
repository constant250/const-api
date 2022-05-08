<?php

namespace App\Services;

use App\Interfaces\ImageableInterface;
use App\Models\Enums\StorageDiskEnum;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileService.
 */
class FileService
{
    /**
     * @var ImageableInterface
     */
    private $imageable;

    /**
     * @var string $root
     */
    private $root;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var Filesystem
     */
    private $localSystem;

    /**
     * @var StorageDiskEnum
     */
    private $storageDisk;

    public static function resolve(ImageableInterface $imageable, StorageDiskEnum $disk, string $dir = null)
    {
        /** @var FileService $fileService */
        $fileService = app(self::class);
        $fileService->imageable = $imageable;
        $fileService->root = $imageable->getRootDestinationPath($dir);
        $fileService->fileSystem = Storage::disk($disk->value);
        $fileService->localSystem = Storage::disk('local');
        $fileService->storageDisk = $disk;

        return $fileService;
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getFilePath(string $filename)
    {
        $filePath = "{$this->root}/{$filename}";
        return $this->fileSystem->url($filePath);
    }

    /**
     * @return array
     */
    public function getDirectoryFiles()
    {
        $systemFiles = $this->fileSystem->files($this->root);

        $files = [];
        foreach ($systemFiles as $file) {
            $files[] = [
                'name' => last(explode('/', $file)),
                'path' => $this->fileSystem->url($file)
            ];
        }

        return $files;
    }

    /**
     * @param UploadedFile $file
     * @param string $nameWithoutExtension
     * @return array
     */
    public function uploadFile(UploadedFile $file, string $nameWithoutExtension): array
    {
        // clean up special character and space
        $nameWithoutExtension = preg_replace('/[^a-z0-9 -]+/', '', $nameWithoutExtension);
        $nameWithoutExtension = str_replace(' ', '-', $nameWithoutExtension);

        $filename = "{$nameWithoutExtension}.{$file->getClientOriginalExtension()}";
        $filePath = "{$this->root}/{$filename}";

        $options = '';

        if ($this->storageDisk->is(StorageDiskEnum::PUBLIC_S3())) {
            $options = 'public';
        }

        $this->fileSystem->put($filePath, file_get_contents($file), $options);

        return [
            'name' => $filename,
            'path' => $this->fileSystem->url($filePath),
            'dir_path' => $filePath
        ];
    }

    public function deleteFile(string $filename)
    {
        $filePath = "{$this->root}/{$filename}";
        $this->fileSystem->delete($filePath);
    }

    // download file from s3 bucket to local filesystem and returns local file path
    public function getLocalFilePath(string $filename)
    {
        // Get the file contents
        $file = file_get_contents($this->getFilePath($filename));
        $localPath = 'tmp/' . $filename;
        $this->localSystem->put($localPath, $file);
        return $this->localSystem->path($localPath);
    }
}
