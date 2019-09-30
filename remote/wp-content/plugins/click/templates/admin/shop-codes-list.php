<?php $codes_obj = $click_admin_args['codes_obj'];?>
<div class="wrap">
  <h1><?php _e('Códigos de la tienda','click');?></h1>
  <a href="<?php echo get_admin_url(null, '/admin.php?page=wp_add_shop_code');?>"><?php _e('Agregar Código','click');?></a>
  <div id="poststuff">
    <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content">
        <div class="meta-box-sortables ui-sortable">
          <form method="post">
            <?php
              $codes_obj -> prepare_items();
              $codes_obj -> display(); 
            ?>
          </form>
        </div>
      </div>
    </div>
    <br class="clear">
  </div>
</div>