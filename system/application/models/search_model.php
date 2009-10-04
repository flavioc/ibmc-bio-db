<?php

class Search_model extends BioModel
{
  private static $public_sequence_where = "EXISTS (SELECT * FROM label_sequence_info WHERE label_sequence_info.seq_id = sequence_info_history.id AND label_sequence_info.name = 'perm_public' AND label_sequence_info.bool_data IS TRUE)";
  
  function Search_model()
  {
    parent::BioModel('label_sequence');
  }
  
  private function __compound_oper($oper)
  {
    return $oper == 'and' || $oper == 'or' || $oper == 'not';
  }

  private function __get_search_labels($term, $label_model, &$ret, $only_public)
  {
    if($term != null) {
      $oper = $term['oper'];

      if($this->__compound_oper($oper)) {
        $operands = $term['operands'];
        foreach($operands as $operand) {
          $this->__get_search_labels($operand, $label_model, $ret, $only_public);
        }
      } else {
        $label = $term['label'];
        if(!array_key_exists($label, $ret)) {
          $label_data = $label_model->get_by_name($label);
          if($label_data['public'] || !$only_public) {
            $ret[$label] = $label_data;
          }
        }
      }
    }
  }

  private function _get_search_labels($term, $label_model, $only_public)
  {
    $ret = array();

    $this->__get_search_labels($term, $label_model, $ret, $only_public);

    return $ret;
  }

  private function __translate_sql_oper($oper, $type)
  {
    switch($type) {
    case 'position':
    case 'integer':
    case 'float':
      return sql_oper($oper);
    case 'text':
    case 'url':
    case 'obj':
      switch($oper) {
      case 'eq': return '=';
      case 'contains':
      case 'starts':
      case 'ends':
        return 'LIKE';
      case 'regexp':
        return 'REGEXP';
      default: return '';
      }
    case 'bool': return 'IS';
    case 'tax': return '=';
    case 'ref': return '=';
    case 'date':
      switch($oper) {
        case 'eq': return '=';
        case 'after': return '>';
        case 'before': return '<';
      }
    }
    
    return null;
  }

  private function __translate_sql_value($oper, $value, $type)
  {
    switch($type) {
    case 'position':
    case 'integer':
      if(!isint($value)) {
        return 0;
      }
      
      return $value;
    case 'float':
      if(!is_numeric($value)) {
        return 0.0;
      }
      
      return $value;
    case 'text':
    case 'url':
    case 'obj':
      switch($oper) {
        case 'regexp':
        case 'eq': break;
        case 'contains':
          $value = "%$value%";
          break;
        case 'starts':
          $value = "$value%";
          break;
        case 'ends':
          $value = "%$value";
          break;
        default:
          return "";
      }
      return $this->db->escape($value);
    case 'bool':
      if($value) {
        return 'TRUE';
      } else {
        return 'FALSE';
      }
    case 'tax':
      if(!is_numeric($value)) {
        return 0;
      }
      
      return $value;
    case 'ref':
      if(!is_numeric($value)) {
        return 0;
      }
      return $value;
    case 'date':
      $newvalue = convert_html_date_to_sql($value);
      if(!$newvalue) {
        return 'NOW()';
      }
      
      return "DATE('$newvalue')";
    }

    return '';
  }
  
  private function __translate_sql_field($field, $type)
  {
    switch($type) {
      case 'date':
        return "DATE($field)";
      case 'obj':
        return $field[0];
      default:
        return $field;
    }
  }

  private function __get_search_where($term, &$labels, $default = "TRUE")
  {
    if($term == null) {
      return $default;
    }

    $oper = $term['oper'];

    if($oper == 'fail') {
      return 'FALSE';
    } elseif($this->__compound_oper($oper)) {
      $operands = $term['operands'];

      if(empty($operands)) {
        return $default;
      }

      if($oper == 'not') {
        $new_default = 'FALSE';

        $operand = $operands[0];

        $part = $this->__get_search_where($operand, $labels, $new_default);

        return "NOT ($part)";
      }

      $ret = "";
      if($oper == 'and') {
        $new_default = 'TRUE';
        $junction = 'AND';
      } else {
        $new_default = 'FALSE';
        $junction = 'OR';
      }

      for($i = 0; $i < count($operands); ++$i) {
        $part = $this->__get_search_where($operands[$i], $labels, $new_default);

        if($i > 0) {
          $ret .= " $junction ($part)";
        } else {
          $ret .= "($part)";
        }
      }

      return $ret;
    } else {
      $label_name = $term['label'];
      $label = $labels[$label_name];
      $label_type = $label['type'];
      $label_id = $label['id'];
      $oper = $term['oper'];
      
      if(!$label) {
        return 'FALSE'; // label not found
      }
      
      if(!is_numeric($label_id)) {
        return 'FALSE'; // invalid label id
      }

      if(array_key_exists('param', $term)) {
        $param = $term['param'];
      } else {
        $param = null;
      }
      
      if($param) {
        $param_sql = " AND `param` = '$param'";
      } else {
        $param_sql = '';
      }

      if(label_special_operator($oper)) {
        if(label_special_purpose($label_name)) {
          switch($label_name) {
            case 'creation_user':
              if($oper == 'exists') {
                return 'creation_user_name IS NOT NULL';
              } else {
                return 'creation_user_name IS NULL';
              }
            case 'update_user':
              if($oper == 'exists') {
                return 'user_name IS NOT NULL';
              } else {
                return 'user_name IS NULL';
              }
            case 'creation_date':
              if($oper == 'exists') {
                return 'creation IS NOT NULL';
              } else {
                return 'creation IS NULL';
              }
            case 'update_date':
              $identifier = $this->db->protect_identifiers('update');
              if($oper == 'exists') {
                return "$identifier IS NOT NULL";
              } else {
                return "$identifier IS NULL";
              }
            default:
              if($oper == 'exists') {
                return 'TRUE';
              } else {
                return 'FALSE';
              }
          }
        }
        
        $sql = "EXISTS (SELECT label_sequence.id FROM label_sequence
          WHERE label_sequence.seq_id = sequence_info_history.id AND label_sequence.label_id = $label_id $param_sql)";

        if($oper == 'notexists') {
          $sql = "NOT $sql";
        }

        return $sql;
      }

      $value = $term['value'];

      if(!label_special_purpose($label_name)) {
        $fields = label_data_fields($label_type);

        // handle position fields
        switch($label_type) {
          case 'position':
            $type = $value['type'];
            if($type == 'start') {
              $fields = 'position_start';
            } else {
              $fields = 'position_length';
            }

            $value = $value['num'];
          
            if(!is_numeric($value)) {
              // invalid data
              return 'FALSE';
            }
            break;
        }
      }

      $sql_oper = $this->__translate_sql_oper($oper, $label_type);
      $sql_value = $this->__translate_sql_value($oper, $value, $label_type);

      // handle special purpose labels
      switch($label_name) {
        case 'name':
          return "name $sql_oper $sql_value";
        case 'content':
          return "content $sql_oper $sql_value";
        case 'creation_user':
          return "creation_user_name $sql_oper $sql_value";
        case 'update_user':
          return "user_name $sql_oper $sql_value";
        case 'creation_date':
          return $this->__translate_sql_field('creation', 'date') . " $sql_oper $sql_value";
        case 'update_date':
          return $this->__translate_sql_field($this->db->protect_identifiers('update'), 'date') . " $sql_oper $sql_value";
      }

      $sql_field = $this->__translate_sql_field($fields, $label_type);
    
      return "EXISTS(SELECT label_sequence.id FROM label_sequence WHERE label_sequence.seq_id = sequence_info_history.id
            AND label_sequence.label_id = $label_id AND $sql_field $sql_oper $sql_value $param_sql)";
    }
  }
  
  // expand search tree cases like taxonomy children and taxonomy and ref seq like operator
  private function __expand_search_tree($term, &$labels)
  {
    if(!$term) {
      return null;
    }
    
    $oper = $term['oper'];
    
    if($this->__compound_oper($oper)) {
      $operands =& $term['operands'];
      $new_operands = array();
      
      foreach($operands as &$operand) {
        $new_operands[] = $this->__expand_search_tree($operand, $labels);
      }
      
      return array('oper' => $oper, 'operands' => $new_operands);
    } else {
      $label_name = $term['label'];
      $label = $labels[$label_name];
      $label_type = $label['type'];
      $oper = $term['oper'];
      
      if(label_special_operator($oper)) {
        return $term;
      }
      
      switch($label_type) {
        case 'ref':
          $val = $term['value'];
          
          if($oper == 'eq') {
            if(!is_numeric($val)) {
              $val = $val['id'];
            }
            if(!is_numeric($val)) {
              return null;
            }
            return array('oper' => $oper, 'label' => $label_name, 'value' => $val);
          } elseif($oper == 'like') {
            $seq_model = $this->load_model('sequence_model');
            $all = $seq_model->get_all(0, 20, array('name' => $value), array(), 'id');
            
            $operands = array();
            
            foreach($all as &$ref) {
              $operands[] = array('label' => $label_name, 'oper' => 'eq', 'value' => $ref['id']);
            }
            
            if(empty($operands)) {
              return array('oper' => 'fail');
            }
            
            return array('oper' => 'oper', 'operands' => &$operands);
          }
          break;
        case 'tax':
          $val = $term['value'];
          $tax_model = $this->load_model('taxonomy_model');
          
          if($oper == 'eq') {
            if(!is_numeric($val)) {
              $val = $val['id'];
            }
            if(!is_numeric($val)) {
              return null;
            }
            $descendants = $tax_model->get_taxonomy_descendants($val, null, 'id');
          } else if($oper == 'like') {
            $all = $tax_model->search($val, null, null, 0, 20, array(), 'id');
            $descendants = array();
            
            // get all descendants
            foreach($all as &$tax) {
              $id = $tax['id'];
              $this_descendants = $tax_model->get_taxonomy_descendants($id, null, 'id');
              $descendants = array_merge($descendants, $this_descendants);
            }
          }
          
          $operands = array();
          
          foreach($descendants as $descendant) {
            $operands[] = array('oper' => 'eq', 'value' => $descendant['id'], 'label' => $label_name);
          }
          
          if(empty($operands)) {
            return array('oper' => 'fail');
          }
          
          return array('oper' => 'or', 'operands' => &$operands);
        default:
          return $term;
      }
    }
  }

  private function __get_search_sql($search, $only_public = false)
  {
    $label_model = $this->load_model('label_model');
    $labels = $this->_get_search_labels($search, $label_model, $only_public);
    $new_search = $this->__expand_search_tree($search, $labels);
    $sql_part = $this->__get_search_where($new_search, $labels);
    if($only_public) {
      return $sql_part . ' AND ' . self::$public_sequence_where;
    } else {
      return $sql_part;
    }
  }
  
  // get total number of sequences with this search tree
  public function get_search_total($search, $transform = null, $only_public = false)
  {
    $sql_where = $this->__get_search_sql($search, $only_public);
    
    if($transform) {
      if($only_public) {
        $public_where = 'WHERE EXISTS(SELECT * FROM label_sequence_info WHERE label_sequence_info.seq_id = label_seqs.ref_data AND label_sequence_info.name = \'perm_public\' AND label_sequence_info.bool_data IS TRUE)';
      } else {
        $public_where = '';
      }
      $sql = "SELECT count(DISTINCT ref_data) AS total
              FROM (SELECT id FROM sequence_info_history WHERE $sql_where) all_seqs
                      NATURAL JOIN (SELECT seq_id AS id, ref_data FROM label_sequence WHERE label_id = $transform AND ref_data IS NOT NULL) label_seqs
              $public_where";
    } else {
      $sql = "SELECT count(id) AS total
              FROM sequence_info_history
              WHERE $sql_where";
    }

    return $this->total_sql($sql);
  }

  public function get_search($search, $coptions = array())
  {
    $default = array('start' => null,
                     'size' => null,
                     'ordering' => array(),
                     'transform' => null,
                     'only_public' => false,
                     'select' => 'id, user_name, update_user_id, `update`, name');

    $options = array_merge($default, $coptions);
    
    $start = $options['start'];
    $size = $options['size'];
    $ordering = $options['ordering'];
    $transform = $options['transform'];
    $only_public = $options['only_public'];
    $select = $options['select'];
    
    $sql_where = $this->__get_search_sql($search, $only_public);
    $sql_limit = sql_limit($start, $size);
    $sql_order = $this->get_order_sql($ordering, 'name', 'asc');
    $select_sql = "DISTINCT $select";
    
    if($transform) {
      $transform_sql = $this->__get_transform_sql($sql_where, $transform, $only_public);
      
      $sql = "SELECT $select_sql
              FROM $transform_sql
              $sql_order $sql_limit";
    } else {
      $sql = "SELECT $select_sql
          FROM sequence_info_history
          WHERE $sql_where $sql_order $sql_limit";
    }

    return $this->rows_sql($sql);
  }
  
  private function __get_transform_sql($sql_where, $transform, $only_public)
  {
    $base = "(SELECT id AS orig_id FROM sequence_info_history WHERE $sql_where) all_seqs
        NATURAL JOIN
         (SELECT seq_id AS orig_id, ref_data AS id FROM label_sequence WHERE label_id = $transform
                                                          AND ref_data IS NOT NULL) label_seqs
        NATURAL JOIN ";
    
    if(!$only_public) {
      return "$base sequence_info_history";
    } else {
      $public_where = 'WHERE ' . self::$public_sequence_where;
      return "$base (SELECT * FROM sequence_info_history $public_where) every_seqs";
    }
  }
  
  private function __get_base_search_sql($search, $transform, $only_public)
  {
    $sql_where = $this->__get_search_sql($search, $only_public);
    
    if($transform) {
      $transform_sql = $this->__get_transform_sql($sql_where, $transform, $only_public);
      
      $base_sql = "SELECT id AS seq_id
                   FROM $transform_sql";
    } else {
      $base_sql = "SELECT id AS seq_id
             FROM sequence_info_history
             WHERE $sql_where";
    }
    
    return $base_sql;
  }
  
  public function get_numeral_search_distribution($search, $label_id, $coptions = array())
  {
    $default = array('transform' => null,
                     'distr' => 'avg',
                     'label_type' => null,
                     'only_public' => false,
                     'param' => null);
                     
    $options = array_merge($default, $coptions);
    $transform = $options['transform'];
    $distr = $options['distr'];
    $label_type = $options['label_type'];
    $only_public = $options['only_public'];
    $param = $options['param'];
    
    $base_sql = $this->__get_base_search_sql($search, $transform, $only_public);
    
    $field = label_data_fields($label_type);
    
    switch($distr) {
      case 'min': $sql_distr = 'MIN'; break;
      case 'max': $sql_distr = 'MAX'; break;
      case 'avg': $sql_distr = 'AVG'; break;
    }
    $sql_distr = "$sql_distr($field)";
    
    if($param) {
      $param_sql = "AND param ='$param'";
    } else {
      $param_sql = '';
    }
    
    $sql =
      "SELECT distr, COUNT(distr) AS total
       FROM (SELECT seq_id, $sql_distr AS distr
             FROM ($base_sql) seqs
                  NATURAL JOIN
                  (SELECT seq_id, $field FROM label_sequence
                   WHERE label_id = $label_id $param_sql) labels
              GROUP BY seq_id) distr_table
       GROUP BY distr
       ORDER BY distr ASC";
    
    return $this->rows_sql($sql);
  }
  
  public function get_other_search_distribution($search, $label_id, $coptions = array())
  {
    $label_model = $this->load_model('label_model');
    $label = $label_model->get($label_id);
    
    if(label_special_purpose($label['name'])) {
      return $this->get_special_purpose_search_distribution($search, $label['name'], $coptions);
    }
    
    $default = array('transform' => null,
                     'label_type' => null,
                     'only_public' => false,
                     'param' => null);
                     
    $options = array_merge($default, $coptions);
    $transform = $options['transform'];
    $label_type = $options['label_type'];
    $only_public = $options['only_public'];
    $param = $options['param'];
    
    $base_sql = $this->__get_base_search_sql($search, $transform, $only_public);
    
    $field = label_data_fields($label_type);
    
    if(is_array($field)) {
      $field = $field[0];
    }
    
    switch($label_type) {
      case 'position':
        $field = "CONCAT(position_start, ' ', position_length)";
        break;
      case 'ref':
        $field = 'sequence_name';
        break;
      case 'tax':
        $field = 'taxonomy_name';
        break;
    }  
    
    if($param) {
      $param_sql = "AND param ='$param'";
    } else {
      $param_sql = '';
    }
    
    $sql =
      "SELECT distr, COUNT(distr) AS total
       FROM ($base_sql) seqs
          NATURAL JOIN
        (SELECT seq_id, $field AS distr FROM label_sequence_extra
         WHERE label_id = $label_id $param_sql) labels
       GROUP BY distr
       ORDER BY distr ASC";
    
    return $this->rows_sql($sql);
  }
  
  private function get_special_purpose_search_distribution($search, $name, $coptions = array())
  {
    $default = array('transform' => null,
                     'only_public' => false);
                     
    $options = array_merge($default, $coptions);
    $transform = $options['transform'];
    $only_public = $options['only_public'];
    
    $base_sql = $this->__get_base_search_sql($search, $transform, $only_public);
    
    switch($name) {
      case 'name':
      case 'content':
        $sql = "SELECT $name AS distr, count(seq_id) AS total
                FROM ($base_sql) seqs
                     NATURAL JOIN
                   (SELECT id AS seq_id, $name FROM sequence) allseqs
                GROUP BY $name
                ORDER BY $name ASC";
        break;
      case 'creation_user':
      case 'update_user':
        if($name == 'creation_user')
          $field = 'creation_user_name';
        else
          $field = 'user_name';
        
        $sql = "SELECT $field AS distr, count(seq_id) AS total
                FROM ($base_sql) seqs
                      NATURAL JOIN
                      (SELECT id AS seq_id, $field FROM sequence_info_history) allseqs
                GROUP BY $field
                ORDER BY $field ASC";
        break;
      case 'creation_date':
      case 'update_date':
        if($name == 'creation_date')
          $field = 'creation';
        else
          $field = '`update`';
          
        $sql = "SELECT distr, count(seq_id) AS total
                FROM ($base_sql) seqs
                     NATURAL JOIN
                     (SELECT id AS seq_id, DATE_FORMAT($field, \"%d-%m-%Y\") AS distr FROM sequence_info_history) allseqs
                GROUP BY distr
                ORDER BY distr ASC";
        break;
    }
    
    return $this->rows_sql($sql);
  }
}