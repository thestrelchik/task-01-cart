<?php

declare(strict_types=1);

function init_public_base(): void
{
    if (defined('PUBLIC_BASE')) {
        return;
    }

    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($dir === '/' || $dir === '.') {
        $dir = '';
    }

    define('PUBLIC_BASE', rtrim($dir, '/'));
}

function public_url(string $path): string
{
    $base = defined('PUBLIC_BASE') ? PUBLIC_BASE : '';

    if ($path === '') {
        return $base === '' ? '' : $base;
    }

    if ($path[0] !== '/') {
        $path = '/' . $path;
    }

    return $base . $path;
}
