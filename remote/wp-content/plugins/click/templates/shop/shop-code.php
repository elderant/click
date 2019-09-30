<div class="shop-code-section">
  <div class="shop-code-container">
    <div>
      Inserte el codigo de la tienda para ir a la seccion de padres.
    </div>
    <div class="code-insert">
    <?php access_discount_shop_handler();?>
      <form id="shop-code-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
        <input type="text" name="shop_code">
        <input type="hidden" name="action" value="access_discount_shop">
        <input type="submit" value="Enviar" disabled>
      </form>
    </div>
  </div>
</div>