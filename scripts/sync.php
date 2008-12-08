<?php

$system_folder = "../system";
require_once('ci_model_remote_open.php');
require_once(APPPATH . "/models/taxonomy_rank_model.php");

$CI->load->model('taxonomy_rank_model');

print_r($CI->taxonomy_rank_model->get_ranks());

for($i = 0; $i < 1000; ++$i) {
  print("\r\bola$i");
  sleep(1);
}

require_once('ci_model_remote_close.php');

?> 
