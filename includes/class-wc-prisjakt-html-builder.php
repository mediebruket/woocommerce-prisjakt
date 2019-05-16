<?php

class WC_PrisjaktHtmlBuilder{

  public static function openForm($method="POST", $action='', $class=null){
    return sprintf('<form method="%s" action="%s" class="%s">', $method, $action, $class );
  }

  public static function closeForm(){
    return '</form>';
  }

  public static function buildInput( $args ){
    $attributes = null;
    foreach ($args as $attr => $value) {
      $attributes .= sprintf(' %s="%s" ', $attr, $value );
    }

    printf('<input %s/>', $attributes );
  }


  
  public static function buildSubmitButton( $text ){
    return sprintf('<button type="submit" class="button save_order button-primary">%s</button>', $text);
  }


  public static function buildNavigation( $items ){
    return '<ul class="nav nav-tabs" role="tablist">'.$items.'</ul>';
  }


  public static function buildNavItem( $text, $tab, $active = null ){
    return sprintf('<li class="nav-item"><a class="nav-link %s" href="%s" role="tab" data-toggle="tab" aria-controls="%s">%s</a></li>', (($active) ? 'active': ''),  '#'.$tab, $tab, $text );
  }


  public static function buildTab( $id, $content, $class=null ){
    $aria = ' aria-labelledby="'.$id.'-tab" ';

    return sprintf('<div class="tab-pane wcc-tab fade %s" id="%s" role="tabpanel" %s>%s</div>', $class, $id, $aria, $content );
  }


  public static function buildLabel( $text, $for=null, $css = 'mb-admin-label inline' ){
    printf('<label class="%s" for="%s">%s</label>', $css, $for, $text );
  }


  public static function buildDesc( $text, $css = 'mb-field-desc' ){
    printf( '<div><span class="%s"></span>%s</div>', $css, $text );
  }


  public static function buildOption( $option, $default_value=null ){?>
    <div class="mb-field-row <?php echo gi($option, 'wrap'); ?>">

      <?php if( $option['type'] == 'text' or $option['type'] == 'number'):

        self::buildLabel( $option['label'], $option['name'], 'mb-admin-label inline' );

        if (isset($option['desc']) && trim($option['desc']) ){
          self::buildDesc( $option['desc'] );
        }

        $args =
          array(
            'type'      => gi($option, 'type'),
            'name'      => gi($option, 'name'),
            'id'        => gi($option, 'name'),
            'value'     => gi($option, 'value'),
            'class'     => gi($option, 'css'),
            'max'       => gi($option, 'max'),
            'min'       => gi($option, 'min'),
            'size'      => gi($option, 'size', 50),
            'maxlength' => gi($option, 'maxlength', ''),
          );

        if ( isset($option['readonly']) && $option['readonly'] ){
          $args['readonly']  = 'readonly';
        }

        self::buildInput( $args );
        ?>
      <?php endif; ?>


      <?php if( $option['type'] == 'date'):
       self::buildLabel( $option['label'], $option['name'], 'mb-admin-label inline' );

        if (isset($option['desc']) && trim($option['desc']) ){
          self::buildDesc( $option['desc'] );
        }

        self::buildInput(
            array(
              'type'  => gi($option, 'type'),
              'name'  => gi($option, 'name'),
              'id'    => gi($option, 'name'),
              'value' => gi($option, 'value'),
              'class'   => ' hasDatepicker datepicker '. gi($option, 'css'),
              'size'  => 50,
            )
          )
        ?>
      <?php endif; ?>

      


      <?php if( $option['type'] == 'checkbox'): ?>
       <?php self::buildLabel( $option['label'], $option['name'], 'mb-admin-label inline' ); ?>
        <?php
          if (isset($option['desc']) && trim($option['desc']) ){
            self::buildDesc( $option['desc'] );
          }

          $checked = null;
          if ( $option['option'] == $option['value'] or $option['value'] == 'on' ){
            $checked = ' checked="checked" ';
          }
        ?>
        <input type="<?php echo $option['type']; ?>" name="<?php echo $option['name']; ?>" id="<?php echo $option['name']; ?>" value="<?php echo $option['option']; ?>" <?php echo $checked; ?> />
      <?php endif; ?>


      <?php if( $option['type'] == 'textarea'): ?>
      <?php self::buildLabel( $option['label'], $option['name'], 'mb-admin-label' ); ?>
        <textarea name="<?php echo $option['name']; ?>" id="<?php echo $option['name']; ?>" class="wcc-textarea" rows="4" cols="50"><?php echo $option['value']; ?></textarea>
      <?php endif; ?>

      <?php if( $option['type'] == 'select'): ?>
        <?php self::buildLabel( $option['label'], $option['name'], 'mb-admin-label' ); ?>
        <?php if ( isset($option['desc']) ): ?>
          <div><span class="mb-field-desc"><?php echo $option['desc']; ?></span></div>
        <?php endif; ?>
        <select name="<?php echo $option['name'] ?>" id="<?php echo $option['name'] ?>" <?php echo (isset($option['attr']) ? $option['attr']: null); ?> >
            <?php foreach ($option['options'] as $value => $title) {
              printf('<option value="%s" %s>%s</option>',  $value, selected( $value, $option['value'], $echo=false ), $title );
            }
          ?>
        </select>
      <?php endif; ?>


      

    </div>
    <?php
  }

} // end of class