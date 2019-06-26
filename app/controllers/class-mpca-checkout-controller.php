<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

class MPCA_Checkout_Controller
{
  public function __construct()
  {
    // Associate the CA with this signup early on in the signup process
    add_action('mepr-signup', array($this, 'process_signup'));

    // In case the user uses a 100% off coupon on a recurring subscription
    add_action('mepr-before-subscription-destroy-create-free-transaction', array($this, 'process_sub_destroy_free_txn'));
  }

  public function process_sub_destroy_free_txn($txn)
  {
    $sub = $txn->subscription();

    $is_corporate_product = get_post_meta($txn->product_id, 'mpca_is_corporate_product', true);

    //The subscription is destroyed so we need to re-associate this CA with the free txn instead
    if ($is_corporate_product) {
      $ca = MPCA_Corporate_Account::find_corporate_account_by_obj($sub);
      $ca->obj_id = $txn->id;
      $ca->obj_type = 'transactions';
      $ca->store();
    }
  }

  public function process_signup($transaction)
  {

    /**
     * Step 1
     */

    $obj = $transaction;
    $type = 'transactions';

    if ($transaction->subscription_id > 0) {
      $obj = $transaction->subscription();
      $type = 'subscriptions';
    }

    $is_corporate_product = get_post_meta($obj->product_id, 'mpca_is_corporate_product', true);
    // $num_sub_accounts = get_post_meta($obj->product_id, 'mpca_num_sub_accounts', true);
    $num_sub_accounts = get_user_meta($transaction->user_id, 'mepr_number_of_dogs', true);

    $this->update_signup_transaction($transaction, $num_sub_accounts);

    if ($is_corporate_product) {
      // create corporate account using the information from above
      $ca = new MPCA_Corporate_Account();
      $ca->obj_id = $obj->id;
      $ca->obj_type = $type;
      $ca->num_sub_accounts = $num_sub_accounts;
      $ca->user_id = $obj->user_id;
      $ca->store();
    }
  }

  public function update_signup_transaction($transaction, $num_sub_accounts)
  {
    global $wpdb;

    $membership_name = get_post_meta($transaction->product_id, '_mepr_product_pricing_title')[0];
    $membership_addon_post_id = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_value = '{$membership_name} Add-on'")[0]->post_id;
    $membership_addon_amount = get_post_meta($membership_addon_post_id, '_mepr_product_price')[0];

    $addon_amount = ($num_sub_accounts - 1) * $membership_addon_amount;
    $final_amount = $transaction->amount + $addon_amount;

    if ($transaction->subscription_id > 0) {
      // subscription 
      $tablename = $wpdb->prefix . 'mepr_subscriptions';
      // update subscription
      $data = array(
        'price' => $final_amount,
        'total' => $final_amount
      );

      $wpdb->update($tablename, $data, array('id' => $transaction->subscription_id));
    }

    $data = array(
      'amount' => $final_amount,
      'total' => $final_amount
    );

    $tablename = $wpdb->prefix . 'mepr_transactions';
    $wpdb->update($tablename, $data, array('id' => $transaction->id));
  }
}