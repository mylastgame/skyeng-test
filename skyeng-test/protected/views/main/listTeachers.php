<div class="col-lg-12" ng-controller="listTeachersConroller">
   <h2>Список учителей</h2>            
   <div class="padding-top20">
   <table class="table table-striped">
      <thead><tr><th>Имя</th><th>Количество учеников</th></tr></thead>
      <tbody><tr ng-repeat="teacher in teachers">
         <td>{{teacher.name}}</td>
         <td>{{teacher.studentsCount}}</td>
      </tr></tbody>
   </table></div>
</div>
