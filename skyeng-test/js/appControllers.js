var testApp = angular.module('testApp', []);

testApp.controller('addPersonController', function ($scope, $http) {
   $scope.addStudent = function() {                              
      $http({
         method: 'POST',
         url: '/rest/addStudent',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
         },
         data: $.param({name: $scope.newStudent})
      }).success(function(data){
         console.log(data);
         if(data.success){            
            $scope.newStudent = '';
         } 
         alert(data.msg);         
      }).error(function(data){                  
         alert('Произошла ошибка при добавлении!');         
      });
                        
   };
    
   $scope.addTeacher = function() {                              
      $http({
         method: 'POST',
         url: '/rest/addTeacher',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
         },
         data: $.param({name: $scope.newTeacher})
      }).success(function(data){
         console.log(data);
         if(data.success){            
            $scope.newTeacher = '';
         } 
         alert(data.msg);         
      }).error(function(data){                  
         alert('Произошла ошибка при добавлении!');         
      });
                        
   };

});

testApp.controller('assignStudentConroller', function ($scope, $http, $timeout) {
   $scope.init = function(){
      $http({
         method: 'GET',
         url: '/rest/getTeachers'         
      }).success(function(data){
         console.log(data);
         $scope.teachers = data;
         $scope.selectedTeacher = $scope.teachers[0];
      });
      $http({
         method: 'GET',
         url: '/rest/getStudents'         
      }).success(function(data){
         console.log(data);
         $scope.students = data;
         $scope.selectedStudent = $scope.students[0];
      });
      $scope.success = true;
   };
   $timeout($scope.init);
      
   $scope.assignStudentToTeacher = function(){
      
      $http({
         method: 'POST',
         url: '/rest/assignStudentToTeacher',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
         },
         data: $.param({teacher: $scope.selectedTeacher, student: $scope.selectedStudent})         
      }).success(function(data){
         console.log(data);         
         $scope.success = data.success;
         $scope.success ? alert('Ученик добавлен') : alert('Ученик уже обучается у данного учителя');
      }).error(function(data){         
         alert('Ошибка при назначении');
      });            
      
   }
});

testApp.controller('listTeachersConroller', function ($scope, $http, $timeout) {
   $scope.teachers = '';
   $scope.init = function(){
      $http({
         method: 'GET',
         url: '/rest/getTeachersWithStudents'         
      }).success(function(data){
         console.log(data);         
         $scope.teachers = data;
      });
      
   };
   $timeout($scope.init);
         
});

testApp.controller('searchTeachersConroller', function ($scope, $http, $timeout) {
   $scope.init = function(){      
      $http({
         method: 'GET',
         url: '/rest/getStudents'         
      }).success(function(data){
         console.log(data);
         $scope.students = data;
         $scope.selectedStudents = null;
      });
      $scope.teachers = null;
   };
   $timeout($scope.init);
      
   $scope.searchTeachersByStudents = function(){
      
      $http({
         method: 'POST',
         url: '/rest/searchTeachersByStudents',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
         },
         data: $.param({students:$scope.selectedStudents})         
      }).success(function(data){
         console.log(data);                  
         $scope.teachers = data.teachers;         
         if(!data.success) alert('Ничего не найдено');           
      }).error(function(data){         
         alert('Ошибка');
      });            
      
   }
});

