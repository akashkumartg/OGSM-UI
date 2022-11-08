<?php

namespace App\Controllers;

use App\Models\Common;
use App\Models\CompanyAnnualPriorities;
use App\Models\LongTermPriorities;
use App\Models\Users;
use App\Models\UsersAndOrg;

date_default_timezone_set("Asia/Kolkata");

class CompanyAnnualPriority extends BaseController
{
    public function ltpDropdown()
    {
        $ltp = new LongTermPriorities();
        $dropDown = $ltp
            ->where('person_responsible', $this->request->getVar('mail'))
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
            'ltp_id' => ['label' => 'LTP', 'rules' => 'required'],
            'cap_title' => "required",
            'cap' => "required",
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

        //$lastNo =  get lst row here - 2

        $data = [
            'user_id' => $this->request->getVar('user_id'),
            'ltp_id' => $this->request->getVar('ltp_id'),
            'cap_title' => $this->request->getVar('cap_title'),
            'cap' => $this->request->getVar('cap'),
            'created_by' => $this->request->getVar('created_by'),
            'created_date' => date('Y-m-d H:i:s')
            // 'JPC_NO' => 'JCP' . $lastNo + 1
        ];

        $common = new Common();
        $user = new Users();
        $cap = new CompanyAnnualPriorities();
        $ltp = new LongTermPriorities();
        $inst = $cap->insert($data);
        $ltpData = $ltp->where('id', $this->request->getVar('ltp_id'))->first();
        if ($inst) {
            $common->initCurlGet("https://apps.t10.me/worklist/api/v1/work-item/" . $ltpData['notificationCode'] . "/completed");
            $workCode = $common->sendUwlNotification(
                MDMAIL,
                $this->request->getVar('cap_title'),
                "Company Annual Priorities",
                "New LTP ( " . $this->request->getVar('cap_title') . " ) Created by ( " . explode('.', $this->request->getVar('created_by'))[0] . " )",
                "site",
                "OGSM",
                "Company Annual Priorities",
                "",
                "https://apps.t10.me/ogsm/index.html?page=CAP&id=" . $ltp->getInsertID(),
            );
            $cap->update($cap->getInsertID(), ["notificationCode" => $workCode->data->workItemCode]);

            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'CAP Added Successfully!'
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
        $ltp = new LongTermPriorities();
        $cap = new CompanyAnnualPriorities();
        $get = $cap
            ->select('long_term_priority.* ,company_annual_priority.*')
            ->join('long_term_priority', 'company_annual_priority.ltp_id = long_term_priority.id', 'left')
            ->orderBy('company_annual_priority.created_date', 'ASEC')
            ->findAll();

        $response = [
            'status' => 'OK',
            'data' => $get,
            'msg' => 'CAP Data Fetched Successfully!'
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

        $cap = new CompanyAnnualPriorities();
        $inst = $cap
            ->select('long_term_priority.* ,company_annual_priority.*')
            ->join('long_term_priority', 'company_annual_priority.ltp_id = long_term_priority.id', 'left')
            ->where('company_annual_priority.id', $this->request->getVar('id'))
            ->first();

        $response = [
            'status' => 'OK',
            'data' => $inst,
            'msg' => 'CAP Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }

    public function update()
    {
        $rules = [
            'id' => "required",
            'user_id' => "required",
            'ltp_id' => ['label' => 'LTP', 'rules' => 'required'],
            'cap_title' => "required",
            'cap' => "required",
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
            'ltp_id' => $this->request->getVar('ltp_id'),
            'cap_title' => $this->request->getVar('cap_title'),
            'cap' => $this->request->getVar('cap'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $cap = new CompanyAnnualPriorities();
        $inst = $cap->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'CAP updated Successfully!'
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

        $cap = new CompanyAnnualPriorities();
        $inst = $cap
            ->where('id', $this->request->getVar('id'))
            ->delete();

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => [],
                'msg' => 'CAP Deleted Successfully!'
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
            'ltp_id' => "required",
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
            'ltp_id' => $this->request->getVar('ltp_id'),
            'is_approved' => $this->request->getVar('is_approved'),
            'approved_by' => $this->request->getVar('approved_by'),
            'approved_date' => date('Y-m-d H:i:s')
        ];

        $cap = new CompanyAnnualPriorities();
        $inst = $cap->update($this->request->getVar('id'), $data);
        $capData = $cap->where('id', $this->request->getVar('id'))->first();
        $common = new Common();
        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'CAP Approved Successfully!'
            ];
            $common->initCurlGet("https://apps.t10.me/worklist/api/v1/work-item/" . $capData['notificationCode'] . "/completed");
            $workCode = $common->sendUwlNotification(
                $capData['created_by'],
                $capData['cap_title'],
                "Long Term Priorities",
                "New LTP ( " . $capData['cap_title'] . " ) Created by ( " . explode('.', $capData['created_by'])[0] . " ), , Create the JC Priority againts Company Annual Priority",
                "site",
                "OGSM",
                "Long Term Priorities",
                "",
                "https://apps.t10.me/ogsm/index.html?page=CAP&id=" . $this->request->getVar('id'),
            );
            $cap->update($this->request->getVar('id'), ["notificationCode" => $workCode->data->workItemCode]);
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
            'ltp_id' => "required",
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
            'ltp_id' => $this->request->getVar('ltp_id'),
            'is_approved' => $this->request->getVar('is_approved'),
            'approved_by' => $this->request->getVar('approved_by'),
            'reject_reason' => $this->request->getVar('reject_reason'),
            'approved_date' => date('Y-m-d H:i:s')
        ];

        $cap = new CompanyAnnualPriorities();
        $inst = $cap
            ->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'CAP Rejected Successfully!'
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

    public function addUserAndOrganization()
    {
        $user = new UsersAndOrg();
        $inst = $user->insertBatch($this->request->getVar());
        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => [],
                'msg' => 'Users Added Successfully!'
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
