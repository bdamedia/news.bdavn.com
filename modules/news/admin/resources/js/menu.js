zaa.controller('MenuController', ['$scope', 'AdminToastService', '$state', '$filter', '$http', function($scope, AdminToastService, $state, $filter, $http) {
    
    // add your angular controller logic here

    $scope.title = 'Menu Management';    
    // $scope.test = function(){
    //     AdminToastService.success('test');
    //     console.log($state.params);
    //     return $state.go('default.route', {'controllerId' : $state.params.controllerId, 'moduleRouteId' : $state.params.moduleRouteId, 'controllerId' : $state.params.controllerId, 'actionId': 'create'});
    // }

    $scope.selected = '';
    $scope.processing = false;
    $scope.positions = [];
    $scope.loadingContent = true;

    $http.get('admin/api-menu/get').then(function(response) {
        $scope.data.pages = response.data.pages;
        $scope.data.categories = response.data.categories;
        $scope.data.menu = response.data.menu;
        $scope.data.menus = response.data.menus;
        $scope.selected = response.data.menu;
        $scope.positions = response.data.positions;
        $scope.loadingContent = false;
    });

    $scope.dropItem = function(dragged,dropped,position, align, element){
        var step  = dragged.index - dropped.index;
        var previousIndex = dropped.index - 1;

        //Move Left/Right
        if(step == 0){
            if(align >= 20 && previousIndex >= 0 && $scope.data.menu.items[dragged.index].level < 5){
                $scope.data.menu.items[dragged.index].level++;
            }else if(align <= -20 && $scope.data.menu.items[dragged.index].level > 1){
                $scope.data.menu.items[dragged.index].level--;
            }

            return;
        }

        //Move Down
        if(step < 0){
            if(position != 'bottom' && step == -1){
                return;
            }

            if(position != 'bottom'){
                
                $items = $filter('filter')($scope.data.menu.items, {action: '!remove'});
                dropped = $items[dropped.index - 1];
            }
        }else{
        //Mode Up
            if(position != 'top' && step == 1){
                return;
            }

            if(position != 'top'){
                $items = $filter('filter')($scope.data.menu.items, {action: '!remove'});
                dropped = $items[dropped.index + 1];
            }
        }
        
        previousIndex = dropped.index - 1;
        
        if(align >= 20 && previousIndex >= 0 && dragged.level < 5){
            dragged.level++;
        }else if(align <= -20 && dragged.level > 1){
            dragged.level--;
        }

        $scope.move(dragged, dropped, align);
    }

    $scope.move = function(dragged, dropped, align){
        var $item = $scope.data.menu.items[dragged.index];
        $scope.data.menu.items.splice(dragged.index, 1);

        Array.prototype.splice.apply($scope.data.menu.items, [dropped.index, 0].concat([{
            id: $item.id, 
            name: $item.name, 
            level: $item.level, 
            url: $item.url, 
            type: $item.type, 
            objectId: $item.objectId, 
            expanded: $item.expanded, 
            action: $item.act, 
            index: $item.index
        }]));

        var i = dragged.index;
        var n = dropped.index;

        if(i > n){
            i = n;
            n = dragged.index;
        }

        for(var j = i; j <= n; j++){
            $scope.data.menu.items[j].index = j;
        }
    }

    $scope.handleError = function(error){
        $scope.processing = false;
        if(error.status != 422){
            AdminToastService.error(error.statusText);
            return;
        }

        angular.forEach(error.data, function(data){
            AdminToastService.error(data.message);
        });
    }

    $scope.deleteMenu = function(){
        $scope.processing = true;
        $http.delete('admin/api-menu/delete', {params: {id: $scope.data.menu.id}}).then(function(response) {
            var i = -1;
            angular.forEach($scope.data.menus, function($menu, index){
                if($menu.id == $scope.data.menu.id){
                    i = index;
                    return;
                }
            });

            if(i > -1){
                $scope.data.menus.splice(i, 1);
                if($scope.data.menus.length)
                    $scope.data.menu = $scope.data.menus[0];
                else
                    $scope.createMenu();
            }

            AdminToastService.success('Delete Successfully!');
            $scope.processing = true;
        }).catch(function(error){
            $scope.handleError(error);
        });
    }

    $scope.saveMenu = function(){
        if($scope.data.menu.name == ''){
            AdminToastService.error('Please enter menu name.');
            return;
        }
        
        $scope.processing = true;
        $http.post('admin/api-menu/post', $scope.data.menu).then(function(response) {
            $scope.processing = false;
            $scope.data.menu.id = response.data.id;
            $scope.data.menu.items = response.data.items;
            $scope.data.menu.positions = response.data.positions;

            angular.forEach($scope.data.menus, function($menu, $index){
                if($menu.id == $scope.data.menu.id){
                    $scope.data.menus[$index].items = response.data.items;
                    $scope.data.menus[$index].positions = response.data.positions;
                    return;
                }
            });

            AdminToastService.success('Save Successfully!');
        }).catch(function(error){
            $scope.handleError(error);
        });
    }

    $scope.isValid = function(hover,dragged){
        return true;
    }

    $scope.data = {
        menu: {
            id: 0, 
            name: '', 
            items: [],
            positions: []
        },
        menus: [],
        // [{id:1, name:'top', items: [
        //     {id: 1, name: 'Cate 1', level: 1, url: '', type: 'pages', objectId: 1, expanded: false, action: '', index: 0},
        //     {id: 2, name: 'Cate 2', level: 2, url: '', type: 'categories', objectId: 1, expanded: false, action: '', index: 1},
        //     {id: 3, name: 'Cate 3', level: 1, url: 'http://test.com', type: 'links', objectId: '', expanded: false, action: '', index: 2}
        // ]}, {id:2, name:'lef', items: [
        //     {id: 1, name: 'Cate 11', level: 1, url: '', type: 'pages', objectId: 1, expanded: false, action: '', index: 0},
        //     {id: 2, name: 'Cate 21', level: 2, url: '', type: 'categories', objectId: 1, expanded: false, action: '', index: 1},
        //     {id: 3, name: 'Cate 31', level: 1, url: 'http://test.com', type: 'links', objectId: '', expanded: false, action: '', index: 2}
        // ]}, {id:3, name:'right', items: [
        //     {id: 1, name: 'Cate 12', level: 1, url: '', type: 'pages', objectId: 1, expanded: false, action: '', index: 0},
        //     {id: 2, name: 'Cate 22', level: 2, url: '', type: 'categories', objectId: 1, expanded: false, action: '', index: 1},
        //     {id: 3, name: 'Cate 32', level: 1, url: 'http://test.com', type: 'links', objectId: '', expanded: false, action: '', index: 2}
        // ]}],
        pages: [],
        categories: [],
        links:{
            url: '',
            name: ''
        },
    }

    $scope.$watch('selected', function (newValue, oldValue, scope) {
        //console.log(newValue, oldValue, scope);
    }, true);

    $scope.createMenu = function(){
        $scope.data.menu = {id: 0, name: '', items: [], positions: $scope.positions};
    }

    $scope.selectMenu = function(){
        if($scope.selected){
            $scope.data.menu = $scope.selected;
        }
    }

    $scope.collapseExpaned = {
        pages: true,
        categories: false,
        links: false
    };

    $scope.toggle = function(elm){
        $scope.collapseExpaned[elm] = !$scope.collapseExpaned[elm];
    }

    $scope.addToMenu = function($model){
        if($model == 'links'){
            var $item = $scope.data.links;
            $scope.data.menu.items.push({id: 0, name: $item.name, level: 1, url: $item.url, type: $model, objectId: '', expanded: false, action: 'add', index: $scope.data.menu.items.length});
            $scope.data.links.name = '';
            $scope.data.links.url = '';
            return;
        }

        $items = $filter('filter')($scope.data[$model], {checked: true});

        angular.forEach($scope.data[$model], function($item, $index){
            if($item.checked){
                $scope.data.menu.items.push({id: 0, name: $item.name, level: 1, url: $item.url, type: $model, objectId: $item.id, expanded: false, action: 'add', index: $scope.data.menu.items.length});
                $scope.data[$model][$index].checked = false;
            }
        });
    }

    $scope.getCheckedItems = function($model){
        return $filter('filter')($scope.data[$model], {checked: true});
    }

    $scope.cancel = function($item){
        $item.action = 'cancel';
        $item.expanded = false;
    }

    $scope.remove = function($item){
        $item.action = 'remove';
    }
}]);