<?php
add_theme_support( 'post-thumbnails' );
add_image_size('logo',170,170);

function osom_kamil_custom_styles() {
  wp_enqueue_style('custom-styles', get_template_directory_uri() . '/css/custom-style.css');
  wp_enqueue_script('custom-styles', get_template_directory_uri() . '/js/app.js', array('jquery'), '1.0.0', true);
}
add_action( 'wp_enqueue_scripts', 'osom_kamil_custom_styles' );

add_action('acf/init', 'my_acf_op_init');

function my_acf_op_init() {
    // Check function exists.
    if( function_exists('acf_add_options_page') ) {
        // Register options page.
        $option_page = acf_add_options_page(array(
            'page_title'    => __('Theme setting'),
            'menu_title'    => __('Theme settings'),
            'menu_slug'     => 'theme-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));
    }
}

function osom_form() {
  // osom form
  ?>
  <form action="" method="post" id="osom-form" name="osom-form">
    <div class="form-fields flexing">
      <div class="column">
        <div class="column-wrapper">
          <div class="wrapper">
            <label for="firstname"><?php _e('First name:','osom-kamil'); ?></label>
            <input type="text" pattern="[^\s]{3,}" name="firstname" required value="">
          </div>
          <div class="wrapper">
            <label for="lastname"> <?php _e('Last name:','osom-kamil'); ?></label>
            <input type="text" pattern="[^\s]{3,}" name="lastname" required value="">
          </div>
          <div class="wrapper">
            <label for="login"> <?php _e('Login:','osom-kamil'); ?></label>
            <input type="text" pattern="[^\s]{3,}" name="user_login" required value="">
          </div>
        </div>
      </div>
      <div class="column">
        <div class="column-wrapper">
          <div class="wrapper">
            <label for="e_mail"> <?php _e('User e-mail:','osom-kamil'); ?></label>
            <input type="text" pattern="[a-zA-Z0-9.-_]{1,}@[a-zA-Z.-]{2,}[.]{1}[a-zA-Z]{2,}" required name="e_mail" value="">
          </div>
          <div class="wrapper">
            <label for="city"> <?php _e('City:','osom-kamil'); ?></label>
            <select class="" name="city">
              <?php if(have_rows('cities')): ?>
                <?php while( have_rows('cities') ): the_row(); ?>
                  <?php $city = get_sub_field('city'); ?>
                  <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                <?php endwhile; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="wrapper flexing buttonWrapper">
            <button id="submit" name="submit" type="submit" disabled><?php _e('Wyślij','osom-kamil') ?></button>
            <input type="hidden" name="action" value="set_form" style="display: none; visibility: hidden; opacity: 0;">
          </div>
        </div>
      </div>
      <?php $policy_text = get_sub_field('policy_text'); ?>
      <?php if ($policy_text): ?>
      <div class="column">
        <div class="column-wrapper">
          <div class="wrapper">
            <p><?php echo $policy_text; ?></p>
            <label for="policy_agree">
              <input type="checkbox" name="policy_agree" value="" required>
              <?php _e('Agree','osom-kamil') ?>
            </label>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </form>
  <?php
}


function osom_ajax_script() {
  wp_enqueue_script('ajax-form', get_template_directory_uri() . '/js/ajax-form.js', array('jquery'), '1.0.0', true);
  wp_localize_script(
    'ajax-form',
    'osom_globals',
    [
      'ajax_url'    => admin_url( 'admin-ajax.php' ),
      'nonce'       => wp_create_nonce( 'osom_nonce' )
    ]
  );
}
add_action( 'wp_enqueue_scripts', 'osom_ajax_script' );

function set_form(){
  // set_form action
  check_ajax_referer( 'osom_nonce' );

  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $user_login = $_POST['user_login'];
  $e_mail = $_POST['e_mail'];
  $city = $_POST['city'];

  $body = 'First name: '.$firstname ."\n";
  $body .= 'Last name: ' . $lastname . "\n";
  $body .= 'User login: ' . $user_login . "\n";
  $body .= 'E mail: ' . $e_mail . "\n";
  $body .= 'City: ' . $city;

  // ACF Option fields
  $settings = get_field('settings','option');
  $save_into_db = $settings['save_form_into_db'];
  $save_into_file = $settings['save_form_into_file'];
  $email_address = $settings['email_address'];

  //save form into databse
  if($save_into_db) {
      global $wpdb;
      $wpdb->insert(
          'data_form',
          array(
            'first_name' => $firstname,
            'last_name' => $lastname,
            'user_login' => $user_login,
            'e_mail' => $e_mail,
            'city' => $city
          ),
          array( '%s' ),
      );
    }

  //save form into file
  if($save_into_file) {
    $form_file = get_stylesheet_directory() .'/form_file.json';
         $json = json_decode(file_get_contents($form_file));
         $json[] = array("First Name" => $firstname, "Last Name" =>  $lastname,"User login" => $user_login,"E-mail" => $e_mail, "City" => $city );
         file_put_contents($form_file, json_encode($json));
  }
  //check if mail is sent
	if(wp_mail($email_address,'Osom form',$body))
       {
           $response = array(
               'status' => true,
               'message' => array()
           );
   } else {
          echo "mail not sent";
   }
  echo json_encode($response);
	die();

}

add_action( 'wp_ajax_set_form', 'set_form' );
add_action( 'wp_ajax_nopriv_set_form', 'set_form' );

function table_from_file() {
  // get data from file and display table
  $form_file = file_get_contents(get_stylesheet_directory() .'/form_file.json');
  if ($form_file === false) {
      echo 'There is no file';
  }

  $osom_file = json_decode($form_file, true);
  if ($osom_file === null) {
      echo 'File is empty';
  } else { ?>
    <table width='100%'>
      <tbody>
        <tr>
          <th> <?php _e('Firstname','osom-kamil'); ?></th>
          <th> <?php _e('Lastname','osom-kamil'); ?></th>
          <th> <?php _e('Login','osom-kamil'); ?></th>
          <th> <?php _e('E-mail','osom-kamil'); ?></th>
          <th> <?php _e('City','osom-kamil'); ?></th>
        </tr>
        <?php foreach($osom_file as $osom_data): ?>
        <tr>
          <td><?php echo $osom_data['First Name']; ?></td>
          <td><?php echo $osom_data['Last Name']; ?></td>
          <td><?php echo $osom_data['User login']; ?></td>
          <td><?php echo $osom_data['E-mail']; ?></td>
          <td><?php echo $osom_data['City']; ?></td>
        </tr>
        <?php endforeach;  ?>
      </tbody>
    </table>
    <?php
  }
}

function table_from_db() {
  // get data from database and display table
  global $wpdb;
  $results = $wpdb->get_results( "SELECT * FROM data_form"); // Query to fetch data from database table and storing in $results
  if(!empty($results)) : ?>
      <table width='100%'>
        <tbody>
          <tr>
            <th> <?php _e('ID','osom-kamil'); ?></th>
            <th> <?php _e('Firstname','osom-kamil'); ?></th>
            <th> <?php _e('Lastname','osom-kamil'); ?></th>
            <th> <?php _e('Login','osom-kamil'); ?></th>
            <th> <?php _e('E-mail','osom-kamil'); ?></th>
            <th> <?php _e('City','osom-kamil'); ?></th>
          </tr>
          <?php foreach($results as $row): ?>
          <tr>
            <td><?php echo $row->ID; ?></td>
            <td><?php echo $row->first_name; ?></td>
            <td><?php echo $row->last_name; ?></td>
            <td><?php echo $row->user_login; ?></td>
            <td><?php echo $row->e_mail; ?></td>
            <td><?php echo $row->city; ?></td>
          </tr>
          <?php endforeach;  ?>
        </tbody>
      </table>
<?php endif;
}

 ?>
