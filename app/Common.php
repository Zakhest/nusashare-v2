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
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('profile_url')) {
    /**
     * Generate profile image URL safely, handling both external OAuth URLs (Google/etc.) and local filenames.
     */
    function profile_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }

        // Jika URL eksternal (OAuth Google dll)
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Jika path relatif assets/ atau uploads/
        if (str_starts_with($path, 'assets/') || str_starts_with($path, 'uploads/')) {
            return base_url($path);
        }

        // Filename lokal diproses lewat proxy controller
        return base_url('image/profile/' . $path);
    }
}

