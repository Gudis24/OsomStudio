<?php
get_header(); ?>

<?php if(have_rows('settings','option')): ?>
  <?php while( have_rows('settings','option') ): the_row(); ?>
    <div class="pageContainer flexing">
      <div class="siteWidth">
        <?php $settings = get_field('settings','option'); ?>
        <?php $save_into_db = $settings['save_form_into_db'];  ?>
        <?php $save_into_file = $settings['save_form_into_file']; ?>
          <?php $logo = $settings['logo']['id'] ?>
          <header class="logoWrapper">
            <?php echo wp_get_attachment_image($logo,'logo'); ?>
            <div class="formTitle"><?php _e('Wypełnij formularz','osom-kamil') ?></div>
          </header>


          <?php echo osom_form(); // display form ?>
          <?php
          if ($save_into_file) { // if checkbox is checked display table using file data
            ?> <h2><?php _e('TABLE FILE','osom-kamil') ?></h2> <?php
            echo table_from_file();
          }
          ?>
          <?php
          if ($save_into_db) { // if checkbox is checked display table using db data
            ?> <h2> <?php _e('TABLE DB','osom-kamil') ?></h2> <?php
                echo table_from_db();
          }; ?>
      </div>
    </div>
  <?php endwhile; ?>
<?php endif; ?>
<?php
get_footer(); ?>
