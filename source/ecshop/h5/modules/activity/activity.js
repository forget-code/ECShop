(function () {

	'use strict';

	angular
		.module('app')
		.config(config);

	config.$inject = ['$stateProvider', '$urlRouterProvider'];

	function config($stateProvider, $urlRouterProvider) {

		$stateProvider
			.state('activity', {
				needAuth: false,
				url: '/activity',
				title: "优惠活动",
				templateUrl: 'modules/activity/activity.html',
			});

	}

})();