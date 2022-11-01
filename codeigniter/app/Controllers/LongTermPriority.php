<?php

namespace App\Controllers;

use App\Models\Common;
use App\Models\LongTermPriorities;
use App\Models\Strategies;
use App\Models\Users;

date_default_timezone_set("Asia/Kolkata");

class LongTermPriority extends BaseController
{
    public function strategieDropdown()
    {
        $strg = new Strategies();
        $dropDown = $strg
        ->where('is_approved',1)
        ->where('is_confidential',0)
        ->findAll();
        $response = [
            'status' => 'OK',
            'data' => $dropDown,
            'msg' => 'Data Fetched Successfully!'
        ];
        return $this->response->setJSON($response);
    }

    public function add()
    {
        $rules = [
            'user_id' => "required",
            'strategie_id' => ['label' => 'Strategy', 'rules' => 'required'],
            'ltp_title' => "required",
            'ltp' => "required",
            'person_responsible' => "required",
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
            'strategie_id' => $this->request->getVar('strategie_id'),
            'ltp_title' => $this->request->getVar('ltp_title'),
            'ltp' => $this->request->getVar('ltp'),
            'person_responsible' => $this->request->getVar('person_responsible'),
            'created_by' => $this->request->getVar('created_by'),
            'created_date' => date('Y-m-d H:i:s')
        ];

        $Common = new Common();
        $user = new Users();
        $ltp = new LongTermPriorities();
        $inst = $ltp->insert($data);
        $userFcmToken = $user->where('id', $this->request->getVar('user_id'))->first();
        if ($inst) {
            if ($userFcmToken['fcm_token']) {
                $notifData = array(
                    'title' => 'Long Term Priority',
                    'body' => 'LTP Created By ' . $userFcmToken['name'],
                    "click_action" => 'http://localhost/ogsm/index.html?page=ltp'
                );
                $Common->sendNotification($notifData, $userFcmToken['fcm_token']);
            }

            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'LTP Added Successfully!'
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

    public function get()
    {
        $Strategie = new Strategies();
        $ltp = new LongTermPriorities();
        $get = $ltp
            ->select('strategies.* ,long_term_priority.*,all_users.email,all_users.name,all_users.job_title')
            ->join('strategies', 'long_term_priority.strategie_id = strategies.id', 'left')
            ->join('all_users', 'long_term_priority.person_responsible = all_users.email', 'left')
            ->orderBy('long_term_priority.created_date', 'ASEC')
            ->findAll();

        $response = [
            'status' => 'OK',
            'data' => $get,
            'msg' => 'LTP Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }

    public function edit()
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

        $ltp = new LongTermPriorities();
        $inst = $ltp
            ->select('strategies.* ,long_term_priority.*,all_users.email,all_users.name,all_users.job_title')
            ->join('strategies', 'long_term_priority.strategie_id = strategies.id', 'left')
            ->join('all_users', 'long_term_priority.person_responsible = all_users.email', 'left')
            ->where('long_term_priority.id', $this->request->getVar('id'))
            ->first();

        $response = [
            'status' => 'OK',
            'data' => $inst,
            'msg' => 'LTP Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }

    public function update()
    {
        $rules = [
            'id' => "required",
            'user_id' => "required",
            'strategie_id' => ['label' => 'Strategy', 'rules' => 'required'],
            'ltp_title' => "required",
            'ltp' => "required",
            'person_responsible' => "required",
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
            'user_id' => $this->request->getVar('user_id'),
            'strategie_id' => $this->request->getVar('strategie_id'),
            'ltp_title' => $this->request->getVar('ltp_title'),
            'ltp' => $this->request->getVar('ltp'),
            'person_responsible' => $this->request->getVar('person_responsible'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $ltp = new LongTermPriorities();
        $inst = $ltp->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'LTP updated Successfully!'
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

        $ltp = new LongTermPriorities();
        $inst = $ltp
            ->where('id', $this->request->getVar('id'))
            ->delete();

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => [],
                'msg' => 'LTP Deleted Successfully!'
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

    public function approved()
    {
        $rules = [
            'id' => "required",
            'user_id' => "required",
            'strategie_id' => "required",
            'is_approved' => "required",
            'approved_by' => "required",
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
            'user_id' => $this->request->getVar('user_id'),
            'strategie_id' => $this->request->getVar('strategie_id'),
            'is_approved' => $this->request->getVar('is_approved'),
            'approved_by' => $this->request->getVar('approved_by'),
            'approved_date' => date('Y-m-d H:i:s')
        ];

        $ltp = new LongTermPriorities();
        $inst = $ltp
            ->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'LTP Approved Successfully!'
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

    public function rejected()
    {
        $rules = [
            'id' => "required",
            'user_id' => "required",
            'strategie_id' => "required",
            'is_approved' => "required",
            'approved_by' => "required",
            'reject_reason' => "required",
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
            'user_id' => $this->request->getVar('user_id'),
            'strategie_id' => $this->request->getVar('strategie_id'),
            'is_approved' => $this->request->getVar('is_approved'),
            'approved_by' => $this->request->getVar('approved_by'),
            'reject_reason' => $this->request->getVar('reject_reason'),
            'approved_date' => date('Y-m-d H:i:s')
        ];

        $ltp = new LongTermPriorities();
        $inst = $ltp
            ->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'LTP Rejected Successfully!'
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
