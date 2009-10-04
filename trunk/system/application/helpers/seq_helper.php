<?php

function is_dna_letter($letter)
{
  $dna_letters = array('A', 'G', 'T', 'C');

  return in_array($letter, $dna_letters);
}

function is_protein_letter($letter)
{
  $protein_letters = array('A', 'B', 'C', 'D', 'E', 'F',
    'G', 'H', 'I', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S',
    'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

  return in_array($letter, $protein_letters);
}

function is_skip_letter($letter)
{
  $skip_letters = array('-', '*');

  return in_array($letter, $skip_letters);
}

function sequence_type($seq)
{
  $type = null;

  $array = str_split(strtoupper($seq));

  foreach($array as $char) {
    if(is_skip_letter($char)) {
      continue;
    }

    if($type == null) {
      if(is_dna_letter($char)) {
      } else if(is_protein_letter($char)) {
        $type = 'protein';
      } else {
        return 'unknown';
      }
    } else if($type == 'protein') {
      if(!is_protein_letter($char)) {
        return 'unknown';
      }
    }
  }

  if(!$type) {
    $type = 'dna';
  }

  return $type;
}

function valid_sequence_type($type)
{
  return $type == 'dna' || $type == 'protein';
}

define('SEQUENCE_SPACING', 40);

function sequence_split($content, $separator = "\n")
{
  return split_string($content, SEQUENCE_SPACING, $separator);
}

function sequence_join($content)
{
  $vec = explode("\n", $content);

  $ret = "";

  foreach($vec as $el) {
    $ret .= $el;
  }

  return sequence_normalize($ret);
}

function sequence_normalize($content)
{
  $content = trim($content);
  $content = str_replace(' ', '', $content);
  $content = str_replace("\t", '', $content);
  $content = str_replace("\n", '', $content);
  
  return strtoupper($content);
}

function sequence_short_content($content)
{
  return substr($content, 0, SEQUENCE_SPACING);
}

function search_tree_to_html($term)
{
  $oper = $term['oper'];

  if($oper == 'and' || $oper == 'or') {
    $operands = $term['operands'];
    $ret = "<li>$oper<ol>";

    foreach($operands as $operand) {
      $este = search_tree_to_html($operand);
      $ret .= "$este";
    }
    return "$ret</ol></li>";
  } else {
    return '<li>' . humanize_search_terminal($term) . '</li>';
  }
}

function humanize_search_terminal($term)
{
  if(!$term) {
    return '-';
  }
  
  $label_name = $term['label'];
  $label_type = $term['type'];
  $oper = $term['oper'];
  
  if(array_key_exists('param', $term)) {
    $param = $term['param'];
  } else {
    $param = null;
  }
  
  $full_label_name = $label_name;
  
  if($param) {
    $full_label_name = $full_label_name.'['.$param.']';
  }

  if(label_special_operator($oper)) {
    if($oper == 'exists') {
      return "$full_label_name $oper";
    } else {
      return "$full_label_name not exists";
    }
  }

  $value = $term['value'];
  $ret = null;
  
  switch($label_type) {
    case 'integer':
    case 'float':
      $oper = sql_oper($oper);
      $ret = "$full_label_name $oper $value";
      break;
    case 'position':
      $oper = sql_oper($oper);
      $type = $value['type'];
      $num = $value['num'];
      $ret = "$full_label_name $type $oper $num";
      break;
    case 'text':
    case 'url':
    case 'obj':
      $ret = "$full_label_name $oper \"$value\"";
      break;
    case 'bool':
      if($value) {
        $ret = "$full_label_name is true";
      } else {
        $ret = "$full_label_name is false";
      }
      break;
    case 'tax':
    case 'ref':
      if($oper == 'eq') {
        $name = $value['name'];
        $ret = "$full_label_name = $name";
      } elseif($oper == 'like') {
        $ret = "$full_label_name like $value";
      }
      break;
    case 'date':
      $ret = "$full_label_name $oper $value";
      break;
  }

  return $ret;
}

function prefix_search_tree_to_string($term)
{
  $oper = $term['oper'];

  if(label_compound_oper($oper)) {
    $operands = $term['operands'];
    $ret = "($oper ";

    foreach($operands as $operand) {
      $este = prefix_search_tree_to_string($operand);
      $ret .= " $este";
    }
    return "$ret)";
  } else {
    $ret = humanize_search_terminal($term);
    return "($ret)";
  }
}

function compound_term($term)
{
  return label_compound_oper($term['oper']);
}

function search_tree_to_string($term, $start_compound = null, $end_compound = null)
{
  if(!$term) {
    return 'Empty query';
  }
  
  $oper = $term['oper'];

  if(label_compound_oper($oper)) {
    $operands = $term['operands'];
    $total = count($operands);

    if($oper == 'or') {
      $oper_str = 'OR';
    } elseif ($oper == 'and') {
      $oper_str = 'AND';
    } else {
      $oper_str = 'NOT';
    }
    
    if($start_compound) {
      $oper_str = "$start_compound$oper_str";
    }
    
    if($end_compound) {
      $oper_str = "$oper_str$end_compound";
    }

    if($total == 0) {
      return "Empty query";
    }

    if($oper == 'not') {
      $operand = $operands[0];
      $operand_str = search_tree_to_string($operand, $start_compound, $end_compound);
      if(compound_term($operand)) {
        return "NOT ($operand_str)";
      } else {
        return "NOT $operand_str";
      }
    }

    $ret = "";

    for($i = 0; $i < $total; ++$i) {
      $operand = $operands[$i];
      $operand_str = search_tree_to_string($operand, $start_compound, $end_compound);

      if($i == 0) {
        if($total == 1) {
          return "$operand_str";
        } else {
          if(compound_term($operand)) {
            $ret = "($operand_str)";
          } else {
            $ret = "$operand_str";
          }
        }
      } else {
        if(compound_term($operand)) {
          $ret = "$ret $oper_str ($operand_str)";
        } else {
          $ret = "$ret $oper_str $operand_str";
        }
      }
    }

    return $ret;
  } else {
    return humanize_search_terminal($term);
  }
}

function run_util_over_sequence($sequence, $command)
{
  $CI =& get_instance();
  $CI->load->library('SequenceExporter');
  
  $sequence = array('name' => 'SEQ', 'content' => $sequence);
  $fasta = $CI->sequenceexporter->write_sequences_to_fasta(array($sequence));
  $output_file = generate_new_file_name();
  
  $command .= " $fasta -outfile $output_file";
  exec($command, $cmdoutput, $ret);
  unlink($fasta);
  
  if($ret) {
    unlink($output_file);
    throw new Exception("command failed: $command => $ret");
  }
  
  $output = file_get_contents($output_file);
  unlink($output_file);
  
  return $output;
}

function build_sub_sequence_name($name, $start, $length)
{
  return 'sub_'.$name.'_'.$start.'_'.$length;
}