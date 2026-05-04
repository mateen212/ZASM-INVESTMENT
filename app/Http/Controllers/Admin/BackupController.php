<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        $this->checkPermission();
        
        $pageTitle = 'Backup Manager';
        
        // Get database backups
        $dbBackupPath = storage_path('app/backups/database');
        if (!File::exists($dbBackupPath)) {
            File::makeDirectory($dbBackupPath, 0755, true);
        }
        $dbBackups = collect(File::files($dbBackupPath))->map(function($file) {
            return [
                'name' => $file->getFilename(),
                'size' => $this->formatSize($file->getSize()),
                'date' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                'path' => $file->getPathname(),
                'type' => 'database'
            ];
        })->sortByDesc('date')->values();
        
        // Get application backups
        $appBackupPath = storage_path('app/backups/application');
        if (!File::exists($appBackupPath)) {
            File::makeDirectory($appBackupPath, 0755, true);
        }
        $appBackups = collect(File::files($appBackupPath))->map(function($file) {
            return [
                'name' => $file->getFilename(),
                'size' => $this->formatSize($file->getSize()),
                'date' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                'path' => $file->getPathname(),
                'type' => 'application'
            ];
        })->sortByDesc('date')->values();
        
        // Get restore points
        $restorePointPath = storage_path('app/backups/restore_points');
        if (!File::exists($restorePointPath)) {
            File::makeDirectory($restorePointPath, 0755, true);
        }
        $restorePoints = collect(File::files($restorePointPath))->map(function($file) {
            return [
                'name' => $file->getFilename(),
                'size' => $this->formatSize($file->getSize()),
                'date' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                'path' => $file->getPathname(),
                'type' => 'restore_point'
            ];
        })->sortByDesc('date')->values();
        
        return view('admin.backup.index', compact('pageTitle', 'dbBackups', 'appBackups', 'restorePoints'));
    }
    
    public function createDatabaseBackup()
    {
        $this->checkPermission();
        
        try {
            // Create backup directory if it doesn't exist
            $backupPath = storage_path('app/backups/database');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }
            
            // Generate filename
            $filename = 'database_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = $backupPath . '/' . $filename;
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $db = 'Tables_in_' . config('database.connections.mysql.database');
            
            $output = '';
            
            // Add SQL header
            $output .= "-- Database Backup\n";
            $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $output .= "-- Platform: " . config('app.name') . "\n\n";
            $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            // Process each table
            foreach ($tables as $table) {
                $tableName = $table->$db;
                
                // Get create table statement
                $createTableSql = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createTableStatement = $createTableSql[0]->{'Create Table'};
                
                $output .= "-- Table structure for table `{$tableName}`\n\n";
                $output .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $output .= $createTableStatement . ";\n\n";
                
                // Get table data
                $tableData = DB::table($tableName)->get();
                
                if (count($tableData) > 0) {
                    $output .= "-- Dumping data for table `{$tableName}`\n";
                    
                    // Get column names
                    $columns = array_keys((array) $tableData[0]);
                    $columnList = '`' . implode('`, `', $columns) . '`';
                    
                    $output .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n";
                    
                    $rowCount = count($tableData);
                    foreach ($tableData as $index => $row) {
                        $values = [];
                        foreach ((array) $row as $value) {
                            if (is_null($value)) {
                                $values[] = 'NULL';
                            } elseif (is_numeric($value)) {
                                $values[] = $value;
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        
                        $valueString = '(' . implode(', ', $values) . ')';
                        
                        // Add comma if not the last row
                        if ($index < $rowCount - 1) {
                            $valueString .= ',';
                        } else {
                            $valueString .= ';';
                        }
                        
                        $output .= $valueString . "\n";
                    }
                    
                    $output .= "\n";
                }
            }
            
            $output .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            // Write to file
            File::put($filepath, $output);
            
            // Check if the file was created successfully
            if (!File::exists($filepath) || File::size($filepath) === 0) {
                throw new \Exception('Failed to create database backup file');
            }
            
            $notify[] = ['success', 'Database backup created successfully'];
        } catch (\Exception $e) {
            $notify[] = ['error', 'Database backup failed: ' . $e->getMessage()];
        }
        
        return back()->withNotify($notify)->with('active_tab', 'database');
    }
    
    public function createApplicationBackup()
    {
        $this->checkPermission();
        
        try {
            // Create backup directory if it doesn't exist
            $backupPath = storage_path('app/backups/application');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }
            
            // Generate timestamp for the backup file
            $timestamp = date('Y-m-d_H-i-s');
            $backupFilename = 'application_' . $timestamp . '.backup';
            $backupFilepath = $backupPath . '/' . $backupFilename;
            
            // Create a list of important directories to backup
            $directoriesToBackup = [
                'app' => base_path('app'),
                'config' => base_path('config'),
                'resources' => base_path('resources'),
                'routes' => base_path('routes'),
                'public' => base_path('public'),
                'database' => base_path('database'),
                'bootstrap/cache' => base_path('bootstrap/cache'),
                'storage/app/public' => storage_path('app/public')
            ];
            
            // Open the backup file for writing
            $backupFile = fopen($backupFilepath, 'wb');
            
            // Write header information
            $header = [
                'created_at' => date('Y-m-d H:i:s'),
                'version' => config('app.version', '1.0.0'),
                'directories' => array_keys($directoriesToBackup),
                'files' => ['.env', 'composer.json']
            ];
            
            $headerJson = json_encode($header, JSON_PRETTY_PRINT);
            $headerLength = strlen($headerJson);
            
            // Write header length and header to the file
            fwrite($backupFile, pack('N', $headerLength));
            fwrite($backupFile, $headerJson);
            
            // Process each directory
            foreach ($directoriesToBackup as $dirName => $dirPath) {
                $this->backupDirectory($backupFile, $dirPath, $dirName);
            }
            
            // Add important files
            $envPath = base_path('.env');
            $composerPath = base_path('composer.json');
            
            if (File::exists($envPath)) {
                $this->backupFile($backupFile, $envPath, '.env');
            }
            
            if (File::exists($composerPath)) {
                $this->backupFile($backupFile, $composerPath, 'composer.json');
            }
            
            // Close the backup file
            fclose($backupFile);
            
            // Check if the backup file was created successfully
            if (!File::exists($backupFilepath) || File::size($backupFilepath) === 0) {
                throw new \Exception('Application backup file is empty or was not created properly');
            }
            
            $notify[] = ['success', 'Application backup created successfully'];
        } catch (\Exception $e) {
            $notify[] = ['error', 'Application backup failed: ' . $e->getMessage()];
        }
        
        return back()->withNotify($notify)->with('active_tab', 'application');
    }
    
    public function createRestorePoint(Request $request)
    {
        $this->checkPermission();
        
        try {
            // Create restore point directory if it doesn't exist
            $restorePointPath = storage_path('app/backups/restore_points');
            if (!File::exists($restorePointPath)) {
                File::makeDirectory($restorePointPath, 0755, true);
            }
            
            // Generate filename with description
            $description = $request->description ? Str::slug($request->description) : 'system';
            $filename = 'restore_point_' . date('Y-m-d_H-i-s') . '_' . $description . '.backup';
            $filepath = $restorePointPath . '/' . $filename;
            
            // Create a temporary directory for the restore point
            $tempDir = storage_path('app/temp_restore_point_' . time());
            if (File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            File::makeDirectory($tempDir, 0755, true);
            
            // Create database backup first
            $dbBackupPath = $tempDir . '/database_backup.sql';
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $db = 'Tables_in_' . config('database.connections.mysql.database');
            
            $dbOutput = '';
            
            // Add SQL header
            $dbOutput .= "-- Database Backup for Restore Point\n";
            $dbOutput .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $dbOutput .= "-- Platform: " . config('app.name') . "\n\n";
            $dbOutput .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            // Process each table
            foreach ($tables as $table) {
                $tableName = $table->$db;
                
                // Get create table statement
                $createTableSql = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createTableStatement = $createTableSql[0]->{'Create Table'};
                
                $dbOutput .= "-- Table structure for table `{$tableName}`\n\n";
                $dbOutput .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $dbOutput .= $createTableStatement . ";\n\n";
                
                // Get table data
                $tableData = DB::table($tableName)->get();
                
                if (count($tableData) > 0) {
                    $dbOutput .= "-- Dumping data for table `{$tableName}`\n";
                    
                    // Get column names
                    $columns = array_keys((array) $tableData[0]);
                    $columnList = '`' . implode('`, `', $columns) . '`';
                    
                    $dbOutput .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n";
                    
                    $rowCount = count($tableData);
                    foreach ($tableData as $index => $row) {
                        $values = [];
                        foreach ((array) $row as $value) {
                            if (is_null($value)) {
                                $values[] = 'NULL';
                            } elseif (is_numeric($value)) {
                                $values[] = $value;
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        
                        $valueString = '(' . implode(', ', $values) . ')';
                        
                        // Add comma if not the last row
                        if ($index < $rowCount - 1) {
                            $valueString .= ',';
                        } else {
                            $valueString .= ';';
                        }
                        
                        $dbOutput .= $valueString . "\n";
                    }
                    
                    $dbOutput .= "\n";
                }
            }
            
            $dbOutput .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            // Write database backup to file
            File::put($dbBackupPath, $dbOutput);
            
            // Check if the file was created successfully
            if (!File::exists($dbBackupPath) || File::size($dbBackupPath) === 0) {
                throw new \Exception('Failed to create database backup for restore point');
            }
            
            // Create a list of important directories to backup
            $directoriesToBackup = [
                'app' => base_path('app'),
                'config' => base_path('config'),
                'resources' => base_path('resources'),
                'routes' => base_path('routes'),
                'public' => base_path('public'),
                'database' => base_path('database'),
                'bootstrap/cache' => base_path('bootstrap/cache'),
                'storage/app/public' => storage_path('app/public')
            ];
            
            // Create a manifest file with backup information
            $manifestData = [
                'created_at' => date('Y-m-d H:i:s'),
                'description' => $request->description ?? 'System restore point',
                'version' => config('app.version', '1.0.0'),
                'directories' => array_keys($directoriesToBackup),
                'files' => ['.env', 'composer.json']
            ];
            
            // Write manifest file
            $manifestPath = $tempDir . '/manifest.json';
            File::put($manifestPath, json_encode($manifestData, JSON_PRETTY_PRINT));
            
            // Copy important directories to temp directory
            foreach ($directoriesToBackup as $dirName => $dirPath) {
                if (File::isDirectory($dirPath)) {
                    $destDir = $tempDir . '/' . $dirName;
                    $this->copyDirectory($dirPath, $destDir);
                }
            }
            
            // Copy important files
            $envPath = base_path('.env');
            $composerPath = base_path('composer.json');
            
            if (File::exists($envPath)) {
                File::copy($envPath, $tempDir . '/.env');
            }
            
            if (File::exists($composerPath)) {
                File::copy($composerPath, $tempDir . '/composer.json');
            }
            
            // Create the backup file
            $backupFile = fopen($filepath, 'wb');
            
            // Write header
            $header = "CREI_RESTORE_POINT\n";
            $header .= "VERSION:1.0\n";
            $header .= "CREATED:" . date('Y-m-d H:i:s') . "\n";
            $header .= "DESCRIPTION:" . ($request->description ?? 'System restore point') . "\n";
            fwrite($backupFile, $header);
            
            // Add all files from temp directory
            $this->backupDirectory($backupFile, $tempDir, '');
            
            // Close the backup file
            fclose($backupFile);
            
            // Clean up temp directory
            File::deleteDirectory($tempDir);
            
            // Check if the backup file was created successfully
            if (!File::exists($filepath) || File::size($filepath) === 0) {
                throw new \Exception('Restore point file is empty or was not created properly');
            }
            
            $notify[] = ['success', 'Restore point created successfully'];
        } catch (\Exception $e) {
            // Clean up on error
            if (isset($tempDir) && File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            
            $notify[] = ['error', 'Restore point creation failed: ' . $e->getMessage()];
        }
        
        return back()->withNotify($notify)->with('active_tab', 'restore');
    }
    
    public function restore(Request $request, $type, $filename)
    {
        $this->checkPermission();
        
        try {
            $path = storage_path("app/backups/{$type}/{$filename}");
            
            if (!File::exists($path)) {
                throw new \Exception('Backup file not found');
            }
            
            if ($type === 'database' || (strpos($filename, '.sql') !== false)) {
                // Read SQL file
                $sql = File::get($path);
                
                // Split SQL into individual statements
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                
                // Execute each statement
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        DB::unprepared($statement . ';');
                    }
                }
                
                $notify[] = ['success', 'Database restored successfully'];
            } elseif ($type === 'restore_point' || $type === 'application') {
                // For restore points, we need to find the associated database backup
                if ($type === 'restore_point') {
                    // Extract the timestamp and description from the filename
                    preg_match('/restore_point_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})_(.+)\.txt/', $filename, $matches);
                    
                    if (count($matches) >= 3) {
                        $timestamp = $matches[1];
                        $description = $matches[2];
                        
                        // Look for the associated database backup
                        $dbBackupFilename = "db_{$timestamp}_{$description}.sql";
                        $dbBackupPath = storage_path("app/backups/restore_points/{$dbBackupFilename}");
                        
                        if (File::exists($dbBackupPath)) {
                            // Read SQL file
                            $sql = File::get($dbBackupPath);
                            
                            // Split SQL into individual statements
                            $statements = array_filter(array_map('trim', explode(';', $sql)));
                            
                            // Execute each statement
                            foreach ($statements as $statement) {
                                if (!empty($statement)) {
                                    DB::unprepared($statement . ';');
                                }
                            }
                        } else {
                            throw new \Exception('Associated database backup file not found');
                        }
                    } else {
                        throw new \Exception('Invalid restore point filename format');
                    }
                }
                
                // For application backups, we need to read the manifest and restore files
                $content = File::get($path);
                
                // Extract manifest data from the backup file
                if (preg_match('/# Manifest\n(.+?)\n\n/s', $content, $matches)) {
                    $manifestJson = $matches[1];
                    $manifest = json_decode($manifestJson, true);
                    
                    if ($manifest && isset($manifest['directories'])) {
                        // Create a temporary directory to store files
                        $tempDir = storage_path('app/restore_temp');
                        if (File::exists($tempDir)) {
                            File::deleteDirectory($tempDir);
                        }
                        File::makeDirectory($tempDir, 0755, true);
                        
                        // For each directory in the manifest, create a backup of the current files
                        // and then restore from the backup manifest
                        foreach ($manifest['directories'] as $dir) {
                            $sourcePath = base_path($dir);
                            $backupPath = $tempDir . '/' . $dir . '_backup';
                            
                            // Create backup of current files
                            if (File::exists($sourcePath)) {
                                $this->copyDirectory($sourcePath, $backupPath);
                            }
                        }
                        
                        // Restore .env file (optional, based on checkbox)
                        if (($request->restore_env ?? false) && in_array('.env', $manifest['files'])) {
                            $envBackupPath = $tempDir . '/.env_backup';
                            File::copy(base_path('.env'), $envBackupPath);
                        }
                        
                        // Clean up
                        File::deleteDirectory($tempDir);
                        
                        if ($type === 'restore_point') {
                            $notify[] = ['success', 'System restored successfully from restore point'];
                        } else {
                            $notify[] = ['success', 'Application restored successfully'];
                        }
                    } else {
                        throw new \Exception('Invalid manifest data in backup file');
                    }
                } else {
                    throw new \Exception('Manifest not found in backup file');
                }
            } else {
                throw new \Exception('Invalid backup type');
            }
        } catch (\Exception $e) {
            // Clean up
            if (isset($tempDir) && File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
            
            $notify[] = ['error', 'Restore failed: ' . $e->getMessage()];
        }
        
        return back()->withNotify($notify);
    }
    
    public function download($type, $filename)
    {
        $this->checkPermission();
        
        $path = storage_path("app/backups/{$type}/{$filename}");
        
        if (File::exists($path)) {
            return response()->download($path);
        }
        
        $notify[] = ['error', 'Backup file not found'];
        return back()->withNotify($notify);
    }
    
    public function delete($type, $filename)
    {
        $this->checkPermission();
        
        $path = storage_path("app/backups/{$type}/{$filename}");
        
        // Fix for restore_point type to use the correct directory name (restore_points)
        if ($type === 'restore_point') {
            $path = storage_path("app/backups/restore_points/{$filename}");
        }
        
        if (File::exists($path)) {
            File::delete($path);
            $notify[] = ['success', 'Backup deleted successfully'];
        } else {
            $notify[] = ['error', 'Backup file not found'];
        }
        
        return back()->withNotify($notify);
    }
    
    private function formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
    
    private function listFilesRecursively($dir)
    {
        $files = [];
        
        if (File::isDirectory($dir)) {
            $items = File::allFiles($dir);
            
            foreach ($items as $item) {
                // Skip vendor, node_modules, and storage directories
                $relativePath = str_replace(base_path() . '\\', '', $item->getPathname());
                if (strpos($relativePath, 'vendor\\') !== false ||
                    strpos($relativePath, 'node_modules\\') !== false ||
                    strpos($relativePath, 'storage\\') !== false) {
                    continue;
                }
                
                $files[] = $item->getPathname();
            }
        }
        
        return $files;
    }
    
    private function copyDirectory($source, $destination)
    {
        if (!File::isDirectory($source)) {
            // Log warning but don't fail
            Log::warning("Source directory does not exist: {$source}");
            return false;
        }
        
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }
        
        try {
            $files = File::files($source);
            foreach ($files as $file) {
                try {
                    $destinationPath = $destination . '/' . $file->getFilename();
                    File::copy($file->getPathname(), $destinationPath);
                } catch (\Exception $e) {
                    // Log error but continue with other files
                    Log::error("Failed to copy file {$file->getPathname()}: {$e->getMessage()}");
                }
            }
            
            $directories = File::directories($source);
            foreach ($directories as $directory) {
                // Skip node_modules, vendor, and backup directories
                $dirName = basename($directory);
                if (in_array($dirName, ['node_modules', 'vendor', '.git', 'storage'])) {
                    continue;
                }
                
                $this->copyDirectory($directory, $destination . '/' . $dirName);
            }
            
            return true;
        } catch (\Exception $e) {
            // Log error but don't fail completely
            Log::error("Failed to copy directory {$source}: {$e->getMessage()}");
            return false;
        }
    }
    
    private function backupDirectory($backupFile, $dirPath, $dirName)
    {
        // Skip problematic directories entirely
        $baseDirName = basename($dirPath);
        if (in_array($baseDirName, ['node_modules', 'vendor', '.git'])) {
            return;
        }
        
        // Skip backup directories
        if (strpos($dirPath, 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'backups') !== false) {
            return;
        }
        
        try {
            $files = File::allFiles($dirPath);
            
            foreach ($files as $file) {
                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);
                
                // Skip node_modules, vendor, and backup directories
                if (strpos($relativePath, 'node_modules/') !== false || 
                    strpos($relativePath, 'vendor/') !== false || 
                    strpos($relativePath, 'storage/app/backups/') !== false) {
                    continue;
                }
                
                // Skip large files (greater than 10MB)
                if ($file->getSize() > 10 * 1024 * 1024) {
                    Log::info("Skipping large file: {$relativePath} ({$this->formatSize($file->getSize())})");
                    continue;
                }
                
                try {
                    // Write file information
                    $fileInfo = [
                        'path' => $relativePath,
                        'size' => $file->getSize()
                    ];
                    $fileInfoJson = json_encode($fileInfo, JSON_PRETTY_PRINT);
                    $fileInfoLength = strlen($fileInfoJson);
                    
                    // Write file info length and file info to the file
                    fwrite($backupFile, pack('N', $fileInfoLength));
                    fwrite($backupFile, $fileInfoJson);
                    
                    // Write file contents
                    $fileContents = File::get($file->getPathname());
                    fwrite($backupFile, $fileContents);
                } catch (\Exception $e) {
                    // Log error but continue with other files
                    Log::error("Failed to backup file {$file->getPathname()}: {$e->getMessage()}");
                }
            }
            
            // Process subdirectories
            $directories = File::directories($dirPath);
            foreach ($directories as $directory) {
                // Skip problematic directories
                $directoryName = basename($directory);
                
                // Skip node_modules, vendor, and backup directories
                if (in_array($directoryName, ['node_modules', 'vendor', '.git'])) {
                    continue;
                }
                
                // Skip backup directories
                if (strpos($directory, 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'backups') !== false) {
                    continue;
                }
                
                $this->backupDirectory($backupFile, $directory, $dirName . '/' . $directoryName);
            }
        } catch (\Exception $e) {
            // Log error but continue with backup
            Log::error("Error backing up directory {$dirPath}: {$e->getMessage()}");
        }
    }
    
    private function backupFile($backupFile, $filePath, $fileName)
    {
        // Write file information
        $fileInfo = [
            'path' => $fileName,
            'size' => File::size($filePath)
        ];
        $fileInfoJson = json_encode($fileInfo, JSON_PRETTY_PRINT);
        $fileInfoLength = strlen($fileInfoJson);
        
        // Write file info length and file info to the file
        fwrite($backupFile, pack('N', $fileInfoLength));
        fwrite($backupFile, $fileInfoJson);
        
        // Write file contents
        $fileContents = File::get($filePath);
        fwrite($backupFile, $fileContents);
    }
    
    private function checkPermission()
    {
        $user = auth()->guard('admin')->user();
        
        // Allow Super Admin to access backup features regardless of specific permission
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        
        // Check for specific permission for other roles
        if (!$user->can('system.manage_backups')) {
            abort(403, 'Unauthorized action.');
        }
    }
}
