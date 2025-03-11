<?php
/**
 * Знайти всі файли, що відповідають заданому шаблону (рекурсивно, починаючи з визначеної директорії).
 *
 * @param string $directory початкова директорія для пошуку
 * @param string $pattern шаблон файлів (наприклад, "*.php" або "*.txt")
 * @return array список знайдених файлів
 */
function findFilesRecursively(string $directory, string $pattern): array {
    $result = [];

    // Використовуємо RecursiveDirectoryIterator для обходу підпапок
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        // Перевіряємо відповідність файлу шаблону
        if (fnmatch($pattern, $file->getFilename())) {
            $result[] = $file->getPathname();
        }
    }

    return $result;
}



/** Знайти всі папки, що відповідають заданому регулярному виразу, починаючи з вказаної директорії, із зануренням в кожну підпапку (рекурсивний пошук) */
function findFoldersByPattern(string $directory, string $pattern = '/.*/'): array {
    $result = [];

    // Перевірка наявності кореневої папки
    if (!is_dir($directory)) {
        throw new InvalidArgumentException("Папка $directory не існує.");
    }

    // Ініціалізація рекурсивного ітератора
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        // Перевіряємо, чи є елемент папкою, та чи відповідає вона регулярному виразу
        if ($item->isDir() && preg_match($pattern, $item->getPathName())) {
            $result[] = $item->getPathname(); // Додаємо повний шлях до папки
        }
    }

    return $result;
}

/** Знайти всі папки, що відповідають заданому регулярному виразу в заданій директорії (без занурення в підпапки) */
function findFoldersInDirectory(string $directory, string $pattern = '/.*/'): array {
    $result = [];

    // Перевірка наявності кореневої папки
    if (!is_dir($directory)) {
        throw new InvalidArgumentException("Папка $directory не існує.");
    }

    // Отримуємо список файлів та папок
    $items = scandir($directory);

    foreach ($items as $item) {
        // Пропускаємо спеціальні елементи "." і ".."
        if ($item === '.' || $item === '..') {
            continue;
        }

        // Формуємо повний шлях до елемента
        $fullPath = $directory . $item;

        // Перевіряємо, чи є елемент папкою, та чи відповідає вона регулярному виразу
        if (is_dir($fullPath) && preg_match($pattern, $item)) {
            $result[] = $fullPath; // Додаємо повний шлях до папки
        }
    }

    return $result;
}