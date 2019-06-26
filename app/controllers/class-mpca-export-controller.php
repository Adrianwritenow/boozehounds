<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

class MPCA_Export_Controller
{
  public function __construct()
  {
    add_action('wp_ajax_mpca_export_csv', array($this, 'ajax_export_csv'));
    add_action('wp_ajax_mpca_edit_dog', array($this, 'edit_dog'));
    add_action('wp_ajax_mpca_remove_dog', array($this, 'remove_dog'));
  }

  public function ajax_export_csv()
  {
    if (!isset($_REQUEST['ca'])) {
      _e('No corporate account specified', 'memberpress-corporate');
      status_header(404);
      exit;
    }

    $ca = new MPCA_Corporate_Account(esc_attr($_REQUEST['ca']));

    if (empty($ca->id)) {
      _e('Unable to export due to Invalid corporate account', 'memberpress-corporate');
      status_header(500);
      exit;
    }

    if (!$ca->current_user_has_access()) {
      _e('Forbidden', 'memberpress-corporate');
      status_header(403);
      exit;
    }

    $filename = $ca->sub_id() . '_sub_accounts_' . uniqid() . '.csv';

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");

    $header = array(
      'email', 'username', 'first_name', 'last_name'
    );

    $user_objs = $ca->sub_users();

    $users = array();
    foreach ($user_objs as $user_obj) {
      $users[] = array(
        $user_obj->user_email,
        $user_obj->user_login,
        $user_obj->first_name,
        $user_obj->last_name,
      );
    }

    $out = fopen('php://output', 'w');
    fputcsv($out, $header);

    foreach ($users as $user) {
      fputcsv($out, $user);
    }

    fclose($out);
    exit;
  }

  public function edit_dog()
  {

    header("Content-Type: application/json");

    if (!isset($_REQUEST['ca'])) {
      _e('No corporate account specified', 'memberpress-corporate');
      status_header(404);
      exit;
    }

    global $wpdb;
    $tablename = $wpdb->prefix . 'mepr_dog_accounts';
    $table_id = array('id' => $_REQUEST['dog']);

    $ca = $_REQUEST['ca'];
    $dog = $_REQUEST['dog'];
    $data = $_POST;
    $dog_account = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mepr_dog_accounts WHERE id = {$dog}");

    if (isset($_FILES['photo'])) {
      $dogPhotosTmpDir = $_FILES["photo"]["tmp_name"];
      $dogPhotosDir = wp_upload_dir()["basedir"] . "/dogs/photos/";
      $photo = $dog_account->user_id . "-" . uniqid() . ".png";
      move_uploaded_file($dogPhotosTmpDir, $dogPhotosDir . $photo);
      $data['photo'] = $photo;
    }
    if (isset($_FILES['vaccination'])) {
      $dogVaccinationsTmpDir = $_FILES["vaccination"]["tmp_name"];
      $dogVaccinationsDir = wp_upload_dir()["basedir"] . "/dogs/vaccinations/";
      $vaccination = $dog_account->user_id . "-" . uniqid() . ".png";
      move_uploaded_file($dogVaccinationsTmpDir, $dogVaccinationsDir . $vaccination);
      $data['vaccination'] = $vaccination;
    }

    $update = $wpdb->update($tablename, $data, $table_id);
    echo json_encode($update);
  }

  public function remove_dog()
  {

    header("Content-Type: application/json");

    global $wpdb;

    if (!isset($_REQUEST['ca'])) {
      _e('No corporate account specified', 'memberpress-corporate');
      status_header(404);
      exit;
    }

    $ca = $_REQUEST['ca'];
    $dog = $_REQUEST['dog'];
    $dog_account = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mepr_dog_accounts WHERE id = {$dog}");
    $transaction = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mepr_transactions WHERE corporate_account_id = {$ca}");
    $membership_name = get_post_meta($transaction->product_id, '_mepr_product_pricing_title')[0];
    $membership_addon_post_id = $wpdb->get_row("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = '{$membership_name} Add-on'")->post_id;
    $membership_addon_amount = get_post_meta($membership_addon_post_id, '_mepr_product_price')[0];
    $final_amount = $transaction->amount - $membership_addon_amount;
    $table_id =  array('id' => $transaction->id);
    $tablename = $wpdb->prefix . 'mepr_transactions';
    if ($transaction->subscription_id > 0) {
      $tablename = $wpdb->prefix . 'mepr_subscriptions';
      // update subscription
      $data = array(
        'price' => $final_amount,
        'total' => $final_amount
      );
      $table_id =  array('id' => $transaction->subscription_id);
    }

    wp_delete_file(wp_get_upload_dir()['baseurl'] . "/dogs/vaccinations/{$dog_account->photo}");
    wp_delete_file(wp_get_upload_dir()['baseurl'] . "/dogs/vaccinations/{$dog_account->vaccination}");

    $update = $wpdb->update($tablename, $data, $table_id);
    $results = $wpdb->get_results("DELETE FROM {$wpdb->prefix}mepr_dog_accounts WHERE id = {$dog}");

    echo json_encode([
      'request' => $_REQUEST,
      'dog_photo' => wp_get_upload_dir()['baseurl'] . "/dogs/vaccinations/{$dog_account->photo}",
      'vaccination' => wp_get_upload_dir()['baseurl'] . "/dogs/vaccinations/{$dog_account->vaccination}",
      'transaction' => $transaction,
      'dog_account' => $dog_account,
      'update' => $update,
      'results' => $results
    ]);

    wp_die();
  }
}