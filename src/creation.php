<?php
/**
 * Створити неіснуючі папки до файлу.
 *
 * @param string $path
 */
function createEmptyDirsToFile(string $path, int $permissions = 0777, bool $recursive = true, $context = null): bool {
    $path = dirname($path);
    if (!is_dir($path)) {
        return mkdir($path, $permissions, $recursive, $context);
    }
    return false;
}