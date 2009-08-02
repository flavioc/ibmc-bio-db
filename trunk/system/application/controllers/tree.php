<?php

class Tree extends BioController {
  
  function Tree() {
    parent::BioController();
    $this->load->model('taxonomy_tree_model');
  }

  function index() {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Tree list');
    $this->use_mygrid();
    $this->smarty->load_scripts(VALIDATE_SCRIPT);
    $this->load->model('user_model');
    $this->smarty->assign('users', $this->user_model->get_users_all());

    $this->smarty->view('tree/list');
  }

  function get_all() {
    if(!$this->logged_in) {
      return $this->invalid_permission_empty();
    }

    $order_name = $this->get_order('name');
    $order_update = $this->get_order('update');
    $order_user = $this->get_order('user_name');

    $filter_name = $this->get_parameter('name');
    $filter_user = $this->get_parameter('user');

    $trees = $this->taxonomy_tree_model->get_trees(
      array('name' => $filter_name,
            'user' => $filter_user),
      array('name' => $order_name,
            'update' => $order_update,
            'user_name' => $order_user));

    $this->json_return($trees);
  }

  function add() {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $this->smarty->assign('title', 'Add tree');

    $this->smarty->load_scripts(VALIDATE_SCRIPT);

    $this->smarty->fetch_form_row('name');

    $this->smarty->view('tree/add');
  }

  function do_add()
  {
    $errors = false;

    $this->load->library('form_validation');

    // define form rules and validate all form fields
    $this->form_validation->set_rules('name', 'Name', 'trim|min_length[2]|max_length[512]');

    if($this->form_validation->run() == FALSE) {
      $errors = true;
    }

    $name = $this->get_post('name');

    if(!$errors) {
      if($this->taxonomy_tree_model->has_name($name)) {
        $this->set_form_error('name', "Name $name is already being used.");
        $errors = true;
      }
    }

    if($errors) {
      $this->assign_row_data('name');

      redirect('tree/add');
    } else {
      $id = $this->taxonomy_tree_model->add($name);

      redirect("tree/view/$id");
    }
  }

  function view($id)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    if(!$this->taxonomy_tree_model->has_tree($id)) {
      $this->smarty->assign('id', $id);
      $this->smarty->assign('title', 'Tree not found');
      $this->smarty->view('tree/not_found');
      return;
    }

    $this->smarty->load_scripts(JEDITABLE_SCRIPT);
    $this->use_impromptu();

    $tree = $this->taxonomy_tree_model->get($id);
    $this->smarty->assign('tree', $tree);

    $this->smarty->assign('title', 'Tree "' . $tree['name'] . '"');
    $this->smarty->view('tree/view');
  }

  function delete($id) {
    if(!$this->logged_in) {
      return $this->invalid_permission_false();
    }

    $this->taxonomy_tree_model->delete_id($id);

    $this->json_return(true);
  }

  function edit_name() {
    if(!$this->logged_in) {
      return $this->invalid_permission_field();
    }

    $id = $this->get_post('tree');
    $value = $this->get_post('value');

    $result = $this->taxonomy_tree_model->edit($id, $value);

    echo $this->taxonomy_tree_model->get_name($id);
  }

  function delete_dialog($id)
  {
    if(!$this->logged_in ||
      !$this->taxonomy_tree_model->has_tree($id))
    {
      return $this->invalid_permission_nothing();
    }

    $this->load->model('taxonomy_model');
    $total = $this->taxonomy_model->count_tree($id);

    $this->smarty->assign('total', $total);

    $tree = $this->taxonomy_tree_model->get_name($id);
    $this->smarty->assign('tree', $tree);

    $this->smarty->view_s('tree/delete');
  }

  function delete_redirect()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }

    $id = $this->get_post('id');
    $this->taxonomy_tree_model->delete_id($id);

    redirect('tree');
  }
  
  function export($id = null)
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    if(!$id) {
      $id = $this->get_post('id');
    }
    
    $this->load->helper('tree_exporter');
    $this->load->model('taxonomy_model');
    
    header('Content-type: text/plain');
    header("Content-Disposition: attachment; filename=\"tree.xml\"");
    
    echo export_tree_xml($this->taxonomy_tree_model,
                         $this->taxonomy_model,
                         $id);
  }
  
  function import()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $this->smarty->fetch_form_row('file');
    $this->smarty->assign('title', 'Import tree from file');
    $this->smarty->view('tree/import');
  }
  
  function do_import()
  {
    if(!$this->logged_in) {
      return $this->invalid_permission();
    }
    
    $this->load->library('upload', $this->__get_xml_upload_config());

    $upload_ret = $this->upload->do_upload('file');
    
    if($upload_ret) {
      $data = $this->upload->data();
      $file = $data['full_path'];
      
      $this->load->helper('tree_importer');
      $this->load->model('taxonomy_model');
      $this->load->model('taxonomy_rank_model');
      
      $ret = import_tree_xml_file($this->taxonomy_tree_model, $this->taxonomy_rank_model, $this->taxonomy_model, $file);
      
      if(!$ret) {
        $this->set_form_error('file', 'Error reading the XML file');
        redirect('tree/import');
      } else {
        $this->smarty->assign('stats', $ret);
        $this->smarty->view('tree/do_import');
      }
    } else {
      $this->set_upload_form_error('file');
      redirect('tree/import');
    }
  }
}