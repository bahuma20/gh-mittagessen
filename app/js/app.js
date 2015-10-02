/**
 * Created by BachhuberMax on 02.10.2015.
 */

moment.locale('de');

angular = angular || {};

angular.module('ghMittagessen', ['ngMaterial', 'md.data.table', 'ngRoute'])
    .config(function($mdThemingProvider, $mdIconProvider){
        $mdThemingProvider.theme('default')
            .primaryPalette('yellow')
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

            .otherwise({
                redirectTo: "/offers"
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

                // Convert date to JS Date
                $scope.offers[key].order_until = moment($scope.offers[key].order_until);
            });

            console.log( $scope.offers);
        });
    }])
    .controller('OfferCtrl', ['$scope', '$routeParams', '$http', function($scope, $routeParams, $http){
        $scope.offer = {};

        $http.get('../api/offer/' + $routeParams.offer_id).success(function(offerData){
            $scope.offer = offerData;

            // Load Restaurant data
            $http.get('../api/restaurant/' + offerData.restaurant).success(function(restaurantData){
                $scope.offer.restaurant = restaurantData;
            });

            // Load Participations
            $http.get('../api/offer/' + offerData.id + '/participation').success(function(participations){
                $scope.offer.participations = participations;
            });

            // Convert date to JS Date
            $scope.offer.order_until = moment($scope.offer.order_until);
        });
    }]);