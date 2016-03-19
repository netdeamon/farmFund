farmApp.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {
    $routeProvider.
    when('/', {
        templateUrl: 'app/templates/index.html',
        action: 'farmApp.indexController'
    });
    $locationProvider.html5Mode(true);
}]);


