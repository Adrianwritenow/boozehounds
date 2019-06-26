<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

class MPCA_User_Admin_Controller
{
  public function __construct()
  {
    add_action('mepr_extra_profile_fields', array($this, 'display_fields'));
    add_action('mepr_user_account_saved', array($this, 'save_user'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
  }

  public function enqueue_scripts()
  {
    wp_enqueue_style('settings_table', MEPR_URL . '/css/settings_table.css');
    wp_enqueue_script('mpca-admin-form', MPCA_JS_URL . '/admin_form.js');
  }

  public function display_fields($user)
  {
    // Instantiate helper for use in view template
    $helper = new MPCA_Admin_Helper();

    // Setup view template variables
    $meta_type = get_user_meta($user->ID, 'mpca_member_type', true);
    $meta_limit = get_user_meta($user->ID, 'mpca_member_sub_account_limit', true);
    $meta_parent_id = get_user_meta($user->ID, 'mpca_member_parent_id', true);

    // Get a list of the user's subscriptions
    $subscriptions = $user->subscriptions();
    foreach ($subscriptions as $subscription) {
      $subscription->dog_accounts = $this->get_user_dog_accounts($subscription->corporate_account->id);
    }

    require MPCA_VIEWS_PATH . '/mpca-edit-user-template.php';
  }

  public function edit_dog_account($action)
  {
    if ($action == 'edit_dog_account') {

      // Edit the dog account
      $user_id = $ca->user_id;
      $userdata = $_REQUEST['userdata'];
      $dogPhotosTmpDir = $_FILES["userdata"]["tmp_name"]["photo"];
      $dogPhotosDir = wp_upload_dir()["basedir"] . "/dogs/photos/";
      $dogVaccinationsTmpDir = $_FILES["userdata"]["tmp_name"]["vaccination"];
      $dogVaccinationsDir = wp_upload_dir()["basedir"] . "/dogs/vaccinations/";
      $photo = $user_id . "-" . uniqid() . ".png";
      $vaccination = $user_id . "-" . uniqid() . ".png";
    }
    if ($action == 'remove_dog_account') { }
  }

  public function save_user($user)
  {
    if (!isset($_POST['mpca'])) {
      return;
    }

    $mpca_data = $_POST['mpca'];

    foreach ($mpca_data as $d) {
      $old_account = MPCA_Corporate_Account::find_corporate_account_by_obj_id($d['obj_id'], $d['obj_type']);

      if (empty($old_account)) {
        if (isset($d['is_corporate'])) { // create
          $d['user_id'] = $user->ID;
          $new_account = new MPCA_Corporate_Account();
          $new_account->load_from_array($d);
          $new_account->store();
        } else {
          // do nothing
        }
      } else {
        if (isset($d['is_corporate'])) { // update
          $old_account->status = 'enabled';
          $old_account->num_sub_accounts = $d['num_sub_accounts'];
          $old_account->store();
        } else { // disable
          $old_account->disable();
        }
      }
    }
  }

  public function get_user_dog_accounts($corporate_id)
  {
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mepr_dog_accounts WHERE ca_id = {$corporate_id}");
    return $results;
  }
}