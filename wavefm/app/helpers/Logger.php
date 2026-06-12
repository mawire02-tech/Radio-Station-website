<?php
/**
 * Logger — Simple file-based logger
 */
class Logger
{
    private static function write(string $level, string $message): void
    {
        $dir = LOG_PATH;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file = $dir . date('Y-m') . '-' . strtolower($level) . '.log';
        $line = sprintf(
            "[%s] [%s] [%s] %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            $message
        );
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $msg): void    { self::write('INFO',    $msg); }
    public static function warning(string $msg): void { self::write('WARNING', $msg); }
    public static function error(string $msg): void   { self::write('ERROR',   $msg); }
    public static function security(string $msg): void{ self::write('SECURITY',$msg); }
}
