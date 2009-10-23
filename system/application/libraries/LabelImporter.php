<?php

class LabelImporter
{
  private $model = null;
  
  function LabelImporter()
  {
    $this->model = load_ci_model('label_model');
  }
  
  public function import_xml($file)
  {
    $xmlDoc = new DOMDocument();
    if(!$xmlDoc->load($file, LIBXML_NOERROR)) {
      return null;
    }

    return $this->import_xml_node($xmlDoc->documentElement);
  }

  public function import_xml_node($top)
  {
    if(!$top || $top->nodeName != 'labels') {
      return null;
    }

    $labels_data = array();

    foreach($top->childNodes as $label) {
      if($label->nodeName != 'label') {
        continue;
      }

      $name_node = find_xml_child($label, 'name');
      if(!$name_node) {
        continue;
      }

      $type_node = find_xml_child($label, 'type');
      if(!$type_node) {
        continue;
      }

      # get name
      $name = xmlspecialchars_decode(trim($name_node->textContent));
      if(!$name) {
        continue;
      }

      # get type
      $type = xmlspecialchars_decode(trim($type_node->textContent));
      if(!$type || !label_valid_type($type)) {
        continue;
      }

      # get comment
      $comment_node = find_xml_child($label, 'comment');
      if($comment_node) {
        $comment = xmlspecialchars_decode($comment_node->textContent);
      } else {
        $comment = '';
      }

      # get default
      $default = $this->__find_and_parse_boolean_node($label, 'default');

      # get must_exist
      $must_exist = $this->__find_and_parse_boolean_node($label, 'must_exist');

      # get auto_on_creation
      $auto_on_creation = $this->__find_and_parse_boolean_node($label, 'auto_on_creation');

      # get auto_on_modification
      $auto_on_modification = $this->__find_and_parse_boolean_node($label, 'auto_on_modification');

      # get deletable
      $deletable = $this->__find_and_parse_boolean_node($label, 'deletable');

      # get multiple
      $multiple = $this->__find_and_parse_boolean_node($label, 'multiple');

      # get public
      $public = $this->__find_and_parse_boolean_node($label, 'public');

      # get editable
      $editable = $this->__find_and_parse_boolean_node($label, 'editable');

      # get code
      $code_node = find_xml_child($label, 'code');
      if($code_node) {
        $code = xmlspecialchars_decode($code_node->textContent);
      } else {
        $code = '';
      }
      
      # get action modification
      $action_modification_node = find_xml_child($label, 'action_modification');
      if($action_modification_node) {
        $action_modification = xmlspecialchars_decode($action_modification_node->textContent);
      } else {
        $action_modification = '';
      }

      # get valid_code
      $valid_code_node = find_xml_child($label, 'valid_code');
      if($valid_code_node) {
        $valid_code = xmlspecialchars_decode($valid_code_node->textContent);
      } else {
        $valid_code = '';
      }

      $label_data = array('name' => $name,
                      'type' => $type,
                      'must_exist' => $must_exist,
                      'auto_on_creation' => $auto_on_creation,
                      'auto_on_modification' => $auto_on_modification,
                      'deletable' => $deletable,
                      'editable' => $editable,
                      'multiple' => $multiple,
                      'default' => $default,
                      'public' => $public,
                      'code' => $code,
                      'action_modification' => $action_modification,
                      'valid_code' => $valid_code,
                      'comment' => $comment);

      if($this->model->has($name)) {
        $id = $this->model->get_id_by_name($name);

        $ret = $this->model->edit($id, $name, $type, $must_exist, $auto_on_creation,
          $auto_on_modification, $deletable, $editable, $multiple, $default,
          $public, $code, $action_modification, $valid_code, $comment);

        $label_data['mode'] = 'edit';
        $label_data['ret'] = $ret;
        $label_data['id'] = $id;
      } else {
          
        $ret = $this->model->add($name, $type, $must_exist, $auto_on_creation,
            $auto_on_modification, $deletable, $editable, $multiple,
            $default, $public, $code, $action_modification, $valid_code, $comment);

        $label_data['mode'] = 'add';
        $label_data['ret'] = $ret;
        $label_data['id'] = $ret;
      }

      $labels_data[] = $label_data;
    }

    return $labels_data;
  }

  private function __find_and_parse_boolean_node($node, $name, $default = false)
  {
    $node = find_xml_child($node, $name);
    if($node) {
      return parse_boolean_value($node->textContent);
    } else {
      return $default;
    }
  }
}