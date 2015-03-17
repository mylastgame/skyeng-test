<?php

/**
 * This is the model class for table "teacher".
 *
 * The followings are the available columns in table 'teacher':
 * @property integer $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property Student[] $students
 */
class Teacher extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'teacher';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required', 'message'=>'Поле не может быть пустым'),
         array('name', 'match', 'pattern'=>'/^\d*\w*$/', 'message'=>'Не правильный формат'),
			array('name', 'length', 'max'=>50),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'students' => array(self::MANY_MANY, 'Student', 'student_teacher(teacher_id, student_id)'),
         'studentsCount' => array(self::STAT, 'Student', 'student_teacher(teacher_id, student_id)')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Teacher the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
   
   //SELECT t.id, t.name, COUNT(st.student_id) FROM teacher t JOIN student_teacher st ON t.id=st.teacher_id where st.student_id IN(1,2) 
   //GROUP BY t.id, t.name HAVING COUNT(st.student_id) = 2;
   public function searchTeachersByStudents($ids=array()){
      if(empty($ids))
         return array();
      
      return Yii::app()->db->createCommand()
         ->select('t.id, t.name')
         ->from($this->tableName().' t')
         ->join('student_teacher st', 't.id=st.teacher_id')
         ->where(array('in', 'st.student_id', $ids))
         ->group('t.id, t.name')
         ->having('COUNT(st.student_id) = \''.count($ids).'\'')   
         ->queryAll();      
      }
      
      
      /*SELECT t.id, t.name, count(st.student_id) AS commonStudents 
       * FROM teacher t JOIN student_teacher st ON t.id=st.teacher_id 
       * WHERE st.student_id IN (SELECT st1.student_id FROM student_teacher st1 
       * JOIN teacher t1 ON st1.teacher_id=t1.id 
       * WHERE t1.id = '4') AND t.id NOT IN('1,2,3,4') GROUP BY t.id, t.name;"
       */
      public function getTeachersWithCommonStudents($id, $not_in_ids = array()){
         
         $not_in_str = '';                     
         array_push($not_in_ids, $id);
         foreach($not_in_ids as $id){
            $not_in_str .= ' AND t.id != \''.$id.'\'';   
         }
            
         $sql ='SELECT t.id, t.name, count(st.student_id) AS commonStudents 
                FROM teacher t 
                JOIN student_teacher st ON t.id=st.teacher_id 
                WHERE st.student_id IN
               (SELECT st1.student_id FROM student_teacher st1 JOIN teacher t1 ON st1.teacher_id=t1.id WHERE t1.id = \''.$id.'\') 
                '.$not_in_str.' GROUP BY t.id, t.name;';         
         $result = Yii::app()->db->createCommand($sql)->queryAll();
         return $result;
      }
      
      
      public function getTeachersWithMaxCommonStudents($teachers = array()){
         if(empty($teachers))
            $teachers = $this->getTeachers();
                  
         $data = array();                  
         $max_common_students = array('count'=>0);
         $used_ids = array();
         
         foreach($teachers as $t){                              
            $result = $this->getTeachersWithCommonStudents($t['id'], $used_ids);
            foreach($result as $teacher){
               if($teacher['commonStudents'] > $max_common_students['count']){
                  $max_common_students['count'] = $teacher['commonStudents'];
                  $max_common_students['t1'] = array('id'=>$t['id'], 'name'=>$t['name']);
                  $max_common_students['t2'] = array('id'=>$teacher['id'], 'name'=>$teacher['name']);
               }
            }
            array_push($used_ids, $t['id']);            
         }         
         $data['max_common_students'] = $max_common_students;
         return $data;
      }
      
      public function getTeachers(){
         $teachers = $this->model()->findAll();
         $data = array();
         foreach($teachers as $t){
            $data[] = $t->attributes;
         } 
         return $data;
      }
      
      
}
