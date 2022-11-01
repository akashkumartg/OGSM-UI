<?php

namespace App\Models;

use CodeIgniter\Model;

class uom extends Model{
    protected $table = "uom";
    protected $primaryKey = 'id';
    protected $allowedFields = ['unit','measurement_unit_text'];
}


