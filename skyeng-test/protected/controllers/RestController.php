<?php
class RestController extends Controller
{
   protected $_callback = 'callback';
   
   public function filters()
   {
      return array();
   }
   
   protected function _sendResponse($body = '', $status = 200)
   {
      // set the status
      $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
      header($status_header);            
      // pages with body are easy
      if($body != '')
      {
         header("content-type:application/json");  
         // send the body
         echo $this->_formatResponse($body);
      }
         // we need to create the body if none is passed
      else
      {
         header("content-type:text/html");  
         // create some body messages
         $message = '';

         // this is purely optional, but makes the pages a little nicer to read
         // for your users.  Since you won't likely send a lot of different status codes,
         // this also shouldn't be too ponderous to maintain
         switch($status)
         {
            case 401:
               $message = 'You must be authorized to view this page.';
               break;
            case 404:
               $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
               break;
            case 500:
               $message = 'The server encountered an error processing your request.';
               break;
            case 501:
               $message = 'The requested method is not implemented.';
               break;
         }

         // servers don't always have a signature turned on 
         // (this is an apache directive "ServerSignature On")
         $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

         // this should be templated in a real-world solution
         $body = '
         <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
         <html>
         <head>
         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
         <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
         </head>
         <body>
         <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
         <p>' . $message . '</p>
         <hr />
         <address>' . $signature . '</address>
         </body>
         </html>';

         echo $body;
      }
      Yii::app()->end();
   }
   
   protected function _getStatusCodeMessage($status)
   {
      // these could be stored in a .ini file and loaded
      // via parse_ini_file()... however, this will suffice
      // for an example
      $codes = Array(
         200 => 'OK',
         400 => 'Bad Request',
         401 => 'Unauthorized',
         402 => 'Payment Required',
         403 => 'Forbidden',
         404 => 'Not Found',
         500 => 'Internal Server Error',
         501 => 'Not Implemented',
      );
      return (isset($codes[$status])) ? $codes[$status] : '';
   }   
   
   protected function _formatResponse($data){
      return json_encode($data);
      //return CJSON::encode($data);      
   }
   
   public function actionAddStudent(){
      
      $student = new Student;
      $student->name = $_POST['name'];
      if($student->validate()){
         $student->save();
         $this->_sendResponse(array('success' => true, 'msg'=>'Ученик добавлен'));      
      } else {
         $this->_sendResponse(array('success' => false, 'msg'=>$student->getError('name')));
      }      
   }
   
   public function actionAddTeacher(){
      
      $teacher = new Teacher;
      $teacher->name = $_POST['name'];
      if($teacher->validate()){
         $teacher->save();
         $this->_sendResponse(array('success' => true, 'msg'=>'Учитель добавлен'));      
      } else {
         $this->_sendResponse(array('success' => false, 'msg'=>$teacher->getError('name')));
      }
      
   }
   
   public function actionGetTeachers(){
            
      $teachers = Teacher::model()->findAll();
      $data = array();
         foreach($teachers as $t)
            $data[] = $t->attributes;
      $this->_sendResponse($data);
      
   }
   
   public function actionGetStudents(){      
      
      $students = Student::model()->findAll();
      $data = array();
         foreach($students as $s)
            $data[] = $s->attributes;
      $this->_sendResponse($data);      
   }
   
   public function actionAssignStudentToTeacher(){      
      $assignation = new StudentTeacher;
      $assignation->student_id = $_POST['student']['id'];
      $assignation->teacher_id = $_POST['teacher']['id'];
      
      if(!StudentTeacher::model()->findByPk(array("student_id" => $_POST['student']['id'], "teacher_id" =>$_POST['teacher']['id']))){
         $assignation->save();
         $this->_sendResponse(array('success' => true));
      } else
         $this->_sendResponse(array('success' => false));            
   }
   
   //SELECT t.id, t.name, COUNT(st.student_id) FROM teacher t LEFT JOIN student_teacher st ON t.id=st.teacher_id GROUP BY t.id, t.name
   public function actionGetTeachersWithStudents(){
      $teachers = Teacher::model()->with('students')->findAll();
      $data = array();
      foreach($teachers as $t){         
         $students = array();
         foreach($t->students as $s) 
            $students[] = $s->attributes;
         $d = $t->attributes;
         $d['students'] = $students;
         $d['studentsCount'] = count($students);
         $data[] = $d;         
      }      
      $this->_sendResponse($data);      
   }
      
   public function actionSearchTeachersByStudents(){      
      $ids = array();
      $data = array();
      foreach($_POST['students'] as $s){
         $ids[] = $s['id'];
      }
      
      $teachers = Teacher::model()->searchTeachersByStudents($ids);
      $data['success'] = empty($teachers) ? false : true;
      $data['teachers']  = $teachers;
      
      $this->_sendResponse($data);
      
   }
   
   public function actionSearchTeachersWithCommonStudents(){      
      $id = (int)$_POST['teacher']['id'];       
      if(!$id)
         $this->_sendResponse(array('success'=>false));      
      
      $teachers = Teacher::model()->getTeachersWithCommonStudents($id);
      if($teachers)
         $this->_sendResponse(array('success'=>true, 'teachers'=>$teachers));
      else
         $this->_sendResponse(array('success'=>false));  
   }
}

?>
