<?php

namespace App\Models;

use CodeIgniter\Model;

class Strategies extends Model{
    protected $table = "strategies";
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','goal_id','strategie_title','strategie','created_by','created_date','updated_date','is_confidential','is_approved','approved_by','reject_reason','approved_date'];
}


