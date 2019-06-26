<div class="mp_wrapper">
  <h3 class="mpca-fat-bottom"><?php printf(__('Dogs for %s', 'memberpress-corporate'), $owner_name); ?></h3>
  <a href="<?php echo $url ?>"><?php echo $url ?></a>
  <div id="mpca_sub_accounts_used" class="mpca-fat-bottom">
    <h4>
      <?php printf(__('%1$s of %2$s Dogs Used', 'memberpress-corporate'), count($dog_accounts), $ca->num_sub_accounts); ?>
    </h4>
  </div>

  <?php MeprView::render('/shared/errors', compact('errors', 'message')); ?>

  <div id="mpca-add-sub-user" class="mpca-fat-bottom">

    <?php
    $sub_welcome_checked = isset($_POST['action']) ? isset($_POST['userdata[welcome]']) : false;
    ?>

    <?php if ($ca->num_sub_accounts > count($dog_accounts)) : ?>
    <button id="mpca-add-sub-user-btn" class="mpca-fat-bottom" type="button"
      value=""><?php _e('Add Dog', 'memberpress-corporate') ?></button>
    <?php endif ?>

    <form action="" method="post" id="mpca-add-sub-user-form" class="mpca-hidden" enctype="multipart/form-data">
      <input type="hidden" name="action" value="manage_dog_accounts" />
      <input type="hidden" name="manage_dog_accounts_form" value="add" />

      <label>
        <span><?php _e('Photo', 'memberpress-corporate'); ?></span>
        <input id="" type="file" name="userdata[photo]" />
      </label>

      <label>
        <span><?php _e('Name', 'memberpress-corporate'); ?> </span>
        <input id="" type="text" name="userdata[name]" />
      </label>

      <label>
        <span><?php _e('Gender', 'memberpress-corporate'); ?> </span>
        <select name="userdata[gender]" id="">
          <option value="male">Male</option>
          <option value="female">Female</option>
        </select>
      </label>

      <label>
        <span><?php _e('Breed', 'memberpress-corporate'); ?> </span>
        <input id="" type="text" name="userdata[breed]" />
      </label>

      <label>
        <span><?php _e('Vaxination Record', 'memberpress-corporate'); ?></span>
        <input id="" type="file" name="userdata[vaccination]" />
      </label>


      <label>
        <span><?php _e('Vaxination Expiration', 'memberpress-corporate'); ?></span>
        <input id="" type="date" name="userdata[vacc_expiration]" />
      </label>

      <input type="submit" value="<?php _e('Submit', 'memberpress-corporate') ?>" />
    </form>
  </div>

  <?php if (!empty($dog_accounts)) : ?>
  <?php $alt = false; ?>
  <table id="mpca-sub-accounts-table" class="mepr-account-table">
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
      <?php
        foreach ($dog_accounts as $dog) :
          ?>
      <tr id="mpca-sub-accounts-row-<?php echo $dog->id; ?>"
        class="mpca-sub-accounts-row <?php echo (isset($alt) && !$alt) ? 'mepr-alt-row' : ''; ?>">
        <td><img src="<?php echo wp_get_upload_dir()['baseurl'] . "/dogs/photos/{$dog->photo}" ?>" alt="" srcset=""
            style="width:80px;height:60px">
        </td>
        <td><?php echo $dog->name; ?></td>
        <td><?php echo $dog->breed; ?></td>
        <td><?php echo $dog->gender; ?></td>
        <td><?php echo $dog->vacc_expiration; ?></td>
        <td><img src="<?php echo wp_get_upload_dir()['baseurl'] . "/dogs/vaccinations/{$dog->vaccination}" ?>" alt=""
            srcset="" style="width:80px;height:60px"></td>
      </tr>
      <?php $alt = !$alt; ?>
      <?php endforeach; ?>
      <?php MeprHooks::do_action('mpca-sub-accounts-table', $mepr_current_user, $dog_accounts); ?>
    </tbody>
  </table>
  <br />
  <div style="clear:both">&nbsp;</div>
  <?php
else :
  _ex('You have no Dog accounts to display.', 'ui', 'memberpress-corporate');
endif;
?>
</div>