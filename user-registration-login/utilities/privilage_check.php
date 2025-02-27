<?php

/**
 * Check if the user has edit page and post privileges.
 * returns true if the user has edit page and post privileges. needed for previewing shortcode
 */
function user_has_edit_page_and_post_privileges(): bool
{
    // get the current user
    $current_user = wp_get_current_user();

    // check if the user has edit page and post privileges
    return $current_user->has_cap('edit_pages') && $current_user->has_cap('edit_posts');
}

/**
 *  This method return true if user can edit a specific page
 * @param $pageId
 * @return bool
 */
function user_can_edit_page($pageId): bool
{
    // get the current user
    $current_user = wp_get_current_user();

    // check if the user can edit the page
    return $current_user->has_cap('edit_pages') && current_user_can('edit_pages', $pageId);
}
