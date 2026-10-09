<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter4.github.io/CodeIgniter4/
 */

if (!function_exists('get_git_version')) {
    /**
     * Retrieve the latest git commit version info
     *
     * @return array
     */
    function get_git_version(): array
    {
        static $versionInfo = null;
        if ($versionInfo !== null) {
            return $versionInfo;
        }

        $repoUrl = 'https://github.com/dekpiano/skj2025';
        $default = [
            'hash' => '',
            'short_hash' => '',
            'date' => '',
            'message' => '',
            'branch' => '',
            'github_url' => $repoUrl
        ];

        // 1. Try git command if exec is allowed
        if (function_exists('exec')) {
            try {
                $output = [];
                $returnVar = 0;
                @exec('git -c safe.directory=* log -1 --format="%H|%h|%cd|%s" --date=format:"%d/%m/%Y"', $output, $returnVar);
                if ($returnVar === 0 && !empty($output[0])) {
                    $parts = explode('|', $output[0]);
                    $branch = '';
                    $branchOut = [];
                    @exec('git -c safe.directory=* rev-parse --abbrev-ref HEAD', $branchOut);
                    if (!empty($branchOut[0])) {
                        $branch = trim($branchOut[0]);
                    }
                    $versionInfo = [
                        'hash' => $parts[0] ?? '',
                        'short_hash' => $parts[1] ?? '',
                        'date' => $parts[2] ?? '',
                        'message' => $parts[3] ?? '',
                        'branch' => $branch,
                        'github_url' => $repoUrl
                    ];
                    return $versionInfo;
                }
            } catch (\Throwable $e) {
                // Fallback to filesystem
            }
        }

        // 2. Fallback: Parse .git directory files directly
        $gitDir = ROOTPATH . '.git';
        if (is_dir($gitDir)) {
            $headFile = $gitDir . '/HEAD';
            if (is_file($headFile)) {
                $headContent = trim((string)@file_get_contents($headFile));
                $hash = '';
                $branch = '';
                $date = '';
                if (str_starts_with($headContent, 'ref: ')) {
                    $ref = trim(substr($headContent, 5));
                    $branch = basename($ref);
                    $refFile = $gitDir . '/' . $ref;
                    if (is_file($refFile)) {
                        $hash = trim((string)@file_get_contents($refFile));
                        $date = date('d/m/Y', filemtime($refFile));
                    } elseif (is_file($gitDir . '/packed-refs')) {
                        $packed = @file_get_contents($gitDir . '/packed-refs');
                        if ($packed && preg_match('/([a-f0-9]{40})\s+' . preg_quote($ref, '/') . '/', $packed, $m)) {
                            $hash = $m[1];
                            $date = date('d/m/Y', filemtime($gitDir . '/packed-refs'));
                        }
                    }
                } else {
                    $hash = $headContent;
                    $date = date('d/m/Y', filemtime($headFile));
                }

                if (!empty($hash)) {
                    $versionInfo = [
                        'hash' => $hash,
                        'short_hash' => substr($hash, 0, 7),
                        'date' => $date ?: date('d/m/Y'),
                        'message' => '',
                        'branch' => $branch,
                        'github_url' => $repoUrl
                    ];
                    return $versionInfo;
                }
            }
        }

        $versionInfo = $default;
        return $versionInfo;
    }
}

if (!function_exists('get_app_version')) {
    /**
     * Get the website version string that auto-increments on every commit
     * Format: v2.1.{commit_count} (e.g. v2.1.49)
     *
     * @param string $prefix
     * @return string
     */
    function get_app_version(string $prefix = 'v'): string
    {
        static $version = null;
        if ($version !== null) {
            return $version;
        }

        // Base version for SKJ System
        $baseVersion = '2.1';
        $commitCount = 0;

        // 1. Try git commit count via CLI
        if (function_exists('exec')) {
            try {
                $out = [];
                $ret = 0;
                @exec('git -c safe.directory=* rev-list --count HEAD', $out, $ret);
                if ($ret === 0 && !empty($out[0]) && is_numeric(trim($out[0]))) {
                    $commitCount = (int)trim($out[0]);
                }
            } catch (\Throwable $e) {
                // Ignore and fall back
            }
        }

        // 2. Fallback: count lines in .git reflog
        if ($commitCount === 0) {
            $gitLogs = ROOTPATH . '.git/logs/HEAD';
            if (is_file($gitLogs)) {
                $lines = @file($gitLogs, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if (!empty($lines)) {
                    $commitCount = count($lines);
                }
            }
        }

        // 3. Fallback check for custom VERSION file
        $versionFile = ROOTPATH . 'VERSION';
        if (is_file($versionFile)) {
            $custom = trim((string)@file_get_contents($versionFile));
            if (!empty($custom)) {
                $version = str_starts_with(strtolower($custom), 'v') ? $custom : $prefix . $custom;
                return $version;
            }
        }

        if ($commitCount > 0) {
            $version = $prefix . $baseVersion . '.' . $commitCount;
        } else {
            $version = $prefix . $baseVersion;
        }

        return $version;
    }
}

if (!function_exists('manager_url')) {
    /**
     * URL for the Executive Information System (EIS / Manager System)
     */
    function manager_url(string $uri = ''): string
    {
        $baseUrl = getenv('MANAGER_URL');
        if (!$baseUrl) {
            $isDev = (ENVIRONMENT === 'development' || (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'localhost')));
            $baseUrl = $isDev ? 'https://localhost:8095/' : 'https://manager.skj.ac.th/';
        }
        return rtrim($baseUrl, '/') . ($uri !== '' ? '/' . ltrim($uri, '/') : '/');
    }
}

