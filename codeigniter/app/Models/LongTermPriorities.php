<?php

namespace App\Models;

use CodeIgniter\Model;

class LongTermPriorities extends Model{
    protected $table = "long_term_priority";
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','strategie_id','ltp_title','ltp','person_responsible','created_by','created_date','updated_date','is_approved','approved_by','reject_reason','approved_date'];
}


