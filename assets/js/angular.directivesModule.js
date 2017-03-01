angular.module('directivesModule', [])
    .directive('contenteditable', function () {
        return {
            restrict: 'A', // only activate on element attribute
            require: '?ngModel', // get a hold of NgModelController
            link: function (scope, element, attrs, ngModel) {
                if (!ngModel) return; // do nothing if no ng-model

                // Specify how UI should be updated
                ngModel.$render = function () {
                    element.html(ngModel.$viewValue || '');
                };

                // Listen for change events to enable binding
                element.on('blur keyup change', function () {
                    scope.$apply(readViewText);
                });

                // No need to initialize, AngularJS will initialize the text based on ng-model attribute

                // Write data to the model
                function readViewText() {
                    var html = element.html();
                    // When we clear the content editable the browser leaves a <br> behind
                    // If strip-br attribute is provided then we strip this out
                    if (attrs.stripBr && html == '<br>') {
                        html = '';
                    }
                    ngModel.$setViewValue(html);
                }
            }
        };
    })
    .directive("durationPicker", function () {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function (scope, elm, attrs, ngModel) {
                var elem = angular.element(elm);
                attr = scope.$eval(attrs.durationPicker);
                elem.durationPicker({
                    hours: attr.hours,
                    minutes: attr.minutes,
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
                    if(newval) {
                        elem.prev().find(attr.hourField).val(newval.split("::")[0]);
                        elem.prev().find(attr.minuteField).val(newval.split("::")[1]);
                    }
                });

            }
        };
    });