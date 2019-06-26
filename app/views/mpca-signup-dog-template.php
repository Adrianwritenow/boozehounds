<?php get_header() ?>

<meta name="upload-directory" content="<?php echo wp_get_upload_dir()['baseurl'] ?>">

<section class="container">

  <div class="mp_wrapper">
    <h3 class="mpca-fat-bottom"><?php printf(__('Dogs for %s', 'memberpress-corporate'), $owner_name); ?></h3>
    <div id="mpca_sub_accounts_used" class="mpca-fat-bottom">
      <h4>
        <?php printf(__('%1$s of %2$s Dogs Used', 'memberpress-corporate'), count($dog_accounts), $ca->num_sub_accounts); ?>
      </h4>
    </div>

    <div id="mpca-add-sub-user" class="mpca-fat-bottom">
      <?php
      $nonce = wp_create_nonce('add_dog_account_nonce');
      $user_id = get_current_user_id();
      ?>

      <form action="<?php echo admin_url("admin-ajax.php?action=add_dog_account&ca={$ca->id}&user_id={$user_id}") ?>"
        method="post" id="mpca-add-sub-user-form" class="mpca-hidden" enctype="multipart/form-data">

        <label>
          <span><?php _e('Photo', 'memberpress-corporate'); ?></span>
          <input type="file" name="photo" />
        </label>

        <label>
          <span><?php _e('Name', 'memberpress-corporate'); ?> </span>
          <input type="text" name="name" />
        </label>

        <label>
          <span><?php _e('Gender', 'memberpress-corporate'); ?> </span>
          <select name="gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
        </label>

        <label>
          <span><?php _e('Breed', 'memberpress-corporate'); ?> </span>
          <input type="text" name="breed" />
        </label>

        <label>
          <span><?php _e('Vaxination Record', 'memberpress-corporate'); ?></span>
          <input type="file" name="vaccination" />
        </label>

        <label>
          <span><?php _e('Vaxination Expiration', 'memberpress-corporate'); ?></span>
          <input type="date" name="vacc_expiration" />
        </label>

        <input type="submit" disabled='disabled' value="<?php _e('Submit', 'memberpress-corporate') ?>" />

      </form>

    </div>
    <table id="mpca-dog-accounts-table" class="mepr-account-table">
      <thead>
        <tr>
          <th></th>
          <th><?php _ex('Name', 'ui', 'memberpress-corporate'); ?></th>
          <th><?php _ex('Breed', 'ui', 'memberpress-corporate'); ?></th>
          <th><?php _ex('Gender', 'ui', 'memberpress-corporate'); ?></th>
          <th><?php _ex('Vacc Expiration', 'memberpress-corporate'); ?></th>
          <th><?php _ex('Vacc Record', 'memberpress-corporate'); ?></th>
          <?php MeprHooks::do_action('mpca-sub-accounts-th', $mepr_current_user, $dog_accounts); ?>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
    <br />
    <div style="clear:both">&nbsp;</div>
    <div>
      <button id="dog-checkout">
        <a disabled="disabled" style="color:white" href="<?php echo $url ?>">Proceed to checkout</a>
      </button>
    </div>
  </div>
</section>



<?php get_footer() ?>