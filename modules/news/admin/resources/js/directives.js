angular.module("zaa").requires.push('ui.tinymce');

zaa.directive("wysiwyg2", function () {
    return {
        restrict: "E",
        scope: {
            "model": "=",
            "options": "=",
            "label": "@label",
            "i18n": "@i18n",
            "id": "@fieldid",
            "placeholder": "@placeholder"
        },
        template: function () {
            return "<div><textarea ui-tinymce=\"tinymceOptions\" ng-model=\"model\"></textarea></div>";
        },
        controller: ['$scope', '$element', '$timeout', '$http', '$rootScope', function ($scope, $element, $timeout, $http, $rootScope) {
            $rootScope.tinymceOptions = {height: '', plugins: '', toolbar: ''};

            $http.get('admin/api-luyawisywyg-config/get').then(function (response) {
                $scope.tinymceOptions = {
                    height: response.data.height,
                    plugins: response.data.plugins,
                    toolbar: response.data.toolbar
                };
                // tinymce bug workaround
                setTimeout(function () {
                    $scope.$broadcast('$tinymce:refresh');
                }, 500);
            });
        }],
    }
});

zaa.directive("wysiwyg2Label", function () {
    return {
        restrict: "E",
        scope: {
            "model": "=",
            "label": "@label",
            "i18n": "@i18n"
        },
        template: function ($scope) {
            return "<div class=\"form-group form-side-by-side\" ng-class=\"{'input--hide-label': i18n}\"><div class=\"form-side form-side-label\"><label class=\"ng-binding\" ng-value='label'>{{label}}</label></div><div class=\"form-side\"><div><textarea ui-tinymce=\"tinymceOptions\" ng-model=\"model\"></textarea></div></div></div>";
        },
        controller: ['$scope', '$element', '$timeout', function ($scope, $element, $timeout) {
            $scope.tinymceOptions = {
                height: 200,
                menubar: false,
                plugins: 'link image code lists textcolor',
                toolbar: 'undo redo | bold underline italic forecolor backcolor | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | link image | removeformat | code'
            };
        }],
    }
});


/**
 * Generate a textarea input.
 */
zaa.directive("zaaUploadImage", function(){
    return {
        restrict: "E",
        scope: {
            "model": "=",
            "options": "=",
            "label": "@label",
            "i18n": "@i18n",
            "id": "@fieldid",
        },
        template: function() {
            return '<div class="form-group form-side-by-side" ng-class="{\'input--hide-label\': i18n}"><div class="form-side form-side-label"><label for="{{id}}">{{label}}</label></div><div class="form-side">'+
                '<input accept="image/*" id="{{id}}" onchange="angular.element(this).scope().uploadFile(this.files)" name="image" type="file" ng-model="model" placeholder="{{placeholder}}"/>'+
                '<div ng-show="image"><br/><img ng-if="image" style="max-width: 50px;max-height: 50px;" src="{{image}}"/></div>' +
                '</div></div>';
        },
        controller : ['$scope', '$http', 'AdminToastService', function($scope, $http, AdminToastService) {
            $scope.image = '';
           
            $http.get('admin/api-news-article/get-image?name='+$scope.model).then(function (response) {
                if(response.data.source)
                    $scope.image = response.data.source;
            });

            $scope.uploadFile = function(files){
                var matched = files[0].name.match(/\.(.+)$/);
                var ext = '';
                if(matched && matched.length > 1)
                    ext = matched[1];
                
                if(angular.lowercase(ext) !== 'jpg' && angular.lowercase(ext) !=='jpeg' && angular.lowercase(ext) !=='png' && angular.lowercase(ext) !=='gif'){
                    window.alert('Sai định đạng image');
                    AdminToastService.error('Sai định đạng image');
                    return;
                }    
                
                var fd = new FormData();
                //Take the first selected file
                fd.append("file", files[0]);

                $http.post('admin/api-news-article/upload', fd, {
                    withCredentials: true,
                    headers: {'Content-Type': undefined },
                    transformRequest: angular.identity
                }).then(function(response){
                    $scope.model = response.data.name;
                    $scope.image = response.data.source;
                }).catch(function(e){
                    
                });
            }
        }]
    }
});