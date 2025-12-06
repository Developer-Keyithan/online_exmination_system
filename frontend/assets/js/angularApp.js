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
    return window.formatDecimal(value, limit);
  };
});
app.filter("formatNIC", function () {
  return function (value) {
    if (!value) return "";
    let number = value.toString().toUpperCase().trim();
    if (/^\d{9}[VX]$/.test(number)) {
      return number.substring(0, 4) + ' ' + number.substring(4, 8) + ' ' + number.substring(8);
    }
    else if (/^\d{12}$/.test(number)) {
      return number.substring(0, 4) + ' ' + number.substring(4, 8) + ' ' + number.substring(8, 12);
    }
    else {
      return value;
    }
  };
});
// app.filter('formatDateTime', function() {
//     return function(datetimeStr) {
//         if (!datetimeStr) return '';
//         let dt = new Date(datetimeStr);
//         if (isNaN(dt)) return datetimeStr; // invalid date fallback

//         let day = String(dt.getDate()).padStart(2, '0');
//         let month = String(dt.getMonth() + 1).padStart(2, '0'); // month starts from 0
//         let year = dt.getFullYear();

//         let hours = dt.getHours();
//         let minutes = String(dt.getMinutes()).padStart(2, '0');
//         let ampm = hours >= 12 ? 'PM' : 'AM';
//         hours = hours % 12;
//         hours = hours ? hours : 12; // 0 -> 12

//         return `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;
//     };
// });

app.filter('formatDateTime', function () {
  return function (datetimeStr, format = 'DD/MM/YYYY HH:mm:ss') {
    if (!datetimeStr) return '';
    var dt = new Date(datetimeStr);
    if (isNaN(dt)) return datetimeStr;

    var day = String(dt.getDate()).padStart(2, '0');
    var monthNum = String(dt.getMonth() + 1).padStart(2, '0');
    var year = dt.getFullYear();

    var hours24 = dt.getHours();
    var hours12 = hours24 % 12 || 12; // 0 => 12
    var minutes = String(dt.getMinutes()).padStart(2, '0');
    var seconds = String(dt.getSeconds()).padStart(2, '0');
    var ampm = hours24 >= 12 ? 'PM' : 'AM';
    var hours12Str = String(hours12).padStart(2, '0');
    var hours24Str = String(hours24).padStart(2, '0');

    // Month names
    var shortMonthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var fullMonthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var shortMonth = shortMonthNames[dt.getMonth()];
    var fullMonth = fullMonthNames[dt.getMonth()];

    // Replace longer tokens first
    return format
      .replace('MMMM', fullMonth)       // full month name
      .replace('MMM', shortMonth)       // short month name
      .replace('MM', monthNum)          // numeric month
      .replace('DD', day)
      .replace('YYYY', year)
      .replace('HH', hours24Str)        // 24-hour
      .replace('hh', hours12Str)        // 12-hour
      .replace('mm', minutes)
      .replace('ss', seconds)
      .replace('A', ampm);              // AM/PM
  };
});
app.filter('fromNow', function () {
  return function (datetimeStr) {
    if (!datetimeStr) return '';
    var now = new Date();
    var dt = new Date(datetimeStr);
    if (isNaN(dt)) return datetimeStr;

    var diff = now - dt; // in ms
    var seconds = Math.floor(diff / 1000);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);
    var weeks = Math.floor(days / 7);
    var months = Math.floor(days / 30);
    var years = Math.floor(days / 365);

    if (seconds < 60) return seconds + ' sec ago';
    if (minutes < 60) return minutes + ' min ago';
    if (hours < 24) return hours + ` hour${hours > 1 ? 's' : ''} ago`;
    if (days < 7) return days + `day${days > 1 ? 's' : ''} ago`;
    if (weeks < 5) return weeks + `week${weeks > 1 ? 's' : ''} ago`;
    if (months < 12) return months + `month${months > 1 ? 's' : ''} ago`;
    return years + `year${years > 1 ? 's' : ''} ago`;
  };
});
app.filter('remainingTime', function () {
  return function (datetimeStr) {
    if (!datetimeStr) return '';
    var now = new Date();
    var dt = new Date(datetimeStr);
    if (isNaN(dt)) return datetimeStr;

    var diff = dt - now; // future time
    if (diff <= 0) return 'Time passed';

    var seconds = Math.floor(diff / 1000);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);

    if (seconds < 60) return seconds + ' sec remaining';
    if (minutes < 60) return minutes + ' min remaining';
    if (hours < 24) return hours + ` hour${hours > 1 ? 's' : ''} remaining`;
    return days + ` day${days > 1 ? 's' : ''} remaining`;
  };
});
// .filter('formatDateTime', function () {
//   return function (dateString) {
//     if (!dateString) return '';
//     const date = new Date(dateString);
//     return date.toLocaleString('en-US', {
//       year: 'numeric',
//       month: 'short',
//       day: 'numeric',
//       hour: '2-digit',
//       minute: '2-digit'
//     });
//   };
// })
app.filter('letterIndex', function () {
  return function (index) {
    return String.fromCharCode(65 + index); // A, B, C, D...
  };
})
app.run([
  "$rootScope",
  function ($rootScope) {
  },
]);