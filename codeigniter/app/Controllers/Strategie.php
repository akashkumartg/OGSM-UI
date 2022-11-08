<?php

namespace App\Controllers;

use App\Models\Goals;
use App\Models\Strategies;
use App\Models\Users;
use App\Models\Common;

date_default_timezone_set("Asia/Kolkata");

class Strategie extends BaseController
{
    public function goalDropdown()
    {
        $strg = new Goals();
        $dropDown = $strg
            ->where('person_responsible', $this->request->getVar('mail'))
            // ->where('is_approved', 1)
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
            'goal_id' => ['label' => 'Goal', 'rules' => 'required'],
            'strategie_title' => "required",
            'strategie' => "required",
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
            'goal_id' => $this->request->getVar('goal_id'),
            'strategie_title' => $this->request->getVar('strategie_title'),
            'strategie' => $this->request->getVar('strategie'),
            'created_by' => $this->request->getVar('created_by'),
            'created_date' => date('Y-m-d H:i:s')
        ];

        $common = new Common();
        // $user = new Users();
        $goals = new Goals();
        $Strategies = new Strategies();
        $inst = $Strategies->insert($data);

        $goalData = $goals->where('id', $this->request->getVar('goal_id'))->first();
        if ($inst) {
            $common->initCurlGet("https://apps.t10.me/worklist/api/v1/work-item/" . $goalData['notificationCode'] . "/completed");
            $workCode = $common->sendUwlNotification(
                MDMAIL,
                $this->request->getVar('strategie_title'),
                "Strategie",
                "New Strategie ( " . $this->request->getVar('strategie_title') . " ) Created by ( " . explode('.', $this->request->getVar('created_by'))[0] . " )",
                "site",
                "OGSM",
                "STRATEGIE",
                "",
                "https://apps.t10.me/ogsm/index.html?page=strategy&id=" . $Strategies->getInsertID(),
            );
            $Strategies->update($Strategies->getInsertID(), ["notificationCode" => $workCode->data->workItemCode]);
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Strategie Added Successfully!'
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
        $goals = new Goals();
        $strategies = new Strategies();
        $get = $strategies
            ->select('goals.*,strategies.*')
            ->join('goals', 'goals.id = strategies.goal_id')
            ->orderBy('strategies.created_date', 'ASEC')
            ->findAll();

        $response = [
            'status' => 'OK',
            'data' => $get,
            'msg' => 'Strategie Data Fetched Successfully!'
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

        $strg = new Strategies();
        $inst = $strg
            ->select('goals.*,strategies.*')
            ->join('goals', 'goals.id = strategies.goal_id')
            ->where('strategies.id', $this->request->getVar('id'))
            ->first();

        $response = [
            'status' => 'OK',
            'data' => $inst,
            'msg' => 'Strategie Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }

    public function update()
    {
        $rules = [
            'id' => "required",
            'user_id' => "required",
            'goal_id' => ['label' => 'Goal', 'rules' => 'required'],
            'strategie_title' => "required",
            'strategie' => "required"
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
            'goal_id' => $this->request->getVar('goal_id'),
            'strategie_title' => $this->request->getVar('strategie_title'),
            'strategie' => $this->request->getVar('strategie'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $strategies = new Strategies();
        $inst = $strategies->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Strategie updated Successfully!'
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

        $strategies = new Strategies();
        $inst = $strategies
            ->where('id', $this->request->getVar('id'))
            ->delete();

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => [],
                'msg' => 'Strategie Deleted Successfully!'
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
            'goal_id' => "required",
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
            'user_id' => $this->request->getVar('user_id'),
            'goal_id' => $this->request->getVar('goal_id'),
            'is_approved' => $this->request->getVar('is_approved'),
            'approved_by' => $this->request->getVar('approved_by'),
            'is_confidential' => $this->request->getVar('is_confidential'),
            'approved_date' => date('Y-m-d H:i:s')
        ];

        $Strategies = new Strategies();
        $inst = $Strategies->update($this->request->getVar('id'), $data);
        $common = new Common();
        $strData = $Strategies->where('id', $this->request->getVar('id'))->first();
        if ($inst) {
            $common->initCurlGet("https://apps.t10.me/worklist/api/v1/work-item/" . $strData['notificationCode'] . "/completed");
            $workCode = $common->sendUwlNotification(
                $strData['created_by'],
                $strData['strategie_title'],
                "Strategie",
                "This Strategie ( " . $strData['strategie_title'] . " ) Approved by ( MD ), Create the Long Term Priority againts Strategy",
                "site",
                "OGSM",
                "STRATEGIE",
                "",
                "https://apps.t10.me/ogsm/index.html?page=strategy&id=" . $this->request->getVar('id'),
            );
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Strategie Approved Successfully!'
            ];
            $Strategies->update($this->request->getVar('id'), ["notificationCode" => $workCode->data->workItemCode]);
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
            'goal_id' => "required",
            'is_approved' => "required",
            'approved_by' => "required",
            'reject_reason' => "required"
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
            'goal_id' => $this->request->getVar('goal_id'),
            'is_approved' => $this->request->getVar('is_approved'),
            'approved_by' => $this->request->getVar('approved_by'),
            'reject_reason' => $this->request->getVar('reject_reason'),
            'approved_date' => date('Y-m-d H:i:s')
        ];

        $Strategies = new Strategies();
        $inst = $Strategies
            ->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Strategie Rejected Successfully!'
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
