<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

/**
 * Extended ServeCommand that passes post_max_size and upload_max_filesize
 * to the PHP built-in server (500MB) for video uploads.
 */
class ServeCommand extends BaseServeCommand
{
    /**
     * Get the full server command including PHP ini overrides for large uploads.
     *
     * @return array<int, string>
     */
    protected function serverCommand(): array
    {
        $base = parent::serverCommand();

        // Insert -d post_max_size=500M -d upload_max_filesize=500M after php_binary()
        // Base is: [php_binary(), '-S', 'host:port', 'server.php']
        $php = array_shift($base);

        return array_merge(
            [$php, '-d', 'post_max_size=500M', '-d', 'upload_max_filesize=500M'],
            $base
        );
    }
}
