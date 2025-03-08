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
 *  This method returns true if a user can edit a specific page
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

/**
 * This method gets all url parameters and determines if the page is in edit mode or preview mode,
 * By checking preview true, and the action is set to edit
 * @param $urlParams
 * @return bool
 */
function is_page_in_edit_mode($urlParams): bool
{
    // determine if in the edit mode
    $isEdit = isset($urlParams['action']) && $urlParams['action'] === 'edit';

    // if in edit mode, check if edit mode is true
    if ($isEdit) {
        return true;
    } else {
        // return if preview is true
        return isset($urlParams['preview']) && $urlParams['preview'] === 'true';
    }
}
