<?php

namespace App\Models;

use CodeIgniter\Model;

class MslUser extends Model{
    protected $table = "all_users";
    protected $primaryKey = 'id';
    protected $allowedFields = ['usr_id','name','email','job_title'];
}


