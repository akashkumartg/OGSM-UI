<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyAnnualPriorities extends Model
{
    protected $table = "company_annual_priority";
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'ltp_id', 'cap_title', 'cap', 'created_by', 'created_date', 'updated_date', 'is_approved', 'approved_by', 'reject_reason', 'notificationCode', 'approved_date'];
}
