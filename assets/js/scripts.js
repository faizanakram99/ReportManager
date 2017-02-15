angular.module("reportsApp", ['moment-picker'])
    .directive("durationPicker", function () {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, elm, attrs, ngModel) {
                var elem = angular.element(elm);
                elem.durationPicker({
                    hours: {label: ':', min: 0, max: 24, placeholder: 'HH'},
                    minutes: {label: '', min: 0, max: 59, placeholder: 'MM'},
                    classname: 'form-control ' + attrs.name,
                    type : 'text'
                });
                elem.prev().find('input').on("change", function () {
                    elem.trigger("change");
                });
                elem.on("change", function (e) {
                    scope.$apply();
                });
                scope.$watch(attrs.ngModel, function (newval) {
                    if(newval && !elem.prev().find("#duration-hours").val()) elem.prev().find("#duration-hours").val(newval.split("::")[0]);
                    if(newval && !elem.prev().find("#duration-minutes").val()) elem.prev().find("#duration-minutes").val(newval.split("::")[1]);
                })

            }
        }
    })
    .controller("mainController", function ($scope, $http, $timeout, $window) {
        $scope.report = {reportdetails : [{}] };
        $scope.currentdate = moment();

        $scope.report.onChange = function () {
            $scope.report.date = moment($scope.currentdate).format("YYYY-MM-DD");
            $http.get("app/requestHandler.php?action=edit&date=" + $scope.report.date)
                .then(function (response) {
                    $scope.report.login = response.data.login;
                    $scope.report.logout = response.data.logout;
                    $scope.report.reportdetails = response.data.reportdetails || [{}];
                });
        };

        $scope.report.onChange();

        $scope.add = function (index) {
            $scope.report.reportdetails.push([index].reportdetail);
            $scope.report.reportdetails[index + 1] = {};
        };

        $scope.remove = function (index) {
            var reportdetail_id = this.reportdetail.reportdetail_id || false;
            reportdetail_id ? $scope.action('delete', reportdetail_id) : $scope.report.reportdetails.splice(index, 1);
            $timeout(function(){
                $scope.returnval ? $scope.report.reportdetails.splice(index, 1) : '' ;
            }, 100);
        };

        $scope.action = function (actiontype, reportdetail_id) {
            var data = JSON.stringify($scope.report);

            if(actiontype == 'delete'){
                var confirmation = confirm("Are you sure you want to delete this report" + ( reportdetail_id ? "line ?" : " ?"));
                if (confirmation){
                    data = reportdetail_id || false;
                }else{
                    return false;
                }
            }
            var url = "app/requestHandler.php?action=" + actiontype + "&date=" + $scope.report.date;
            $http.post(url, data).then(function (response) {
                if (response.status == 200) {
                    $scope.returnval = true;
                    alert(response.data);
                    if(actiontype == "delete" && !reportdetail_id){
                        $scope.report = { reportdetails: [{}] }; 
                        angular.element(".durationpicker-duration").val('');
                    }
                }
            });            
        };

        $scope.preview = function(){
            $window.location = "/app/preview.php";
        }
    });