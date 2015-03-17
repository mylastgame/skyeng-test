<div class="col-lg-12" ng-controller="assignStudentConroller">
   <h2>Назначение ученика</h2>
   <div class="padding-top20">
   <form ng-submit="assignStudentToTeacher()">
       
      <div class="col-md-2">
         Учитель:<select ng-model="selectedTeacher" ng-options="t as t.name for t in teachers"></select>
      </div>
      
      <div class="col-md-2">
         Ученик:<select ng-model="selectedStudent" ng-options="s as s.name for s in students"></select>
      </div>      
      
      <div class="col-md-1"><input class="btn-primary" type="submit" value="Назначить"></div>      
   </form>      </div>
</div>

