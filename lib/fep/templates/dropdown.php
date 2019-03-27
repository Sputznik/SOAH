<select name="<?php _e( $field['name'] );?>" class="<?php _e( $field['class'] );?>">
  <option value="0"><?php _e( $field['label'] );?></option>
  <?php foreach( $field['options'] as $option ):?>
  <option value="<?php _e( $option['slug'] )?>" data-state="<?php _e( $option['parent'] )?>"><?php _e( $option['title'] );?></option>
  <?php endforeach;?>
</select>
