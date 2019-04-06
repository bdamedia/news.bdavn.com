<script>
zaa.bootstrap.register('AdsController', ['$scope', 'AdminToastService', function($scope, AdminToastService) {
    
    // add your angular controller logic here

    $scope.title = 'Ads Management';    
    $scope.test = function(){
        AdminToastService.success('test')
    }
}]);
</script>
<div class="luya-content" ng-controller="AdsController">
    <h1>Create Add</h1>
    <button ng-click="test()">Test</button>
    <a href="#" ng-click="">back</a>
</div>