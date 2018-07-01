<?php

namespace SpriiTestApp\core\controller;

use SpriiTestApp\http\Request;
use SpriiTestApp\http\HtmlResponse;
use SpriiTestApp\http\RedirectResponse;
use SpriiTestApp\model\Student;
use SpriiTestApp\model\Subject;

class DefaultController extends Controller {

    public function index() {

        $subject = new Subject();
        $student = new Student();
        $response = new HtmlResponse();
        $response->setHtml($this->generateContent(array(
                    'systemUrl' => $this->getConfig()->systemUrl->__toString(),
                    'id' => $student->id,
                    'firstName' => $student->firstName,
                    'lastName' => $student->lastName,
                    'subjects' => $subject->fetchAll(),
                    'students' => $student->fetchAll(),
        )));
        return $response;
    }
    
    public function editStudent(Request $request) {

        $subject = new Subject();
        $student = new Student();
        
        $student->id = $request->get('id');
        $student = $student->read();
        
        $response = new HtmlResponse();
        $response->setHtml($this->generateContent(array(
                    'systemUrl' => $this->getConfig()->systemUrl->__toString(),
                    'id' => $student->id,
                    'firstName' => $student->firstName,
                    'lastName' => $student->lastName,
                    'subjects' => $subject->fetchAll(),
                    'students' => $student->fetchAll(),
        )));
        return $response;
    }

    public function saveStudent() {

        $requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if ($requestMethod === 'POST') {
            $id = filter_input(INPUT_POST, 'id');
            $firstName = filter_input(INPUT_POST, 'firstName');
            $lastName = filter_input(INPUT_POST, 'lastName');
            $subjects = filter_input(INPUT_POST, 'subjects', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY);
            
            $student = new Student();
            if (!empty($id)) {
                $student->id = $id;
                $student->read();
            }

            $student->firstName = $firstName;
            $student->lastName = $lastName;
            $student->subjects = $subjects;

            $student->save();
        } else {
            // TODO: Warn    
        }

        $response = new RedirectResponse();
        $response->setUrl('./');
        return $response;
    }
    
    public function deleteStudent(Request $request) {
        $id = $request->get('id');
        if (!empty($id)) {
            $student = new Student();
            $student->id = $id;
            $student->delete();
        }
        
        $response = new RedirectResponse();
        $response->setUrl('../../');
        return $response;
    }

    public function defaultAction() {
        return $this->index();
    }

}
