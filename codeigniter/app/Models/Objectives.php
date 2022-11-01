<?php

namespace App\Models;

use CodeIgniter\Model;

class Objectives extends Model{
    protected $table = "objectives";
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','objective_title','objective','created_by','created_date','updated_date'];
}


