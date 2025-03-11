<?php
/**
 * Скопіювати файл $from на місце $to.
 * 
 * @param string $from файл для копіювання
 * @param string $to скопійований файл
 * @param string|bool $replace визначає необхідність перезапису файла якщо він вже існує
 * (bool - для точного визначення перезапису, або рядок для автоматичного режиму, в якому перезапис відбувається лише якщо початковий файл змінився)
 * 
 * @return true якщо файл успішно скопійовано
 * @return false якщо файл уже існує а перезапис не потрібен
 * 
 * @throw якщо не вдалося скопіювати файл або файл для копіювання не знайдено
 */
function copyFile(string $from, string $to, string|bool $replace = 'auto'): bool {
    if (file_exists($to) && $replace === false || file_exists($from) && file_exists($to) && is_string($replace) && filemtime($from) < filemtime($to)) {
        return false;
    }

    if (file_exists($from)) {
        // Створюємо папки на шляху до файлу (якщо їх немає)
        createEmptyDirsToFile($to);

        // Копіюємо заготовлений файл у цільове місце
        if (copy($from, $to)) {
            return true;
        } else {
            throw new Exception("Не вдалося скопіювати файл \'{$from}\' у \'{$to}\'!");//translate('frmwrk>exc>error_copy_file', ['fromFile' => $from, 'toFile' => $to])
        }
    } else {
        throw new Exception("Файл для копіювання \'{$from}\' не знайдено!");//translate('frmwrk>exc>file_to_copy_not_found', ['fromFile' => $from])
    }
}