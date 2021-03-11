<?php

namespace Bliksem_Relationships;

class BliksemRelationships
{

    // static: variable belongs to class, not the instances of the class
    protected static $users;

    public static function onActivation()
    {
        // set up sponsor lookup table
        global $wpdb;

        $table = $wpdb->prefix . BliksemRelationshipsUsers::SPONSOR_TABLE;
        $collate = $wpdb->collate;

        $sql = "CREATE TABLE {$table} (
  lookupID int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  userID bigint(20) UNSIGNED NOT NULL,
  sponsorID bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY(lookupID),
  KEY userID (userID),
  KEY sponsorID (sponsorID)
) COLLATE {$collate}";

        // Create the table
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
register_activation_hook(__FILE__, 'BliksemRelationships::onActivation');



class BliksemRelationshipsUsers
{
    protected static $users;
    protected static $data = [];
    const SPONSOR_TABLE = 'sponsor_lookup';

    // Lookup parent user information
    public static function getUsers()
    {
        if (isset(self::$users)) {
            return self::$users;
        }
        return self::$users = get_users([
            'role' => 'partner',
            'orderby' => 'display_name',
            'fields' => ['ID', 'display_name'],
        ]);
    }

    // Obtain parent user id
    public static function getParents($userId)
    {
        global $wpdb;
        $rows = $wpdb->get_results($sql = 'select sponsorID from ' . $wpdb->prefix . self::SPONSOR_TABLE . ' where userID = ' . $userId, 'ARRAY_A');
        return empty($rows) ? [] : array_map(function ($item) {
            return $item['sponsorID'];
        }, $rows);
    }

    // Search for user id and return username
    public static function getUserName($id, $default = false)
    {
        if (!empty(self::$data[$id])) {
            return self::$data[$id];
        }
        if (false !== ($user = get_user_by('ID', (int) $id))) {
            return self::$data[$id] = $user->display_name;
        }
        return $default;
    }

    public static function getFormDataNames($data)
    {
        // test $data is an array
        if (!is_array($data)) {
            return $data;
        }
        // test $data contains digits
        if (!ctype_digit($data[0])) {
            // these are likely user IDs
            return $data;
        }

        $data = array_map(function ($item) {
            if (ctype_digit($item) && false !== ($name = BliksemRelationshipsUsers::getUserName($item))) {
                return $name;
            }
            return $item;
        }, $data);
        return $data;
    }

    public static function adminScripts($context)
    {
        if ('user-edit.php' !== $context && 'profile.php' !== $context) {
            return;
        }
        // THis is dependent on Gravity Forms
        if (class_exists('GFCommon', false)) {
            wp_enqueue_script('gform_chosen', false, array('jquery'), GFCommon::$version, true);
            wp_enqueue_style('gform_chosen');
        }
    }

    public static function userFields($user)
    {
        if (! in_array('agent', $user->roles)) {
            return;
        }

        $parents = self::getParents($user->ID); ?>

<!-- Seperation of PHP to HTML ############################################################################# -->

<h3>Partner Information</h3>
<table class="form-table">
    <tr>
        <th><label for="user_parents">Partner</label></th>
        <td>
            <select class="chosen-select" name="user_parents[]" id="user_parents" multiple="multiple">
                <?php foreach (self::getUsers() as $item) {?>
                <option value="<?php echo $item->ID; ?>" <?php if (in_array($item->ID, $parents)) {
            echo ' selected="selected"';
        }?>><?php echo $item->display_name; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>

<!-- Controls how the selected option displays on the page -->
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#user_parents').chosen({
        width: '25em',
        placeholder_text_multiple: 'Select Parent',
        max_selected_options: 1
    });
});
</script>

<input type="hidden" name="_update_parents" value="1" />

<!-- Seperation of HTML to PHP ############################################################################# -->

<?php
    }
    public static function saveUser($userId)
    {
        // if $_POST['_update_parents'] is not set, do not continue
        if (! isset($_POST['_update_parents'])) {
            return;
        }
        
        // if current user does not have edit permissions, do not continue
        if (!current_user_can('edit_user', $userId)) {
            return false;
        }

        // if Sparents does not exist or if it is wrong type, create $parents array
        if (empty($_POST['user_parents']) || !is_array($parents = $_POST['user_parents'])) {
            $parents = [];
        } else {
            $parents = array_filter($parents, function ($item) {
                return ctype_digit($item);
            });
        }

        // Call Wordpress Database
        global $wpdb;

        //
        $table = $wpdb->prefix . self::SPONSOR_TABLE;
        if (empty($parents)) {
            $wpdb->query('delete from ' . $table . ' where userID = ' . $userId);
            return;
        }
        $rows = $wpdb->get_results($sql = 'select sponsorID from ' . $table . ' where userID = ' . $userId, 'ARRAY_A');
        $rows = empty($rows) ? [] : array_map(function ($item) {
            return $item['sponsorID'];
        }, $rows);
        // If parent already exists and is equal to new query, do not proceed
        if ($rows == $parents) {
            return;
        }

        // delete entries not chosen
        $wpdb->query('delete from ' . $table . ' where sponsorID not in (' . implode(',', $parents) . ') and userID = ' . $userId);
        
        // add ones that do not exists
        foreach (array_diff($parents, $rows) as $id) {
            $wpdb->query($sql = $wpdb->prepare('insert into ' . $table . ' (userID, sponsorID) values (%d, %d)', [$userId, $id]));
        }
    }
}
add_action('admin_enqueue_scripts', 'BliksemRelationshipsUsers::adminScripts');
add_action('show_user_profile', 'BliksemRelationshipsUsers::userFields');
add_action('edit_user_profile', 'BliksemRelationshipsUsers::userFields');
add_action('personal_options_update', 'BliksemRelationshipsUsers::saveUser');
add_action('edit_user_profile_update', 'BliksemRelationshipsUsers::saveUser');