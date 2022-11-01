<?php

namespace App\Models;

use CodeIgniter\Model;

class allUsers extends Model{
    protected $table = "all_users";
    protected $primaryKey = 'id';
    protected $allowedFields = ['usr_id','name','email','mobile_number','job_title'];
}