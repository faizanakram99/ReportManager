angular.module("reportsApp", ['moment-picker'])
    .controller("mainController", function ($scope, $http, $timeout, $window) {

        function url(action, date) {
            return "src/Reports/requestHandler.php?action="+action+"&date="+date;
        }

        $scope.report = {reportdetails : [{}] };
        $scope.currentdate = moment();
        $scope.refresh = true;

        $scope.renderEmailVisibility = function(){
            $scope.report.reportdetails.forEach(function (reportdetail, index) {
                $scope.showemail = false;
                if(!reportdetail.reportdetail_id) return;
                $scope.showemail = true;
            });
        };

        $scope.report.onChange = function (date) {

            //When date is changed
            if(date){
                $scope.report.date = moment($scope.currentdate).format("YYYY-MM-DD");

                if($scope.refresh){
                    $http.get(url('edit', $scope.report.date))
                        .then(function (response) {
                            $scope.report.login = response.data.login;
                            $scope.report.logout = response.data.logout;
                            $scope.report.reportdetails = response.data.reportdetails || [{}];
                            $scope.renderEmailVisibility();
                            $scope.report.onChange();
                        });
                }
            }

            // When login or logout time is changed.
            else {
                if($scope.report.login && $scope.report.logout){
                    $scope.workhours = $scope.report.login + " - " + $scope.report.logout;
                }
            }
        }


        $scope.report.onChange(true);

        $scope.add = function (index) {
            $scope.report.reportdetails.push([index].reportdetail);
            $scope.report.reportdetails[index + 1] = {};
            $scope.renderEmailVisibility();
        };

        $scope.remove = function (index) {
            let reportdetail_id = this.reportdetail.reportdetail_id || false;
            reportdetail_id ? $scope.action('delete', reportdetail_id) : $scope.report.reportdetails.splice(index, 1);
            $scope.renderEmailVisibility();
        };

        $scope.action = function (actiontype, reportdetail_id) {
            let data = JSON.stringify($scope.report);

            if(actiontype == 'delete'){
                let confirmation = confirm("Are you sure you want to delete this report" + ( reportdetail_id ? "line ?" : " ?"));
                if (confirmation){
                    data = reportdetail_id || false;
                }else{
                    return false;
                }
            }

            $http.post(url(actiontype, $scope.report.date), data).then(function (response) {
                if (response.status === 200) {
                    $scope.returnval = true;
                    alert(response.data);
                }
                $scope.report.onChange(true);
                $scope.renderEmailVisibility();
            });            
        };
                
        $scope.email = function(){
            let report = document.getElementById('imagecontainer').innerHTML;
            let currentdate = document.getElementById("currentdate").value;
            let data = JSON.stringify(report);

            $http.post(url('email', currentdate), data).then(function (response) {
                if (response.status === 200) alert(response.data);
            });
        }
    });