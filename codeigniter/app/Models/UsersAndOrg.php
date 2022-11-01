<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersAndOrg extends Model{
    protected $table = "users_organization";
    protected $primaryKey = 'id';

    protected $allowedFields = ['report_manager_user_id','report_manager_name','report_manager_email','report_manager_roll','user_id','user_name','user_email','user_roll'];
}


