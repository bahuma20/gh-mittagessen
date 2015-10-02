/**
 * Created by BachhuberMax on 02.10.2015.
 */

moment.locale('de');

angular = angular || {};

angular.module('ghMittagessen', ['ngMaterial', 'md.data.table', 'ngRoute'])
    .config(function($mdThemingProvider, $mdIconProvider){
        $mdThemingProvider.theme('default')
            .primaryPalette('teal')
            .accentPalette('indigo');
    })
    .config(["$routeProvider", function($routeProvider) {
        $routeProvider
            .when("/offers", {
                templateUrl: "templates/offers.html",
                controller: "OffersCtrl"
            })

            .when('/offers/:offer_id', {
                templateUrl: "templates/offer.html",
                controller: "OfferCtrl"
            })

            .when('/login', {
                templateUrl: "templates/login.html",
                controller: "LoginCtrl"
            })

            .when("/logout", {
                template: "",
                controller: "LogoutCtrl"
            })

            .otherwise({
                redirectTo: "/offers"
            });
    }])
    .run(["$rootScope", "$http", function($rootScope, $http) {
        $http.get('../api/auth/current').success(function(data) {
           console.log(data);
            if (data.status == "error" && data.code == 100)
                $rootScope.loggedIn = false;
            else {
                $rootScope.loggedIn = true;
                $rootScope.loggedInUser = data;
            }
        });
    }])
    .controller('OffersCtrl', ['$scope', '$http', function($scope, $http){
        $scope.offers = [];

        $http.get('../api/offer').success(function(data){
            $scope.offers = data;

            angular.forEach(data, function(val, key){
                // Load Restaurant data
                $http.get('../api/restaurant/' + val.restaurant).success(function(data){
                    $scope.offers[key].restaurant = data;
                });

                // Load User data
                $http.get('../api/user/' + val.user).success(function(data){
                    $scope.offers[key].user = data;
                });

                // Convert date to JS Date
                $scope.offers[key].order_until = moment($scope.offers[key].order_until);
            });

            console.log( $scope.offers);
        });
    }])
    .controller('OfferCtrl', ['$scope', '$rootScope', '$routeParams', '$http', function($scope, $rootScope, $routeParams, $http){
        $scope.participation = {};

        $scope.saveParticipation = function(participation, offer) {
          $http.post("../api/participation", {
              offer: offer.id,
              order: participation.order,
              user: $rootScope.loggedInUser.id
          }).success(function(data){
              $scope.loadOffer();
              $scope.participation = {};
          });
        };

        $scope.offer = {};

        $scope.loadOffer = function () {
            $http.get('../api/offer/' + $routeParams.offer_id).success(function(offerData){
                $scope.offer = offerData;

                // Load Restaurant data
                $http.get('../api/restaurant/' + offerData.restaurant).success(function(restaurantData){
                    $scope.offer.restaurant = restaurantData;
                });

                // Load User data
                $http.get('../api/user/' + offerData.user).success(function(userData){
                    $scope.offer.user = userData;
                });

                // Load Participations
                $http.get('../api/offer/' + offerData.id + '/participation').success(function(participations){
                    $scope.offer.participations = participations;

                    angular.forEach(participations, function(value, index){
                        $http.get('../api/user/' + participations[index].user).success(function(user) {
                            $scope.offer.participations[index].user = user;
                        });
                    });
                });

                // Convert date to JS Date
                $scope.offer.order_until = moment($scope.offer.order_until);
            });
        };

        $scope.loadOffer();

    }])
    .controller("LoginCtrl", ["$rootScope", "$scope", "$location", "$mdToast", "$http", function($rootScope, $scope, $location, $mdToast, $http) {
        if ($rootScope.loggedIn) {
            $location.path("/offers");
        }

        $scope.user = {
            name: "",
            password: ""
        };

        $scope.loading = false;

        $scope.login = function() {

            $scope.loading = true;

            $http.post('../api/auth/login', {
                username: $scope.user.name,
                password: $scope.user.password
            }).success(function(data) {
                if (data.status == "success") {

                    $http.get('../api/auth/current').success(function (data){

                        $rootScope.loggedInUser = data;
                        $rootScope.loggedIn = true;
                        $location.path("/offers");
                        $mdToast.show($mdToast.simple().content("Eingeloggt als " + data.name).position("top right"));
                        $scope.loading = false;
                    });
                } else {
                    var message = "User / Passwort nicht korrekt";

                    if (data.code == 1)
                        message = "Benutzer nicht gefunden";
                    else if (data.code == 2)
                        message = "Passwort falsch";

                    $mdToast.show($mdToast.simple().content(message).position("top right"));
                    $scope.loading = false;
                }
            });
        };
    }])
    .controller("LogoutCtrl", ["$rootScope", "$location", "$http", function($rootScope, $location, $http) {
        $http.get('../api/auth/logout').success(function(data) {
            console.log("Logged out");
            $rootScope.loggedIn = false;
            $rootScope.loggedUser = {};
            $location.path("/offers");
        });
    }]);
;