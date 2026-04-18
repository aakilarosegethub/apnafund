<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base controller for web and themed pages.
 *
 * Sets {@see Controller::$activeTheme} from {@see activeTheme()} so child controllers can render
 * `resources/views/themes/{theme}/...` views consistently.
 */
abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /** @var string Active theme key/path prefix for Blade views (e.g. `themes.green.`). */
    public $activeTheme;

    /**
     * Resolves the active theme for this request.
     *
     * @return void
     */
    function __construct() {
        $this->activeTheme = activeTheme();
    }
}
