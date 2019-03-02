(function () {

	'use strict';

	angular
		.module('app')
		.controller('ActivityController', ActivityController);

	ActivityController.$inject = ['$scope', '$http', '$location', '$stateParams', '$state', 'ActivityModel', 'ENUM'];

	function ActivityController($scope, $http, $location, $stateParams, $state, ActivityModel, ENUM) {

		$scope.activityModel = ActivityModel;
		$scope.touchActivity = _touchActivity;

		$scope.activityModel.reload();
		function _touchActivity(activity) {
            $state.go('activity-detail', {
            	activity: activity.id
            });
        }
	}

})();