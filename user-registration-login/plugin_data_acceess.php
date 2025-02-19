<?php

/**
 * Provides access to some plugin data
 */
class PluginDataAccess {
    /**
     * Get the list of disposable email domains
     * @return array
     */
    public static function getDisposableEmailDomains(): array
    {
        $emailListPath = plugin_dir_path(__FILE__) . 'assets/file/disposable_email_blocklist.conf';

        $file = new SplFileObject($emailListPath);

        $domains = [];

        while (!$file->eof()) {
            $line = $file->fgets();
            $line = trim($line);
            if (!empty($line)) {
                $domains[] = $line;
            }
        }

        return $domains;
    }
}