angular.module("reportsApp", ['ngResource', 'moment-picker'])
    .controller("mainController", function ($scope, $resource, $timeout, $window) {
        $scope.report = {reportdetails: [{}]};
        $scope.currentdate = moment();
        $scope.refresh = true;

        const Report = $resource('/web/app.php/:date', {date: '@date'});
        const getReport = () => Report.get({date: $scope.date});

        $scope.changeDate = () => {
            $scope.date = moment($scope.currentdate).format("YYYY-MM-DD");
            $scope.dateText = moment($scope.currentdate).format("MMM Do YYYY");
            if($scope.refresh) $scope.report = getReport();
        }

        $scope.changeDate();

        $scope.toggleEmail = () => !($scope.report.reportdetails && $scope.report.reportdetails.find(x => x && !x.id));

        $scope.add = (index) => {
            $scope.report.reportdetails.push({});
            $scope.toggleEmail();
        };

        $scope.remove = (index) => {
            $scope.report.reportdetails.splice(index, 1);
            $scope.toggleEmail();
        };

        $scope.save = () => {
            Report.save({date: $scope.date}, JSON.stringify($scope.report), (response) => {
                $scope.report = getReport();
                alert(response.message);
            }, (error) => console.log(error));
        };

        $scope.delete = () => {
            Report.delete({date: $scope.date}, (response) => {
                $scope.report = getReport();
                alert(response.message);
            }, (error) => console.log(error));
        };

        $scope.email = () => {
            Report.get({date: $scope.date, email: true}, (response) => alert(response.message), (error) => console.log(error));
        }
    });