(function($, window, document){

  $(document).ready(function($) {
    var settings = {
      async: true,
      crossDomain: true,
      url: "http://3.217.125.251/wp-json/mp/v1/coupons",
      method: "POST",
      dataType: "json",
      headers: {
        'Authorization': 'Basic ' + btoa('root:root') //Using Application Passwords plugin for this
      },
      data: {
        coupon_code: "TESTCODE2",
        should_expire: 0,
        discount_type: "dollar",
        discount_amount: 25,
        valid_memberships: [869],
        trial: 1,
        trial_days: 90,
        trial_amount: 25
      }
    }
    $.ajax(settings).done(function(response) {
      console.log(response);
    });
  });

})(jQuery, window, document)