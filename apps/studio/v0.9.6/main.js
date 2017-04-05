/*! Studio-v2 - v2.0.0 - 2014-04-03
* https://github.com/kaltura/player-studio
* Copyright (c) 2014 Kaltura */
'use strict';
var cl = function (val) {
  return console.log(val);
};
var KMCModule = angular.module('KMCModule', [
    'pascalprecht.translate',
    'ngRoute',
    'KMC.controllers',
    'KMC.filters',
    'KMC.services',
    'KMC.directives',
    'ngAnimate',
    'LocalStorageModule',
    'KMC.menu'
  ]);
KMCModule.config([
  '$routeProvider',
  '$locationProvider',
  '$httpProvider',
  '$tooltipProvider',
  '$translateProvider',
  function ($routeProvider, $locationProvider, $httpProvider, $tooltipProvider, $translateProvider) {
    $translateProvider.useStaticFilesLoader({
      prefix: 'i18n/',
      suffix: '.json'
    });
    $translateProvider.preferredLanguage('en_US');
    $translateProvider.fallbackLanguage('en_US');
    if (window.location.href.indexOf('debug') != -1) {
    }
    $translateProvider.useStorage('localStorageService');
    $tooltipProvider.options({
      placement: 'right',
      'appendToBody': true,
      'popupDelay': 800
    });
    $tooltipProvider.setTriggers({ 'customShow': 'customShow' });
    $httpProvider.defaults.useXDomain = true;
    delete $httpProvider.defaults.headers.common['X-Requested-With'];
    var $http, interceptor = [
        '$q',
        '$injector',
        function ($q, $injector) {
          var notificationChannel;
          function success(response) {
            $http = $http || $injector.get('$http');
            if ($http.pendingRequests.length < 1) {
              notificationChannel = notificationChannel || $injector.get('requestNotificationChannel');
              notificationChannel.requestEnded();
            }
            return response;
          }
          function error(response) {
            logTime('httpRequest failed -');
            $http = $http || $injector.get('$http');
            if ($http.pendingRequests.length < 1) {
              notificationChannel = notificationChannel || $injector.get('requestNotificationChannel');
              notificationChannel.requestEnded();
            }
            return $q.reject(response);
          }
          return function (promise) {
            notificationChannel = notificationChannel || $injector.get('requestNotificationChannel');
            notificationChannel.requestStarted();
            return promise.then(success, error);
          };
        }
      ];
    $httpProvider.responseInterceptors.push(interceptor);
    $routeProvider.when('/login', {
      templateUrl: 'view/login.html',
      controller: 'LoginCtrl',
      resolve: {
        'apiService': [
          'api',
          'apiService',
          function (api, apiService) {
            return apiService;
          }
        ]
      }
    });
    $routeProvider.when('/list', {
      templateUrl: 'view/list.html',
      controller: 'PlayerListCtrl',
      resolve: {
        'apiService': [
          'api',
          'apiService',
          'localStorageService',
          '$location',
          function (api, apiService, localStorageService, $location) {
            return ksCheck(api, apiService, localStorageService, $location).then(function () {
              return apiService;
            });
          }
        ]
      }
    });
    var ksCheck = function (api, apiService, localStorageService, $location) {
      try {
        var kmc = window.parent.kmc;
        if (kmc && kmc.vars) {
          if (kmc.vars.ks)
            localStorageService.add('ks', kmc.vars.ks);
        }
      } catch (e) {
        cl('Could not located parent.kmc: ' + e);
      }
      var ks = localStorageService.get('ks');
      if (!ks) {
        $location.path('/login');
        return false;
      } else {
        api.then(function () {
          apiService.setKs(ks);
        });
      }
      return api;
    };
    $routeProvider.when('/edit/:id', {
      templateUrl: 'view/edit.html',
      controller: 'PlayerEditCtrl',
      reloadOnSearch: false,
      resolve: {
        'PlayerData': [
          'PlayerService',
          '$route',
          'api',
          'apiService',
          'localStorageService',
          '$location',
          function (PlayerService, $route, api, apiService, localStorageService, $location) {
            var apiLoaded = ksCheck(api, apiService, localStorageService, $location);
            if (apiLoaded) {
              return apiLoaded.then(function (api) {
                return PlayerService.getPlayer($route.current.params.id);
              });
            }
          }
        ],
        'editProperties': 'editableProperties',
        'menuSvc': 'menuSvc'
      }
    });
    $routeProvider.when('/newByTemplate', {
      templateUrl: 'view/new-template.html',
      controller: 'PlayerCreateCtrl',
      reloadOnSearch: false,
      resolve: {
        'templates': [
          'playerTemplates',
          function (playerTemplates) {
            return playerTemplates.listSystem();
          }
        ],
        'userId': function () {
          return '1';
        }
      }
    });
    $routeProvider.when('/new', {
      templateUrl: 'view/edit.html',
      controller: 'PlayerEditCtrl',
      reloadOnSearch: false,
      resolve: {
        'api': [
          'api',
          'apiService',
          'localStorageService',
          '$location',
          function (api, apiService, localStorageService, $location) {
            return ksCheck(api, apiService, localStorageService, $location);
          }
        ],
        'PlayerData': function (api, PlayerService) {
          return api.then(function () {
            return PlayerService.newPlayer();
          });
        },
        'menuSvc': 'menuSvc'
      }
    });
    $routeProvider.when('/logout', {
      resolve: {
        'logout': [
          'localStorageService',
          'apiService',
          '$location',
          function (localStorageService, apiService, $location) {
            if (localStorageService.isSupported()) {
              localStorageService.clearAll();
            }
            apiService.unSetks();
            $location.path('/login');
          }
        ]
      }
    });
    $routeProvider.otherwise({
      resolve: {
        'res': [
          'api',
          'apiService',
          'localStorageService',
          '$location',
          function (api, apiService, localStorageService, $location) {
            if (ksCheck(api, apiService, localStorageService, $location)) {
              return $location.path('/list');
            }
          }
        ]
      }
    });
  }
]).run(function ($rootScope, $rootElement, $location, menuSvc) {
  var appLoad = new Date();
  var debug = false;
  setTimeout(function () {
    window.localStorage.setItem('updateHash', 'true');
  }, 1000);
  if (typeof window.parent.kmc != 'undefined') {
    $('html').addClass('inKmc');
  }
  var logTime = function (eventName) {
    if ($location.search()['debug']) {
      var now = new Date();
      var diff = Math.abs(appLoad.getTime() - now.getTime());
      cl(eventName + ' ' + Math.ceil(diff / 1000) + 'sec ' + diff % 1000 + 'ms');
    }
  };
  window.logTime = logTime;
  logTime('AppJsLoad');
  $rootScope.constructor.prototype.$safeApply = function (fn) {
    var phase = this.$root.$$phase;
    if (phase == '$apply' || phase == '$digest')
      this.$eval(fn);
    else
      this.$apply(fn);
  };
  $rootScope.constructor.prototype.openTooltip = function ($event) {
    if (menuSvc.currentTooltip == $event.target)
      return;
    menuSvc.currentTooltip = $event.target;
    $($event.target).trigger('customShow');
    $event.preventDefault();
    $event.stopPropagation();
    return false;
  };
  $rootScope.routeName = '';
  $rootScope.$on('$routeChangeSuccess', function () {
    appLoad = new Date();
    var url = $location.url().split('/');
    if (debug) {
      $location.search({ debug: true });
    }
    if (url[1].indexOf('?') != -1) {
      url[1] = url[1].substr(0, url[1].indexOf('?'));
    }
    $rootScope.routeName = url[1];
  });
  $rootScope.$on('$routeChangeStart', function () {
    if ($location.search()['debug']) {
      debug = true;
    } else {
      debug = false;
    }
  });
  var kmc = window.parent.kmc;
  if (kmc && kmc.vars.studio.showFlashStudio === false) {
    $('#flashStudioBtn').hide();
  }
  if (kmc && kmc.vars.studio.showHTMLStudio === false) {
    $('#htmlStudioBtn').hide();
  }
});
'use strict';
var DirectivesModule = angular.module('KMC.directives', [
    'colorpicker.module',
    'ui.bootstrap',
    'ui.select2',
    'ui.sortable'
  ]);
DirectivesModule.directive('timeago', [function () {
    return {
      restrict: 'CA',
      link: function (scope, iElement, iAttrs) {
        if (typeof $.timeago == 'function') {
          scope.$observe('timeago', function (newVal) {
            if (newVal && !isNaN(newVal)) {
              var date = scope.timestamp * 1000;
              iElement.text($.timeago(date));
            }
          });
        }
      }
    };
  }]);
DirectivesModule.directive('hiddenValue', [function () {
    return {
      template: '<input type="hidden" value="{{model}}"/>',
      scope: { model: '=' },
      controller: function ($scope, $element, $attrs) {
        if ($attrs['initvalue']) {
          $scope.model = $attrs['initvalue'];
        }
      },
      restrict: 'EA'
    };
  }]);
DirectivesModule.directive('modelRadio', [
  'menuSvc',
  function (menuSvc) {
    return {
      restrict: 'EA',
      replace: true,
      require: '?playerRefresh',
      templateUrl: 'template/formcontrols/modelRadio.html',
      scope: {
        'model': '=',
        'strModel': '@model',
        'label': '@',
        'helpnote': '@',
        'require': '@'
      },
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          var menuData = menuSvc.getControlData($attrs.model);
          $scope.options = menuData.options;
          var ngModelCntrl;
          var controls = [];
          return {
            setChoice: function (value) {
              angular.forEach(controls, function (control) {
                control.setValue(value);
              });
            },
            registerControl: function (cntrl) {
              controls.push(cntrl);
            },
            getValue: function () {
              return $scope.model;
            },
            regContoller: function (cntrl) {
              if (!ngModelCntrl)
                menuSvc.menuScope.playerEdit.$addControl(cntrl);
              ngModelCntrl = cntrl;
            },
            isRequired: $attrs.require
          };
        }
      ],
      link: function (scope, element, attributes, prController) {
        if (prController) {
          prController.setValueBased();
        }
        if (scope.require) {
          scope.$watch('model', function (newval) {
            if (!newval)
              $(element).find('.form-group').addClass('ng-invalid');
            else {
              $(element).find('.form-group').removeClass('ng-invalid');
            }
          });
        }
      }
    };
  }
]);
;
DirectivesModule.directive('dname', [
  'menuSvc',
  function (menuSvc) {
    return {
      require: '?ngModel',
      priority: 100,
      link: function ($scope, $element, $attrs, $ngModelCntrl) {
        if ($ngModelCntrl) {
          var dname = $scope.$eval($attrs['dname']);
          $element.attr('name', dname);
          if ($ngModelCntrl) {
            $ngModelCntrl.$name = dname;
            menuSvc.menuScope.playerEdit.$addControl($ngModelCntrl);
          }
        }
      }
    };
  }
]);
DirectivesModule.directive('valType', function () {
  return {
    restrict: 'A',
    link: function ($scope, $element, $attrs) {
      if (($attrs['valType'] == 'url' || $attrs['valType'] == 'email') && window.IE != 8) {
        $element.attr('type', $attrs['valType']);
      }
    }
  };
});
DirectivesModule.directive('modelEdit', [
  '$modal',
  function ($modal) {
    var modalEditCntrl = [
        '$scope',
        function ($scope) {
          if (typeof $scope.model == 'undefined')
            $scope.model = '';
          $scope.modelValue = $scope.model;
        }
      ];
    return {
      replace: true,
      restrict: 'EA',
      scope: {
        'label': '@',
        'helpnote': '@',
        'model': '=',
        'icon': '@',
        'require': '@',
        'strModel': '=model'
      },
      controller: modalEditCntrl,
      templateUrl: 'template/formcontrols/modelEdit.html',
      compile: function (tElement, tAttr) {
        if (tAttr['endline'] == 'true') {
          tElement.append('<hr/>');
        }
        return function (scope, element, attrs) {
          scope.doModal = function () {
            var modal = $modal.open({
                templateUrl: 'template/dialog/textarea.html',
                controller: 'ModalInstanceCtrl',
                resolve: {
                  settings: function () {
                    return {
                      'close': function (result, value) {
                        scope.model = value;
                        modal.close(result);
                      },
                      'title': attrs.label,
                      'message': scope.model
                    };
                  }
                }
              });
          };
        };
      }
    };
  }
]);
DirectivesModule.directive('modelTags', [
  'menuSvc',
  function (menuSvc) {
    return {
      replace: true,
      restrict: 'EA',
      scope: {
        'label': '@',
        'model': '=',
        'helpnote': '@',
        'icon': '@',
        'strModel': '=model'
      },
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          $scope.selectOpts = {
            simple_tags: true,
            'multiple': true,
            tokenSeparators: [
              ',',
              ' '
            ]
          };
          $scope.selectOpts['tags'] = menuSvc.doAction($attrs.source);
        }
      ],
      templateUrl: 'template/formcontrols/modelTags.html',
      compile: function (tElement, tAttr) {
        if (tAttr['endline'] == 'true') {
          tElement.append('<hr/>');
        }
        return function (scope, element, attr) {
        };
      }
    };
  }
]);
DirectivesModule.directive('listEntriesThumbs', function () {
  return {
    restrict: 'A',
    controller: [
      '$scope',
      '$element',
      '$attrs',
      function ($scope, $element, $attrs) {
        if ($attrs.listEntriesThumbs == 'true') {
          var format = function (player) {
            if (!player.thumbnailUrl)
              return player.name;
            return '<img class=\'thumb\' src=\'' + player.thumbnailUrl + '\'/>' + player.name;
          };
          $scope.addOption({
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function (m) {
              return m;
            }
          });
        }
      }
    ]
  };
});
DirectivesModule.directive('parentContainer', [
  'sortSvc',
  function (sortSvc) {
    return {
      restrict: 'A',
      controller: function () {
        var cntrl = {
            register: function (container, model) {
              sortSvc.register(container, model);
            },
            update: function (newVal, oldVal, model) {
              sortSvc.update(newVal, oldVal, model);
            }
          };
        return cntrl;
      }
    };
  }
]);
DirectivesModule.directive('sortOrder', [
  'sortSvc',
  function (sortSvc) {
    return {
      restrict: 'EA',
      replace: true,
      scope: {},
      templateUrl: 'template/formcontrols/sortOrder.html',
      controller: [
        '$scope',
        function ($scope) {
          $scope.getObjects = function () {
            $scope.containers = sortSvc.getObjects();
          };
          $scope.getObjects();
          sortSvc.sortScope = $scope;
          $scope.$on('sortContainersChanged', function () {
            $scope.getObjects();
          });
          $scope.$watchCollection('containers', function (newVal, oldVal) {
            if (newVal != oldVal) {
              sortSvc.saveOrder($scope.containers);
            }
          });
          $scope.sortableOptions = {
            update: function (e, ui) {
              cl($scope.containers);
            },
            axis: 'y'
          };
        }
      ],
      link: function (scope, element, attrs) {
      }
    };
  }
]);
DirectivesModule.directive('infoAction', [
  'menuSvc',
  function (menuSvc) {
    return {
      restrict: 'EA',
      replace: 'true',
      controller: [
        '$scope',
        function ($scope) {
          $scope.check = function (action) {
            return menuSvc.checkAction(action);
          };
          $scope.btnAction = function (action) {
            menuSvc.doAction(action);
          };
        }
      ],
      scope: {
        'model': '=',
        'btnLabel': '@',
        'btnClass': '@',
        'action': '@',
        'helpnote': '@',
        'label': '@'
      },
      templateUrl: 'template/formcontrols/infoAction.html'
    };
  }
]);
DirectivesModule.directive('prettyRadio', [
  '$rootScope',
  function ($rootScope) {
    return {
      restrict: 'AC',
      require: [
        'ngModel',
        '^modelRadio'
      ],
      transclude: 'element',
      controller: function ($scope, $element, $attrs) {
        $scope.checked = false;
        $scope.setValue = function (value) {
          if (value == $attrs.value) {
            $scope.checked = true;
          } else
            $scope.checked = false;
        };
        if ($scope.$eval($attrs['model']) == $attrs.value) {
          $scope.checked = true;
        }
      },
      compile: function (tElement, tAttrs, transclude) {
        return function (scope, iElement, iAttr, cntrls) {
          var ngController = cntrls[0];
          var modelRadioCntrl = cntrls[1];
          var wrapper = $('<span class="clearfix prettyradio"></span>');
          var clickHandler = $('<a href="#" class=""></a>').appendTo(wrapper);
          modelRadioCntrl.registerControl(scope);
          modelRadioCntrl.regContoller(ngController);
          var inputVal = iAttr.value;
          var watchProp = 'model';
          if (typeof iAttr['model'] != 'undefined') {
            watchProp = iAttr['model'];
          }
          transclude(scope, function (clone) {
            return iElement.replaceWith(wrapper).append(clone);
          });
          clickHandler.on('click', function (e) {
            e.preventDefault();
            ngController.$setViewValue(inputVal);
            $rootScope.$safeApply(scope, function () {
              modelRadioCntrl.setChoice(inputVal);
            });
            return false;
          });
          var formatter = function () {
            modelRadioCntrl.setChoice(inputVal);
          };
          ngController.$viewChangeListeners.push(formatter);
          scope.$watch('checked', function (newVal) {
            if (newVal) {
              clickHandler.addClass('checked');
            } else
              clickHandler.removeClass('checked');
          });
        };
      }
    };
  }
]);
DirectivesModule.directive('divider', [function () {
    return {
      replace: true,
      restrict: 'EA',
      template: '<hr class="divider"/>'
    };
  }]);
DirectivesModule.directive('readOnly', [
  '$filter',
  function ($filter) {
    return {
      restrict: 'EA',
      replace: 'true',
      scope: {
        model: '=',
        helpnote: '@'
      },
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          if ($attrs['filter']) {
            if (typeof $filter($attrs['filter']) == 'function')
              $scope.model = $filter($attrs['filter'])($scope.model);
          }
          if ($attrs['initvalue']) {
            if (typeof $scope.model == 'undefined' || $scope.model === '')
              $scope.model = $attrs['initvalue'];
          }
          $scope.label = $attrs.label + ':';
        }
      ],
      templateUrl: 'template/formcontrols/readOnly.html'
    };
  }
]);
DirectivesModule.directive('modelButton', [
  'menuSvc',
  function (menuSvc) {
    return {
      restrict: 'EA',
      replace: 'true',
      controller: [
        '$scope',
        function ($scope) {
          $scope.check = function (action) {
            return menuSvc.checkAction(action);
          };
          $scope.btnAction = function (action) {
            menuSvc.doAction(action);
          };
        }
      ],
      scope: {
        'label': '@',
        'action': '@',
        'btnClass': '@',
        helpnote: '@'
      },
      templateUrl: 'template/formcontrols/modelButton.html'
    };
  }
]);
DirectivesModule.directive('onFinishRender', [
  '$timeout',
  'requestNotificationChannel',
  function ($timeout, requestNotificationChannel) {
    return {
      restrict: 'A',
      link: function (scope, element, attr) {
        if (scope.$last === true) {
          var timeVar;
          if (timeVar) {
            $timeout.cancel(timeVar);
          }
          timeVar = $timeout(function () {
            requestNotificationChannel.requestEnded('list');
            timeVar = null;
          });
        }
      }
    };
  }
]);
DirectivesModule.directive('onbeforeunload', [
  '$window',
  '$filter',
  '$location',
  function ($window, $filter, $location) {
    var unloadtext, forms = [];
    function handleOnbeforeUnload() {
      var i, form, isDirty = false;
      for (i = 0; i < forms.length; i++) {
        form = forms[i];
        if (form.scope[form.name].$dirty) {
          isDirty = true;
          break;
        }
      }
      if (isDirty) {
        return unloadtext;
      } else {
        return undefined;
      }
    }
    return function ($scope, $element) {
      if ($element[0].nodeName.toLowerCase() !== 'form') {
        throw new Error('onbeforeunload directive must only be set on a angularjs form!');
      }
      forms.push({
        'name': $element[0].name,
        'scope': $scope
      });
      try {
        unloadtext = $filter('translate')('onbeforeunload');
      } catch (err) {
        unloadtext = '';
      }
      var formName = $element[0].name;
      $scope.$watch(formName + '.$dirty', function (newVal, oldVal) {
        if (newVal && newVal != oldVal) {
          $window.onbeforeunload = handleOnbeforeUnload;
        }
      });
      $scope.$on('$locationChangeSuccess', function (e, origin, dest) {
        if (origin.split('?')[0] != dest.split('?')[0]) {
          $window.ononbeforeunload = false;
        }
      });
      $scope.$on('$destory', function () {
        $window.ononbeforeunload = false;
      });
    };
  }
]);
'use strict';
angular.module('KMC.filters', ['ngSanitize']).filter('HTMLunsafe', [
  '$sce',
  function ($sce) {
    return function (val) {
      return $sce.trustAsHtml(val);
    };
  }
]).filter('timeago', function () {
  return function (input) {
    if (typeof $.timeago == 'function' && !isNaN(input)) {
      var date = input * 1000;
      return $.timeago(date);
    } else
      return input;
  };
}).filter('range', function () {
  return function (input) {
    var lowBound, highBound;
    switch (input.length) {
    case 1:
      lowBound = 0;
      highBound = parseInt(input[0]) - 1;
      break;
    case 2:
      lowBound = parseInt(input[0]);
      highBound = parseInt(input[1]);
      break;
    default:
      return input;
    }
    var result = [];
    for (var i = lowBound; i <= highBound; i++)
      result.push(i);
    return result;
  };
}).filter('startFrom', function () {
  return function (input, start) {
    if (input) {
      start = +start;
      return input.slice(start);
    }
    return [];
  };
});
'use strict';
var KMCMenu = angular.module('KMC.menu', []);
KMCMenu.controller('menuCntrl', [
  'menuSvc',
  '$scope',
  function (menuSvc, $scope) {
    logTime('menuCntrl');
    var getWidth = function () {
      return $('#mp-menu').width();
    };
    var closeMenu = function () {
      var width = getWidth();
      $('#mp-pusher').animate({ 'left': '0' }, {
        duration: 200,
        queue: true
      });
      $('#mp-menu').animate({ 'left': '-' + width });
      $('#mp-pusher >.wrapper').animate({ 'width': '100%' });
    };
    var resetMenu = function () {
      var width = getWidth();
      $('#mp-pusher').css({ 'left': width });
      $('#mp-menu').css({ 'left': -width });
    };
    var openMenu = function () {
      var width = getWidth();
      $('#mp-pusher').animate({ 'left': width }, {
        duration: 200,
        queue: true
      });
      $('#mp-menu').animate({ 'left': -width }, {
        duration: 200,
        queue: false
      });
      $('#mp-pusher >.wrapper').animate({ 'width': '70%' }, {
        duration: 200,
        queue: true
      });
    };
    $scope.menuShown = true;
    $scope.menuInitDone = false;
    $(window).resize(function () {
      if ($scope.menuShown === true)
        resetMenu();
      else {
        closeMenu();
      }
    });
    $scope.$on('menuChange', function () {
      $scope.menuShown = true;
    });
    $scope.togglemenu = function (e) {
      $scope.menuShown = !$scope.menuShown;
      var disTarget = $(e.target);
      if (disTarget.is('i')) {
        disTarget = disTarget.parent('a');
      }
      if (!$scope.menuShown)
        disTarget.css('transform', 'rotate(180deg)');
      else
        disTarget.css('transform', '');
    };
    $scope.$watch('menuShown', function (newVal, oldVal) {
      if (newVal != oldVal) {
        if (newVal) {
          openMenu();
        } else {
          closeMenu();
        }
      }
    });
  }
]);
KMCMenu.factory('menuSvc', [
  'editableProperties',
  '$timeout',
  '$compile',
  '$location',
  '$templateCache',
  function (editableProperties, $timeout, $compile, $location, $templateCache) {
    var menudata = null;
    editableProperties.then(function (data) {
      menudata = data;
    });
    var menuItems = [];
    var menuFn = {};
    var refreshableDirectives = function (jsonName) {
      switch (jsonName) {
      case 'modaledit':
      case 'select2data':
      case 'dropdown':
      case 'alignment':
      case 'container':
      case 'checkbox':
      case 'color':
      case 'url':
      case 'text':
      case 'number':
      case 'radio':
      case 'multiinput':
        return true;
      default:
        return false;
      }
    };
    var JSON2directiveDictionary = function (jsonName) {
      switch (jsonName) {
      case 'modaledit':
        return '<div model-edit/>';
      case 'divider':
        return '<div divider/>';
      case 'tags':
        return '<div model-tags/>';
      case 'select2data':
        return '<div select2-data/>';
      case 'dropdown':
        return '<div model-select/>';
      case 'container':
        return '<div model-select parent-container=""/>';
      case 'checkbox':
        return '<div model-checkbox/>';
      case 'color':
        return '<div model-color/>';
      case 'text':
        return '<div model-text/>';
      case 'url':
        return '<div model-text validate="url"/>';
      case 'number':
        return '<div model-number/>';
      case 'readonly':
        return '<div read-only/>';
      case 'featuremenu':
        return '<div feature-menu/>';
      case 'radio':
        return '<div model-radio/>';
      case 'button':
        return '<div model-button/>';
      case 'infoAction':
        return '<div info-action/>';
      case 'sortOrder':
        return '<div sort-order/>';
      case 'hidden':
        return '<span hidden-value/>';
      case 'multiinput':
        return '<div multiple-value-input>';
      }
    };
    var search = function (path, obj, target) {
      for (var k in obj) {
        if (obj.hasOwnProperty(k) && (k == 'label' || k == 'children' || typeof obj[k] == 'object'))
          if (obj[k] == target)
            return path + '[\'' + k + '\']';
          else if (typeof obj[k] == 'object') {
            var result = search(path + '[\'' + k + '\']', obj[k], target);
            if (result)
              return result;
          }
      }
      return false;
    };
    var Search4ControlModelData = function (path, obj, target) {
      for (var k in obj) {
        if (obj.hasOwnProperty(k) && (k == 'label' || k == 'children' || typeof obj[k] == 'object'))
          if (obj[k] && typeof obj[k].model != 'undefined' && obj[k].model == target)
            return obj[k];
          else if (typeof obj[k] === 'object') {
            var result = Search4ControlModelData(path + '[\'' + k + '\']', obj[k], target);
            if (result)
              return result;
          }
      }
      return false;
    };
    var menuSvc = {
        menuScope: {},
        currentTooltip: null,
        closeTooltips: function (e) {
          if (menuSvc.currentTooltip && e.target != menuSvc.currentTooltip) {
            $(menuSvc.currentTooltip).trigger('customShow');
            menuSvc.currentTooltip = null;
          }
        },
        get: function () {
          return menudata;
        },
        getModalData: function (model) {
          return menuSvc.menuScope.$eval(model);
        },
        setModelData: function (model, value) {
          if (model.indexOf('data.') !== 0)
            model = 'data.' + model;
          var parent = model.substr(0, model.lastIndexOf('.'));
          var last = model.substr(model.lastIndexOf('.') + 1);
          var parentObj = menuSvc.getModalData(parent);
          parentObj[last] = value;
        },
        getOrMakeModelData: function (model, makeEnabled) {
          var knownParent = menuSvc.getKnownParent(model);
          var missing = model.replace(knownParent, '').split('.');
          angular.forEach(missing, function (misObhName) {
            if (misObhName) {
              var knwonObject = menuSvc.getModalData(knownParent)[misObhName] = {};
              if (makeEnabled) {
                knwonObject['_featureEnabled'] = true;
              }
              knownParent += '.' + misObhName;
            }
          });
          return menuSvc.getModalData(model);
        },
        getKnownParent: function (model) {
          var checkObject = menuSvc.getModalData(model);
          var getParent = function (value) {
            return value.substr(0, value.lastIndexOf('.'));
          };
          model = getParent(model);
          while (model && typeof checkObject == 'undefined') {
            model = getParent(model);
            checkObject = menuSvc.getModalData(model);
          }
          return model;
        },
        getControlData: function (model) {
          if (typeof model == 'string') {
            if (model.indexOf('data.') === 0)
              model = model.substr(model.indexOf('.') + 1);
            return Search4ControlModelData('', menudata, model);
          }
        },
        currentPage: '',
        setMenu: function (setTo) {
          menuSvc.currentPage = setTo;
          $location.search('menuPage', setTo);
          if (typeof menuSvc.spinnerScope != 'undefined' && setTo != 'search') {
            menuSvc.spinnerScope.spin();
          }
          menuSvc.menuScope.$broadcast('menuChange', setTo);
        },
        buildMenu: function (baseData) {
          if (menuItems.length === 0) {
            if (window.IE == 8) {
              $templateCache.remove('template/menu/menuPage.html');
            }
            var menuJsonObj = menuSvc.get();
            angular.forEach(menuJsonObj, function (value) {
              var menuItem = menuSvc.buildMenuItem(value, baseData);
              menuFn[menuItem.attr('pagename')] = $compile(menuItem);
            });
          }
          return menuFn;
        },
        buildMenuItem: function (item, BaseData, parentModel) {
          var elm = '';
          switch (item.type) {
          case 'menu':
            var menuLevelObj = angular.element('<div menu-level pagename="' + item.model + '" />');
            if (typeof parentModel != 'undefined') {
              menuLevelObj.attr('parent-label', parentModel.label);
              menuLevelObj.attr('parent-page', parentModel.model);
            }
            var parentMenu = writeFormElement(item, menuLevelObj);
            elm = writeChildren(item, parentMenu, true);
            checkItemSections(item, elm);
            break;
          case 'featuremenu':
            elm = writeChildren(item, writeFormElement(item, 'featuremenu'));
            break;
          default:
            var directive = JSON2directiveDictionary(item.type);
            if (directive)
              elm = writeFormElement(item, directive);
            break;
          }
          return elm;
          function writeChildren(item, parent, eachInLi) {
            angular.forEach(item.children, function (subitem) {
              switch (subitem.type) {
              case 'menu':
                parent.append(menuSvc.buildMenuItem(subitem, item.model, item));
                break;
              case 'featuremenu':
                parent.append(writeChildren(subitem, writeFormElement(subitem, 'featuremenu')));
                break;
              default:
                parent.append(writeFormElement(subitem, subitem.type));
                break;
              }
            });
            if (eachInLi === true) {
              parent.children().each(function () {
                if (!$(this).is('menu-level'))
                  $(this).wrap('<li>');
              });
            }
            return parent;
          }
          function writeFormElement(item, directive) {
            var strDirective;
            if (typeof directive == 'string') {
              strDirective = directive;
              directive = JSON2directiveDictionary(directive);
              if (!directive)
                return;
            }
            var elm = angular.element(directive);
            if (typeof item.model != 'undefined' && item.model[0] == '~') {
              elm.attr('model', item.model.substr(1));
            } else {
              elm.attr('model', 'data.' + item.model);
            }
            if (strDirective) {
              if (item['player-refresh'] !== false) {
                if (refreshableDirectives(strDirective)) {
                  elm.attr('player-refresh', item['player-refresh'] || true);
                }
              }
            }
            angular.forEach(item, function (value, key) {
              if (key != 'model' && key != 'player-refresh' && (typeof value == 'string' || typeof value == 'number' || typeof value == 'boolean')) {
                elm.attr(key, value);
              }
            });
            if (item.require) {
              elm.attr('label', '* ' + item.label);
            }
            return elm;
          }
          function checkItemSections(item, elm) {
            if (item.sections) {
              if (item.sections.type == 'tabs') {
                var sectionsDir = angular.element('<li ka-tabs></li>');
                sectionsDir.attr('heading', item.sections.title);
                var tabsSetDir = angular.element('<div tabset></div>').appendTo(sectionsDir);
                angular.forEach(item.sections.tabset, function (value) {
                  var tabDir = angular.element('<div tab section="' + value.key + '" heading="' + value.title + '"></div>');
                  $(elm).find('li>div[section=' + value.key + ']').each(function (index, child) {
                    $(child).parents('li').remove().appendTo(tabDir);
                  });
                  tabDir.appendTo(tabsSetDir);
                });
                elm.prepend(sectionsDir);
              } else if (item.sections.type == 'kaDynamicSection') {
                angular.forEach(item.sections.sectionsConfig, function (section, sectionKey) {
                  var sectionPart = angular.element('<li ka-dynamic-section model="data.' + section.model + '" section="' + sectionKey + '"></li>');
                  var templateControls = angular.element('<ul>');
                  $(elm).find('li>div[section=' + sectionKey + ']').each(function (index, child) {
                    $(child).parents('li').remove().appendTo(templateControls);
                  });
                  $templateCache.put('dynamicSections/' + sectionKey, templateControls);
                  elm.prepend(sectionPart);
                });
              }
            }
          }
        },
        menuSearch: function (searchValue) {
          var foundLabel = search('menudata', menudata, searchValue);
          if (foundLabel) {
            var foundModelObj = eval(foundLabel.substr(0, foundLabel.lastIndexOf('[\'label\']')));
            var foundModel = foundModelObj.model;
            var lastChild = foundLabel.lastIndexOf('[\'children\']');
            var lastMenu = foundLabel.substr(0, lastChild);
            var menuPage = eval(lastMenu);
            var featureMenu = [];
            if (typeof menuPage == 'object') {
              if (menuPage.type == 'featuremenu') {
                while (typeof menuPage == 'object' && menuPage.type == 'featuremenu') {
                  featureMenu.push(menuPage);
                  lastChild = lastMenu.lastIndexOf('[\'children\']');
                  menuPage = eval(lastMenu.substr(0, lastChild));
                  lastMenu = foundLabel.substr(0, lastChild);
                }
              }
              if (menuPage.type == 'menu' && menuPage.model.indexOf('.') !== -1) {
                var previousMenu = eval(lastMenu.substr(0, lastMenu.lastIndexOf('[\'children\']')));
                menuSvc.setMenu(previousMenu.model);
                $timeout(function () {
                  menuSvc.setMenu(menuPage.model);
                });
              } else {
                menuSvc.setMenu(menuPage.model);
              }
              if (featureMenu.length) {
                angular.forEach(featureMenu, function (value) {
                  menuSvc.menuScope.$broadcast('openFeature', 'data.' + value.model);
                });
              }
              $timeout(function () {
                menuSvc.menuScope.$broadcast('highlight', 'data.' + foundModel);
                if (foundModelObj.type == 'featuremenu') {
                  menuSvc.menuScope.$broadcast('openFeature', 'data.' + foundModel);
                }
              });
              return true;
            }
          } else {
            return false;
          }
        },
        actions: [],
        registerAction: function (callStr, dataFn, context) {
          if (typeof dataFn == 'function') {
            if (!context)
              menuSvc.actions[callStr] = dataFn;
            else {
              menuSvc.actions[callStr] = {
                applyOn: context,
                funcData: dataFn
              };
            }
          } else if (typeof dataFn == 'object') {
            menuSvc.actions[callStr] = {
              applyOn: dataFn,
              funcData: function () {
                return dataFn;
              }
            };
          }
        },
        doAction: function (action, arg) {
          if (typeof menuSvc.actions[action] == 'function') {
            return menuSvc.actions[action].call(arg);
          } else if (typeof menuSvc.actions[action] == 'object' && typeof menuSvc.actions[action].funcData == 'function') {
            var retData = menuSvc.actions[action].funcData.apply(menuSvc.actions[action].applyOn, arg);
            return retData;
          }
        },
        getAction: function (action) {
          return menuSvc.actions[action];
        },
        checkAction: function (action) {
          if (typeof menuSvc.actions[action] == 'function') {
            return true;
          }
          return false;
        },
        makeFeatureCheckbox: function ($scope, $attrs) {
          if ($attrs['model']) {
            var ModelArr = $attrs['model'].split('.');
            $scope.FeatureModel = ModelArr.pop();
            var parentStr = ModelArr.join('.');
            $scope.parentModel = menuSvc.menuScope.$eval(parentStr);
            $scope.featureModelCon = menuSvc.menuScope.$eval($attrs['model']);
            $scope.featureCheckbox = $attrs.featureCheckbox == 'false' ? false : true;
            if ($scope.featureCheckbox) {
              if (!$scope.featureModelCon) {
                if ($scope.parentModel)
                  $scope.featureModelCon = $scope.parentModel[$scope.FeatureModel] = { _featureEnabled: false };
                else
                  $scope.featureModelCon = { _featureEnabled: false };
              }
              $scope.isDisabled = $scope.featureModelCon._featureEnabled ? false : true;
            }
          }
        },
        linkFn4FeatureCheckbox: function (scope) {
          if (scope.featureCheckbox) {
            scope.$watch('featureModelCon._featureEnabled', function (newval, oldVal) {
              if (newval != oldVal) {
                if (!newval) {
                  scope.isDisabled = true;
                  if (typeof scope.isCollapsed != 'undefined') {
                    $timeout(function () {
                      scope.isCollapsed = true;
                    });
                  } else {
                    if (typeof scope.goBack == 'function') {
                      scope.goBack();
                    }
                  }
                } else {
                  scope.isDisabled = false;
                  if (scope.parentModel)
                    scope.parentModel[scope.FeatureModel] = scope.featureModelCon;
                }
              }
            });
          }
        }
      };
    return menuSvc;
  }
]).directive('featureMenu', [
  'menuSvc',
  function (menuSvc) {
    return {
      restrict: 'EA',
      replace: true,
      templateUrl: 'template/menu/featureMenu.html',
      transclude: true,
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          menuSvc.makeFeatureCheckbox($scope, $attrs);
          $scope.isCollapsed = true;
          $scope.openFeature = function () {
            if ($scope.isCollapsed) {
              $scope.isCollapsed = false;
            }
          };
          $scope.toggleFeature = function () {
            $scope.isCollapsed = !$scope.isCollapsed;
          };
        }
      ],
      scope: {
        label: '@',
        description: '@'
      },
      compile: function (tElement, tAttr) {
        if (tAttr['endline'] != 'false') {
          tElement.append('<hr/>');
        }
        return function (scope, element, attributes) {
          scope.$watch('isCollapsed', function (newVal, oldVal) {
            if (newVal != oldVal) {
              scope.$root.$broadcast('layoutChange');
            }
          });
          menuSvc.linkFn4FeatureCheckbox(scope);
          scope.$on('openFeature', function (e, args) {
            if (args == attributes['model']) {
              scope.openFeature();
            }
          });
        };
      }
    };
  }
]).directive('model', [
  '$timeout',
  function ($timeout) {
    return {
      restrict: 'A',
      link: function (scope, iElem, iAttr) {
        scope.$on('highlight', function (e, data) {
          if (iAttr.model == data) {
            var elm = iElem;
            if (iElem.parent().is('li'))
              elm = iElem.parent();
            var originalBG = elm.css('background') || 'transparent';
            elm.css({ 'backgroundColor': 'rgba(253,255,187,1)' });
            $timeout(function () {
              elm.animate({ 'backgroundColor': 'rgba(253,255,187,0)' }, 1000, function () {
                elm.css({ 'backgroundColor': originalBG }, 1000);
              });
            }, 4000);
          }
        });
      }
    };
  }
]).directive('navmenu', [
  'menuSvc',
  '$compile',
  '$timeout',
  '$routeParams',
  'PlayerService',
  function (menuSvc, $compile, $timeout, $routeParams, PlayerService) {
    return {
      templateUrl: 'template/menu/navmenu.html',
      restrict: 'EA',
      priority: 100,
      transclude: true,
      controller: function ($scope) {
        $scope.scroller = null;
        menuSvc.menuScope = $scope;
        $scope.menuInitDone = false;
        $scope.data = $scope.$parent.data;
        $scope.settings = $scope.$parent.settings;
        return { spinnerScope: null };
      },
      compile: function (tElement) {
        var menuElem = tElement.find('#mp-base >  ul');
        return function ($scope, $element, $attrs, controller) {
          var menuData = menuSvc.buildMenu('data');
          menuSvc.spinnerScope = controller.spinnerScope;
          var timeVar = null;
          var timeVar1 = null;
          $scope.menuInitDone = false;
          $scope.$on('menuChange', function (e, page) {
            if (page != 'search') {
              if (page.indexOf('.') === -1 && menuElem.children('[pagename="' + page + '"]').length === 0) {
                menuData[page]($scope, function (htmlData) {
                  htmlData.appendTo(menuElem);
                });
              }
              if (timeVar) {
                $timeout.cancel(timeVar);
              }
              timeVar = $timeout(function () {
                if (menuSvc.spinnerScope) {
                  menuSvc.spinnerScope.endSpin();
                }
                timeVar = null;
              });
            }
          });
          $timeout(function () {
            var page = $routeParams['menuPage'] || 'basicDisplay';
            if (typeof menuData[page] === 'undefined') {
              cl('can not load requested page, perhaps a sub page ?');
              page = 'basicDisplay';
            }
            menuSvc.setMenu(page);
            logTime('menuInitDone');
            $('div.section[ng-view]').on('click', menuSvc.closeTooltips);
            $scope.menuInitDone = true;
            $scope.$root.$broadcast('menuInitDone');
          }, 200).then(function () {
            $timeout(function () {
              if (!$scope.newPlayer) {
                $scope.playerEdit.$setPristine();
              }
            }, 500);
          });
        };
      }
    };
  }
]).controller('menuSearchCtl', [
  '$scope',
  'menuSvc',
  function ($scope, menuSvc) {
    var menuObj = menuSvc.get();
    $scope.menuData = [];
    $scope.checkSearch = function (val) {
      if (val)
        console.log(val);
      $scope.notFound = false;
      if ($scope.menuSearch) {
        $scope.searchMenuFn();
      }
    };
    $scope.menuSearch = '';
    $scope.searchMenuFn = function () {
      var searchResult = menuSvc.menuSearch($scope.menuSearch);
      if (!searchResult)
        $scope.notFound = true;
      else {
        $scope.menuSearch = '';
      }
    };
    var getLabels = function (obj) {
      angular.forEach(obj, function (value, key) {
        if (value != menuObj[key])
          $scope.menuData.push(value.label);
        if (value.children) {
          getLabels(value.children);
        }
      });
    };
    getLabels(menuObj);
  }
]).directive('menuLevel', [
  'menuSvc',
  '$window',
  '$routeParams',
  function (menuSvc, $window, $routeParams) {
    return {
      templateUrl: 'template/menu/menuPage.html',
      replace: true,
      transclude: 'true',
      restrict: 'EA',
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          menuSvc.makeFeatureCheckbox($scope, $attrs);
          if (!$attrs['parentPage']) {
            $scope.isDisabled = false;
          }
          $scope.selfOpenLevel = function () {
            menuSvc.setMenu($attrs.pagename);
          };
          $scope.goBack = function () {
            menuSvc.setMenu($attrs.parentPage);
          };
          $scope.openLevel = function (arg) {
            if (typeof arg == 'undefined')
              return $scope.isOnTop = true;
            else if (arg == $scope.pagename) {
              return $scope.isOnTop = true;
            }
            return $scope.isOnTop = false;
          };
          $scope.isOnTop = false;
        }
      ],
      compile: function (tElement, tAttr) {
        if (tAttr['endline'] == 'true') {
          tElement.find('div.header').append('<hr/>');
        }
        if (tAttr['parentPage']) {
          var content = tElement.html();
          tElement.replaceWith(angular.element('<div type="menupage" class="form-element"></div>').append(content));
        }
        return function ($scope, $element) {
          $scope.$on('menuChange', function (event, arg) {
            $scope.openLevel(arg);
          });
          menuSvc.linkFn4FeatureCheckbox($scope);
          $scope.$watch('isOnTop', function (newVal) {
            if (newVal) {
              $element.parents('.mp-level:not("#mp-base")').addClass('mp-level-in-stack');
              $element.children('.mp-level:first').addClass('mp-level-open').removeClass('mp-level-in-stack');
            } else {
              $element.children('.mp-level:first').removeClass('mp-level-open');
              $element.parents('.mp-level.mp-level-in-stack:not("#mp-base")').removeClass('mp-level-open mp-level-in-stack');
            }
          });
        };
      },
      scope: {
        'label': '@',
        'model': '=',
        'pagename': '@',
        'parentPage': '@',
        'parentLabel': '@',
        'description': '@'
      }
    };
  }
]).directive('menuHead', [
  'menuSvc',
  '$compile',
  function (menuSvc, $compile) {
    return {
      restrict: 'EA',
      template: '<div id=\'mp-mainlevel\'><ul></ul></div>',
      replace: true,
      scope: {},
      transclude: true,
      controller: [
        '$scope',
        '$element',
        function ($scope) {
          $scope.activeItem = menuSvc.currentPage;
          $scope.changeActiveItem = function (menupage) {
            if ($scope.activeItem != menupage) {
              $scope.activeItem = menupage;
              menuSvc.setMenu(menupage);
            }
          };
        }
      ],
      compile: function (tElement) {
        var ul = tElement.find('ul');
        var elements = menuSvc.get();
        return function ($scope, $element, $attrs, contorller, transcludeFn) {
          transcludeFn($scope, function (clone) {
            ul.prepend(clone);
          });
          angular.forEach(elements, function (value, key) {
            var elm = angular.element('<li></li>');
            elm.html('<a ng-click="changeActiveItem(\'' + value.model + '\')" class="icon icon-' + value.icon + '"  ng-class="{active: activeItem == \'' + value.model + '\'}" tooltip-placement="right" tooltip="' + value.label + '"></a>');
            $compile(elm)($scope).appendTo(ul);
          });
          $scope.$watch(function () {
            return menuSvc.currentPage;
          }, function (newVal, oldVal) {
            if (newVal != oldVal && newVal != $scope.activeItem)
              $scope.changeActiveItem(newVal);
          });
        };
      }
    };
  }
]);
'use strict';
angular.module('KMCModule').controller('LoginCtrl', [
  '$scope',
  'apiService',
  '$location',
  'localStorageService',
  'requestNotificationChannel',
  '$filter',
  function ($scope, apiService, $location, localStorageService, requestNotificationChannel, $filter) {
    requestNotificationChannel.requestEnded('list');
    $scope.formError = true;
    $scope.formHelpMsg = $filter('translate')('You must login to use this application');
    $scope.email = '';
    $scope.pwd = '';
    $scope.login = function () {
      apiService.doRequest({
        'service': 'user',
        'action': 'loginbyloginid',
        'loginId': $scope.email,
        'password': $scope.pwd
      }).then(function (data) {
        if (localStorageService.isSupported()) {
          localStorageService.add('ks', data);
        }
        apiService.setKs(data);
        $location.path('/list');
      }, function (errorMsg) {
        $scope.formError = true;
        $scope.formHelpMsg = errorMsg;
      });
    };
  }
]);
'use strict';
angular.module('KMC.controllers', []).controller('ModalInstanceCtrl', [
  '$scope',
  '$modalInstance',
  'settings',
  function ($scope, $modalInstance, settings) {
    $scope.title = '';
    $scope.message = '';
    $scope.buttons = [
      {
        result: false,
        label: 'Cancel',
        cssClass: 'btn-default'
      },
      {
        result: true,
        label: 'OK',
        cssClass: 'btn-primary'
      }
    ];
    $scope.close = function (result) {
      $modalInstance.close(result);
    };
    $scope.cancel = function () {
      $modalInstance.dismiss('cancel');
    };
    angular.extend($scope, settings);
  }
]);
;
'use strict';
angular.module('KMCModule').controller('PlayerCreateCtrl', [
  '$scope',
  '$filter',
  'templates',
  'userId',
  'playerTemplates',
  function ($scope, $filter, templates, userId, playerTemplates) {
    $scope.title = $filter('translate')('New player - from template');
    $scope.templates = templates.data;
    $scope.templateType = 'system';
    $scope.userId = userId;
    $scope.loading = false;
    $scope.$watch('templateType', function (newVal, oldVal) {
      if (newVal != oldVal) {
        if (newVal == 'user') {
          $scope.loading = true;
          playerTemplates.listUser($scope.userID).success(function (response) {
            $scope.templates = response;
            $scope.loading = false;
          });
        } else {
          $scope.loading = true;
          $scope.templates = templates.data;
          $scope.loading = false;
        }
      }
    });
    $scope.makeTooltip = function (index) {
      var item = $scope.templates[index];
      if (item && typeof item.settings != 'undefined' && typeof item.settings.name != 'undefined')
        return item.settings.name + '<br/>' + item.id + '<br/> Any information you will decide to show';
    };
  }
]);
;
'use strict';
angular.module('KMCModule').controller('PlayerEditCtrl', [
  '$scope',
  'PlayerData',
  '$routeParams',
  '$filter',
  'menuSvc',
  'PlayerService',
  'apiService',
  '$timeout',
  'requestNotificationChannel',
  function ($scope, PlayerData, $routeParams, $filter, menuSvc, PlayerService, apiService, $timeout, requestNotificationChannel) {
    logTime('editCntrlLoad');
    $scope.playerId = PlayerData.id;
    $scope.newPlayer = !$routeParams.id;
    $scope.data = PlayerData;
    $scope.debug = $routeParams.debug;
    $scope.$on('$destory', function () {
      PlayerService.clearCurrentRefresh();
    });
    $scope.getDebugInfo = function (partial) {
      if (!partial)
        return $scope.data;
      else
        return $scope.data[partial];
    };
    $scope.masterData = angular.copy($scope.data);
    $scope.userEntriesList = [];
    $scope.settings = {};
    $timeout(function () {
      apiService.listMedia().then(function (data) {
        $scope.userEntries = data;
        angular.forEach($scope.userEntries.objects, function (value) {
          if (typeof value.playlistType == 'undefined')
            $scope.userEntriesList.push({
              'id': value.id,
              'text': value.name
            });
        });
        $scope.settings.previewEntry = PlayerService.getPreviewEntry() ? PlayerService.getPreviewEntry() : $scope.userEntriesList[0];
        PlayerService.setPreviewEntry($scope.settings.previewEntry);
        PlayerService.playerRefresh().then(function () {
          menuSvc.menuScope.playerInitDone = true;
        });
      });
    }, 200);
    menuSvc.registerAction('listEntries', function () {
      return $scope.userEntriesList;
    });
    var timeVar = null;
    menuSvc.registerAction('queryEntries', function (query) {
      if (query.term) {
        var data = { results: [] };
        if (timeVar) {
          $timeout.cancel(timeVar);
        }
        timeVar = $timeout(function () {
          apiService.searchMedia(query.term).then(function (results) {
            angular.forEach(results.objects, function (entry) {
              data.results.push({
                id: entry.id,
                text: entry.name
              });
            });
            timeVar = null;
            return query.callback(data);
          });
        }, 200);
      } else
        return query.callback({ results: $scope.userEntriesList });
    });
    $scope.$watch('settings.previewEntry', function (newVal, oldVal) {
      if (newVal != oldVal && typeof oldVal != 'undefined') {
        PlayerService.setPreviewEntry($scope.settings.previewEntry);
        PlayerService.playerRefresh();
      }
    });
    requestNotificationChannel.requestEnded('edit');
  }
]);
angular.module('KMCModule').controller('editPageDataCntrl', [
  '$scope',
  'PlayerService',
  'apiService',
  '$modal',
  '$location',
  'menuSvc',
  'localStorageService',
  function ($scope, playerService, apiService, $modal, $location, menuSvc, localStorageService) {
    $scope.autoRefreshEnabled = playerService.autoRefreshEnabled;
    $scope.$watch('autoRefreshEnabled', function (newVal, oldVal) {
      if (newVal != oldVal) {
        if (newVal && $scope.checkPlayerRefresh()) {
          playerService.playerRefresh();
        }
        playerService.autoRefreshEnabled = newVal;
      }
    });
    $scope.refreshPlayer = function () {
      playerService.playerRefresh().then(function () {
        playerService.refreshNeeded = false;
      });
    };
    $scope.checkPlayerRefresh = function () {
      if (menuSvc.menuScope && menuSvc.menuScope.menuInitDone && menuSvc.menuScope.playerInitDone)
        return playerService.refreshNeeded;
      else
        return false;
    };
    $scope.save = function () {
      playerService.savePlayer($scope.data).then(function (value) {
        menuSvc.menuScope.playerEdit.$setPristine();
        $scope.masterData = value;
        localStorageService.remove('tempPlayerID');
        if ($scope.newPlayer) {
          apiService.setCache(false);
        }
        $modal.open({
          templateUrl: 'template/dialog/message.html',
          controller: 'ModalInstanceCtrl',
          resolve: {
            settings: function () {
              return {
                'title': 'Save Player Settings',
                'message': 'Player Saved Successfully',
                buttons: [{
                    result: true,
                    label: 'OK',
                    cssClass: 'btn-primary'
                  }]
              };
            }
          }
        });
      }, function (msg) {
        $modal.open({
          templateUrl: 'template/dialog/message.html',
          controller: 'ModalInstanceCtrl',
          resolve: {
            settings: function () {
              return {
                'title': 'Player save failure',
                'message': msg
              };
            }
          }
        });
      });
    };
    $scope.formValidation = function () {
      if (typeof menuSvc.menuScope.playerEdit != 'undefined' && menuSvc.menuScope.playerEdit.$error) {
        var obj = menuSvc.menuScope.playerEdit.$error;
        var empty = true;
        angular.forEach(obj, function (value, key) {
          if (value !== false) {
            empty = false;
          }
        });
        if (!empty)
          return obj;
        return null;
      }
    };
    $scope.cancel = function () {
      if (menuSvc.menuScope.playerEdit.$pristine) {
        $location.url('/list');
      } else {
        var modal = $modal.open({
            templateUrl: 'template/dialog/message.html',
            controller: 'ModalInstanceCtrl',
            resolve: {
              settings: function () {
                return {
                  'title': 'Navigation confirmation',
                  message: 'You are about to leave this page without saving, are you sure you want to discard the changes?'
                };
              }
            }
          });
        modal.result.then(function (result) {
          if (result) {
            $location.url('/list');
          }
        });
      }
    };
    $scope.saveEnabled = function () {
      if (typeof menuSvc.menuScope.playerEdit != 'undefined') {
        if (menuSvc.menuScope.playerEdit.$valid)
          return !angular.equals($scope.data, $scope.masterData);
        else
          return false;
      }
    };
  }
]);
'use strict';
angular.module('KMCModule').controller('PlayerListCtrl', [
  'apiService',
  'loadINI',
  '$location',
  '$rootScope',
  '$scope',
  '$filter',
  '$modal',
  '$timeout',
  '$log',
  '$compile',
  '$window',
  'localStorageService',
  'requestNotificationChannel',
  'PlayerService',
  '$q',
  'menuSvc',
  function (apiService, loadINI, $location, $rootScope, $scope, $filter, $modal, $timeout, $log, $compile, $window, localStorageService, requestNotificationChannel, PlayerService, $q, menuSvc) {
    requestNotificationChannel.requestStarted('list');
    $rootScope.lang = 'en-US';
    $scope.search = '';
    $scope.searchSelect2Options = {};
    $scope.currentPage = 1;
    $scope.maxSize = parseInt(localStorageService.get('listSize')) || 10;
    $scope.$watch('maxSize', function (newval, oldval) {
      if (newval != oldval) {
        localStorageService.set('listSize', newval);
        $scope.$broadcast('layoutChange');
      }
    });
    $scope.triggerLayoutChange = function () {
      $scope.$broadcast('layoutChange');
    };
    $scope.uiSelectOpts = {
      width: '60px',
      minimumResultsForSearch: -1
    };
    var config = null;
    try {
      var kmc = window.parent.kmc;
      if (kmc && kmc.vars && kmc.vars.studio.config) {
        config = kmc.vars.studio.config;
        $scope.UIConf = angular.fromJson(config);
      }
    } catch (e) {
      $log.error('Could not located parent.kmc: ' + e);
    }
    if (!config) {
      loadINI.getINIConfig().success(function (data) {
        $scope.UIConf = data;
      });
    }
    if (localStorageService.get('tempPlayerID')) {
      var deletePlayerRequest = {
          'service': 'uiConf',
          'action': 'delete',
          'id': localStorageService.get('tempPlayerID')
        };
      apiService.doRequest(deletePlayerRequest).then(function (data) {
        localStorageService.remove('tempPlayerID');
      });
    }
    if (menuSvc.menuScope.playerEdit && !menuSvc.menuScope.playerEdit.$pristine) {
      apiService.setCache(false);
      PlayerService.clearCurrentPlayer();
    }
    var playersRequest = {
        'filter:tagsMultiLikeOr': 'kdp3,html5studio',
        'filter:orderBy': '-updatedAt',
        'filter:objTypeEqual': '1',
        'filter:objectType': 'KalturaUiConfFilter',
        'filter:creationModeEqual': '2',
        'ignoreNull': '1',
        'page:objectType': 'KalturaFilterPager',
        'pager:pageIndex': '1',
        'pager:pageSize': '999',
        'service': 'uiConf',
        'action': 'list'
      };
    apiService.doRequest(playersRequest).then(function (data) {
      $scope.data = data.objects;
      $scope.calculateTotalItems();
      PlayerService.cachePlayers(data.objects);
      setTimeout(function () {
        $scope.triggerLayoutChange();
      }, 300);
    });
    $scope.filtered = $filter('filter')($scope.data, $scope.search) || $scope.data;
    $scope.calculateTotalItems = function () {
      if ($scope.filtered)
        $scope.totalItems = $scope.filtered.length;
      else if ($scope.data) {
        $scope.totalItems = $scope.data.length;
      }
    };
    $scope.requiredVersion = PlayerService.getRequiredVersion();
    $scope.sort = {
      sortCol: 'createdAt',
      reverse: true
    };
    $scope.sortBy = function (colName) {
      $scope.sort.sortCol = colName;
      $scope.sort.reverse = !$scope.sort.reverse;
    };
    $scope.checkVersionNeedsUpgrade = function (item) {
      var html5libVersion = item.html5Url.substr(item.html5Url.indexOf('/v') + 2, 1);
      return html5libVersion == '1' || item.config === null;
    };
    $scope.showSubTitle = true;
    $scope.getThumbnail = function (item) {
      if (typeof item.thumbnailUrl != 'undefined')
        return item.thumbnailUrl;
      else
        return $scope.defaultThumbnailUrl;
    };
    $scope.defaultThumbnailUrl = 'img/mockPlayerThumb.png';
    var timeVar;
    $scope.$watch('search', function (newValue, oldValue) {
      $scope.showSubTitle = newValue;
      if (newValue.length > 0) {
        $scope.title = $filter('translate')('search for') + ' "' + newValue + '"';
      } else {
        if (oldValue)
          $scope.title = $filter('translate')('Players list');
      }
      if (timeVar) {
        $timeout.cancel(timeVar);
      }
      timeVar = $timeout(function () {
        $scope.triggerLayoutChange();
        $scope.calculateTotalItems();
        timeVar = null;
      }, 100);
    });
    $scope.oldVersionEditText = $filter('translate')('This player must be updated before editing. <br/>' + 'Some features and design may be lost.');
    var goToEditPage = function (id) {
      requestNotificationChannel.requestStarted('edit');
      $location.path('/edit/' + id);
    };
    $scope.goToEditPage = function (item, $event) {
      if ($event)
        $event.preventDefault();
      if (!$scope.checkVersionNeedsUpgrade(item)) {
        goToEditPage(item.id);
        return false;
      } else {
        var msgText = $scope.oldVersionEditText;
        var modal = $modal.open({
            templateUrl: 'template/dialog/message.html',
            controller: 'ModalInstanceCtrl',
            resolve: {
              settings: function () {
                return {
                  'title': 'Edit confirmation',
                  'message': msgText,
                  buttons: [
                    {
                      result: false,
                      label: 'Cancel',
                      cssClass: 'btn-default'
                    },
                    {
                      result: true,
                      label: 'Upgrade',
                      cssClass: 'btn-primary'
                    }
                  ]
                };
              }
            }
          });
        modal.result.then(function (result) {
          if (result) {
            $scope.update(item).then(function () {
              goToEditPage(item.id);
            });
          }
        }, function () {
          return $log.info('edit when outdated modal dismissed at: ' + new Date());
        });
      }
    };
    $scope.newPlayer = function () {
      $location.path('/new');
    };
    $scope.duplicate = function (item) {
      PlayerService.clonePlayer(item).then(function (data) {
        $scope.data.unshift(data[1]);
        PlayerService.cachePlayers($scope.data);
        $scope.goToEditPage(data[1]);
      });
    };
    $scope.deletePlayer = function (item) {
      var modal = $modal.open({
          templateUrl: 'template/dialog/message.html',
          controller: 'ModalInstanceCtrl',
          resolve: {
            settings: function () {
              return {
                'title': 'Delete confirmation',
                'message': 'Are you sure you want to delete the player?'
              };
            }
          }
        });
      modal.result.then(function (result) {
        if (result)
          PlayerService.deletePlayer(item.id).then(function () {
            $scope.data.splice($scope.data.indexOf(item), 1);
            $scope.triggerLayoutChange();
          }, function (reason) {
            $modal.open({
              templateUrl: 'template/dialog/message.html',
              controller: 'ModalInstanceCtrl',
              resolve: {
                settings: function () {
                  return {
                    'title': 'Delete failure',
                    'message': reason
                  };
                }
              }
            });
          });
      }, function () {
        $log.info('Delete modal dismissed at: ' + new Date());
      });
    };
    $scope.update = function (player) {
      var upgradeProccess = $q.defer();
      var currentVersion = player.html5Url.split('/v')[1].split('/')[0];
      var text = '<span>' + $filter('translate')('Do you want to update this player?<br>Some features and design may be lost.') + '</span>';
      var html5lib = player.html5Url.substr(0, player.html5Url.indexOf('/v') + 2) + window.MWEMBED_VERSION + '/mwEmbedLoader.php';
      var modal = $modal.open({
          templateUrl: 'template/dialog/message.html',
          controller: 'ModalInstanceCtrl',
          resolve: {
            settings: function () {
              return {
                'title': 'Update confirmation',
                'message': text
              };
            }
          }
        });
      modal.result.then(function (result) {
        if (result)
          PlayerService.playerUpdate(player, html5lib).then(function (data) {
            player.config = angular.fromJson(data.config);
            player.html5Url = html5lib;
            player.tags = 'html5studio,player';
            upgradeProccess.resolve('upgrade finished successfully');
          }, function (reason) {
            $modal.open({
              templateUrl: 'template/dialog/message.html',
              controller: 'ModalInstanceCtrl',
              resolve: {
                settings: function () {
                  return {
                    'title': 'Update player failure',
                    'message': reason
                  };
                }
              }
            });
            upgradeProccess.reject('upgrade canceled');
          });
      }, function () {
        $log.info('Update player dismissed at: ' + new Date());
        upgradeProccess.reject('upgrade canceled');
      });
      return upgradeProccess.promise;
    };
  }
]);
;
'use strict';
var KMCServices = angular.module('KMC.services', []);
KMCServices.config([
  '$httpProvider',
  function ($httpProvider) {
    $httpProvider.defaults.useXDomain = true;
    delete $httpProvider.defaults.headers.common['X-Requested-With'];
  }
]);
KMCServices.factory('apiCache', [
  '$cacheFactory',
  function ($cacheFactory) {
    return $cacheFactory('apiCache', { capacity: 10 });
  }
]);
KMCServices.factory('sortSvc', [function () {
    var containers = {};
    var sorter = {};
    var Container = function Container(name) {
      this.name = name;
      this.elements = [];
      containers[name] = this;
    };
    Container.prototype.addElement = function (model) {
      this.elements.push(model);
    };
    Container.prototype.callObjectsUpdate = function () {
      angular.forEach(this.elements, function (model) {
        cl(model.sortVal + ' ' + model.model);
      });
    };
    Container.prototype.removeElement = function (model) {
      var index = this.elements.indexOf(model);
      if (index != -1)
        this.elements.splice(index, 1);
    };
    sorter.sortScope = '';
    sorter.register = function (containerName, model) {
      var container = typeof containers[containerName] == 'undefined' ? new Container(containerName) : containers[containerName];
      container.addElement(model);
    };
    sorter.update = function (newVal, oldVal, model) {
      var oldContainer = containers[oldVal];
      var newContainer = !containers[newVal] ? new Container(newVal) : containers[newVal];
      if (oldContainer) {
        oldContainer.removeElement(model);
      }
      newContainer.addElement(model);
      if (typeof sorter.sortScope == 'object') {
        sorter.sortScope.$broadcast('sortContainersChanged');
      }
    };
    sorter.getObjects = function () {
      return containers;
    };
    sorter.saveOrder = function (containersObj) {
      containers = containersObj;
      angular.forEach(containers, function (container) {
        container.callObjectsUpdate();
      });
    };
    return sorter;
  }]);
KMCServices.factory('PlayerService', [
  '$http',
  '$modal',
  '$log',
  '$q',
  'apiService',
  '$filter',
  'localStorageService',
  function ($http, $modal, $log, $q, apiService, $filter, localStorageService) {
    var playersCache = {};
    var currentPlayer = {};
    var previewEntry;
    var previewEntryObj;
    var playerId = 'kVideoTarget';
    var currentRefresh = null;
    var nextRefresh = false;
    var defaultCallback = function () {
      playersService.refreshNeeded = false;
      currentRefresh.resolve(true);
      currentRefresh = null;
      if (nextRefresh) {
        nextRefresh = false;
        playerRefresh();
      }
      logTime('renderPlayerDone');
    };
    var playerRefresh = function () {
      if (!currentRefresh) {
        currentRefresh = $q.defer();
        try {
          playersService.renderPlayer(defaultCallback);
        } catch (e) {
          currentRefresh.reject(e);
        }
      } else {
        nextRefresh = true;
      }
      return currentRefresh.promise;
    };
    var playersService = {
        autoRefreshEnabled: false,
        clearCurrentRefresh: function () {
          currentRefresh = null;
        },
        'refreshNeeded': false,
        getCurrentRefresh: function () {
          return currentRefresh;
        },
        'clearCurrentPlayer': function () {
          currentPlayer = {};
        },
        'setPreviewEntry': function (previewObj) {
          localStorageService.set('previewEntry', previewObj);
          previewEntry = previewObj.id;
          previewEntryObj = previewObj;
        },
        'getPreviewEntry': function () {
          if (!previewEntry) {
            return localStorageService.get('previewEntry');
          } else {
            return previewEntryObj;
          }
        },
        'renderPlayer': function (callback) {
          logTime('renderPlayer');
          if (currentPlayer && typeof kWidget != 'undefined') {
            var data2Save = angular.copy(currentPlayer.config);
            data2Save.plugins = playersService.preparePluginsDataForRender(data2Save.plugins);
            var flashvars = { 'jsonConfig': angular.toJson(data2Save) };
            if ($('html').hasClass('IE8')) {
              angular.extend(flashvars, { 'wmode': 'transparent' });
            }
            $('#Companion_300x250').empty();
            $('#Companion_728x90').empty();
            window.mw.setConfig('forceMobileHTML5', true);
            window.mw.setConfig('Kaltura.EnableEmbedUiConfJs', true);
            kWidget.embed({
              'targetId': playerId,
              'wid': '_' + currentPlayer.partnerId,
              'uiconf_id': currentPlayer.id,
              'flashvars': flashvars,
              'entry_id': previewEntry,
              'readyCallback': function (playerId) {
                document.getElementById(playerId).kBind('layoutBuildDone', function () {
                  if (typeof callback == 'function') {
                    callback();
                  }
                });
              }
            });
          } else {
            throw function () {
              return 'player could not be rendered';
            };
          }
        },
        'setKDPAttribute': function (attrStr, value) {
          var kdp = document.getElementById('kVideoTarget');
          if ($.isFunction(kdp.setKDPAttribute) && typeof attrStr != 'undefined' && attrStr.indexOf('.') != -1) {
            var obj = attrStr.split('.')[0];
            var property = attrStr.split('.')[1];
            kdp.setKDPAttribute(obj, property, value);
          }
        },
        playerRefresh: playerRefresh,
        newPlayer: function () {
          var deferred = $q.defer();
          playersService.getDefaultConfig().success(function (data, status, headers, config) {
            var request = {
                'service': 'uiConf',
                'action': 'add',
                'uiConf:objectType': 'KalturaUiConf',
                'uiConf:objType': 1,
                'uiConf:description': '',
                'uiConf:height': '395',
                'uiConf:width': '560',
                'uiConf:swfUrl': '/flash/kdp3/v3.9.8/kdp3.swf',
                'uiConf:fUrlVersion': '3.9.8',
                'uiConf:version': '161',
                'uiConf:name': 'New Player',
                'uiConf:tags': 'html5studio,player',
                'uiConf:html5Url': '/html5/html5lib/v' + window.MWEMBED_VERSION + '/mwEmbedLoader.php',
                'uiConf:creationMode': 2,
                'uiConf:config': angular.toJson(data)
              };
            apiService.setCache(false);
            apiService.doRequest(request).then(function (data) {
              playersService.setCurrentPlayer(data);
              apiService.setCache(true);
              localStorageService.set('tempPlayerID', data.id);
              deferred.resolve(data);
            }, function (reason) {
              deferred.reject(reason);
            });
          }).error(function (data, status, headers, config) {
            cl('Error getting default player config');
          });
          return deferred.promise;
        },
        clonePlayer: function (srcUi) {
          var deferred = $q.defer();
          var request = {
              service: 'multirequest',
              'action': null,
              '1:service': 'uiconf',
              '1:action': 'clone',
              '1:id': srcUi.id,
              '2:service': 'uiconf',
              '2:action': 'update',
              '2:id': '{1:result:id}',
              '2:uiConf:name': 'Copy of ' + srcUi.name,
              '2:uiConf:objectType': 'KalturaUiConf'
            };
          apiService.doRequest(request).then(function (data) {
            deferred.resolve(data);
          }, function (reason) {
            deferred.reject(reason);
          });
          return deferred.promise;
        },
        'getPlayer': function (id) {
          var foundInCache = false;
          var deferred = $q.defer();
          if (typeof currentPlayer.id != 'undefined') {
            if (currentPlayer.id == id || id == 'currentEdit') {
              currentPlayer.config.plugins = this.preparePluginsDataForRender(currentPlayer.config.plugins);
              playersService.setCurrentPlayer(currentPlayer);
              deferred.resolve(currentPlayer);
              foundInCache = true;
            }
          }
          if (!foundInCache) {
            if (typeof playersCache[id] != 'undefined') {
              playersService.setCurrentPlayer(playersCache[id]);
              deferred.resolve(currentPlayer);
              foundInCache = true;
            }
          }
          if (!foundInCache) {
            var request = {
                'service': 'uiConf',
                'action': 'get',
                'id': id
              };
            apiService.doRequest(request).then(function (result) {
              playersService.setCurrentPlayer(result);
              deferred.resolve(currentPlayer);
            });
          }
          return deferred.promise;
        },
        setCurrentPlayer: function (player) {
          if (typeof player.config == 'string') {
            player.config = angular.fromJson(player.config);
          }
          if (typeof player.config != 'undefined' && typeof player.config.plugins != 'undefined') {
            player.config = playersService.addFeatureState(player.config);
          }
          currentPlayer = player;
        },
        addFeatureState: function (data) {
          angular.forEach(data.plugins, function (value, key) {
            if ($.isArray(value))
              data.plugins[key] = {};
            if (data.plugins[key]._featureEnabled !== false)
              data.plugins[key]._featureEnabled = true;
          });
          return data;
        },
        cachePlayers: function (playersList) {
          if ($.isArray(playersList)) {
            angular.forEach(playersList, function (player) {
              playersCache[player.id] = player;
            });
          } else {
            playersCache[playersList.id] = playersList;
          }
        },
        'deletePlayer': function (id) {
          var deferred = $q.defer();
          var rejectText = $filter('translate')('Delete action was rejected: ');
          if (typeof id == 'undefined' && currentPlayer)
            id = currentPlayer.id;
          if (id) {
            var request = {
                'service': 'uiConf',
                'action': 'delete',
                'id': id
              };
            apiService.doRequest(request).then(function (result) {
              deferred.resolve(result);
            }, function (msg) {
              deferred.reject(rejectText + msg);
            });
          } else {
            deferred.reject(rejectText);
          }
          return deferred.promise;
        },
        'getRequiredVersion': function () {
          return 2;
        },
        'getDefaultConfig': function () {
          return $http.get('js/services/defaultPlayer.json');
        },
        'preparePluginsDataForRender': function (data) {
          var copyobj = data.plugins || data;
          angular.forEach(copyobj, function (value, key) {
            if (angular.isObject(value)) {
              if (typeof value._featureEnabled == 'undefined' || value._featureEnabled === false) {
                delete copyobj[key];
              } else {
                playersService.preparePluginsDataForRender(value);
              }
            } else {
              if (key == '_featureEnabled') {
                copyobj['plugin'] = true;
                delete copyobj[key];
              }
            }
          });
          return copyobj;
        },
        'savePlayer': function (data) {
          var deferred = $q.defer();
          var data2Save = angular.copy(data.config);
          data2Save.plugins = playersService.preparePluginsDataForRender(data2Save.plugins);
          var request = {
              'service': 'uiConf',
              'action': 'update',
              'id': data.id,
              'uiConf:name': data.name,
              'uiConf:tags': data.tags,
              'uiConf:description': data.description ? data.description : '',
              'uiConf:config': angular.toJson(data2Save)
            };
          apiService.doRequest(request).then(function (result) {
            playersCache[data.id] = data;
            var kmc = window.parent.kmc;
            if (kmc && kmc.preview_embed) {
              kmc.preview_embed.updateList(false);
            }
            deferred.resolve(result);
          });
          return deferred.promise;
        },
        'playerUpdate': function (playerObj, html5lib) {
          var deferred = $q.defer();
          var rejectText = $filter('translate')('Update player action was rejected: ');
          var method = 'get';
          var url = window.kWidget.getPath() + 'services.php';
          var params = {
              service: 'upgradePlayer',
              uiconf_id: playerObj.id,
              ks: localStorageService.get('ks')
            };
          if (window.IE < 10) {
            params['callback'] = 'JSON_CALLBACK';
            method = 'jsonp';
          }
          $http({
            url: url,
            method: method,
            params: params
          }).success(function (data, status, headers, config) {
            if (data['uiConfId']) {
              delete data['uiConfId'];
              delete data['widgetId'];
              delete data.vars['ks'];
            }
            var request = {
                'service': 'uiConf',
                'action': 'update',
                'id': playerObj.id,
                'uiConf:tags': 'html5studio,player',
                'uiConf:html5Url': html5lib,
                'uiConf:config': angular.toJson(data).replace('"vars":', '"uiVars":')
              };
            apiService.doRequest(request).then(function (result) {
              deferred.resolve(result);
            }, function (msg) {
              deferred.reject(rejectText + msg);
            });
          }).error(function (data, status, headers, config) {
            deferred.reject('Error updating UIConf: ' + data);
            $log.error('Error updating UIConf: ' + data);
          });
          return deferred.promise;
        }
      };
    return playersService;
  }
]);
;
KMCServices.factory('requestNotificationChannel', [
  '$rootScope',
  function ($rootScope) {
    var _START_REQUEST_ = '_START_REQUEST_';
    var _END_REQUEST_ = '_END_REQUEST_';
    var obj = { 'customStart': null };
    obj.requestStarted = function (customStart) {
      $rootScope.$broadcast(_START_REQUEST_, customStart);
      if (customStart) {
        obj.customStart = customStart;
      }
    };
    obj.requestEnded = function (customStart) {
      if (obj.customStart) {
        if (customStart == obj.customStart) {
          $rootScope.$broadcast(_END_REQUEST_, customStart);
          obj.customStart = null;
        } else
          return;
      } else
        $rootScope.$broadcast(_END_REQUEST_);
    };
    obj.onRequestStarted = function ($scope, handler) {
      $scope.$on(_START_REQUEST_, function (event, evdata) {
        if (evdata != 'ignore')
          handler();
      });
    };
    obj.onRequestEnded = function ($scope, handler) {
      $scope.$on(_END_REQUEST_, function (event, evdata) {
        if (evdata != 'ignore')
          handler();
      });
    };
    return obj;
  }
]);
KMCServices.directive('canSpin', [function () {
    return {
      require: [
        '?^loadingWidget',
        '?^navmenu'
      ],
      priority: 1000,
      link: function ($scope, $element, $attrs, controllers) {
        $scope.target = $('<div class="spinWrapper"></div>').prependTo($element);
        $scope.spinner = null;
        $scope.spinRunning = false;
        $scope.opts = {
          lines: 15,
          length: 27,
          width: 8,
          radius: 60,
          corners: 1,
          rotate: 0,
          direction: 1,
          color: '#000',
          speed: 0.6,
          trail: 24,
          shadow: true,
          hwaccel: true,
          className: 'spinner',
          zIndex: 2000000000,
          top: 'auto',
          left: 'auto'
        };
        var initSpin = function () {
          $scope.spinner = new Spinner($scope.opts).spin();
        };
        $scope.endSpin = function () {
          if ($scope.spinner)
            $scope.spinner.stop();
          $scope.spinRunning = false;
        };
        $scope.spin = function () {
          if ($scope.spinRunning)
            return;
          if ($scope.spinner === null)
            initSpin();
          $scope.spinner.spin($scope.target[0]);
          $scope.spinRunning = true;
        };
        angular.forEach(controllers, function (controller) {
          if (typeof controller != 'undefined')
            controller.spinnerScope = $scope;
        });
      }
    };
  }]);
KMCServices.directive('loadingWidget', [
  'requestNotificationChannel',
  function (requestNotificationChannel) {
    return {
      restrict: 'EA',
      scope: {},
      replace: true,
      controller: function () {
      },
      template: '<div class=\'loadingOverlay\'><a can-spin></a></div>',
      link: function (scope, element, attrs, controller) {
        element.hide();
        var startRequestHandler = function () {
          element.show();
          controller.spinnerScope.spin();
        };
        var endRequestHandler = function () {
          element.hide();
          controller.spinnerScope.endSpin();
        };
        requestNotificationChannel.onRequestStarted(scope, startRequestHandler);
        requestNotificationChannel.onRequestEnded(scope, endRequestHandler);
      }
    };
  }
]);
;
KMCServices.factory('editableProperties', [
  '$q',
  'api',
  '$http',
  function ($q, api, $http) {
    var deferred = $q.defer();
    api.then(function () {
      var method = 'get';
      var url = window.kWidget.getPath() + 'services.php?service=studioService';
      if (window.IE < 10) {
        url += '&callback=JSON_CALLBACK';
        method = 'jsonp';
      }
      $http[method](url).then(function (result) {
        var data = result.data;
        if (typeof data == 'object')
          deferred.resolve(result.data);
        else {
          cl('JSON parse error of playerFeatures');
          deferred.reject(false);
        }
      }, function (reason) {
        deferred.reject(reason);
      });
    });
    return deferred.promise;
  }
]);
KMCServices.factory('loadINI', [
  '$http',
  function ($http) {
    var iniConfig = null;
    return {
      'getINIConfig': function () {
        if (!iniConfig) {
          iniConfig = $http.get('studio.ini', {
            responseType: 'text',
            headers: { 'Content-type': 'text/plain' },
            transformResponse: function (data, headers) {
              var config = data.match(/widgets\.studio\.config \= \'(.*)\'/)[1];
              data = angular.fromJson(config);
              return data;
            }
          });
        }
        return iniConfig;
      }
    };
  }
]);
KMCServices.provider('api', function () {
  var injector = angular.injector(['ng']);
  var $q = injector.get('$q');
  var apiObj = null;
  return {
    $get: [
      'loadINI',
      function (loadINI) {
        var deferred = $q.defer();
        if (!apiObj) {
          var require = function (file, callback) {
            var head = document.getElementsByTagName('head')[0];
            var script = document.createElement('script');
            script.src = file;
            script.type = 'text/javascript';
            if (script.addEventListener) {
              script.addEventListener('load', callback, false);
            } else if (script.readyState) {
              script.onreadystatechange = callback;
            }
            head.appendChild(script);
          };
          var loadHTML5Lib = function (url) {
            var initKw = function () {
              if (typeof kWidget != 'undefined') {
                kWidget.api.prototype.type = 'POST';
                apiObj = new kWidget.api();
                deferred.resolve(apiObj);
              }
            };
            require(url, function () {
              if (typeof kWidget == 'undefined') {
                setTimeout(function () {
                  initKw();
                }, 100);
              } else {
                initKw();
              }
            });
          };
          var html5lib = null;
          try {
            var kmc = window.parent.kmc;
            if (kmc && kmc.vars && kmc.vars.studio.config) {
              var config = angular.fromJson(kmc.vars.studio.config);
              html5lib = kmc.vars.api_url + '/html5/html5lib/' + config.html5_version + '/mwEmbedLoader.php';
              loadHTML5Lib(html5lib);
            }
          } catch (e) {
            cl('Could not located parent.kmc: ' + e);
          }
          if (!html5lib) {
            loadINI.getINIConfig().success(function (data) {
              var url = data.html5lib;
              loadHTML5Lib(url);
            });
          }
        } else
          deferred.resolve(apiObj);
        return deferred.promise;
      }
    ]
  };
});
KMCServices.factory('apiService', [
  'api',
  '$q',
  '$timeout',
  '$location',
  'localStorageService',
  'apiCache',
  'requestNotificationChannel',
  '$filter',
  function (api, $q, $timeout, $location, localStorageService, apiCache, requestNotificationChannel, $filter) {
    var apiService = {
        apiObj: api,
        unSetks: function () {
          delete apiService.apiObj;
        },
        setKs: function (ks) {
          apiService.apiObj.then(function (api) {
            api.setKs(ks);
          });
        },
        setWid: function (wid) {
          apiService.getClient().then(function (api) {
            api.wid = wid;
          });
        },
        getKey: function (params) {
          var key = '';
          for (var i in params) {
            key += params[i] + '_';
          }
          return key;
        },
        listMedia: function () {
          var request = {
              'service': 'baseentry',
              'action': 'list'
            };
          return apiService.doRequest(request);
        },
        searchMedia: function (term) {
          var request = {
              'service': 'baseentry',
              'action': 'list',
              'filter:freeText': term,
              'filter:mediaTypeIn': '1,2,5,6,201',
              'filter:objectType': 'KalturaMediaEntryFilter',
              ignoreNull: '1'
            };
          return apiService.doRequest(request, true);
        },
        useCache: true,
        setCache: function (useCache) {
          apiService.useCache = useCache;
        },
        doRequest: function (params, ignoreSpinner) {
          var deferred = $q.defer();
          var params_key = apiService.getKey(params);
          if (apiCache.get(params_key) && apiService.useCache) {
            deferred.resolve(apiCache.get(params_key));
          } else {
            if (!ignoreSpinner) {
              requestNotificationChannel.requestStarted('api');
            }
            apiService.apiObj.then(function (api) {
              api.doRequest(params, function (data) {
                if (data.code) {
                  if (data.code == 'INVALID_KS') {
                    localStorageService.remove('ks');
                    $location.path('/login');
                  }
                  if (!ignoreSpinner) {
                    requestNotificationChannel.requestEnded('api');
                  }
                  var message = $filter('translate')(data.code);
                  deferred.reject(message);
                } else {
                  apiCache.put(params_key, data);
                  apiService.useCache = true;
                  if (!ignoreSpinner) {
                    requestNotificationChannel.requestEnded('api');
                  }
                  deferred.resolve(data);
                }
              });
            });
          }
          return deferred.promise;
        }
      };
    return apiService;
  }
]);
KMCServices.factory('playerTemplates', [
  '$http',
  function ($http) {
    return {
      'listSystem': function () {
        return $http.get('http://mrjson.com/data/5263e32d85f7fef869f2a63b/template/list.json');
      },
      'listUser': function () {
        return $http.get('http://mrjson.com/data/5263e32d85f7fef869f2a63b/userTemplates/list.json');
      }
    };
  }
]);
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('mcustomScrollbar', [
  '$timeout',
  function ($timeout) {
    return {
      priority: 0,
      scope: {},
      restrict: 'AC',
      link: function (scope, element, attr) {
        var afterScroll;
        var height = '99%';
        if (scope['pagename'] == 'search')
          return;
        scope.scroller = null;
        var options = scope.$eval(attr['mcustomScrollbar']);
        var timeVar = null;
        scope.$on('layoutChange', function () {
          if (timeVar) {
            $timeout.cancel(timeVar);
          }
          timeVar = $timeout(function () {
            if (scope.scroller)
              scope.scroller.mCustomScrollbar('update');
            timeVar = null;
          }, 300);
        });
        var opts = {
            horizontalScroll: false,
            mouseWheel: true,
            autoHideScrollbar: true,
            contentTouchScroll: true,
            theme: 'dark',
            set_height: height,
            advanced: {
              autoScrollOnFocus: false,
              updateOnBrowserResize: true,
              updateOnContentResize: false
            }
          };
        angular.extend(opts, options);
        var makeOrUpdateScroller = function () {
          return $timeout(function () {
            if (typeof $().mCustomScrollbar == 'function') {
              if (scope.scroller) {
                scope.scroller.mCustomScrollbar('update');
              } else {
                scope.scroller = element.mCustomScrollbar(opts);
              }
            }
          }, 1000);
        };
        if (attr['menuscroller']) {
          scope.$on('menuChange', function (e, menupage) {
            if (attr['menuscroller'] == menupage) {
              makeOrUpdateScroller();
            } else if (scope.scroller) {
              scope.scroller.mCustomScrollbar('destroy');
              scope.scroller = null;
            }
          });
        } else {
          afterScroll = makeOrUpdateScroller();
        }
        var checkScroll = function (value) {
          if (value == 'block') {
            $('#tableHead').css('padding-right', '18px');
          } else {
            $('#tableHead').css('padding-right', '0px');
          }
        };
        if (scope.$root.routeName == 'list' && $('#tableHead').length) {
          afterScroll.then(function () {
            var scrollTools = $(element).find('.mCSB_scrollTools');
            scope.scrollerCss = scrollTools.css('display');
            $timeout(function () {
              checkScroll(scope.scrollerCss);
            }, 200);
            scope.$watch(function () {
              return scope.scrollerCss = scrollTools.css('display');
            }, function (value) {
              checkScroll(value);
            });
            var timeVar;
            $(window).resize(function () {
              if (timeVar) {
                $timeout.cancel(timeVar);
              }
              timeVar = $timeout(function () {
                checkScroll(scrollTools.css('display'));
                timeVar = null;
              }, 200);
            });
          });
        }
      }
    };
    ;
  }
]);
;
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('modelCheckbox', function () {
  return {
    restrict: 'EA',
    templateUrl: 'template/formcontrols/modelCheckbox.html',
    require: '?playerRefresh',
    replace: true,
    compile: function (tElement, tAttr) {
      if (tAttr['endline'] == 'true') {
        tElement.append('<hr/>');
      }
      return function ($scope, $element, $attrs, playerRefreshCnt) {
        if (playerRefreshCnt) {
          playerRefreshCnt.setValueBased();
        }
      };
    },
    controller: [
      '$scope',
      '$element',
      '$attrs',
      function ($scope, $element, $attrs) {
        if ($scope.model === '' || typeof $scope.model == 'undefined') {
          if ($attrs.initvalue === 'true')
            $scope.model = true;
        }
      }
    ],
    scope: {
      label: '@',
      helpnote: '@',
      model: '=',
      'require': '@'
    }
  };
}).directive('prettycheckbox', function () {
  return {
    restrict: 'AC',
    require: [
      'ngModel',
      '?playerRefresh'
    ],
    template: '<a data-ng-click="check()"></a>',
    link: function (scope, $element, iAttr, controllers) {
      var ngController = controllers[0];
      var prController = controllers[1];
      if (prController) {
        prController.setValueBased();
      }
      var clickHandler = $($element).find('a');
      scope.check = function () {
        ngController.$setViewValue(!ngController.$viewValue);
      };
      var formatter = function (value) {
        var innerVal = typeof value != 'undefined' ? value : ngController.$modelValue;
        if (innerVal) {
          clickHandler.addClass('checked');
        } else {
          clickHandler.removeClass('checked');
        }
        return innerVal;
      };
      ngController.$render = formatter;
      ngController.$parsers.push(formatter);
      if (scope['require']) {
        ngController.$setValidity('required', true);
      }
    }
  };
});
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('modelColor', [function () {
    return {
      restrict: 'EA',
      priority: 10,
      require: '?playerRefresh',
      replace: true,
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          if (typeof $scope.model == 'undefined') {
            if ($attrs.initvalue)
              $scope.model = $attrs.initvalue;
            else
              $scope.model = '#ffffff';
          }
          $scope.initValue = $scope.model.toString();
        }
      ],
      link: function ($scope, $elemennt, $attrs, prController) {
        if (prController) {
          prController.setValueBased();
        }
      },
      scope: {
        'label': '@',
        'strModel': '@model',
        'helpnote': '@',
        'model': '='
      },
      templateUrl: 'template/formcontrols/modelColor.html'
    };
  }]);
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('modelNumber', [
  'menuSvc',
  function (menuSvc) {
    return {
      templateUrl: 'template/formcontrols/modelNumber.html',
      replace: true,
      restrict: 'EA',
      priority: 10,
      scope: {
        model: '=',
        helpnote: '@',
        label: '@',
        'require': '@',
        'strModel': '@model'
      },
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          $scope.defaults = {
            initvalue: parseInt($attrs['initvalue']) || 0,
            from: parseInt($attrs['from']) || 0,
            to: parseInt($attrs['to']) || 1000,
            stepSize: parseInt($attrs['stepsize']) || 1,
            readonly: false
          };
          $scope.inputForm = {};
          if (typeof $scope.model != 'number' && !(typeof $scope.model == 'string' && parseInt($scope.model))) {
            $scope.model = $scope.defaults['initvalue'] || 0;
          }
          return $scope;
        }
      ]
    };
  }
]).directive('numberInput', [
  '$timeout',
  '$q',
  function ($timeout, $q) {
    return {
      require: [
        '^modelNumber',
        'ngModel',
        '?^playerRefresh'
      ],
      restrict: 'A',
      priority: 500,
      scope: true,
      templateUrl: 'template/formcontrols/numberInput.html',
      link: function ($scope, $element, $attrs, controllers) {
        var modelScope = controllers[0];
        var ngModelCtrl = controllers[1];
        var inputControl = $element.find('input');
        modelScope.modelCntrl = ngModelCtrl;
        modelScope.inputForm = $scope.inputForm;
        var prController = controllers[2] ? controllers[2] : null;
        var timeVar = null;
        if (prController) {
          prController.setUpdateFunction(function (prScope, element) {
            inputControl.on('change softChange', function () {
              if (timeVar) {
                $timeout.cancel(timeVar);
              }
              timeVar = $timeout(function () {
                prScope.$emit('controlUpdateAllowed', prScope.prModel.key);
                timeVar = null;
              }, 1000);
            });
          });
        }
        if (typeof $scope.model != 'number' && !(typeof $scope.model == 'string' && parseInt($scope.model))) {
          ngModelCtrl.$setViewValue($scope.defaults['initvalue'] || 0);
        }
        inputControl.on('blur change', function () {
          var inValue = inputControl.val();
          if (inValue === '') {
            $scope.$apply(function () {
              ngModelCtrl.$setViewValue($scope.defaults['initvalue'] || 0);
            });
          } else {
            inValue = parseInt(inValue);
            if ($scope.passValidation(inValue)) {
              $scope.$apply(function () {
                change(inValue);
              });
            }
          }
        });
        inputControl.on('keydown', function (e) {
          if (e.keyCode == 38 || e.keyCode == 40) {
            e.preventDefault();
            $scope.$apply(function () {
              if (e.keyCode == 38) {
                $scope.increment();
              } else {
                $scope.decrement();
              }
            });
          }
        });
        ngModelCtrl.$parsers.push(function (value) {
          return modelScope.model = value;
        });
        var change = function (value) {
          inputControl.trigger('softChange');
          ngModelCtrl.$setViewValue(value);
        };
        $scope.increment = function () {
          var resultVal = ngModelCtrl.$viewValue + $scope.defaults.stepSize;
          if (resultVal < $scope.defaults.to)
            change(resultVal);
          else
            change($scope.defaults.to);
        };
        $scope.passValidation = function (resultVal) {
          if (typeof resultVal == 'number' && resultVal > $scope.defaults.from && resultVal < $scope.defaults.to)
            return true;
        };
        $scope.decrement = function () {
          var resultVal = ngModelCtrl.$viewValue - $scope.defaults.stepSize;
          if (resultVal > $scope.defaults.from)
            change(resultVal);
          else
            change($scope.defaults.from);
        };
      }
    };
  }
]);
;
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.provider('sections', [function () {
    var templates = {
        dynamic: 'template/menu/dynamicSections.html',
        tabs: 'template/menu/tabs.html'
      };
    this.$get = [
      '$compile',
      'menuSvc',
      '$templateCache',
      function ($compile, menuSvc, $templateCache) {
        return function (sectionType) {
          return {
            restrict: 'AE',
            replace: true,
            templateUrl: templates[sectionType],
            transclude: true,
            scope: function () {
              if (sectionType == 'tabs') {
                return { heading: '@' };
              } else if (sectionType == 'dynamic') {
                return { modelData: '=model' };
              }
            }(),
            controller: function ($scope, $element, $attrs) {
              if (sectionType == 'tabs') {
                $scope.tabset = { heading: $attrs['heading'] };
              } else if (sectionType == 'dynamic') {
                $scope.configData = menuSvc.getControlData($attrs.model);
                $scope.configData.sectionName = $attrs.section;
                var sections = [1];
                if (typeof $scope.modelData.sections != 'undefined') {
                  sections = $scope.modelData.sections.split(',');
                  if (sections.length <= 0) {
                    cl('section' + $attrs.section + ' has been reset because of bad data');
                    sections = [1];
                    $scope.modelData.sections = 1;
                  }
                } else {
                  $scope.modelData = menuSvc.getOrMakeModelData($attrs.model, true);
                  $scope.modelData.sections = 1;
                }
                $scope.modelData['_featureEnabled'] = true;
                var removeHandel = angular.element('<a class="btn btn-xs" ng-click="removeSection($event)">X</a>');
                var wrapper = angular.element('<div class="dynSection" index=""></div>');
                var box = wrapper.append(removeHandel);
                var createSection = function (index) {
                  var newSection = $compile(box.clone().attr('index', index))($scope);
                  var Controls = $templateCache.get('dynamicSections/' + $scope.configData.sectionName).clone();
                  modifyModel(Controls, index);
                  var html = $compile(Controls)(menuSvc.menuScope);
                  $element.find('div.dynSections').append(newSection.append(html));
                };
                var getModelByIndex = function (orginalModel, index) {
                  if (typeof index == 'undefined')
                    index = '';
                  var modelPre = $scope.configData.modelPre;
                  var modelPost = $scope.configData.modelPost;
                  if (modelPre.indexOf('#') != -1)
                    modelPre = modelPre.replace('#', index);
                  if (modelPost.indexOf('#') != -1)
                    modelPost = modelPost.replace('#', index);
                  return modelPre + orginalModel + modelPost;
                };
                var modifyModel = function (template, modelindex) {
                  template.find('[model]').each(function (index, control) {
                    $(control).attr('model', getModelByIndex($(control).attr('model'), modelindex));
                  });
                };
                var removeSectionData = function (inputHolder) {
                  inputHolder.find('[model]').each(function (index, obj) {
                    var model = $(obj).attr('model');
                    var parent = menuSvc.getModalData(menuSvc.getKnownParent(model));
                    if (parent) {
                      var child = model.substr(model.lastIndexOf('.') + 1);
                      if (typeof parent[child] != 'undefined') {
                        delete parent[child];
                      }
                    }
                  });
                };
                angular.forEach(sections, function (value) {
                  if (value == 1 && $scope.configData.remove1 === true) {
                    createSection();
                  } else
                    createSection(value);
                });
                $scope.addSection = function () {
                  createSection(sections.length + 1);
                  sections.push(sections.length + 1);
                  $scope.modelData.sections += ',' + sections.length;
                };
                $scope.removeSection = function (e) {
                  var dynbox = $(e.target).parent();
                  var index = $(dynbox).attr('index');
                  if (index === true || index === '')
                    index = 0;
                  removeSectionData(dynbox);
                  dynbox.remove();
                  sections.splice(index - 1, 1);
                  $scope.modelData.sections = sections.join(',');
                };
              }
            },
            link: function ($scope, $element, $attrs, controller, transclude) {
              if (sectionType == 'dynamic') {
              }
            }
          };
        };
      }
    ];
    ;
  }]);
;
DirectivesModule.directive('kaDynamicSection', [
  'sections',
  function (sections) {
    return sections('dynamic');
  }
]);
DirectivesModule.directive('kaTabs', [
  'sections',
  function (sections) {
    return sections('tabs');
  }
]);
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('modelSelect', [
  'menuSvc',
  function (menuSvc) {
    return {
      replace: true,
      restrict: 'EA',
      priority: 1,
      require: ['?parentContainer'],
      scope: {
        label: '@',
        model: '=',
        initvalue: '@',
        helpnote: '@',
        selectOpts: '@',
        'strModel': '@model',
        'require': '@'
      },
      compile: function (tElement, tAttr) {
        if (tAttr['endline'] == 'true') {
          tElement.append('<hr/>');
        }
        return function ($scope, $element, $attrs, controllers) {
          var parentCntrl = controllers[0] ? controllers[0] : null;
          if (parentCntrl) {
            var pubObj = {
                model: $attrs.model,
                label: $attrs.label.replace('Location', ''),
                sortVal: menuSvc.getControlData($attrs.model).sortVal
              };
            parentCntrl.register($scope.model, pubObj);
            $scope.$watch('model', function (newVal, oldVal) {
              if (newVal != oldVal)
                parentCntrl.update(newVal, oldVal, pubObj);
            });
          }
          var menuData = menuSvc.getControlData($attrs.model);
          if (menuData) {
            $scope.options = menuData.options;
          }
        };
      },
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          if (!$scope.selectOpts) {
            $scope.selectOpts = {};
          }
          if ($attrs.placehold) {
            $scope.selectOpts['placeholder'] = $attrs.placehold;
          }
          if (!$attrs.showSearch) {
            $scope.selectOpts.minimumResultsForSearch = -1;
          }
          $scope.options = [];
          $scope.checkSelection = function (value) {
            if (value == $scope.model)
              return true;
            else if (typeof value == 'number' && parseFloat($scope.model) == value) {
              return true;
            }
            return false;
          };
          $scope.initSelection = function () {
            if ($scope.model === '' || typeof $scope.model == 'undefined') {
              $scope.model = $attrs.initvalue;
            }
            return $scope.model;
          };
          $scope.selectOpts.initSelection = $scope.initSelection();
          $scope.uiselectOpts = angular.toJson($scope.selectOpts);
          $scope.setOptions = function (optsArr) {
            $scope.options = optsArr;
          };
        }
      ],
      templateUrl: 'template/formcontrols/modelSelect.html'
    };
  }
]);
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('modelText', [
  'menuSvc',
  function (menuSvc) {
    return {
      replace: true,
      restrict: 'EA',
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          $scope.type = 'text';
          var form = menuSvc.menuScope.playerEdit;
          var makeWatch = function (value, retProp) {
            $scope.$watch(function () {
              if (form[$attrs['model']]) {
                var inputCntrl = form[$attrs['model']];
                if (typeof inputCntrl.$error[value] != 'undefined');
                return inputCntrl.$error[value];
              }
              return false;
            }, function (newVal) {
              $scope[retProp] = newVal;
            });
          };
          if ($scope.require) {
            makeWatch('required', 'reqState');
          }
          if ($attrs['initvalue'] && (typeof $scope.model == 'undefined' || $scope.model === '')) {
            $scope.model = $attrs['initvalue'];
          }
          if ($attrs['validation'] == 'url' || $attrs['validation'] == 'email') {
            makeWatch($attrs['validation'], 'valState');
            $scope.type = $attrs['validation'];
          } else {
            var pattern = $attrs['validation'];
            var isValid, regex;
            try {
              regex = new RegExp(pattern, 'i');
              isValid = true;
            } catch (e) {
              isValid = false;
            }
            if (isValid) {
              $scope.validation = regex;
              makeWatch('pattern', 'valState');
            }
          }
          if (typeof $scope.validation == 'undefined') {
            $scope.validation = {
              test: function () {
                return true;
              },
              match: function () {
                return true;
              }
            };
          }
          $scope.isDisabled = false;
        }
      ],
      scope: {
        'label': '@',
        'model': '=',
        'icon': '@',
        'placehold': '@',
        'strModel': '@model',
        'helpnote': '@',
        'require': '@'
      },
      compile: function (tElement, tAttr) {
        if (tAttr['endline'] == 'true') {
          tElement.append('<hr/>');
        }
        return function ($scope, $element, $attrs) {
          var inputElm = $($element).find('input');
          if ($attrs.initvalue) {
            inputElm.on('click', function (e) {
              if (inputElm.val() == $attrs.initvalue) {
                e.preventDefault();
                inputElm.select();
              }
            });
          }
        };
      },
      templateUrl: 'template/formcontrols/modelText.html'
    };
    ;
  }
]);
;
DirectivesModule.directive('ngPlaceholder', function () {
  return {
    restrict: 'A',
    require: 'ngModel',
    link: function (scope, element, attr, ctrl) {
      var placeholder = (attr['type'] == 'url' || attr['valType'] == 'url') && attr['ngPlaceholder'].length <= 0 ? 'http://' : attr['ngPlaceholder'];
      var placehold = function () {
        element.val(placeholder);
        if (attr['require']) {
          ctrl.$setValidity('required', false);
        }
        element.addClass('placeholder');
      };
      var unplacehold = function () {
        if (placeholder != 'http://')
          element.val('');
        element.removeClass('placeholder');
      };
      var value = ctrl.$viewValue;
      var makePlace = function (val) {
        value = val;
        if (!val && placeholder.length > 0) {
          placehold();
          return '';
        }
        return val;
      };
      ctrl.$parsers.unshift(makePlace);
      ctrl.$formatters.unshift(makePlace);
      element.bind('focus', function () {
        if (value === '' || value == placeholder)
          unplacehold();
      });
      element.bind('blur', function () {
        if (element.val() === '' || value == placeholder)
          placehold();
      });
    }
  };
  ;
});
;
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('multipleValueInput', function () {
  return {
    templateUrl: 'template/formcontrols/multipleValueInput.html',
    replace: true,
    scope: {
      model: '=',
      label: '@',
      icon: '@'
    },
    controller: [
      '$scope',
      '$element',
      '$attrs',
      function ($scope, $element, $attrs) {
        $scope.splitModel = [];
        if ($attrs['initvalue'] && !$scope.model) {
          $scope.model = $attrs['initvalue'];
        }
        $scope.remove = function (index) {
          $scope.splitModel.splice(index, 1);
        };
        $scope.splitChar = $attrs['splitChar'] || ';';
        if ($scope.model) {
          $scope.splitModel = $scope.model.split($scope.splitChar);
          if ($scope.splitModel[$scope.splitModel.length - 1] === '') {
            $scope.splitModel.splice($scope.splitModel.length - 1, 1);
          }
        }
        $scope.add = function () {
          $scope.splitModel.push('');
        };
      }
    ],
    compile: function (tElem, tAttrs) {
      if (tAttrs['endline']) {
        tElem.append('<hr/>');
      }
      return function ($scope, $element, $attrs) {
        $scope.$watchCollection('splitModel', function (newVal, oldVal) {
          if (newVal === oldVal)
            return;
          var str = '';
          angular.forEach(newVal, function (value) {
            if ($.trim(value).length) {
              str += value + $scope.splitChar;
            }
          });
          if (str !== $scope.model) {
            $scope.model = str;
          }
        });
      };
    }
  };
});
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('playerRefresh', [
  'PlayerService',
  'menuSvc',
  '$timeout',
  '$interval',
  function (PlayerService, menuSvc, $timeout, $interval) {
    return {
      restrict: 'A',
      priority: 100,
      scope: true,
      require: ['?ngModel'],
      controllerAs: 'prController',
      controller: function ($scope, $element, $attrs) {
        $scope.options = { valueBased: false };
        $scope.prModel = {
          key: '',
          value: null,
          valueChanged: false,
          oldValue: null
        };
        $scope.updateFunction = function (prScope, elem) {
          var triggerElm;
          if (elem.is('input') || elem.is('select')) {
            triggerElm = elem;
          } else {
            triggerElm = $(elem).find('input[ng-model], select[ng-model]');
          }
          var event = 'change';
          if (triggerElm.is('input')) {
            event = 'blur';
          }
          triggerElm.on(event, function () {
            prScope.$emit('controlUpdateAllowed', prScope.prModel.key);
          });
        };
        var timeOutRun = null;
        $scope.$on('$destroy', function () {
          if (timeOutRun) {
            $interval.cancel(timeOutRun);
          }
        });
        var ctrObj = {
            makeRefresh: function () {
              PlayerService.playerRefresh($attrs['playerRefresh']).then(function () {
                $scope.prModel.valueChanged = false;
              });
            },
            setValueBased: function () {
              $scope.options.valueBased = true;
            },
            setUpdateFunction: function (func) {
              $scope.updateFunction = func;
            }
          };
        return ctrObj;
      },
      link: function (scope, iElement, iAttrs, controllers) {
        if (iAttrs['playerRefresh'] == 'boolean') {
          scope.options.valueBased = true;
        }
        var ngController = controllers[0] ? controllers[0] : null;
        if (iAttrs['playerRefresh'] != 'false') {
          if (!ngController) {
            scope.prModel.key = iAttrs['model'];
          } else {
            scope.prModel.key = iAttrs['ngModel'];
          }
        }
        $timeout(function () {
          if (!scope.options.valueBased) {
            scope.updateFunction(scope, iElement);
            scope.$parent.$parent.$on('controlUpdateAllowed', function (e, modelKey) {
              if (modelKey == scope.prModel.key && scope.prModel.valueChanged === true) {
                e.stopPropagation();
                scope.prController.makeRefresh();
              }
            });
          }
          var actOnModelChange = function () {
            if (iAttrs['playerRefresh'] == 'true') {
              if (scope.prModel.key == 'featureModelCon._featureEnabled') {
                scope.prController.makeRefresh();
              } else {
                if (PlayerService.autoRefreshEnabled) {
                  if (!scope.options.valueBased) {
                    scope.prModel.valueChanged = true;
                    PlayerService.refreshNeeded = true;
                  } else {
                    scope.prController.makeRefresh();
                  }
                } else {
                  PlayerService.refreshNeeded = true;
                }
              }
            } else {
              if (iAttrs['playerRefresh'] == 'aspectToggle') {
                $('#spacer').toggleClass('narrow');
              } else {
                PlayerService.setKDPAttribute(iAttrs['playerRefresh'], scope.prModel.value);
              }
            }
          };
          if (!ngController) {
            scope.prModel.value = menuSvc.getModalData(iAttrs['model']);
            scope.$watch(function () {
              return scope.prModel.value = menuSvc.getModalData(iAttrs['model']);
            }, function (newVal, oldVal) {
              if (newVal != oldVal) {
                actOnModelChange();
              }
            });
          } else {
            scope.prModel.value = ngController.$modelValue;
            ngController.$viewChangeListeners.push(function () {
              scope.prModel.oldValue = scope.prModel.value;
              scope.prModel.value = ngController.$viewValue;
              if (scope.prModel.oldValue != scope.prModel.value) {
                actOnModelChange();
              }
            });
          }
        }, 100);
      }
    };
  }
]);
'use strict';
var DirectivesModule = angular.module('KMC.directives');
DirectivesModule.directive('select2Data', [
  'menuSvc',
  '$filter',
  function (menuSvc, $filter) {
    return {
      replace: true,
      restrict: 'EA',
      scope: {
        'label': '@',
        'model': '=',
        'icon': '@',
        'helpnote': '@',
        'initvalue': '@',
        'require': '@',
        'strModel': '=model'
      },
      controller: [
        '$scope',
        '$element',
        '$attrs',
        function ($scope, $element, $attrs) {
          $scope.selectOpts = {};
          if (typeof $attrs['flatmodel'] != 'undefined') {
            var modelData = menuSvc.getModalData('data.' + $attrs['flatmodel']);
            if (modelData) {
              $scope.model = {
                'id': modelData,
                'text': modelData
              };
            }
          }
          if (typeof $attrs['allowCustomValues'] != 'undefined') {
            $scope.selectOpts.createSearchChoice = function (term) {
              var translatedText = $filter('translate')($attrs['allowCustomValues']);
              return {
                id: term,
                text: term + ' (' + translatedText + ')'
              };
            };
          }
          $scope.selectOpts['data'] = menuSvc.doAction($attrs.source);
          if ($attrs.query) {
            $scope.selectOpts['data'].results = [];
            if ($attrs.minimumInputLength) {
              $scope.selectOpts['minimumInputLength'] = $attrs.minimumInputLength;
            }
            $scope.selectOpts['query'] = menuSvc.getAction($attrs.query);
          }
          if ($attrs.placehold) {
            $scope.selectOpts['placeholder'] = $attrs.placehold;
          }
          $scope.selectOpts['width'] = $attrs.width;
        }
      ],
      templateUrl: 'template/formcontrols/select2Data.html',
      compile: function (tElement, tAttr) {
        if (tAttr['endline'] == 'true') {
          tElement.append('<hr/>');
        }
        if (tAttr.showEntriesThumbs == 'true') {
          tElement.find('input').attr('list-entries-thumbs', 'true');
        }
        if (tAttr.placeholder)
          tElement.find('input').attr('data-placeholder', tAttr.placeholder);
        return function ($scope, $element, $attrs) {
          if (typeof $attrs['flatmodel'] != 'undefined') {
            $scope.$watch('model', function (newVal, oldVal) {
              if (newVal != oldVal) {
                menuSvc.setModelData($attrs.flatmodel, newVal.id);
              }
            });
          }
        };
      }
    };
  }
]);