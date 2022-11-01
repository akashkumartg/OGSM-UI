<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model{
    protected $table = "user";
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','email','job_title','fcm_token','created_date'];
}