<?php
$root = realpath(__DIR__ . '/../../');
$skip = ['.git',  'node_modules'];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$count = 0;
$errors = [];

foreach ($iterator as $file) {
    $path = $file->getPathname();
    $skip_this = false;

    foreach ($skip as $dir) {
        if (strpos($path, DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR) !== false) {
            $skip_this = true;
            break;
        }
    }

    if ($skip_this) continue;

    try {
        if ($file->isDir()) {
            chmod($path, 0755);
        } else {
            chmod($path, 0644);
        }
        $count++;
    } catch (Exception $e) {
        $errors[] = $path . ' → ' . $e->getMessage();
    }
}

echo "<pre>✅ Permissions fixed for $count files/directories in: $root</pre>";

if ($errors) {
    echo "<pre>⚠️ Errors:\n" . implode("\n", $errors) . "</pre>";
}
