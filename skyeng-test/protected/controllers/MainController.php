<?php
class MainController extends Controller{
   
   public function actionIndex(){
      $this->render('index');
   }
         
   public function actionAssignStudent(){
      $this->render('assignStudent');
   }
         
   public function actionListTeachers(){
      $this->render('listTeachers');
   }
   
   public function actionSearchTeachers(){
      $this->render('searchTeachers');
   }
   
   public function actionSearchTeachersWithCommonStudents(){
      
      $data = Teacher::model()->getTeachersWithMaxCommonStudents();
      $this->render('searchTeachersWithCommonStudents', array('data'=>$data));
   }
      
}
?>
