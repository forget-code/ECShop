(function() {

    'use strict';

    angular
        .module('app')
        .controller('ActivityDetailController', ActivityDetailController);


    ActivityDetailController.$inject = ['$scope', '$rootScope', '$http', '$timeout', '$location', '$state', '$stateParams', '$window', 'AppAuthenticationService', 'MyOrderModel', 'ENUM', '$interval', 'API'];

    function ActivityDetailController($scope, $rootScope, $http, $timeout, $location, $state, $stateParams, $window, AppAuthenticationService, MyOrderModel, ENUM, $interval, API) {

        var PER_PAGE = 10;
        
        $scope.reload = _reload;
        $scope.activityInfo = null;
        $scope.activityId = $stateParams.activity;        
        $scope.formatUrl = _formatUrl;        
        $scope.day_front = "0";
        $scope.hour_front = "0";
        $scope.minute_front = "0";
        $scope.second_front = "0";

        $scope.day_last = "0";
        $scope.hour_last = "0";
        $scope.minute_last = "0";
        $scope.second_last = "0";


        $scope.isLoading = false;
        $scope.isactive = false;
        $scope.isEmpty = false;
        $scope.isLoaded = false;
        $scope.isLastPage = false;
        $scope.timer = null;
        $scope.loadMore = _loadMore;
        $scope.countdownBegin = false;        

        $scope.touchProduct = _touchProduct;
        $scope.touchGoHome = _touchGoHome;
        $scope.systemTimestamp = Date.parse(new Date()) / 1000;
        $scope.userScope   = "全部商品可用";

        function _touchGoHome() {
            $rootScope.goHome();
        }


        function _reloadActivityInfo() {
            var params = {
                            activity: $scope.activityId
                        };
            API.activity
                .get(params)
                .then(function(activity) {
                    if (activity) {
                        $scope.activityInfo = activity;

                        $scope.systemTimestamp =  Date.parse(new Date()) / 1000;
                        if($scope.systemTimestamp > $scope.activityInfo.start_at 
                        && $scope.systemTimestamp < $scope.activityInfo.end_at){
                            _setupProductList();    
                        }else if($scope.systemTimestamp < $scope.activityInfo.start_at){
                            $scope.countdownBegin = true;
                        }
                        

                        if($scope.activityInfo.range == ENUM.ACTIVITY_FAR.FAR_ALL){
                            $scope.userScope = "全部商品可用";       
                        }
                        else if( $scope.activityInfo.range == ENUM.ACTIVITY_FAR.FAR_CATEGORY){
                           $scope.userScope =  "部分分类可用";
                        }
                        else if( $scope.activityInfo.range == ENUM.ACTIVITY_FAR.FAR_BRAND){
                           $scope.userScope =  "部分品牌可用"; 
                       } else if( $scope.activityInfo.range == ENUM.ACTIVITY_FAR.FAR_GOODS){
                            $scope.userScope =  "部分商品可用"; 
                       }  
                        $scope.userScope+= "\/";
                         for ( var i = 0; i < $scope.activityInfo.user_rank.length; ++i ) {
                                var rank = $scope.activityInfo.user_rank[i];
                                $scope.userScope+= rank.name;
                                if(i!=$scope.activityInfo.user_rank.length-1){
                                    $scope.userScope+= "、";
                                }
                         }                     
                         $scope.userScope+= "可用";
                        // 开始之前先暂停
                        $interval.cancel($scope.timer);
                        $scope.timer = $interval(function() {
                            _reloadTime();
                        }, 1000, -1);
                    };
                });
        }


        function _reloadTime() {
            // 得到目前系统时间到结束时间之间的差，换算成天时分秒
            // 得到当前系统时间戳            
            $scope.systemTimestamp =  Date.parse(new Date()) / 1000;

            // 把时间字符串变为时间，然后得到当前活动结束时间戳
            var endTimestamp = $scope.activityInfo.end_at;            
            if($scope.systemTimestamp < $scope.activityInfo.start_at){
                var endTimestamp = $scope.activityInfo.start_at;
            }else{
                if($scope.countdownBegin){
                    $scope.countdownBegin = false;
                    _setupProductList();                    
                }
            }
            

            // 比较当前的时间 在结束时间戳大于现在时间戳的时候  才继续进行下一步处理
            if (endTimestamp >= $scope.systemTimestamp) {
                var currentTimestamp = endTimestamp - $scope.systemTimestamp;
                var day = Math.floor(currentTimestamp / 86400);
                var hour = Math.floor(currentTimestamp % 86400 / 3600);
                var minute = Math.floor(currentTimestamp % 86400 % 3600 / 60);
                var second = Math.floor(currentTimestamp % 86400 % 3600 % 60);

                if (day < 10) {
                    $scope.day_front = "0";
                    $scope.day_last = day;
                } else if (day > 99) {
                    $scope.day_front = "9";
                    $scope.day_last = "9";
                } else {
                    $scope.day_last = day - ((Math.floor(day / 10)) * 10); //个位
                    $scope.day_front = (day - ((Math.floor(day / 100)) * 100) - $scope.day_last) / 10; //十位数
                }

                if (hour < 10) {
                    $scope.hour_front = "0";
                    $scope.hour_last = hour;
                } else {
                    $scope.hour_last = hour - ((Math.floor(hour / 10)) * 10); //个位
                    $scope.hour_front = (hour - ((Math.floor(hour / 100)) * 100) - $scope.hour_last) / 10; //十位数
                }

                if (minute < 10) {
                    $scope.minute_front = "0";
                    $scope.minute_last = minute;
                } else {
                    $scope.minute_last = minute - ((Math.floor(minute / 10)) * 10); //个位
                    $scope.minute_front = (minute - ((Math.floor(minute / 100)) * 100) - $scope.minute_last) / 10; //十位数
                }

                if (second < 10) {
                    $scope.second_front = "0";
                    $scope.second_last = second;
                } else {
                    $scope.second_last = second - ((Math.floor(second / 10)) * 10); //个位
                    $scope.second_front = (second - ((Math.floor(second / 100)) * 100) - $scope.second_last) / 10; //十位数
                }
            }
        }

        function _formatUrl(url) {
            var timestamp = Math.round(new Date().getTime() / 1000);

            if (-1 == url.indexOf('?')) {

                return url + '?v=' + timestamp;
            } else {
                return url + '&v=' + timestamp;
            }
        }

        function _touchProduct(product) {
            $state.go('product', {
                product: product.id,
            });
        }



        function _setupProductList() {
                $scope.products = null;
                $scope.isEmpty = false;
                $scope.isLoaded = false;

                _activeFetch(1, PER_PAGE);
        }

        function _activeFetch(page, perPage) {
            $scope.isLoading = true;
            API.product.list({                    
                    activity: $scope.activityId,
                    page: page,
                    per_page: PER_PAGE
                })
                .then(function(products) {
                    // if(products == ''){
                    //     $scope.toast('没有更多商品');
                    // }
                    $scope.products = $scope.products ? $scope.products.concat(products) : products;
                    $scope.isEmpty = ($scope.products && $scope.products.length) ? false : true;
                    $scope.isLoaded = true;
                    $scope.isLoading = false;
                    $scope.isLastPage = (products && products.length < perPage) ? !$scope.isEmpty : false;
                });
        }

        function _loadMore() {
            if ($scope.isLoading)
                return;
            if ($scope.isLastPage)
                return;

            if ($scope.products && $scope.products.length) {
                if ($scope.products.length % 10) {
                    return;
                }
                _activeFetch(($scope.products.length / PER_PAGE) + 1, PER_PAGE);
            } else {
                // _activeFetch(1, PER_PAGE);
            }
        }


        function _reload() {
            //微信jssdk分享
            // var option = {};
            // option.title = '闪购商城';
            // option.desc = '点击并关注公众号，注册新闪购会员，更多惊喜等着你';
            // //$rootScope.initConfig(option);
            _reloadActivityInfo();

        }

        _reload();

    }

})();