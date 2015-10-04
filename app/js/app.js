/**
 * Created by BachhuberMax on 02.10.2015.
 */

moment.locale('de');

angular = angular || {};

angular.module('ghMittagessen', ['ngMaterial', 'md.data.table', 'ngRoute', 'ngUpload'])
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

            .when("/offers/create", {
                templateUrl: "templates/create-offer.html",
                controller: "CreateOfferCtrl"
            })

            .when('/offers/:offer_id', {
                templateUrl: "templates/offer.html",
                controller: "OfferCtrl"
            })

            .when('/restaurants/add',{
                templateUrl: "templates/create-restaurant.html",
                controller: "CreateRestaurantCtrl"
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
    .filter('newlines', function () {
        return function(text) {
            if(text)
                return text.replace(/\n/g, '<br/>');
            return '';
        }
    })
    .filter('unsafe', function($sce) {
        return function(val) {
            return $sce.trustAsHtml(val);

        };
    })
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
        });
    }])
    .controller('OfferCtrl', ['$scope', '$rootScope', '$routeParams', '$http', "$mdToast", function($scope, $rootScope, $routeParams, $http, $mdToast){
        $scope.participation = {};

        $scope.saveParticipation = function(participation, offer) {
          $http.post("../api/participation", {
              offer: offer.id,
              order: participation.order,
              user: $rootScope.loggedInUser.id
          }).success(function(data){
              $scope.loadOffer();
              $scope.participation = {};
              $mdToast.show($mdToast.simple().content('Bestellung wurde eingetragen').position('bottom right'));
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
    .controller("CreateOfferCtrl", ["$scope", "$rootScope", "$location", "$http", "$mdToast", function($scope, $rootScope, $location, $http, $mdToast) {
        if (!$rootScope.loggedIn)
            $location.path("/login");


        $scope.newOffer = {
            tempTime: moment(moment().format("YYYY-MM-DD") + "12:00:00", "YYYY-MM-DD HH:mm:ss").toDate()
        };
        $scope.restaurants = [];

        $http.get('../api/restaurant').success(function(data){
            $scope.restaurants = data;
        });

        $scope.save = function(offer) {
            offer.order_until = offer.tempTime.toISOString();

            $http.post('../api/offer', {
                restaurant: offer.restaurant,
                user: $rootScope.loggedInUser.id,
                order_until: offer.order_until
            }).success(function(data){
                $mdToast.show($mdToast.simple().content('Angebot hinzugefügt').position('bottom right'));
                $location.path("/offers");
            });
        };
    }])
    .controller("CreateRestaurantCtrl", ["$scope", "$http", "$mdDialog", "$location", "$mdToast", function($scope, $http, $mdDialog, $location, $mdToast) {
        $scope.newRestaurant = {
            name: "",
            speisekarten_url: ""
        };

        $scope.bildUploadCompleted = function (content) {
            console.log(content);

            if (content.status == "error") {
                $mdDialog.show(
                    $mdDialog.alert()
                        .parent(angular.element(document.querySelector('body')))
                        .clickOutsideToClose(true)
                        .title('Fehler beim Upload')
                        .content('Es können nur Bild-Dateien (png, jpg, jpeg) hochgeladen werden.')
                        .ariaLabel('Uploadfehler')
                        .ok('OK')
                );
                return false;
            }

            $scope.newRestaurant.image = content.filename;
        };

        $scope.speisekartenUploadCompleted = function (content) {
            console.log(content);

            if (content.status == "error") {
                $mdDialog.show(
                    $mdDialog.alert()
                        .parent(angular.element(document.querySelector('body')))
                        .clickOutsideToClose(true)
                        .title('Fehler beim Upload')
                        .content('Es können nur PDF Dateien hochgeladen werden.')
                        .ariaLabel('Uploadfehler')
                        .ok('OK')
                );
                return false;
            }

            var path = window.location.pathname.substring(0, window.location.pathname.length - 1);
            path = path.substring(1);
            var pathSegements = path.split("/");
            pathSegements.pop();
            path = pathSegements.join("/");

            var assetsUrl = window.location.protocol + "//" + window.location.host + "/" + path + "/assets/files";

            $scope.newRestaurant.speisekarten_url = assetsUrl + "/" + content.filename;
        };

        $scope.submitButtonDisabled = function() {
            if ($scope.newRestaurant.speisekarten_url == "" || $scope.newRestaurant.name == "")
                return true;

            else {
                return false;
            }
        };

        $scope.save = function(restaurant) {

            $http.post('../api/restaurant', {
                name: restaurant.name,
                image_url: restaurant.image,
                speisekarten_url: restaurant.speisekarten_url
            }).success(function(data) {
                $mdToast.show($mdToast.simple().content('Restaurant hinzugefügt').position('bottom right'));
                $location.path("/offers/create");
            });
        };
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
                        $mdToast.show($mdToast.simple().content("Eingeloggt als " + data.name).position("bottom right"));
                        $scope.loading = false;
                    });
                } else {
                    var message = "User / Passwort nicht korrekt";

                    if (data.code == 1)
                        message = "Benutzer nicht gefunden";
                    else if (data.code == 2)
                        message = "Passwort falsch";

                    $mdToast.show($mdToast.simple().content(message).position("bottom right"));
                    $scope.loading = false;
                }
            });
        };
    }])
    .controller("LogoutCtrl", ["$rootScope", "$location", "$http", "$mdToast", function($rootScope, $location, $http, $mdToast) {
        $http.get('../api/auth/logout').success(function(data) {
            console.log("Logged out");
            $rootScope.loggedIn = false;
            $rootScope.loggedUser = {};
            $mdToast.show($mdToast.simple().content("Sie wurden ausgeloggt.").position("bottom right"));
            $location.path("/offers");
        });
    }]);
;