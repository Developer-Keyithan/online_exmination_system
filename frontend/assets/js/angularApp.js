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
app.filter('formatMobileNumber', function () {
  return function (number) {
    if (!number) return '';

    // Remove non-digits
    number = number.toString().replace(/\D/g, '');

    // Remove leading 0 if length is 10
    if (number.length === 10 && number.startsWith('0')) {
      number = number.slice(1); // Remove first 0 → length 9
    }

    // Format only if length is 9
    if (number.length === 9) {
      return '+94 ' + number.slice(0, 3) + ' ' + number.slice(3, 6) + ' ' + number.slice(6);
    }

    // fallback
    return number;
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
  return function (input, format) {
    format = format || 'DD/MM/YYYY HH:mm:ss';
    if (!input) return '';

    // Normalize input to string
    var s = String(input).trim();

    // If already looks like "hh:mm am/pm" and format requests only time, try to return normalized
    var ampmMatch = s.match(/^\s*(\d{1,2}):(\d{2})(?::(\d{2}))?\s*(am|pm|AM|PM)\s*$/);
    if (ampmMatch) {
      // convert to 24-hour Date object using today as date
      var hh = parseInt(ampmMatch[1], 10);
      var mm = parseInt(ampmMatch[2], 10);
      var ss = ampmMatch[3] ? parseInt(ampmMatch[3], 10) : 0;
      var ampm = ampmMatch[4].toLowerCase();
      if (ampm === 'pm' && hh < 12) hh += 12;
      if (ampm === 'am' && hh === 12) hh = 0;
      var dt = new Date();
      dt.setHours(hh, mm, ss, 0);
      return formatDate(dt, format);
    }

    // Time-only like "10:30" or "10:30:00"
    if (/^\d{1,2}:\d{2}(:\d{2})?$/.test(s)) {
      var parts = s.split(':');
      var hh = parseInt(parts[0], 10);
      var mm = parseInt(parts[1], 10);
      var ss = parts[2] ? parseInt(parts[2], 10) : 0;
      var dt = new Date();
      dt.setHours(hh, mm, ss, 0);
      return formatDate(dt, format);
    }

    // If format "YYYY-MM-DD HH:MM:SS" convert space to T for safer parsing
    var isoCand = s;
    if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/.test(s)) {
      isoCand = s.replace(' ', 'T');
    }

    var dt = new Date(isoCand);

    // If parsing failed, attempt fallback parsing for other common formats (DD/MM/YYYY HH:MM)
    if (isNaN(dt.getTime())) {
      // try DD/MM/YYYY HH:MM(:SS)?
      var alt = s.match(/^(\d{2})\/(\d{2})\/(\d{4})\s+(\d{1,2}):(\d{2})(?::(\d{2}))?$/);
      if (alt) {
        var day = parseInt(alt[1], 10);
        var mon = parseInt(alt[2], 10) - 1;
        var yr = parseInt(alt[3], 10);
        var hh = parseInt(alt[4], 10);
        var mm = parseInt(alt[5], 10);
        var ss = alt[6] ? parseInt(alt[6], 10) : 0;
        dt = new Date(yr, mon, day, hh, mm, ss);
      }
    }

    if (isNaN(dt.getTime())) {
      // give up, return original input so user can see it
      return input;
    }

    return formatDate(dt, format);


    // ---------- helpers ----------
    function pad(n) { return String(n).padStart(2, '0'); }

    function formatDate(dt, fmt) {
      var day = pad(dt.getDate());
      var month = pad(dt.getMonth() + 1);
      var year = String(dt.getFullYear());
      var H = dt.getHours();
      var hh = pad(H % 12 || 12);
      var HH = pad(H);
      var mm = pad(dt.getMinutes());
      var ss = pad(dt.getSeconds());
      var A = H >= 12 ? 'PM' : 'AM';
      var a = H >= 12 ? 'pm' : 'am';

      var shortMonthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
      var fullMonthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      var MMM = shortMonthNames[dt.getMonth()];
      var MMMM = fullMonthNames[dt.getMonth()];

      // token map (longest keys first)
      var map = {
        'MMMM': MMMM,
        'MMM': MMM,
        'MM': month,
        'DD': day,
        'YYYY': year,
        'HH': HH,
        'hh': hh,
        'mm': mm,
        'ss': ss,
        'A': A,
        'a': a
      };

      var tokens = Object.keys(map).sort(function (a, b) { return b.length - a.length; });
      var out = fmt;
      tokens.forEach(function (tok) {
        out = out.replace(new RegExp(tok, 'g'), map[tok]);
      });
      return out;
    }
  };
});
app.filter('fromNow', function () {
  return function (datetimeStr) {
    if (!datetimeStr) return '';

    var now = new Date();
    var dt = new Date(datetimeStr);
    if (isNaN(dt)) return datetimeStr;

    var diff = dt - now;           // future >0, past <0
    var absDiff = Math.abs(diff);

    var seconds = Math.floor(absDiff / 1000);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);
    var weeks = Math.floor(days / 7);
    var months = Math.floor(days / 30);
    var years = Math.floor(days / 365);

    var text = '';

    // ------------------ FUTURE ------------------
    if (diff > 0) {
      if (days === 0 && hours >= 1) {           // 1–24 hours remaining
        text = 'today, in ' + hours + ' hour' + (hours > 1 ? 's' : '');
      } else if (days === 0) {                  // <1 hour
        if (minutes < 1) text = 'today, in a few seconds';
        else text = 'today, in ' + minutes + ' min';
      } else if (days === 1) text = 'tomorrow';
      else if (days < 7) text = 'in ' + days + ' days';
      else if (weeks < 5) text = 'in ' + weeks + ' week' + (weeks > 1 ? 's' : '');
      else if (months < 12) text = 'in ' + months + ' month' + (months > 1 ? 's' : '');
      else text = 'in ' + years + ' year' + (years > 1 ? 's' : '');
    }
    // ------------------ PAST ------------------
    else {
      if (days === 0 && hours >= 1) {           // 1–24 hours ago
        text = 'today, ' + hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
      } else if (days === 0) {                  // <1 hour
        if (minutes < 1) text = 'today, just now';
        else text = 'today, ' + minutes + ' min ago';
      } else if (days === 1) text = 'yesterday';
      else if (days < 7) text = days + ' days ago';
      else if (weeks < 5) text = weeks + ' week' + (weeks > 1 ? 's' : '') + ' ago';
      else if (months < 12) text = months + ' month' + (months > 1 ? 's' : '') + ' ago';
      else text = years + ' year' + (years > 1 ? 's' : '') + ' ago';
    }

    return text;
  };
});
app.filter('fromNowDate', function () {
  return function (datetimeStr) {
    if (!datetimeStr) return '';

    var now = new Date();
    var dt = new Date(datetimeStr);
    if (isNaN(dt)) return datetimeStr;

    var diff = dt - now;
    var days = Math.floor(Math.abs(diff) / (1000 * 60 * 60 * 24));

    if (diff >= 0) { // future
      if (days === 0) return 'today';
      if (days === 1) return 'tomorrow';
      if (days < 7) return 'in ' + days + ' days';
      return dt.toLocaleDateString();
    } else { // past
      if (days === 0) return 'today';
      if (days === 1) return 'yesterday';
      if (days < 7) return days + ' days ago';
      return dt.toLocaleDateString();
    }
  };
});
app.filter('fromNowTime', function () {
  return function (datetimeStr) {
    if (!datetimeStr) return '';

    var now = new Date();
    var dt = new Date(datetimeStr);
    if (isNaN(dt)) return datetimeStr;

    var diff = dt - now;           // future >0, past <0
    var absDiff = Math.abs(diff);

    var seconds = Math.floor(absDiff / 1000);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);
    var weeks = Math.floor(days / 7);
    var months = Math.floor(days / 30);
    var years = Math.floor(days / 365);

    var text = '';

    // ------------------ FUTURE ------------------
    if (diff > 0) {
      if (days === 0 && hours >= 1) {           // 1–24 hours remaining
        text = 'today, in ' + hours + ' hour' + (hours > 1 ? 's' : '');
      } else if (days === 0) {                  // <1 hour
        if (minutes < 1) text = 'today, in a few seconds';
        else text = 'today, in ' + minutes + ' min';
      } else if (days === 1) text = 'tomorrow';
      else if (days < 7) text = 'in ' + days + ' days';
      else if (weeks < 5) text = 'in ' + weeks + ' week' + (weeks > 1 ? 's' : '');
      else if (months < 12) text = 'in ' + months + ' month' + (months > 1 ? 's' : '');
      else text = 'in ' + years + ' year' + (years > 1 ? 's' : '');
    }
    // ------------------ PAST ------------------
    else {
      if (days === 0 && hours >= 1) {           // 1–24 hours ago
        text = 'today, ' + hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
      } else if (days === 0) {                  // <1 hour
        if (minutes < 1) text = 'today, just now';
        else text = 'today, ' + minutes + ' min ago';
      } else if (days === 1) text = 'yesterday';
      else if (days < 7) text = days + ' days ago';
      else if (weeks < 5) text = weeks + ' week' + (weeks > 1 ? 's' : '') + ' ago';
      else if (months < 12) text = months + ' month' + (months > 1 ? 's' : '') + ' ago';
      else text = years + ' year' + (years > 1 ? 's' : '') + ' ago';
    }

    return text;
  };
});
app.filter('fromNowHoursOnly', function () {
  return function (datetimeStr) {
    if (!datetimeStr) return '';

    var now = new Date();
    var dt = new Date(datetimeStr);
    if (isNaN(dt)) return datetimeStr;

    var diff = dt - now;            // future >0, past <0
    var absDiff = Math.abs(diff);

    var minutes = Math.floor(absDiff / (1000 * 60));
    var hours   = Math.floor(minutes / 60);
    var days    = Math.floor(hours / 24);

    var text = '';

    if (days === 0) { // same day
      if (hours >= 1) {  // 1–24 hours
        if (diff > 0) text = 'today, in ' + hours + ' hour' + (hours > 1 ? 's' : '');
        else text = 'today, ' + hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
      } else {           // <1 hour
        if (diff > 0) text = 'today, in ' + minutes + ' min';
        else text = 'today, ' + minutes + ' min ago';
      }
    } else {           // >24 hours
      if (diff > 0) {  // future
        if (days === 1) text = 'tomorrow';
        else if (days < 7) text = 'in ' + days + ' days';
        else text = dt.toLocaleDateString();
      } else {          // past
        if (days === 1) text = 'yesterday';
        else if (days < 7) text = days + ' days ago';
        else text = dt.toLocaleDateString();
      }
    }

    return text;
  };
});
app.directive('liveFromNow', ['$interval', '$filter', function ($interval, $filter) {
  return {
    restrict: 'A',
    scope: {
      datetime: '@liveFromNow'
    },
    link: function (scope, element) {
      function updateTime() {
        element.text($filter('fromNow')(scope.datetime));
      }
      updateTime();
      var intervalPromise = $interval(updateTime, 1000);
      element.on('$destroy', function () {
        $interval.cancel(intervalPromise);
      });
    }
  };
}]);

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
app.filter('formatTime', function () {
  return function (time) {
    if (typeof time !== 'number') return '00:00:00';
    const hours = Math.floor(time / 3600);
    const minutes = Math.floor((time % 3600) / 60);
    const seconds = time % 60;

    return hours.toString().padStart(2, '0') + ':' +
      minutes.toString().padStart(2, '0') + ':' +
      seconds.toString().padStart(2, '0');
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
  return function (index, mode) {
    if (mode === 'A') {
      return String.fromCharCode(65 + index);
    } else if (mode === 'a') {
      return String.fromCharCode(97 + index);
    } else {
      return index + 1;
    }
  };
});
app.filter('safeHtml', ['$sce', function ($sce) {
  return function (html) {
    return $sce.trustAsHtml(html);
  };
}]);

app.run([
  "$rootScope",
  function ($rootScope) {
  },
]);