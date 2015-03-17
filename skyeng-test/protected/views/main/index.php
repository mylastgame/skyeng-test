<div class="col-lg-12" ng-controller="addPersonController">
   <h2>Новый ученик / учитель</h2>
   <div class="padding-top20"><p><form ng-submit="addStudent()">
      Новый ученик: <input type="text" ng-model="newStudent"  name="newStudent" size="30">
      <input class="btn-primary" type="submit" value="Добавить">      
   </form></p>
   
   <p><form ng-submit="addTeacher()">
      Новый учитель: <input type="text" ng-model="newTeacher"  size="30">
      <input class="btn-primary" type="submit" value="Добавить">
   </form></p>   </div>
</div>
