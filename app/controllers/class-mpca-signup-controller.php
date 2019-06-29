<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

class MPCA_Signup_Controller
{
  public function __construct()
  {
    // Check for errors after the checkout form has been submitted
    add_filter('mepr-validate-signup', array($this, 'validate_ca_signup'));

    // Add Dog accounts befor proceeding to checkout
    add_action('mepr-signup', array($this, 'add_dogs'), 11);

    // Add Scripts
    add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

    // Associate sub account if processing corporate account signup
    add_filter('mepr-signup-checkout-url', array($this, 'associate_sub_account'), 10, 2);

    // Handle error view if CA is invalid
    add_filter('mepr_view_get_string_/checkout/form', array($this, 'display_error'), 1, 2);
  }

  public function enqueue_scripts()
  {
    wp_register_script('mpca-dog-accounts', MPCA_JS_URL . '/mpca-dog-accounts.js', array(), MPCA_VERSION);
    wp_enqueue_script('mpca-dog-accounts', MPCA_JS_URL . '/mpca-dog-accounts.js', array('jquery', 'mpca-dog-accounts'), MPCA_VERSION);
    wp_enqueue_script('mpca-manage-account', MPCA_JS_URL . '/mpca-manage-account.js', array('jquery'));
  }

  public function add_dogs($transaction)
  {

    global $wpdb;

    $user_id = $transaction->user_id;
    $trans_num = $transaction->trans_num;
    $trans_id = $transaction->id;
    $trans = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mepr_transactions WHERE id = {$trans_id}");
    $ca = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mepr_corporate_accounts WHERE id = {$trans->corporate_account_id}");
    if ($trans->subscription_id > 0) {
      $ca = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}mepr_corporate_accounts WHERE obj_id = {$trans->subscription_id}");
    }
    $dog_accounts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mepr_dog_accounts WHERE user_id = {$user_id} AND ca_id = {$ca->id}");
    $url = $transaction->checkout_url();

    require MPCA_VIEWS_PATH . '/mpca-signup-dog-template.php';
  }

  public function validate_ca_signup($errors)
  {
    extract($_POST);

    if (isset($mpca_corporate_account_id)) {
      $ca = MPCA_Corporate_Account::find_by_uuid($mpca_corporate_account_id);

      if (empty($ca->id)) {
        array_push($errors, 'Invalid corporate account (1)');
      }

      // Check if the sub-account limit will be exceeded
      $error = $ca->validate();

      if (is_wp_error($error)) {
        array_push($errors, __($error->get_error_message(), 'memberpress-corporate'));
      }
    }

    return $errors;
  }

  public function associate_sub_account($url, $txn)
  {

    /**
     * Step 2
     */


    $mepr_options = MeprOptions::fetch();
    $sa_id = $txn->user()->ID;

    if (isset($_REQUEST['ca'])) {
      $ca = MPCA_Corporate_Account::find_by_uuid($_REQUEST['ca']);

      if (empty($ca->id)) {
        return _e('Invalid corporate account (2)', 'memberpress-corporate');
      }

      $ca->add_sub_account_user($sa_id);

      // Signup email handling
      $mailer = MeprEmailFactory::fetch('Mepr_Sub_Account_Signup_Email');
      if ($mailer->enabled()) {
        $mailer->send_sub_account_signup_email($txn);
      } else {
        MeprUtils::send_signup_notices($txn, true, false);
      }

      $product = new MeprProduct($txn->product_id);
      $sanitized_title = sanitize_title($product->post_title);
      $query_params = array('membership' => $sanitized_title, 'trans_num' => $txn->trans_num, 'membership_id' => $product->ID);
      // Skip the payment options; set url to be the thank you page instead
      $url = $mepr_options->thankyou_page_url(build_query($query_params));

      // Sub accounts don't need the txn so we delete it here
      $txn->destroy();
    }

    return $url;
  }

  public function display_error($view, $vars)
  {
    if (isset($_REQUEST['ca'])) {
      $ca = MPCA_Corporate_Account::find_by_uuid($_REQUEST['ca']);

      if (empty($ca->id)) {
        $errors = array(__('Invalid corporate account (3)', 'memberpress-corporate'));
        $view = MeprView::get_string('/shared/errors', compact('errors'));
      }
    }

    return $view;
  }
}