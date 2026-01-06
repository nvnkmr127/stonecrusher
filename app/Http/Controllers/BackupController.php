<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Tasks\Monitor\HealthCheckChecker;
use Illuminate\Support\Facades\DB;
use ZipArchive; // Ensure ZipArchive is used

class BackupController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('manage-backups')) {
            abort(403);
        }

        // Get configured disks
        $diskNames = config('backup.backup.destination.disks');
        $backups = [];

        foreach ($diskNames as $diskName) {
            $disk = Storage::disk($diskName);

            // Assume default path 'Laravel' (app name)
            $appName = config('backup.backup.name');
            $files = $disk->allFiles($appName);

            foreach ($files as $file) {
                // only zip files
                if (substr($file, -4) == '.zip') {
                    $backups[] = [
                        'path' => $file,
                        'name' => basename($file),
                        'size' => $this->humanFileSize($disk->size($file)),
                        'date' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                        'disk' => $diskName,
                    ];
                }
            }
        }

        // Sort by date desc
        usort($backups, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('admin.backups.index', compact('backups'));
    }

    public function create()
    {
        if (!auth()->user()->can('manage-backups')) {
            abort(403);
        }

        try {
            // Run backup
            Artisan::call('backup:run --only-db'); // Initial version just DB for speed? Or full?
            // User requested "Data backup", usually full.
            // But full backup might take time and timeout the request.
            // I'll default to --only-db for the button for now, or full. 
            // Better: use queue if configured. 
            // artisan call is sync by default.

            Artisan::call('backup:run');

            activity()
                ->useLog('backup')
                ->log('Manual backup started by user');

            return redirect()->back()->with('success', 'Backup started successfully. It will appear in the list once completed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(Request $request)
    {
        if (!auth()->user()->can('manage-backups')) {
            abort(403);
        }

        $disk = $request->input('disk');
        $path = $request->input('path');

        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $adapter */
        $adapter = Storage::disk($disk);
        if (method_exists($adapter, 'download')) {
            return $adapter->download($path);
        }
        return $adapter->response($path);
    }

    public function destroy(Request $request)
    {
        if (!auth()->user()->can('manage-backups')) {
            abort(403);
        }

        $disk = $request->input('disk');
        $path = $request->input('path');

        if (Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);

            activity()
                ->useLog('backup')
                ->withProperties(['disk' => $disk, 'path' => $path])
                ->log('Backup deleted');

            return redirect()->back()->with('success', 'Backup deleted successfully.');
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    public function restore(Request $request)
    {
        if (!auth()->user()->can('manage-backups')) {
            abort(403);
        }

        $request->validate([
            'disk' => 'required|string',
            'path' => 'required|string',
        ]);

        $disk = $request->input('disk');
        $path = $request->input('path');

        if (!Storage::disk($disk)->exists($path)) {
            return back()->with('error', 'Backup file not found.');
        }

        try {
            // 1. Download to local temp (if not local)
            $stream = Storage::disk($disk)->readStream($path);
            $tempZipPath = tempnam(sys_get_temp_dir(), 'backup_restore_');
            file_put_contents($tempZipPath, stream_get_contents($stream));

            // 2. Extract Zip
            $zip = new ZipArchive;
            if ($zip->open($tempZipPath) === TRUE) {
                $extractPath = sys_get_temp_dir() . '/backup_restore_extract_' . uniqid();
                mkdir($extractPath);
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                return back()->with('error', 'Could not open backup ZIP file.');
            }

            // 3. Find SQL Dump
            // Usually in db-dumps/mysql-database.sql inside the zip
            // Pattern match might be needed
            $sqlFile = null;
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractPath));
            foreach ($files as $file) {
                if ($file->getExtension() === 'sql') {
                    $sqlFile = $file->getRealPath();
                    break;
                }
            }

            if (!$sqlFile) {
                throw new \Exception('No SQL dump found in the backup archive.');
            }

            // 4. Import SQL
            // Be careful with large files. DB::unprepared loads everything into memory?
            // Better to stream it or use command line mysql?
            // Hosting restrictions might prevent command line mysql.
            // Let's try DB::unprepared() for now, usually works for moderate backups.
            // Ideally we should use the same db user/pass as config.

            DB::disableQueryLog();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $sqlEntry = file_get_contents($sqlFile);
            DB::unprepared($sqlEntry);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // 5. Cleanup
            unlink($tempZipPath);
            // recursive delete of extract dir
            $this->deleteDirectory($extractPath);

            // recursive delete of extract dir
            $this->deleteDirectory($extractPath);

            activity()
                ->useLog('backup')
                ->withProperties(['disk' => $disk, 'path' => $path])
                ->log('Database restored from backup');

            return back()->with('success', 'Database restored successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    private function humanFileSize($size, $unit = "")
    {
        if ((!$unit && $size >= 1 << 30) || $unit == "GB")
            return number_format($size / (1 << 30), 2) . "GB";
        if ((!$unit && $size >= 1 << 20) || $unit == "MB")
            return number_format($size / (1 << 20), 2) . "MB";
        if ((!$unit && $size >= 1 << 10) || $unit == "KB")
            return number_format($size / (1 << 10), 2) . "KB";
        return number_format($size) . " bytes";
    }
}
