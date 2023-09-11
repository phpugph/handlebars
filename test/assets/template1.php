<?php //-->
use \Handlebars\HandlebarsData as Data;
use \Handlebars\HandlebarsRuntime as Runtime;

return function($data = array()) {
  $noop = function() {};
  $context = $data = Data::i($data);
  $helper = Runtime::getHelpers($data);
	
  $buffer = ''; 

  $buffer .= '<div class="product-fields">'."\n";
  $buffer .= '  <div class="form-group';
  $buffer .= $helper['if'](
    $data->find('errors.product_title'), 
    array(
      'name' => 'if',
      'args' => 'if errors.product_title',
      'hash' => array(),
      'fn' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= ' has-error';

        if(is_array($context)) {
          $data->pop();
        }

        return $buffer;
      },

      'inverse' => $noop
    )
  );

  $buffer .= ' clearfix">'."\n";
  $buffer .= '    <label class="control-label">';
  $buffer .= htmlspecialchars((string) $helper['_'](
    'Title', 
    array(
      'name' => '_',
      'args' => '_ \'Title\'',
      'hash' => array(),
      'fn' => $noop,
      'inverse' => $noop
    )
  ), ENT_COMPAT, 'UTF-8');

  $buffer .= '</label>'."\n";
  $buffer .= '    <div>'."\n";
  $buffer .= '      <input'."\n";
  $buffer .= '        type="text"'."\n";
  $buffer .= '        class="form-control"'."\n";
  $buffer .= '        name="product_title"'."\n";
  $buffer .= '        placeholder="';
  $buffer .= htmlspecialchars((string) $helper['_'](
    'What is the name of this product?', 
    array(
      'name' => '_',
      'args' => '_ \'What is the name of this product?\' id=\'nav-bar\'',
      'hash' => array('id' => 'nav-bar'),
      'fn' => $noop,
      'inverse' => $noop
    )
  ), ENT_COMPAT, 'UTF-8');

  $buffer .= '"'."\n";
  $buffer .= '        value="';
  $buffer .= $data->find('item.product_title');

  $buffer .= '" />'."\n";
  $buffer .= "\n";
  $buffer .= '      ';
  $buffer .= $helper['if'](
    $data->find('errors.product_title'), 
    array(
      'name' => 'if',
      'args' => 'if errors.product_title',
      'hash' => array(),
      'fn' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= "\n";
        $buffer .= '      <span class="help-text text-danger">';
        $buffer .= htmlspecialchars($data->find('errors.product_title') ?? '', ENT_COMPAT, 'UTF-8');

        $buffer .= '</span>'."\n";
        $buffer .= '      ';

        if(is_array($context)) {
          $data->pop();
        }

        return $buffer;
      },

      'inverse' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= "\n";
        $buffer .= '      <span class="help-text text-danger">';
        $buffer .= $helper['noop'](
          array(
            'name' => 'noop',
            'hash' => array(),
            'fn' => function($context = null) use ($noop, $data, &$helper) {
              if(is_array($context)) {
                $data->push($context);
              }

              $buffer = '';
              $buffer .= 'unknown';
              if(is_array($context)) {
                $data->pop();
              }

              return $buffer;
            },
            'inverse' => $noop
          )
        );

        $buffer .= '</span>'."\n";
        $buffer .= '      ';

          if(is_array($context)) {
            $data->pop();
          }

        return $buffer;
      }

    )
  );

  $buffer .= "\n";
  $buffer .= '    </div>'."\n";
  $buffer .= '  </div>'."\n";
  $buffer .= "\n";
  $buffer .= '  <div class="form-group';
  $buffer .= $helper['if'](
    $data->find('errors.product_detail'), 
    array(
      'name' => 'if',
      'args' => 'if errors.product_detail',
      'hash' => array(),
      'fn' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= ' has-error';

        if(is_array($context)) {
          $data->pop();
        }

        return $buffer;
      },

      'inverse' => $noop
    )
  );

  $buffer .= ' clearfix">'."\n";
  $buffer .= '    <label class="control-label">';
  $buffer .= htmlspecialchars((string) $helper['_'](
    'Detail', 
    array(
      'name' => '_',
      'args' => '_ \'Detail\'',
      'hash' => array(),
      'fn' => $noop,
      'inverse' => $noop
    )
  ), ENT_COMPAT, 'UTF-8');

  $buffer .= '</label>'."\n";
  $buffer .= '    <div>'."\n";
  $buffer .= '      <textarea'."\n";
  $buffer .= '        class="form-control"'."\n";
  $buffer .= '        name="product_detail"'."\n";
  $buffer .= '        placeholder="';
  $buffer .= htmlspecialchars((string) $helper['_'](
    'Enter some details about this product.', 
    array(
      'name' => '_',
      'args' => '_ \'Enter some details about this product.\'',
      'hash' => array(),
      'fn' => $noop,
      'inverse' => $noop
    )
  ), ENT_COMPAT, 'UTF-8');

  $buffer .= '">';
  $buffer .= $data->find('item.product_detail');

  $buffer .= '</textarea>'."\n";
  $buffer .= "\n";
  $buffer .= '      ';
  $buffer .= $data->find('#if errors.product_detail');

  $buffer .= "\n";
  $buffer .= '      <span class="help-text text-danger">';
  $buffer .= htmlspecialchars($data->find('errors.product_detail') ?? '', ENT_COMPAT, 'UTF-8');

  $buffer .= '</span>'."\n";
  $buffer .= '      ';
  $buffer .= $data->find('/if');

  $buffer .= "\n";
  $buffer .= '    </div>'."\n";
  $buffer .= '  </div>'."\n";
  $buffer .= "\n";
  $buffer .= '  <div class="form-group';
  $buffer .= $helper['if'](
    $data->find('errors.product_brand'), 
    array(
      'name' => 'if',
      'args' => 'if errors.product_brand',
      'hash' => array(),
      'fn' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= ' has-error';

        if(is_array($context)) {
          $data->pop();
        }

        return $buffer;
      },

      'inverse' => $noop
    )
  );

  $buffer .= ' clearfix">'."\n";
  $buffer .= '    <label class="control-label">';
  $buffer .= htmlspecialchars((string) $helper['_'](
    'Brand', 
    array(
      'name' => '_',
      'args' => '_ \'Brand\'',
      'hash' => array(),
      'fn' => $noop,
      'inverse' => $noop
    )
  ), ENT_COMPAT, 'UTF-8');

  $buffer .= '</label>'."\n";
  $buffer .= '    <div>'."\n";
  $buffer .= '      <input'."\n";
  $buffer .= '        type="text"'."\n";
  $buffer .= '        class="form-control"'."\n";
  $buffer .= '        name="product_brand"'."\n";
  $buffer .= '        placeholder="';
  $buffer .= $helper['_'](
    'What brand is this?', 
    array(
      'name' => '_',
      'args' => '_ \'What brand is this?\' id=\'nav-bar\'',
      'hash' => array('id' => 'nav-bar'),
      'fn' => $noop,
      'inverse' => $noop
    )
  );

  $buffer .= '"'."\n";
  $buffer .= '        value="';
  $buffer .= htmlspecialchars($data->find('item.product_brand') ?? '', ENT_COMPAT, 'UTF-8');

  $buffer .= '" />'."\n";
  $buffer .= "\n";
  $buffer .= '      ';
  $buffer .= $helper['if'](
    $data->find('errors.product_brand'), 
    array(
      'name' => 'if',
      'args' => 'if errors.product_brand',
      'hash' => array(),
      'fn' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= "\n";
        $buffer .= '      <span class="help-text text-danger">';
        $buffer .= htmlspecialchars($data->find('errors.product_brand') ?? '', ENT_COMPAT, 'UTF-8');

        $buffer .= '</span>'."\n";
        $buffer .= '      ';

        if(is_array($context)) {
          $data->pop();
        }

        return $buffer;
      },

      'inverse' => $noop
    )
  );

  $buffer .= "\n";
  $buffer .= '    </div>'."\n";
  $buffer .= '  </div>'."\n";
  $buffer .= "\n";
  $buffer .= '  <div class="form-group';
  $buffer .= $helper['if'](
    $data->find('errors.product_price'), 
    array(
      'name' => 'if',
      'args' => 'if errors.product_price',
      'hash' => array(),
      'fn' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= ' has-error';

        if(is_array($context)) {
          $data->pop();
        }

        return $buffer;
      },

      'inverse' => $noop
    )
  );

  $buffer .= ' clearfix">'."\n";
  $buffer .= '    <label class="control-label">';
  $buffer .= htmlspecialchars((string) $helper['_'](
    'Price', 
    array(
      'name' => '_',
      'args' => '_ \'Price\'',
      'hash' => array(),
      'fn' => $noop,
      'inverse' => $noop
    )
  ), ENT_COMPAT, 'UTF-8');

  $buffer .= '</label>'."\n";
  $buffer .= '    <div>'."\n";
  $buffer .= '      <input'."\n";
  $buffer .= '        type="number"'."\n";
  $buffer .= '        class="form-control"'."\n";
  $buffer .= '        name="product_price"'."\n";
  $buffer .= '        min = "0"'."\n";
  $buffer .= '        step = "0.01"'."\n";
  $buffer .= '        placeholder="';
  $buffer .= htmlspecialchars((string) $helper['_'](
    'How much do you want to sell it for?', 
    array(
      'name' => '_',
      'args' => '_ \'How much do you want to sell it for?\'',
      'hash' => array(),
      'fn' => $noop,
      'inverse' => $noop
    )
  ), ENT_COMPAT, 'UTF-8');

  $buffer .= '"'."\n";
  $buffer .= '        ';
  $buffer .= $helper['noop'](
    $data->find('item'), 
    array(
      'name' => 'noop',
      'args' => 'noop item id=true',
      'hash' => array('id' => true),
      'fn' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= "\n";
        $buffer .= '        value="';
        $buffer .= htmlspecialchars($data->find('product_price') ?? '', ENT_COMPAT, 'UTF-8');

        $buffer .= '"'."\n";
        $buffer .= '        ';

        if(is_array($context)) {
          $data->pop();
        }

        return $buffer;
      },

      'inverse' => $noop
    )
  );

  $buffer .= "\n";
  $buffer .= '      />'."\n";
  $buffer .= "\n";
  $buffer .= '      ';
  $buffer .= $helper['if'](
    $data->find('errors.product_price'), 
    array(
      'name' => 'if',
      'args' => 'if errors.product_price',
      'hash' => array(),
      'fn' => function($context = null) use ($noop, $data, &$helper) {
        if(is_array($context)) {
          $data->push($context);
        }

        $buffer = '';
        $buffer .= "\n";
        $buffer .= '      <span class="help-text text-danger">';
        $buffer .= htmlspecialchars($data->find('errors.product_price') ?? '', ENT_COMPAT, 'UTF-8');

        $buffer .= '</span>'."\n";
        $buffer .= '      ';

        if(is_array($context)) {
          $data->pop();
        }

        return $buffer;
      },

      'inverse' => $noop
    )
  );

  $buffer .= "\n";
  $buffer .= '    </div>'."\n";
  $buffer .= '  </div>'."\n";
  $buffer .= '</div>'."\n";
  $buffer .= '';

  return $buffer;
};