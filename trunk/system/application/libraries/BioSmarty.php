<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require "Smarty-2.6.20/libs/Smarty.class.php";

function form_label_error($what, $for, $data = array())
{
  $data['class'] = 'formerror';

  return form_label($what, $for, $data);
}

function smarty_block_evalphp($params, $content, &$smarty, &$repeat)
{
  $content = trim($content);
  if($content == '') {
    return "";
  }
  return(eval("return " . $content . ";"));
}

function smarty_function_display_none($params, &$smarty)
{
  return "style=\"display: none;\"";
}

function smarty_function_form_label_error($params, &$smarty)
{
  $data = array();

  $id = $params['id'];
  if($id) {
    $data['id'] = $id;
  }

  return form_label_error($params['msg'], $params['for'], $data);
}

function smarty_function_form_label($params, &$smarty)
{
  $data = array(
    'class' => $params['class'],
  );

  $id = $params['id'];
  if($id) {
    $data['id'] = $id;
  }

  return form_label($params['msg'], $params['for'], $data);
}

function smarty_function_form_password($params, &$smarty)
{
  $name = $params['name'];
  $password_data = array(
    'name' => $name,
    'id' => $name,
    'size' => $params['size']
  );
  
  $autocomplete = $params['autocomplete'];
  if($autocomplete) {
    $password_data['autocomplete'] = $autocomplete;
  }
  return form_password($password_data);
}

function smarty_function_form_submit($params, &$smarty)
{
  $class = $params['class'];
  if(!$class) {
    $class = 'submit';
  }

  $name = $params['name'];

  $id = $params['id'];
  if(!$id) {
    $id = $name;
  }

  $data = array(
    'value' => $params['msg'],
    'class' => $class
  );
  
  if($name) {
    $data['name'] = $name;
  }
  
  if($id) {
    $data['id'] = $id;
  }
  
  return form_submit($data);
}

function smarty_function_form_open($params, &$smarty)
{
  $data = array();

  $name = $params['name'];

  if($name) {
    $data['id'] = $name;
  }

  $multipart = $params['multipart'];
  if($multipart) {
    $multipart = ($multipart == 'yes' ? true : false);
  }

  $class = $params['class'];
  if($class) {
    $data['class'] = $class;
  }

  $to = $params['to'];
  if(!$to) {
    $to = '#';
  }

  $method = $params['method'];
  if(!$method) {
    $method = 'post';
  }
  $data['method'] = $method;

  if($multipart) {
    return form_open_multipart($to, $data, '');
  } else {
    return form_open($to, $data);
  }
}

function smarty_function_anchor($params, &$smarty)
{
  return anchor($params['to'], $params['msg']);
}

function smarty_function_form_end($params, &$smarty)
{
  return '</form>';
}

function common_input_textarea(&$data, $params)
{
  $readonly = $params['readonly'];
  if(!$readonly) {
    $readonly = null;
  } else {
    $readonly = ($readonly == 'true') ? 'readonly' : null;
  }

  if($readonly) {
    $data['readonly'] = $readonly;
  }

  $name = $params['name'];
  $data = array(
    'name' => $name,
  );
  $id = $params['id'];
  if(!$id) {
    $id = $name;
  }

  $data['id'] = $id;

  $init = $params['value'];
  if($init || $init == '0') {
    $data['value'] = $init;
  }

  $class = $params['class'];
  if($class) {
    $data['class'] = $class;
  }
}

function smarty_function_form_input($params, &$smarty)
{
  $data = array();

  common_input_textarea(&$data, $params);

  $max_value = $params['max'];
  if($max_value) {
    $data['maxlength'] = $max_value;
  }

  $size = $params['size'];
  if($size) {
    $data['size'] = $size;
  }

  $readonly = $params['readonly'];
  if($readonly) {
    $data['readonly'] = $readonly;
  }
  
  $autocomplete = $params['autocomplete'];
  if($autocomplete) {
    $data['autocomplete'] = $autocomplete;
  }

  return form_input($data);
}

function smarty_function_form_textarea($params, &$smarty)
{
  $data = array();

  common_input_textarea(&$data, $params);

  $rows = $params['rows'];
  if($rows) {
    $data['rows'] = $rows;
  }

  $cols = $params['cols'];
  if($cols) {
    $data['cols'] = $cols;
  }

  return form_textarea($data);
}

function smarty_function_form_hidden($params, &$smarty)
{
  $name = $params['name'];
  $value = $params['value'];

  return form_hidden($name, $value);
}

function smarty_function_form_checkbox($params, &$smarty)
{ 
  $initial = $smarty->get_initial_var($params['name']);
  if($initial) {
    $params['checked'] = TRUE;
  }
  
  return form_checkbox(__fill_checkbox_radio($params));
}

function __fill_checkbox_radio(&$params)
{
  $data = array();

  $name = $params['name'];
  $data['name'] = $name;

  $id = $params['id'];
  if(!$id) {
    $id = $name;
  }
  $data['id'] = $id;

  $value = $params['value'];
  if(!$value) {
    $value = 'yes';
  }

  $data['value'] = $value;

  $class = $params['class'];
  if($class) {
    $data['class'] = $class;
  }

  $checked = $params['checked'];
  $data['checked'] = ($checked ? TRUE : FALSE);
  
  return $data;
}

function smarty_function_form_radio($params, &$smarty)
{
  $options = explode(',', $params['options']);
  $msgs = explode(',', $params['msgs']);
  $checked = $params['checked'];
  $params['options'] = null;
  
  $ret = '';
  $i = 0;
  foreach($options as $option) {
    $params['checked'] = ($option == $checked);
    $params['value'] = $option;
    $ret .= form_radio(__fill_checkbox_radio($params)) . $msgs[$i++];
  }
  
  return $ret;
}

function smarty_function_form_select($params, &$smarty)
{
  $name = $params['name'];
  $data = array('name' => $name);

  $id = $params['id'];
  if(!$id) {
    $id = $name;
  }

  $options = array();

  $class = $params['class'];
  if($class) {
    $options['class'] = $class;
  }

  $blank = $params['blank'];
  if($blank) {
    $options['0'] = '';
  }

  $key = $params['key'];
  if(!$key) {
    $key = 'id';
  }

  $value = $params['value'];
  if(!$value) {
    $value = 'name';
  }

  $data = $params['data'];
  if($data) {
    foreach($data as $row_data) {
      $this_key = $row_data[$key];
      $options[$this_key] = $row_data[$value];
    }
  }

  $start = $params['start'];
  if($start == null && $start != "0" && $start != 0) {
    $start = '1';
  }
  
  return form_dropdown($name, $options, $start, "id=\"$id\"");
}

function smarty_function_form_upload($params, &$smarty)
{
  $name = $params['name'];
  $upload_data = array(
    'name' => $name,
    'id' => $name,
    'size' => $params['size'],
  );
  return form_upload($upload_data);
}

function smarty_function_top_dir($params, &$smarty)
{
  return dirname(site_url());
}

function smarty_function_site($params, &$smarty)
{
  return site_url();
}

function smarty_function_form_row($params, &$smarty)
{
  $what = $params['name'];
  $type = $params['type'];
  $simple = $params['simple'];

  if(!$type) {
    $type = 'text';
  }

  $label_data = array(
    'to' => $what,
    'msg' => $params['msg'],
  );

  if(!$simple) {
    $label_data['class'] = 'desc'; // style for label descriptions
  }

  $label_error_data = array(
    'msg' => $smarty->get_template_vars($what . '_error'),
    'for' => $what,
  );

  $ret = '';
  if(!$simple) {
    $row_id = $params['row_id'];
    $ret .= '<div class="row"';
    if($row_id) {
      $ret .= " id=\"$row_id\"";
    }

    $ret .= ">\n";
  }

  $ret .= smarty_function_form_label($label_data, $smarty) . "\n";

  $size = $params['size'];
  $class = $params['class'];
  $id = $params['id'];

  if($type == 'password') {
    $password_data = array(
      'name' => $what,
      'size' => $size,
      'class' => $class,
      'autocomplete' => $params['autocomplete']
    );
    $ret .= smarty_function_form_password($password_data, $smarty);
  } else if($type == 'upload') {
    $upload_data = array(
      'name' => $what,
      'size' => $size,
      'id' => $id,
      'class' => $class,
    );
    $ret .= smarty_function_form_upload($upload_data, $smarty);
  } else if($type == 'checkbox') {
    $value = $params['value'];
    $checked = $params['checked'];

    $check_data = array(
      'name' => $what,
      'class' => $class,
      'id' => $id,
      'value' => $value,
      'checked' => $checked,
    );

    $ret .= smarty_function_form_checkbox($check_data, $smarty);
  } else if($type == 'radio'){
    $options = $params['options'];
    $msgs = $params['msgs'];
    $checked = $params['checked'];
    
    $radio_data = array(
      'name' => $what,
      'class' => $class,
      'checked' => $checked,
      'options' => $options,
      'msgs' => $msgs);
      
      $ret .= smarty_function_form_radio($radio_data, $smarty);
  } else if($type == 'select') {
    $start = $params['start'];
    $initial = $smarty->get_initial_var($what);

    if($initial) {
      $start = $initial;
    }

    $select_data = array(
      'name' => $what,
      'class' => $class,
      'id' => $id,
      'start' => $start,
      'blank' => $params['blank'],
      'key' => $params['key'],
      'value' => $params['value'],
      'data' => $params['data']
    );

    $ret .= smarty_function_form_select($select_data, $smarty);
  } else {
    $value = $params['value'];

    if(!$value) {
      $value = $smarty->get_initial_var($what);
    }

    $input_data = array(
      'name' => $what,
      'id' => $id,
      'value' => $value,
      'size' => $size,
      'class' => $class,
      'cols' => $params['cols'],
      'rows' => $params['rows'],
      'maxlength' => $params['maxlength'],
      'readonly' => $params['readonly'],
      'autocomplete' => $params['autocomplete']
    );

    if($type == 'textarea') {
      $ret .= smarty_function_form_textarea($input_data, $smarty);
    } else {
      $ret .= smarty_function_form_input($input_data, $smarty);
    }
  }

  $ret .= "\n" . smarty_function_form_label_error($label_error_data, $smarty) .  "\n";
 
  if(!$simple) {
    $ret .= '</div>' . "\n";
  }

  return $ret;
}

function smarty_function_button($params, &$smarty)
{
  $name = $params['name'];
  $method = $params['method'];

  if(!$method) {
    $method = 'get';
  }

  $form_name = "form_$name";

  $form_open_params = array(
    'name' => $form_name,
    'to' => $params['to'],
    'method' => $method,
  );

  $form_end_params = array();

  return smarty_function_form_open($form_open_params, &$smarty) .
    '<input class="submit" type="submit" value="' . $params['msg'] . '" />' .
    smarty_function_form_end($form_end_params, &$smarty);
}

function smarty_function_assign_id($params, &$smarty)
{
  $var = $params['var'];
  $key = $params['key'];
  $dict = $params['dict'];
  $val = $dict[$key];

  $smarty->assign($var, $var . '_' . strval($val));
}

function smarty_function_encode_json_data($params, &$smarty)
{
  $key = $params['key'];
  if(!$key) {
    $key = 'id';
  }
  
  $value = $params['value'];
  if(!$value) {
    $value = 'name';
  }

  $addblank = $params['blank'];

  $data = $params['data'];

  if($addblank) {
    $blank = array(
      $key => '0',
      $value => '',
    );

    array_unshift($data, $blank);
  }

  $ret = '"{';
  $first = true;

  foreach($data as $row) {
    $my_key = $row[$key];
    $my_value = $row[$value];

    if(!$first) {
      $ret .= ',';
    } else {
      $first = false;
    }

    $ret .= "'$my_key':'$my_value'";
  }

  $ret .= '}"';
  return $ret;
}

function smarty_function_loader_pic($params, &$smarty)
{
  $site = base_url();
  return "<img id=\"loader\" src=\"$site/images/loading.gif\" style=\"display: none;\" />";
}

function smarty_function_boolean($params, &$smarty)
{
  $value = $params['value'];

  if($value) {
    return 'Yes';
  } else {
    return 'No';
  }
}

function smarty_function_random($params, &$smarty)
{
  srand((double)microtime() * 1000000);

  $min = $params['min'];
  $max = $params['max'];

  if(!$min) {
    $min = 0;
  }

  if(!$max) {
    $max = 32768;
  }

  $random_number = rand($max, $min);

  return $random_number;
}

function findexts($filename)
{
  $exts = split("[/\\.]", $filename) ;
  $n = count($exts)-1;

  if($n == 0) {
    return null;
  }

  return $exts[$n];
} 

function smarty_function_include_js($params, &$smarty)
{
  $top_dir = smarty_function_top_dir(array(), &$smarty);
  $name = $params['name'];
  $random = smarty_function_random(array(), &$smarty);
  $ext = findexts($name);
  if($ext == null) {
    $name = "$name.js";
  }

  return "<script type=\"text/javascript\" src=\"$top_dir/scripts/$name?random=$random\"></script>";
}

function smarty_function_to_js($params, &$smarty)
{
  $data = $params['value'];
  $var = $params['var'];

  return "var $var = " . json_encode($data) . ";";
}

function smarty_function_encode_json($params, &$smarty)
{
  $value = $params['value'];
  
  return json_encode($value);
}

function smarty_function_split_value($params, &$smarty)
{
  $value =& $params['val'];
  $MAX_SPACING = 40;
  
  return split_string($value, $MAX_SPACING, '<br />');
}

class BioSmarty extends Smarty
{
  private $controller;

	function BioSmarty()
  {
    parent::Smarty();
    $this->register_block('e', 'smarty_block_evalphp');
    $this->register_function('form_label_error', 'smarty_function_form_label_error');
    $this->register_function('form_label', 'smarty_function_form_label');
    $this->register_function('form_password', 'smarty_function_form_password');
    $this->register_function('form_upload', 'smarty_function_form_upload');
    $this->register_function('form_submit', 'smarty_function_form_submit');
    $this->register_function('form_open', 'smarty_function_form_open');
    $this->register_function('form_end', 'smarty_function_form_end');
    $this->register_function('form_checkbox', 'smarty_function_form_checkbox');
    $this->register_function('form_textarea', 'smarty_function_form_textarea');
    $this->register_function('form_input', 'smarty_function_form_input');
    $this->register_function('form_select', 'smarty_function_form_select');
    $this->register_function('form_hidden', 'smarty_function_form_hidden');
    $this->register_function('form_row', 'smarty_function_form_row');
    $this->register_function('form_radio', 'smarty_function_form_radio');
    $this->register_function('button', 'smarty_function_button');
    $this->register_function('anchor', 'smarty_function_anchor');
    $this->register_function('top_dir', 'smarty_function_top_dir');
    $this->register_function('site', 'smarty_function_site');
    $this->register_function('assign_id', 'smarty_function_assign_id');
    $this->register_function('encode_json_data', 'smarty_function_encode_json_data');
    $this->register_function('loader_pic', 'smarty_function_loader_pic');
    $this->register_function('boolean', 'smarty_function_boolean');
    $this->register_function('random', 'smarty_function_random');
    $this->register_function('include_js', 'smarty_function_include_js');
    $this->register_function('to_js', 'smarty_function_to_js');
    $this->register_function('display_none', 'smarty_function_display_none');
    $this->register_function('encode_json', 'smarty_function_encode_json');
    $this->register_function('split_value', 'smarty_function_split_value');

		$config =& get_config();
		
		$this->template_dir = (!empty($config['smarty_template_dir']) ? $config['smarty_template_dir'] 
																	  : BASEPATH . 'application/views/');
																	
		$this->compile_dir  = (!empty($config['smarty_compile_dir']) ? $config['smarty_compile_dir'] 
																	 : BASEPATH . 'cache/'); //use CI's cache folder        
		
		if (function_exists('site_url')) {
    		// URL helper required
			$this->assign("site_url", site_url()); // so we can get the full path to CI easily
		}

    $this->header = 'header.tpl';
    $this->footer = 'footer.tpl';
  }

  private function __build_template_path($file)
  {
    return $this->template_dir . $file;
  }

  private function __get_resource_name($name)
  {
    $resource_name = $name;

		if (strpos($resource_name, '.') === false) {
			$resource_name .= '.tpl';
		}
		
		// check if the template file exists.
		if (!is_file($this->template_dir . $resource_name)) {
			show_error("template: [$resource_name] cannot be found.");
		}

    return $resource_name;
  }

	public function view($resource_name)
	{  
	  $this->controller->load->model('comment_model');
    $comment = htmlspecialchars($this->controller->comment_model->get());
    $this->assign('comment_header', $comment);
    
    $this->controller->load->model('sequence_model');
    $total_seqs = $this->controller->sequence_model->get_total();
    $this->assign('total_seqs', $total_seqs);
    
    parent::display($this->header);
    $ret = parent::display($this->__get_resource_name($resource_name));
    parent::display($this->footer);

    return $ret;
  }

  public function view_s($resource_name)
  {
    return parent::display($this->__get_resource_name($resource_name));
  }

  public function view_js($js_temp)
  {
    return parent::display($this->__get_resource_name("$js_temp.js.tpl"));
  }

  public function set_controller($what)
  {
    $this->controller = $what;
  }

  // assign the $scripts variable to all the arguments passed in
  // all the js scripts will be loaded from the scripts/ directory
  public function load_scripts()
  {
    $scripts = func_get_args();
    foreach($scripts as $script) {
      $this->append_no_repeat('scripts', $script);
    }
  }

  // same thing, but for css stylesheets
  public function load_stylesheets()
  {
    $stylesheets = func_get_args();
    foreach($stylesheets as $sheet) {
      $this->append_no_repeat('stylesheets', $sheet);
    }
  }
  
  private function append_no_repeat($name, $value)
  {
    $obj =& $this->get_template_vars($name);
    if(is_array($obj)) {
      foreach($obj as $thing) {
        if($thing == $value) {
          return;
        }
      }
    }
    
    $this->append($name, $value);
  }

  public function fetch_form_row($what, $default = null)
  {
    $initial_str = build_initial_name($what);
    $initial = $this->controller->session->flashdata($initial_str);

    if(!$initial) {
      $initial = $this->controller->get_parameter($what);
    }

    if($initial) {
      $this->assign($initial_str, $initial);
    } else if($default) {
      $this->assign($initial_str, $default);
    }
    
    $error = $this->controller->get_form_error($what);
    if($error) {
      $this->assign(build_error_name($what), $error);
    }
  }

  public function get_initial_var($var)
  {
    $var_name = build_initial_name($var);
    return $this->get_template_vars($var_name);
  }

  public function set_initial_var($var, $value)
  {
    $var_name = build_initial_name($var);
    return $this->assign($var_name, $value);
  }
}