<?php

namespace App\Models;

use CodeIgniter\Model;

class Goals extends Model
{
    protected $table = "goals";

    protected $primaryKey = 'id';

    protected $allowedFields = ['user_id', 'objective_id', 'goal_title', 'goals', 'unit', 'unit_of_measure', 'target_date_for_achieving', 'person_responsible', 'target_date_submission', 'created_by', 'created_date', 'updated_date', 'is_approved', 'approved_by', 'notificationCode', 'approved_date'];
}
