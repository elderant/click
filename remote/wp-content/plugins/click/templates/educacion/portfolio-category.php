<?php global $post;?>
<?php $term_id = $post -> click_args -> current_category_id;?>
<?php $display_name = $post -> click_args -> current_category_display_name;?>
<?php $args = click_educacion_get_category_html_args($term_id, $display_name == __('Todos', 'click') ? true : false);?>

<li class="category-item item-<?php echo $term_id;?>">
  <a class="list-action<?php echo $args -> main_list;?>" 
      data-category="<?php echo 'portfolio_category_' . $term_id;?>">
    <div class="select-title uppercase"><?php echo $display_name?></div>
    <?php if(count($args -> child_categories) > 0) : ?>
      <div class="select-icon display"><i class="fas fa-chevron-down"></i></div>
      <div class="select-icon hidden"><i class="fas fa-chevron-up"></i></div>
    <?php else : ?>
      <label for="option-<?php echo $term_id;?>"></label>
      <?php if($display_name == __('Todos', 'click')) : ?>
        <input type="checkbox" name="option-all" value="<?php echo $term_id;?>">
      <?php else : ?>
        <input type="checkbox" name="option-<?php echo $term_id;?>" value="<?php echo $term_id;?>">
      <?php endif;?>
    <?php endif;?> 
  </a>
  <?php if(count($args -> child_categories) > 0) : ?>
    <ul class="category-list hidden">
      <?php foreach($args -> child_categories as $category) : ?>
        <?php click_educacion_get_category_html($category -> term_id, $category -> name);?>
      <?php endforeach; ?>
    </ul>
  <?php endif;?> 
</li>