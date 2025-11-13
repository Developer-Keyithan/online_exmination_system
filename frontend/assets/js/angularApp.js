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
app.filter("formatDecimal", function () {
  return function (value, limit) {
    // if (!value) return "0.00";
    return window.formatDecimal(value, limit);
  };
});
app.filter("formatNIC", function () {
  return function (value) {
    if (!value) return "";

    // Remove spaces and make uppercase
    let number = value.toString().toUpperCase().trim();

    // Old NIC (9 digits + V/X)
    if (/^\d{9}[VX]$/.test(number)) {
      return number.substring(0, 4) + ' ' + number.substring(4, 8) + ' ' + number.substring(8);
    }
    // New NIC (12 digits)
    else if (/^\d{12}$/.test(number)) {
      return number.substring(0, 4) + ' ' + number.substring(4, 8) + ' ' + number.substring(8, 12);
    }
    // Invalid - return original
    else {
      return value;
    }
  };
});
app.run([
  "$rootScope",
  function (
    $rootScope,
  ) {
  },
]);
