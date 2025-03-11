<?php
/**
 * Підключити файл в режимі include (без передачі другого аргументу)
 * або include_once (пердавши true дргуим аргументом).
 * Шлях має бути вже нормалізованим згідно роздільника каталогів поточної ОС!
 * Рекомендується передавати абсолютний шлях!
 * 
 * Функція може приймати масив змінних для доступу до них з підключеного файлу!
 */
function includeFile(string $path, bool $once = false, array &$variables = []): mixed {
    if (!empty($variables)) {
        extract($variables);
    }
    if ($once !== false) {
        return include_once $path;
    } else {
        return include $path;
    }
}

/**
 * Підключити файл в режимі require (без передачі другого аргументу)
 * або require_once (пердавши true дргуим аргументом).
 * Шлях має бути вже нормалізованим згідно роздільника каталогів поточної ОС!
 * Рекомендується передавати абсолютний шлях!
 * 
 * Функція може приймати масив змінних для доступу до них з підключеного файлу!
 */
function requireFile(string $path, bool $once = false, array &$variables = []): mixed {
    if (!empty($variables)) {
        extract($variables);
    }
    if ($once !== false) {
        return require_once $path;
    } else {
        return require $path;
    }
}