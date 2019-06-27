<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
} ?>

<tr>
  <td colspan="2">

    <h3><?php _e('Dog Accounts', 'memberpress-corporate'); ?></h3>

    <?php
    foreach ($subscriptions as $sub) :
      $i = uniqid();
      // $product = $sub->product();


      ?>
    <table class="form-table">
      <input type="hidden" name="mpca[<?php echo $i ?>][obj_type]"
        value="<?php echo MPCA_Corporate_Account::get_obj_type($sub) ?>" />
      <input type="hidden" name="mpca[<?php echo $i ?>][obj_id]" value="<?php echo $sub->id ?>" />

      <tr>
        <th><?php _e('Dog Account?', 'memberpress-corporate'); ?></th>
        <td>
          <label>
            <?php echo $helper->subscription_header_html($sub); ?>
          </label>
        </td>
      </tr>
    </table>

    <div id="" class="mepr-sub-box-white mepr_corporate_options_box_<?php echo $i; ?>">
      <div class="mepr-arrow mepr-white mepr-up mepr-sub-box-arrow"></div>
      <table class="form-table">
        <tr id="mpca-sub-account-limit-row">
          <th id='myheading'><?php _e('Number of Dog Accounts', 'memberpress-corporate'); ?></th>
          <td>
            <label for="num_of_dogs"><?php echo count($sub->dog_accounts) ?></label>
          </td>
        </tr>
        <?php
          if ($sub->is_corporate_account) {
            ?>
        <tr>
          <th><?php _e('Sub Account Usage', 'memberpress-corporate'); ?></th>
          <td>
            <?php
                printf(
                  __('%1$s of %2$s Sub Accounts Used', 'memberpress-corporate'),
                  count($sub->dog_accounts),
                  $sub->corporate_account->num_sub_accounts
                );
                ?>
          </td>
        </tr>
        <tr>
          <th><?php _e('Actions', 'memberpress-corporate'); ?></th>
          <td>
            <a href="<?php echo $sub->corporate_account->sub_account_management_url(); ?>"
              class="button"><?php _e('Manage Sub Accounts', 'memberpress-corporate'); ?></a>
            <a href="<?php echo $sub->corporate_account->import_url(); ?>"
              class="button"><?php _e('Import Sub Accounts', 'memberpress-corporate'); ?></a>
            <a href="<?php echo $sub->corporate_account->export_url(); ?>"
              class="button"><?php _e('Export Sub Accounts', 'memberpress-corporate'); ?></a>
          </td>
        </tr>
        <?php
        }
        ?>
      </table>
      <div class="edit-container"></div>
    </div>

    <table class="form-table mepr-sub-box-white dog-table">
      <tr>
        <th>ID</th>
        <th>Photo</th>
        <th>Name</th>
        <th>Gender</th>
        <th>Breed</th>
        <th>Vacc Expiration</th>
        <th>Vaccination</th>
        <th>Actions</th>
      </tr>
      <?php
        foreach ($sub->dog_accounts as $dog) :
          $id = $dog->id;
          $photo = $dog->photo;
          $name = $dog->name;
          $gender = $dog->gender;
          $breed = $dog->breed;
          $vacc_expiration = $dog->vacc_expiration;
          $vaccination = $dog->vaccination;
          $now = date("Y-m-d"); // this format is string comparable
          if ($now > $dog->vacc_expiration) {
            $expired = false;
          } else {
            $expired = true;
          }
          ?>
      <tr
        data-edit-url="<?php echo admin_url("admin-ajax.php?action=mpca_edit_dog&ca={$sub->corporate_account->id}&dog={$id}") ?>"
        data-dog-id="<?php echo $id ?>">
        <td>
          <p><?php echo $id ?></p>
        </td>
        <td>
          <a target="_blank" href="<?php echo wp_get_upload_dir()['baseurl'] . "/dogs/photos/{$photo}" ?>">
            <img style="width:80px;height:60px"
              src="<?php echo wp_get_upload_dir()['baseurl'] . "/dogs/photos/{$photo}" ?>" alt="<?php echo $name ?>"
              srcset="">
          </a>
        </td>
        <td>
          <p><?php echo $name ?></p>
        </td>
        <td>
          <p><?php echo $gender ?></p>
        </td>
        <td>
          <p><?php echo $breed ?></p>
        </td>
        <td>
          <p><?php echo $vacc_expiration ?></p><span> <?php echo $expired ? 'Active' : 'Expired' ?></span>
        </td>
        <td>
          <a target="_blank" href="<?php echo wp_get_upload_dir()['baseurl'] . "/dogs/vaccinations/{$vaccination}" ?>">
            <img style="width:80px;height:60px"
              src="<?php echo wp_get_upload_dir()['baseurl'] . "/dogs/vaccinations/{$vaccination}" ?>"
              alt="<?php echo $name ?>" srcset="">
          </a>
        </td>
        <td>
          <p><a class='admin-edit-dog'
              href="<?php echo admin_url("admin-ajax.php?action=mpca_edit_dog&ca={$sub->corporate_account->id}&dog={$id}") ?>">edit
              dog</a></p>
        </td>
        <td>
          <p><a class='admin-remove-dog'
              href="<?php echo admin_url("admin-ajax.php?action=mpca_remove_dog&ca={$sub->corporate_account->id}&dog={$id}") ?>">remove
              dog</a></p>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>

    <?php endforeach; ?>

  </td>
</tr>