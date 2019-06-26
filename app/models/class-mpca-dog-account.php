<?php if (!defined('ABSPATH')) {
  die('You are not allowed to call this page directly.');
}

class MPCA_Dog_Account extends MeprBaseModel
{

  public function __construct($obj = null)
  {
    $this->initialize(
      array(
        'id' => 0,
        'user_id' => 0,
        'name' => '',
        'breed' => '',
        'gender' => '',
        'photo' => '',
        'vaccination' => '',
        'vacc_expiration' => '',
        'ca_id' => 0
      ),
      $obj
    );
  }

  public function store()
  {
    $mepr_db = MeprDb::fetch();
    $mpca_db = MPCA_Db::fetch();

    $vals = (array)$this->get_values();
  }
}