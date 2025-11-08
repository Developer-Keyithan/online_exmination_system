var app = angular.module('ngApp', []);
app.constant("API_URL", window.baseUrl);
app.constant("window", window);
app.constant("jQuery", window.jQuery);
app.config([
  "$httpProvider",
  function ($httpProvider) {
    $httpProvider.defaults.headers.post["Content-Type"] =
      "application/x-www-form-urlencoded; charset=UTF-8";
  },
]);
app.directive("bindHtmlCompile", [
  "$compile",
  function ($compile) {
    return {
      restrict: "A",
      link: function (scope, element, attrs) {
        scope.$watch(attrs.bindHtmlCompile, function (newValue) {
          if (newValue) {
            // Inject the new HTML and compile it within the current scope
            element.html(newValue);
            $compile(element.contents())(scope);
          }
        });
      },
    };
  },
]);
app.run([
  "$rootScope",
  function (
    $rootScope,
  ) {
  },
]);
