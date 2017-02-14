angular.module("reportsApp", ['moment-picker'])
    .directive("durationPicker", function () {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, elm, attrs, ngModel) {
                var elem = angular.element(elm);
                elem.durationPicker({
                    hours: {label: 'h', min: 0, max: 24},
                    minutes: {label: 'm', min: 0, max: 24},
                    classname: 'form-control ' + attrs.name
                });
                elem.prev().find('input').on("change", function () {
                    elem.trigger("change");
                });
                elem.on("change", function (e) {
                    scope.$apply();
                });
                scope.$watch(attrs.ngModel, function (newval) {
                    if(newval && !elem.prev().find("#duration-hours").val()) elem.prev().find("#duration-hours").val(newval.slice(0,1));
                    if(newval && !elem.prev().find("#duration-minutes").val()) elem.prev().find("#duration-minutes").val(newval.slice(3,5).match(/\d+/));
                })

            }
        }
    })
    .controller("mainController", function ($scope, $http) {
        $scope.report = {};

        $scope.currentdate = moment();
        $scope.report.reportdetails = [];
        $scope.report.reportdetails[0] = {};

        $scope.report.onChange = function () {
            $scope.report.date = moment($scope.currentdate).format("YYYY-MM-DD");
            $http.get("app/requestHandler.php?action=edit&date=" + $scope.report.date)
                .then(function (response) {
                    $scope.report.login = response.data.login;
                    $scope.report.logout = response.data.logout;
                    $scope.report.reportdetails = response.data.reportdetails ? response.data.reportdetails : [{}];
                });
        };

        $scope.add = function (index) {
            $scope.report.reportdetails.push([index].reportdetail);
            $scope.report.reportdetails[index + 1] = {};
        };

        $scope.remove = function (index) {
            $scope.report.reportdetails.splice(index, 1);
        };

        $scope.action = function (actiontype) {
            var data = JSON.stringify($scope.report);
            var url = "app/requestHandler.php?action=" + actiontype + "&date=" + $scope.report.date;

            $http.post(url, data).then(function (response) {
                if (response.status = 200) alert(response.data);
            });
        };
    });