<div class="col-lg-12" ng-controller="searchTeachersConroller">
   <h2>Поиск учителей по ученикам</h2>  
   <div class="col-md-2 padding-top20">
   <form ng-submit="searchTeachersByStudents()">             
      <label>Ученики:</label></br>
      <select multiple="multiple" size="15" ng-model="selectedStudents" ng-options="t as t.name for t in students"></select></br>                                   
      Искать учителей обучающих: <i ng-repeat="s in selectedStudents">{{s.name}}, </i> </br>
      <input class="btn-primary" type="submit" value="Искать">
      
   </form>      
   </div>
   <div class="col-md-2 padding-top20"">
      <label>Учителя:</label></br>   
      <ul>
         <li ng-repeat="t in teachers">{{t.name}} [id - {{t.id}}]</li>
      </ul>
   </div>
</div>