<div class="shop-code-section">
  <div class="shop-code-container">
    <div>
      <h5>
        <span class="">
          Inserte el codigo de la tienda 
        </span>
        <span class="">para ir a la seccion de padres.</span>
        </h5>
    </div>
    <div class="code-insert">
    <?php access_discount_shop_handler();?>
      <form id="shop-code-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
        <input type="hidden" name="action" value="access_discount_shop">  
        <input type="text" name="shop_code" class="wpcf7-form-control wpcf7-text" placeholder="<?php _e('Código','click'); ?>">
        <input type="submit" value="Enviar" class="wpcf7-form-control wpcf7-submit" disabled>
      </form>
    </div>
  </div>
</div>