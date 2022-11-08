<?php

namespace App\Models;

use CodeIgniter\Model;

class JCPriorities extends Model
{
    protected $table = "jc_priority";
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'cap_id', 'jcp_no', 'jcp_title', 'jcp', 'target_date', 'person_responsible', 'remind_employee_on', 'remind_is_on', 'status_by_emp', 'comments_by_is', 'result', 'created_date', 'created_by', 'updated_date', 'is_approved', 'approved_by', 'notificationCode', 'approved_date'];
}
