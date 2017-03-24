<?php

namespace Spatie\Snapshots\Test;


use PHPUnit\Framework\TestCase;

class Filesystem extends TestCase
{
    protected $snapshotsDir = __DIR__.DIRECTORY_SEPARATOR.'__snapshots__';

    protected $snapshotStubsDir = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'__snapshots__';

    protected $exampleSnapshotsDir = __DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'example_snapshots';

    public function prepareSnapshots()
    {
        $this->deleteDirectory($this->snapshotsDir);

        $this->copyDirectory($this->snapshotStubsDir, $this->snapshotsDir);
    }

    public function assertSnapshotMatchesExample($snapshotPath, $examplePath)
    {
        $snapshot = $this->snapshotsDir.DIRECTORY_SEPARATOR.$snapshotPath;
        $example = $this->exampleSnapshotsDir.DIRECTORY_SEPARATOR.$examplePath;

        return $this->assertFileEquals($example, $snapshot);
    }

    protected function deleteDirectory(string $path): bool
    {
        if (! file_exists($path)) {
            return true;
        }
        if (! is_dir($path)) {
            return unlink($path);
        }
        foreach (scandir($path) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (! $this->deleteDirectory($path.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($path);
    }

    protected function copyDirectory(string $sourcePath, string $destinationPath)
    {
        mkdir($destinationPath);

        $sourceDirectory = opendir($sourcePath);

        while (($file = readdir($sourceDirectory)) !== false) {

            if (in_array($file, ['.', '..'])) {
                continue;
            }

            if (is_dir($sourcePath.DIRECTORY_SEPARATOR.$file)) {
                $this->copyDirectory($sourcePath.DIRECTORY_SEPARATOR.$file, $destinationPath.DIRECTORY_SEPARATOR.$file);
                continue;
            }

            copy($sourcePath.DIRECTORY_SEPARATOR.$file, $destinationPath.DIRECTORY_SEPARATOR.$file);
        }

        closedir($sourceDirectory);
    }
}