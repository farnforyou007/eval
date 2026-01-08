<?php
namespace App\Api;

use App\Models\Qustions;

class QustionsApi {
    private $qustionsModel;

    public function __construct() {
        $this->qustionsModel = new Qustions();
    }

    public function listQuestions() {
        return $this->qustionsModel->getAll();
    }

    public function questionBySubject($id) {
       return $this->qustionsModel->getBySubjectId($id);
    }

}
