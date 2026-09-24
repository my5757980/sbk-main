<?php

/**
 * AssetHelper
 *
 */

/**
 * Return's admin assets directory
 *
 * CALLING PROCEDURE
 *
 * In controller call it like this:
 * $adminAssetsDirectory = adminAssetsDir() . $site_settings->site_logo;
 *
 * In View call it like this:
 * {{ asset(adminAssetsDir() . $site_settings->site_logo) }}
 *
 * @param string $role
 *
 * @return bool
 */
function uploadsDir($path = '')
{
    return $path != '' ? 'uploads/' . $path . '/' : 'uploads/';
}

function uploadsUrl($file = '')
{
    return $file != '' && file_exists(uploadsDir('users') . $file) ? uploadsDir('users') . $file : 'avatar.jpg';
}

function adminHasAssets($image)
{
    if (!empty($image) && file_exists(uploadsDir() . $image)) {
        return true;
    } else {
        return false;
    }
}

function matchChecked($param1, $param2)
{
    return $param1 == $param2 ? ' checked="checked" ' : '';
}

function matchSelected($param1, $param2)
{
    return $param1 == $param2 ? ' selected="selected" ' : '';
}

function generateRandomString($length = 10)
{
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

function getGender($id = null)
{
    $values = [
        '1' => 'Male',
        '2' => 'Female',
    ];

    return isset($id) && $id <= 2 && $id >= 1 ? $values[$id] : $values;
}

function getStatus($id = null)
{
    $values = [
        '0' => 'Inactive',
        '1' => 'Active',
    ];

    return isset($id) && $id <= 2 && $id >= 1 ? $values[$id] : $values;
}

function filterUrl($key = '', $value = '')
{
    $data = $_SERVER['QUERY_STRING'];

    $data = str_replace(urlencode($key) . '=' . $value, '', $data);
    $data = str_replace('&&', '&', $data);

    return $data;
}

function pageTypes($type = '')
{
    $data = [
        'default' => 'Default',
        'home' => 'Home',
        'contact' => 'Contact',
    ];

    return isset($type) && !empty($type) ? $data[$type] : $data;
}

function pageStatuses($status = '')
{
    $data = [
        'published' => 'Published',
        'draft' => 'Draft'
    ];

    return isset($status) && !empty($status) ? $data[$status] : $data;
}

function generatePageUniqueSlug($title, $currentSlug = null)
{
    $slug = \Str::slug($title); // Generate the initial slug

    if ($currentSlug === null) {
        // This is a new record, so check for existing slugs
        $count = \App\Models\Page::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            return $slug .= '-' . ($count + 1); // Append a unique identifier
        }
    } else {
        // This is an existing record, update the slug only if it's different from the current one
        if ($slug !== $currentSlug) {
            $count = \App\Models\Page::where('slug', 'like', $slug . '%')->count();
            if ($count > 0) {
                return $slug .= '-' . ($count + 1); // Append a unique identifier
            }
        }
    }

    return $slug;
}

function generateBlogUniqueSlug($title, $currentSlug = null)
{
    $slug = \Str::slug($title); // Generate the initial slug

    if ($currentSlug === null) {
        // This is a new record, so check for existing slugs
        $count = \App\Models\Blog::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            return $slug .= '-' . ($count + 1); // Append a unique identifier
        }
    } else {
        // This is an existing record, update the slug only if it's different from the current one
        if ($slug !== $currentSlug) {
            $count = \App\Models\Blog::where('slug', 'like', $slug . '%')->count();
            if ($count > 0) {
                return $slug .= '-' . ($count + 1); // Append a unique identifier
            }
        }
    }

    return $slug;
}



function generateServiceUniqueSlug($title, $currentSlug = null)
{
    $slug = \Str::slug($title);

    if ($currentSlug === null) {
        $count = \App\Models\Service::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            return $slug . '-' . ($count + 1);
        }
    } else {
        if ($slug !== $currentSlug) {
            $count = \App\Models\Service::where('slug', 'like', $slug . '%')->where('slug', '!=', $currentSlug)->count();
            if ($count > 0) {
                return $slug . '-' . ($count + 1);
            }
        }
    }

    return $slug;
}
