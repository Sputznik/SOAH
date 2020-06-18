<select name="<?php _e( $field['name'] );?>" class="<?php _e( $field['class'] );?>">
  <?php if( isset( $field['placeholder'] ) ):?>
  <option value="0"><?php _e( isset( $field['placeholder'] ) ? $field['placeholder'] : $field['label'] );?></option>
  <?php endif;?>
  <?php foreach( $field['options'] as $option ):?>
  <option <?php if( isset( $field['selected'] ) && $option['slug'] == $field['selected'] ){ _e("selected='selected'");}?> value="<?php _e( $option['slug'] )?>" data-state="<?php _e( isset( $option['parent'] ) ? $option['parent'] : "" );?>"><?php _e( $option['title'] );?></option>
  <?php endforeach;?>
</select>
