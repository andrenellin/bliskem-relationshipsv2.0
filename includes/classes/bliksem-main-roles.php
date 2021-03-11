<?php

namespace Bliksem_Relationships;

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Roles Class
 *
 * This class handles the role creation and assignment of capabilities for those roles.
 *
 * These roles let us have Sales People, Marketers, etc, each of whom can do
 * certain things within the CRM
 *
 * @since 1.4.4
 */
class Main_Roles extends Roles
{

    /**
     * Valid owner roles..
     *
     * @var array
     */
    public static $owner_roles = [
        'administrator',
        'partner',
        'agent',
        'legal',
        'client'
    ];

    /**
     * Get the list of valid owner roles...
     *
     * @return mixed|void
     */
    public static function get_owner_roles()
    {
        return apply_filters('bliksem/owner_roles', self::$owner_roles);
    }

    /**
     * Returns an array  of role => [
     *  'role' => '',
     *  'name' => '',
     *  'caps' => []
     * ]
     *
     * In this case caps should just be the meta cap map for other WP related stuff.
     *
     * @return array[]
     */
    public function get_roles()
    {
        // TODO Revisit sales rep & sales manager caps...

        return apply_filters('bliksem/roles/get_roles', [
            [
                'role' => 'partner',
                'name' => _x('Partner', 'role', 'bliksem'),
                'caps' => [
                    'read'         => true,
                    'edit_posts'   => false,
                    'upload_files' => false,
                    'delete_posts' => false
                ]
            ],
            [
                'role' => 'agent',
                'name' => _x('Agent', 'role', 'bliksem'),
                'caps' => [
                    'read'         => true,
                    'edit_posts'   => false,
                    'upload_files' => false,
                    'delete_posts' => false
                ]
            ],
            [
                'role' => 'legal',
                'name' => _x('Legal', 'role', 'bliksem'),
                'caps' => [
                    'read'         => true,
                    'edit_posts'   => false,
                    'upload_files' => false,
                    'delete_posts' => false
                ]
            ],
            [
                'role' => 'client',
                'name' => _x('Client', 'role', 'bliksem'),
                'caps' => [
                    'read'         => true,
                    'edit_posts'   => false,
                    'upload_files' => false,
                    'delete_posts' => false
                ]
            ]
        ]);
    }
}