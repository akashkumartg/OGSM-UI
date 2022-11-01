<?php

namespace App\Controllers;

use App\Models\Common;
use App\Models\CompanyAnnualPriorities;
use App\Models\JCPriorities;
use App\Models\MslUser;
use App\Models\Users;

date_default_timezone_set("Asia/Kolkata");

class JCPriority extends BaseController
{
    public function capDropdown()
    {
        $cap = new CompanyAnnualPriorities();
        $dropDown = $cap
            ->where('is_approved', 1)
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
            'cap_id' => ['label' => 'CAP', 'rules' => 'required'],
            'jcp_title' => "required",
            'jcp' => "required",
            'target_date' => "required",
            'person_responsible' => "required",
            'remind_employee_on' => "required",
            'remind_is_on' => "required",
            'status_by_emp' => "required",
            'result' => "required",
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
            'cap_id' => $this->request->getVar('cap_id'),
            'jcp_title' => $this->request->getVar('jcp_title'),
            'jcp' => $this->request->getVar('jcp'),
            'target_date' => $this->request->getVar('target_date'),
            'person_responsible' => $this->request->getVar('person_responsible'),
            'remind_employee_on' => $this->request->getVar('remind_employee_on'),
            'remind_is_on' => $this->request->getVar('remind_is_on'),
            'status_by_emp' => $this->request->getVar('status_by_emp'),
            'result' => $this->request->getVar('result'),
            'created_by' => $this->request->getVar('created_by'),
            'created_date' => date('Y-m-d H:i:s')
        ];

        $user = new Users();
        $jcp = new JCPriorities();
        $Common = new Common();
        $inst = $jcp->insert($data);
        $jcpNo = 'JCP' . $inst;
        $jcp->update($inst, array('jcp_no' => $jcpNo));
        $userFcmToken = $user->where('id', $this->request->getVar('user_id'))->first();
        if ($inst) {
            if ($userFcmToken['fcm_token']) {
                $notifData = array(
                    'title' => 'JC Priority',
                    'body' => 'JCP Created By ' . $userFcmToken['name'],
                    "click_action" => 'http://localhost/ogsm/index.html?page=jcp'
                );
                $Common->sendNotification($notifData, $userFcmToken['fcm_token']);
            }

            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'JCP Added Successfully!'
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

        $jcp = new JCPriorities();
        $get = $jcp
            ->select('company_annual_priority.*,jc_priority.*,all_users.name,all_users.email,all_users.job_title')
            ->join('company_annual_priority', 'jc_priority.cap_id = company_annual_priority.id', 'left')
            ->join('all_users', 'jc_priority.person_responsible = all_users.email', 'left')
            ->orderBy('jc_priority.created_date', 'ASEC')
            ->findAll();

        $response = [
            'status' => 'OK',
            'data' => $get,
            'msg' => 'JCP Data Fetched Successfully!'
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

        $jcp = new JCPriorities();
        $inst = $jcp
            ->select('company_annual_priority.*,jc_priority.*,all_users.name,all_users.email,all_users.job_title')
            ->join('company_annual_priority', 'jc_priority.cap_id = company_annual_priority.id', 'left')
            ->join('all_users', 'jc_priority.person_responsible = all_users.email', 'left')
            ->where('jc_priority.id', $this->request->getVar('id'))
            ->first();

        $response = [
            'status' => 'OK',
            'data' => $inst,
            'msg' => 'JCP Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }

    public function update()
    {
        $rules = [
            'user_id' => "required",
            'cap_id' => ['label' => 'CAP', 'rules' => 'required'],
            'jcp_title' => "required",
            'jcp' => "required",
            'target_date' => "required",
            'person_responsible' => "required",
            'remind_employee_on' => "required",
            'remind_is_on' => "required",
            'status_by_emp' => "required",
            'result' => "required"
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
            'cap_id' => $this->request->getVar('cap_id'),
            'jcp_title' => $this->request->getVar('jcp_title'),
            'jcp' => $this->request->getVar('jcp'),
            'target_date' => $this->request->getVar('target_date'),
            'person_responsible' => $this->request->getVar('person_responsible'),
            'remind_employee_on' => $this->request->getVar('remind_employee_on'),
            'remind_is_on' => $this->request->getVar('remind_is_on'),
            'status_by_emp' => $this->request->getVar('status_by_emp'),
            'result' => $this->request->getVar('result'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $jcp = new JCPriorities();
        $inst = $jcp->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'JCP updated Successfully!'
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

        $jcp = new JCPriorities();
        $inst = $jcp
            ->where('id', $this->request->getVar('id'))
            ->delete();

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => [],
                'msg' => 'JCP Deleted Successfully!'
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
            'cap_id' => "required",
            'comments_by_is' => "required",
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
            'cap_id' => $this->request->getVar('cap_id'),
            'comments_by_is' => $this->request->getVar('comments_by_is'),
            'is_approved' => $this->request->getVar('is_approved'),
            'approved_by' => $this->request->getVar('approved_by'),
            'approved_date' => date('Y-m-d H:i:s')
        ];

        $jcp = new JCPriorities();
        $inst = $jcp
            ->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'JCP Approved Successfully!'
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
