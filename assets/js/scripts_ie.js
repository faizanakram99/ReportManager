'use strict';

angular.module("reportsApp", ['ngResource', 'moment-picker']).controller("mainController", function ($scope, $resource, $timeout, $window) {
    $scope.report = { reportdetails: [{}] };
    $scope.currentdate = moment();
    $scope.refresh = true;

    var Report = $resource('/web/app.php/:date', { date: '@date' });
    var getReport = function getReport() {
        return Report.get({ date: $scope.date }, function (report) {
            return $scope.report = report;
        });
    };

    $scope.$watch('currentdate', function (newval) {
        $scope.date = moment(newval).format("YYYY-MM-DD");
        $scope.dateText = moment(newval).format("MMM Do YYYY");
        if ($scope.refresh) getReport();
    });

    $scope.toggleEmail = function () {
        return !($scope.report.reportdetails && $scope.report.reportdetails.find(function (x) {
            return x && !x.id;
        }));
    };

    $scope.$watch('report.login + report.logout', function () {
        return $scope.workhours = $scope.report.login + ' - ' + $scope.report.logout;
    });

    $scope.add = function (index) {
        $scope.report.reportdetails.push({});
        $scope.toggleEmail();
    };

    $scope.remove = function (index) {
        $scope.report.reportdetails.splice(index, 1);
        $scope.toggleEmail();
    };

    $scope.save = function () {
        Report.save({ date: $scope.date }, JSON.stringify($scope.report), function (response) {
            getReport();
            alert(response.message);
        }, function (error) {
            return console.log(error);
        });
    };

    $scope.delete = function () {
        Report.delete({ date: $scope.date }, function (response) {
            getReport();
            alert(response.message);
        }, function (error) {
            return console.log(error);
        });
    };

    $scope.email = function () {
        Report.get({ date: $scope.date, email: true }, function (response) {
            return alert(response.message);
        }, function (error) {
            return console.log(error);
        });
    };
});