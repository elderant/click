<?php global $title;?>
<div class="wrap">
  <h1 class="wp-heading-inline"><?php echo $title;?></h1>
  <hr class="wp-header-end">
  <?php update_shop_code_handler();?>
  <form id="shop-code-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
    <input type="hidden" name="action" value="update_shop_code">
    <input type="hidden" name="name" value="<?php echo $click_admin_args['name'];?>">
    <div id="poststuff">
      <div id="post-body" class="metabox-holder columns-2">
        <div id="post-body-content">
          <div class="titlediv">
            <div class="titlewrap">
              <input type="text" name="shop_code_name" id="name" size="30" placeholder="Nombre">
            </div>
          </div>
          <div class="titlediv">
            <div class="titlewrap">
              <input type="text" name="shop_code_base" id="code" size="30" placeholder="Código">
            </div>
          </div>
          <input type="submit" value="Guardar" class="button button-primary button-large">
        </div>
      </div>
    </div>
  </form>
</div>