<?php
/**
 * Знайти всі файли, що відповідають заданому шаблону (рекурсивно, починаючи з визначеної директорії).
 *
 * Важливі примітки:
 * - Шлях директорії для пошуку може закінчуватись з або без роздільника каталогів
 * - В порівнянні з шаблоном бере участь весь шлях до файлу (true) або лише його назва (false) в залежності від значення $matchFullPath
 * - Шаблони в цій функції не підтримують регулярні вирази - детальніше про спосіб визначення шаблонів читайте тут:
 * https://php.net/manual/en/function.fnmatch.php
 *
 * @param string $directory початкова директорія для пошуку
 * @param string $pattern шаблон файлів (наприклад, "*.jpg" для пошуку jpg-файлів або "*" для пошуку всіх файлів)
 * @param bool $matchFullPath порівнювати з повним шляхом (true), чи лише з назвою файла (false)
 * @return array список знайдених файлів
 */
function findFilesRecursively(string $directory, string $pattern = '*', bool $matchFullPath = true): array {
    $result = [];

    // Використовуємо RecursiveDirectoryIterator для обходу підпапок
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    if ($matchFullPath) {
        foreach ($iterator as $item) {
            // Перевіряємо, чи є елемент файлом, та чи відповідає повний шлях шаблону
            if (is_file($item->getPathname()) && fnmatch($pattern, $item->getPathname())) {
                $result[] = $item->getPathname();
            }
        }
    } else {
        foreach ($iterator as $item) {
            // Перевіряємо, чи є елемент файлом, та чи відповідає назву файла шаблону
            if (is_file($item->getPathname()) && fnmatch($pattern, $item->getFilename())) {
                $result[] = $item->getPathname();
            }
        }
    }

    return $result;
}

/**
 * Знайти всі файли, що відповідають заданому регулярному виразу (рекурсивно, починаючи з визначеної директорії).
 *
 * Важливі примітки:
 * - Шлях директорії для пошуку може закінчуватись з або без роздільника каталогів
 * - В порівнянні з регулярним виразом бере участь весь шлях до файлу (true) або лише його назва (false) в залежності від значення $matchFullPath
 *
 * @param string $directory початкова директорія для пошуку
 * @param string $regex регулярний вираз
 * @param bool $matchFullPath порівнювати з повним шляхом (true), чи лише з назвою файла (false)
 * @return array список знайдених файлів
 */
function findFilesRecursivelyRegex(string $directory, string $regex = '/.*/', bool $matchFullPath = true): array {
    $result = [];

    // Використовуємо RecursiveDirectoryIterator для обходу підпапок
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    if ($matchFullPath) {
        foreach ($iterator as $item) {
            // Перевіряємо, чи є елемент файлом, та чи відповідає повний шлях регулярному виразу
            if (is_file($item->getPathname()) && preg_match($regex, $item->getPathname())) {
                $result[] = $item->getPathname();
            }
        }
    } else {
        foreach ($iterator as $item) {
            // Перевіряємо, чи є елемент файлом, та чи відповідає назву файла регулярному виразу
            if (is_file($item->getPathname()) && preg_match($regex, $item->getFilename())) {
                $result[] = $item->getPathname();
            }
        }
    }

    return $result;
}

/**
 * Знайти всі файли, що відповідають заданому регулярному виразу в заданій директорії (без занурення в підпапки).
 *
 * Важливі примітки:
 * - Шлях директорії для пошуку обов'язково має закінчуватись роздільником каталогів!
 * - В порівнянні з регулярним виразом бере участь лише частина шляху, що починається після заданої директорії
 * (не використовуйте в регулярному виразі частину, яка міститься в директорії для пошуку)
 *
 * @param string $directory директорія для пошуку
 * @param string $regex регулярний вираз
 * @return array список знайдених файлів
 */
function findFilesInDirectory(string $directory, string $regex = '/.*/'): array {
    $result = [];

    // Отримуємо список файлів та папок
    $items = scandir($directory);

    foreach ($items as $item) {
        // Пропускаємо спеціальні елементи "." і ".."
        if ($item === '.' || $item === '..') {
            continue;
        }

        // Формуємо повний шлях до елемента
        $fullPath = $directory . $item;

        // Перевіряємо, чи є елемент файлом, та чи відповідає він регулярному виразу
        if (is_file($fullPath) && preg_match($regex, $item)) {
            $result[] = $fullPath; // Додаємо повний шлях до файла
        }
    }

    return $result;
}



/**
 * Знайти всі папки, що відповідають заданому регулярному виразу, починаючи з вказаної директорії, із зануренням в кожну підпапку (рекурсивний пошук).
 *
 * Важливі примітки:
 * - Шлях директорії для пошуку може закінчуватись з або без роздільника каталогів
 * - В порівнянні з регулярним виразом бере участь весь шлях до файлу
 *
 * @param string $directory початкова директорія для пошуку
 * @param string $regex регулярний вираз
 * @return array список знайдених папок
 */
function findFoldersRecursively(string $directory, string $regex = '/.*/'): array {
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
        if ($item->isDir() && preg_match($regex, $item->getPathName())) {
            $result[] = $item->getPathname(); // Додаємо повний шлях до папки
        }
    }

    return $result;
}

/**
 * Знайти всі папки, що відповідають заданому регулярному виразу в заданій директорії (без занурення в підпапки).
 *
 * Важливі примітки:
 * - Шлях директорії для пошуку обов'язково має закінчуватись роздільником каталогів!
 * - В порівнянні з регулярним виразом бере участь лише частина шляху, що починається після заданої директорії
 * (не використовуйте в регулярному виразі частину, яка міститься в директорії для пошуку)
 *
 * @param string $directory директорія для пошуку
 * @param string $regex регулярний вираз
 * @return array список знайдених папок
 */
function findFoldersInDirectory(string $directory, string $regex = '/.*/'): array {
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
        if (is_dir($fullPath) && preg_match($regex, $item)) {
            $result[] = $fullPath; // Додаємо повний шлях до папки
        }
    }

    return $result;
}