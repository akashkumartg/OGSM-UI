<?php

namespace App\Controllers;

use App\Models\allUsers;
use App\Models\Users;

date_default_timezone_set("Asia/Kolkata");

class User extends BaseController
{
    public function get()
    {
        $users = new Users();
        $user = $users->findAll();
        $response = [
            'status' => 'OK',
            'data' => $user,
            'msg' => 'Data Inserted Successfully!'
        ];
        return $this->response->setJSON($response);
    }

    public function AddAllUsers()
    {
        foreach ($this->request->getVar() as $key => $value) {
            $allData[] = array(
                'usr_id' => $value->usr_id,
                'name' => $value->name,
                'email' => $value->email,
                'mobile_number'=> $value->mobile_number,
                'job_title' => $value->job_title,
            );
        }

        if ($allData) {
            $alluser = new allUsers();
            $alluser->insertBatch($allData);
        }
    }

    public function add()
    {
        $rules = [
            'name' => "required",
            'email' => "required",
            'job_title' => "required",
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
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'job_title' => $this->request->getVar('job_title'),
            'created_date' => date('Y-m-d H:i:s')
        ];

        $user = new Users();
        $getUser = $user
            ->where('email', $this->request->getVar('email'))
            ->first();
        if (!$getUser) {
            $inst = $user->insert($data);
            if ($inst) {
                $response = [
                    'status' => 'OK',
                    'data' => $inst,
                    'msg' => 'Welcome To OGSM!'
                ];
            } else {
                $response = [
                    'status' => 'ERROR',
                    'data' => [],
                    'msg' => 'Something Went Wrong!'
                ];
            }
            return $this->response->setJSON($response);
        } else {
            $response = [
                'status' => 'OK',
                'data' => $getUser['id'],
                'msg' => 'Welcome Back!'
            ];
            return $this->response->setJSON($response);
        }
    }

    public function update()
    {
        $rules = [
            'id' => "required",
            'fcm_token' => "required"

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
            'id' => $this->request->getVar('id'),
            'fcm_token' => $this->request->getVar('fcm_token'),
        ];

        $user = new Users();
        $inst = $user->update($this->request->getVar('id'), $data);
        // print_r($inst);die;

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Token updated Successfully!'
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
