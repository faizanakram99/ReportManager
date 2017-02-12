angular.module("reportsApp",[])
    .controller("mainController", function($scope, $http){
        $scope.report = {};
        $scope.report.date = new Date();
        $scope.report.reportdetails = [];
        $scope.report.reportdetails[0] = {};

        $scope.action = function(actiontype){
            if(actiontype == 'add'){
                $scope.report.reportdetails.push(this);
            }
            else if(actiontype == 'remove'){
                 $scope.report.reportdetails.pop(this);
            }
        };
        
    });