<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
} ?>

<style>
.dog-table td {
  padding: 0;
}

.dog-table tr:first-child {
  height: 40px;
}

.dog-table tr {
  height: 80px;
}

.dog-table img {
  width: 80px;
  height: 60px;
}
</style>

<tr>
  <td colspan="2">
    <h3><?php _e('Dog Accounts', 'memberpress-corporate'); ?></h3>

    <div class="mepr-sub-box-white">

      <table class="form-table mepr-sub-box-white dog-table">
        <tr>
          <th>ID</th>
          <th>Photo</th>
          <th>Name</th>
          <th>Gender</th>
          <th>Breed</th>
          <th>Vacc Expiration</th>
          <th>Vaccination</th>
        </tr>
        <?php
        foreach ($dog_accounts as $dog) :
          $id = $dog->id;
          $photo = $dog->photo;
          $name = $dog->name;
          $gender = $dog->gender;
          $breed = $dog->breed;
          $vacc_expiration = $dog->vacc_expiration;
          $vaccination = $dog->vaccination;
          ?>
        <tr>
          <td>
            <p><?php echo $id ?></p>
          </td>
          <td>
            <img src="<?php echo wp_get_upload_dir()['baseurl'] . "/dogs/{$photo}" ?>" alt="" srcset="">
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
            <p><?php echo $vacc_expiration ?></p>
          </td>
          <td>
            <img style="width:80px;height:60px"
              src="<?php echo wp_get_upload_dir()['baseurl'] . "/dogs/{$vaccination}" ?>" alt="" srcset="">
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </td>
</tr>