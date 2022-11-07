<?php

namespace Wsl\CourseManager;

class Console
{
    /**
     * Formate un message pour l'afficher dans le terminal
     * @return void
     */
    public static function print(string $message, ...$params): void
    {
        if (!empty($params)) {
            $message = sprintf($message, ...$params);
        }
        echo $message . PHP_EOL;
    }
}
