<?php
/**
 * @package Bliksem
 * @version 1.0
 */

/**
 * Save parent relationship to SPONSOR_TABLE
 *
 */

function bliksem_add_entry_to_sponsor_table($user_id, $entry)
{
    $form_id = rgar($entry, 'form_id');
    $user = get_user_by('id', $user_id);
    $user_email = $user->user_email;

    // Get relevant entry
    $search_criteria['field_filters'][] = array( 'key' => 1, 'value' => $user_email );

    $entries = GFAPI::get_entries($form_id, $search_criteria);
    
    // Store use_id of parent user
    $parent_id = $entries[0][5];
    // create/update user meta for the $user_id
    update_user_meta($user_id, 'bliksem_users_parent', (int) $parent_id);



    // TESTING
    // echo '<pre>';
    // echo '<hr>';
    // // echo $parent_id;
    // // print_r($entries);
    // echo '<hr>';
    // echo '</pre>';

    // // Call Wordpress Database
    global $wpdb;

    //
    $table = $wpdb->prefix . BliksemRelationshipsUsers::SPONSOR_TABLE;
    
    // add ones that do not exists
    $wpdb->query($sql = $wpdb->prepare('insert into ' . $table . ' (userID, sponsorID) values (%d, %d)', [$user_id, $parent_id]));
}

add_action('gform_user_registered', 'bliksem_add_entry_to_sponsor_table', 10, 2);