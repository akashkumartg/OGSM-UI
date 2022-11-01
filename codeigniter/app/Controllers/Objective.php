<?php

namespace App\Controllers;

use App\Models\Objectives;

date_default_timezone_set("Asia/Kolkata");

class Objective extends BaseController
{
    public function add()
    {
        $rules = [
            'user_id' => "required",
            'objective_title' => "required",
            'objective' => "required",
            'created_by' => "required"
        ];

        if (!$this->validate($rules)) {
            $response = [
                'status' => 'ERROR',
                'data' => [],
                'msg' => array_values($this->validator->getErrors())[0]
            ];
            return $this->response->setJSON($response);
        }

        $data = [
            'user_id' => $this->request->getVar('user_id'),
            'objective_title' => $this->request->getVar('objective_title'),
            'objective' => $this->request->getVar('objective'),
            'created_by' => $this->request->getVar('created_by'),
            'created_date' => date('Y-m-d H:i:s')
        ];

        $objectives = new Objectives();
        $inst = $objectives->insert($data);
        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Objective Added Successfully!'
            ];
        } else {
            $response = [
                'status' => 'ERROR',
                'data' => [],
                'msg' => 'Something Went Wrong!'
            ];
        }
        return $this->response->setJSON($response);
    }

    public function get(){
        $object = new Objectives();
        $objectives = $object
        ->orderBy('created_date', 'ASEC')
        ->findAll();

        $response = [
            'status' => 'OK',
            'data' => $objectives,
            'msg' => 'Objective Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }

    public function edit(){
        $rules = [
            'id' => "required",
        ];

        if (!$this->validate($rules)) {
            $response = [
                'status' => 'ERROR',
                'data' => [],
                'msg' => array_values($this->validator->getErrors())[0]
            ];
            return $this->response->setJSON($response);
        }

        $object = new Objectives();
        $inst = $object
            ->where('id', $this->request->getVar('id'))
            ->first();

        $response = [
            'status' => 'OK',
            'data' => $inst,
            'msg' => 'Objective Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }

    public function update()
    {
        $rules = [
            'id'=>"required",
            'user_id' => "required",
            'objective_title' => "required",
            'objective' => "required"
        ];

        if (!$this->validate($rules)) {
            $response = [
                'status' => 'ERROR',
                'data' => [],
                'msg' => array_values($this->validator->getErrors())[0]
            ];
            return $this->response->setJSON($response);
        }

        $data = [
            'user_id' => $this->request->getVar('user_id'),
            'objective_title' => $this->request->getVar('objective_title'),
            'objective' => $this->request->getVar('objective'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $objectives = new Objectives();
        $inst = $objectives
            ->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Objective updated Successfully!'
            ];
        } else {
            $response = [
                'status' => 'ERROR',
                'data' => [],
                'msg' => 'Something Went Wrong!'
            ];
        }
        return $this->response->setJSON($response);
    }

    public function delete()
    {
        $rules = [
            'id' => "required",
        ];

        if (!$this->validate($rules)) {
            $response = [
                'status' => 'ERROR',
                'data' => [],
                'msg' => array_values($this->validator->getErrors())[0]
            ];
            return $this->response->setJSON($response);
        }

        $objectives = new Objectives();
        $inst = $objectives
            ->where('id', $this->request->getVar('id'))
            ->delete();

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => [],
                'msg' => 'Objective Deleted Successfully!'
            ];
        } else {
            $response = [
                'status' => 'ERROR',
                'data' => [],
                'msg' => 'Something Went Wrong!'
            ];
        }
        return $this->response->setJSON($response);
    }
}
