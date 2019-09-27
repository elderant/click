<div class="filter-section ">
  <div class="filter-container">
    <div class="filter-label">
      <span><?php _e('Filtrar por : ','click');?></span>
    </div>
    <?php foreach($click_args['term_query'] -> get_terms() as $term) : ?>
      <ul class="main-list">
        <?php click_educacion_get_category_html($term -> term_id, $term -> name, $click_args);?>
      </ul>
    <?php endforeach;?>
  </div>
</div>