<?php

namespace App\Controllers;

use App\Models\Common;
use App\Models\Goals;
use App\Models\Objectives;
use App\Models\uom;
use App\Models\Users;

date_default_timezone_set("Asia/Kolkata");

class Goal extends BaseController
{
    public function objectiveDropdown()
    {
        $obj = new Objectives();
        $dropDown = $obj->findAll();
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
            'objective_id' => ['label' => 'Objective', 'rules' => 'required'],
            'goal_title' => "required",
            'goals' => "required",
            'unit' => "required",
            'unit_of_measure' => "required",
            'target_date_for_achieving' => "required",
            'person_responsible' => "required",
            'target_date_submission' => "required",
            'created_by' => "required",
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
            'objective_id' => $this->request->getVar('objective_id'),
            'goal_title' => $this->request->getVar('goal_title'),
            'goals' => $this->request->getVar('goals'),
            'unit' => $this->request->getVar('unit'),
            'unit_of_measure' => $this->request->getVar('unit_of_measure'),
            'target_date_for_achieving' => $this->request->getVar('target_date_for_achieving'),
            'person_responsible' => $this->request->getVar('person_responsible'),
            'target_date_submission' => $this->request->getVar('target_date_submission'),
            'created_by' => $this->request->getVar('created_by'),
            'created_date' => date('Y-m-d H:i:s')
        ];

        $Common = new Common();
        $user = new Users();
        $goals = new Goals();
        $inst = $goals->insert($data);
        $goalData = $goals->where('id', $goals->getInsertID())->first();
        if ($inst) {
            $workCode = $Common->sendUwlNotification(
                $this->request->getVar('person_responsible'),
                $this->request->getVar('goal_title'),
                "Goal",
                "New Goal ( " . $this->request->getVar('goal_title') . " ) Created by ( " . explode('.', $this->request->getVar('created_by'))[0] . " ), Create the Strategy againts Goal",
                "site",
                "OGSM",
                "GOAL",
                "",
                "https://apps.t10.me/ogsm/index.html?page=strategy&id=" . $goals->getInsertID(),
            );
            $goals->update($goalData['id'], ["notificationCode" => $workCode->data->workItemCode]);
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Goal Added Successfully!'
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
        $obj = new Objectives();
        $goals = new Goals();
        $get = $goals
            ->select('objectives.* ,goals.* ,all_users.email,all_users.name,all_users.job_title')
            ->join('objectives', 'goals.objective_id = objectives.id', 'left')
            ->join('all_users', 'goals.person_responsible = all_users.email', 'left')
            ->orderBy('goals.created_date', 'ASEC')
            ->findAll();

        $response = [
            'status' => 'OK',
            'data' => $get,
            'msg' => 'Goal Data Fetched Successfully!'
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

        $goal = new Goals();
        $inst = $goal

            ->select('objectives.* ,goals.* ,all_users.email,all_users.name,all_users.job_title')
            ->join('objectives', 'goals.objective_id = objectives.id', 'left')
            ->join('all_users', 'goals.person_responsible = all_users.email', 'left')
            ->where('goals.id', $this->request->getVar('id'))
            ->first();

        $response = [
            'status' => 'OK',
            'data' => $inst,
            'msg' => 'Goal Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }

    public function update()
    {
        $rules = [
            'id' => "required",
            'user_id' => "required",
            'objective_id' => ['label' => 'Objective', 'rules' => 'required'],
            'goal_title' => "required",
            'goals' => "required",
            'unit' => "required",
            'unit_of_measure' => "required",
            'target_date_for_achieving' => "required",
            'person_responsible' => "required",
            'target_date_submission' => "required",
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
            'objective_id' => $this->request->getVar('objective_id'),
            'goal_title' => $this->request->getVar('goal_title'),
            'goals' => $this->request->getVar('goals'),
            'unit' => $this->request->getVar('unit'),
            'unit_of_measure' => $this->request->getVar('unit_of_measure'),
            'target_date_for_achieving' => $this->request->getVar('target_date_for_achieving'),
            'person_responsible' => $this->request->getVar('person_responsible'),
            'target_date_submission' => $this->request->getVar('target_date_submission'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $goals = new Goals();
        $inst = $goals
            ->update($this->request->getVar('id'), $data);

        if ($inst) {
            $Common = new Common();
            // $Common->sendUwlNotification(
            //     $this->request->getVar('person_responsible'),
            //     $this->request->getVar('goal_title'),
            //     "Goal",
            //     "New Goal Created ( " . $this->request->getVar('goals') . " )",
            //     "site",
            //     "OGSM",
            //     "GOAL",
            //     "",
            //     "https://apps.t10.me/ogsm",
            // );
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Goal updated Successfully!'
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

        $goals = new Goals();
        $inst = $goals
            ->where('id', $this->request->getVar('id'))
            ->delete();

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => [],
                'msg' => 'Goal Deleted Successfully!'
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
            'objective_id' => "required",
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
            'objective_id' => $this->request->getVar('objective_id'),
            'is_approved' => $this->request->getVar('is_approved'),
            'approved_by' => $this->request->getVar('approved_by'),
            'approved_date' => date('Y-m-d H:i:s')
        ];

        $goals = new Goals();
        $inst = $goals
            ->update($this->request->getVar('id'), $data);

        if ($inst) {
            $response = [
                'status' => 'OK',
                'data' => $data,
                'msg' => 'Goal Approved Successfully!'
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

    public function uom()
    {
        $uom = new uom();
        $get = $uom
            ->select('unit,measurement_unit_text')
            ->findAll();

        $response = [
            'status' => 'OK',
            'data' => $get,
            'msg' => 'Uom Data Fetched Successfully!'
        ];

        return $this->response->setJSON($response);
    }
}
