<?php

// Script to fix all migration files with incorrect Schema::create syntax

$directory = 'database/migrations';
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Fix Schema::create syntax
        $content = preg_replace(
            '/Schema::create\(\s*[\'"]([^\'"]+)[\'"],\s*function\s*\([^)]*\)\s*use\s*\([^)]*\)\s*\{([^}]*)\},\s*\);/s',
            'Schema::create(\'$1\', function $2) { $3 });',
            $content
        );
        
        // Fix cascadeOnDelete syntax
        $content = str_replace('->cascadeOnDelete()', '->cascadeOnDelete()', $content);
        
        file_put_contents($file->getPathname(), $content);
        echo "Fixed: " . $file->getPathname() . "\n";
    }
}

echo "All migrations have been fixed!\n";
