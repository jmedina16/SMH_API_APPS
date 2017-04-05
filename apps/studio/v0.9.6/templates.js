angular.module('KMCModule').run(['$templateCache', function($templateCache) {
  'use strict';

  $templateCache.put('template/dialog/message.html',
    "<div class=\"modal-content\"><div class=\"modal-header\"><button type=\"button\" class=\"close\" ng-click=\"cancel()\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button><h4 class=\"modal-title\">{{title}}</h4></div><div class=\"modal-body\"><p ng-bind-html=\"message | HTMLunsafe\"></p></div><div class=\"modal-footer\"><button ng-repeat=\"btn in buttons\" ng-click=\"close(btn.result)\" class=\"btn\" ng-class=\"btn.cssClass\">{{ btn.label}}</button></div></div>"
  );


  $templateCache.put('template/dialog/textarea.html',
    "<div class=\"modal-content\"><div class=\"modal-header\"><button type=\"button\" class=\"close\" ng-click=\"cancel()\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button><h4 class=\"modal-title\">{{title}}</h4></div><div class=\"modal-body\"><textarea class=\"fullwidth\" rows=\"10\" ng-model=\"message\">{{message}}</textarea></div><div class=\"modal-footer\"><button ng-repeat=\"btn in buttons\" ng-click=\"close(btn.result,message)\" class=\"btn\" ng-class=\"btn.cssClass\">{{ btn.label}}</button></div></div>"
  );


  $templateCache.put('template/formcontrols/infoAction.html',
    "<label><span class=\"control-label\"><span ng-bind-html=\"label\"></span></span><i ng-if=\"icon\" class=\"icon {{icon}}\"></i> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span> <span class=\"button-wrapper\"><button type=\"button\" ng-if=\"check(action)\" ng-click=\"btnAction(action)\" class=\"btn btn-default {{btnClass}}\">{{ btnLabel }}</button></span> <span class=\"infodata control-data\">{{ model }}</span></label>"
  );


  $templateCache.put('template/formcontrols/modelButton.html',
    "<label ng-if=\"check(action)\"><i class=\"icon {{icon}}\"></i> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span> <button type=\"button\" ng-click=\"btnAction(action)\" class=\"btn btn-default {{btnClass}}\">{{ label }}</button></label>"
  );


  $templateCache.put('template/formcontrols/modelCheckbox.html',
    "<label><span class=\"control-label\" ng-bind-html=\"label\"></span> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span><div dname=\"strModel\" require=\"require\" class=\"prettycheckbox pull-right\" ng-model=\"model\"></div></label>"
  );


  $templateCache.put('template/formcontrols/modelColor.html',
    "<label><span class=\"control-label\" ng-bind-html=\"label\"></span> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span> <span data-color=\"{{initValue}}\" ng-model=\"model\" colorpicker=\"\" class=\"colorExample\" ng-style=\"{'background-color': model}\"></span></label>"
  );


  $templateCache.put('template/formcontrols/modelEdit.html',
    "<label><span class=\"control-label\" ng-bind-html=\"label\"></span><div class=\"fullwidth\"><i ng-if=\"icon\" class=\"icon {{icon}}\"></i> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span><input ng-required=\"{{require}}\" dname=\"strModel\" type=\"text\" readonly ng-model=\"model\" ng-click=\"doModal()\"></div></label>"
  );


  $templateCache.put('template/formcontrols/modelNumber.html',
    "<label class=\"spinEdit\"><span class=\"control-label\" ng-bind-html=\"label\"></span> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span><div class=\"pull-right\"><div number-input=\"\" dname=\"strModel\" ng-form=\"inputForm\" ng-disabled=\"isDisabled\" defaults=\"defaults\" ng-model=\"model\" class=\"input-group\"></div></div><div ng-show=\"inputForm.$invalid\"><span class=\"help-inline alert-danger\" ng-show=\"inputForm.$error['max']\">{{'The maximum value is'|translate}} {{defaults.to}}</span> <span class=\"help-inline alert-danger\" ng-show=\"inputForm.$error['min']\">{{'The minimum value is' |translate}} {{defaults.from}}</span> <span class=\"help-inline alert-danger\" ng-show=\"inputForm.$error['pattern']\">{{'Only numbers are accepted' |translate}}</span></div></label>"
  );


  $templateCache.put('template/formcontrols/modelRadio.html',
    "<div class=\"form-element\"><div class=\"radioLabel\"><span class=\"control-label\" ng-bind-html=\"label\"></span> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span></div><div class=\"form-group\"><label ng-repeat=\"option in options\"><input dname=\"$parent.strModel\" value=\"{{ option.value }}\" type=\"radio\" ng-required=\"!$parent.model && require\" ng-model=\"$parent.model\" pretty-radio=\"\"><span class=\"optionLabel\">{{ option.label }}</span></label></div></div>"
  );


  $templateCache.put('template/formcontrols/modelSelect.html',
    "<label><span class=\"control-label\"><span ng-bind-html=\"label\"></span> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span></span> <span class=\"pull-right\"><select dname=\"strModel\" ng-required=\"require\" ui-select2=\"{{uiselectOpts}}\" ng-model=\"model\"><option ng-selected=\"checkSelection(item.value)\" value=\"{{item.value}}\" ng-repeat=\"item in options\">{{item.label}}</option></select></span></label>"
  );


  $templateCache.put('template/formcontrols/modelTags.html',
    "<label><span class=\"control-label\" ng-bind-html=\"label\"></span> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span><div class=\"fullwidth\"><i ng-if=\"icon\" class=\"icon {{icon}}\"></i><input dname=\"strModel\" type=\"text\" ng-required=\"require\" ui-select2=\"selectOpts\" ng-model=\"model\"></div></label>"
  );


  $templateCache.put('template/formcontrols/modelText.html',
    "<div class=\"form-element\"><span class=\"control-label\"><span ng-bind-html=\"label\"></span> <i ng-if=\"icon\" class=\"icon {{icon}}\"></i> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span></span> <span class=\"inputHolder\"><input ng-disabled=\"isDisabled\" dname=\"strModel\" ng-placeholder=\"{{placehold}}\" ng-pattern=\"validation\" ng-required=\"{{require}}\" class=\"form-control\" type=\"text\" val-type=\"{{type}}\" ng-model=\"model\"><span class=\"help-inline alert-danger\" ng-show=\"require && reqState\">{{\"This field is required.\" | translate }}</span> <span class=\"help-inline alert-danger\" ng-show=\"validation && valState\">{{\"This field doesn't have the right format of value.\" | translate}}</span></span></div>"
  );


  $templateCache.put('template/formcontrols/multipleValueInput.html',
    "<label><span class=\"control-label\" ng-bind-html=\"label\"></span> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span> <span class=\"pull-right\" data-ng-click=\"add()\">+Add</span> <i ng-if=\"icon\" class=\"icon {{icon}}\"></i> <span class=\"inputHolder\"><span class=\"input-group\" ng-repeat=\"single in splitModel track by $index\"><input class=\"form-control\" type=\"text\" ng-model=\"splitModel[$index]\"><span class=\"input-group-addon\" data-ng-click=\"remove($index)\">X</span></span></span></label>"
  );


  $templateCache.put('template/formcontrols/numberInput.html',
    "<input type=\"number\" ng-disabled=\"$parent.isDisabled\" step=\"{{defaults.stepsize}}\" ng-model=\"$parent.model\" min=\"{{defaults.from}}\" max=\"{{defaults.to}}\" dname=\"$parent.strModel\" ng-readonly=\"defaults.readonly\" ng-required=\"require\" ng-pattern=\"/[0-9].*/\" class=\"small pull-left\"><div class=\"input-group-addon spinedit\"><i class=\"glyphicon glyphicon-chevron-up\" ng-click=\"increment()\"></i> <i class=\"glyphicon glyphicon-chevron-down\" ng-click=\"decrement()\"></i></div>"
  );


  $templateCache.put('template/formcontrols/readOnly.html',
    "<label class=\"clearfix\"><span class=\"control-label\"><span ng-bind-html=\"label\"></span> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span></span> <i ng-if=\"icon\" class=\"icon {{icon}}\"></i> <span class=\"control-data infodata\">{{ model }}</span></label>"
  );


  $templateCache.put('template/formcontrols/select2Data.html',
    "<label><span class=\"control-label\" ng-bind-html=\"label\"></span> <i ng-if=\"icon\" class=\"icon {{icon}}\"></i> <span class=\"featureDesc\" ng-if=\"helpnote\"><i class=\"glyphicon glyphicon-question-sign\" ng-click=\"openTooltip($event)\" ng-mouseover=\"openTooltip($event)\" data-tooltip-trigger=\"customShow\" data-tooltip-html-unsafe=\"$scope.helpnote\"></i></span><input dname=\"strModel\" ng-required=\"require\" type=\"hidden\" ui-select2=\"selectOpts\" ng-model=\"model\"></label>"
  );


  $templateCache.put('template/formcontrols/sortOrder.html',
    "<div class=\"form-element\"><ul><li ng-repeat=\"container in containers\"><div class=\"sortContainer\"><h4>{{container.name}}</h4><ul ui-sortable=\"sortableOptions\" ng-model=\"container.elements\"><li class=\"sortObj\" ng-repeat=\"obj in container.elements\"><i class=\"glyphicon glyphicon-chevron-right\"></i>{{ obj.label }}</li></ul></div></li></ul></div>"
  );


  $templateCache.put('template/menu/dynamicSections.html',
    "<li class=\"form-element dyanmicSections\"><span class=\"control-label\">{{configData.title}}</span> <a ng-click=\"addSection()\" class=\"btn btn-primary\">{{configData.addButtonTxt}}</a><div class=\"dynSections\"></div></li>"
  );


  $templateCache.put('template/menu/featureMenu.html',
    "<div class=\"form-element\"><span class=\"pull-right\" ng-if=\"featureCheckbox\"><div class=\"prettycheckbox\" player-refresh=\"true\" ng-click=\"openFeature()\" ng-model=\"featureModelCon._featureEnabled\"></div></span><div class=\"header\" ng-click=\"toggleFeature()\"><span class=\"featureLabel\" ng-bind-html=\"label\"></span> <i ng-class=\"{rotate90:!isCollapsed}\" class=\"glyphicon glyphicon-play\"></i></div><span ng-hide=\"isCollapsed\" class=\"description\" ng-bind-html=\"description\"></span><div ng-transclude=\"\" ng-class=\"{disabled:isDisabled}\" collapse=\"isCollapsed\"></div></div>"
  );


  $templateCache.put('template/menu/menuPage.html',
    "<li><div class=\"header\" data-ng-click=\"selfOpenLevel()\"><span ng-if=\"parentPage && featureCheckbox\" class=\"pull-right\"><span class=\"prettycheckbox\" player-refresh=\"true\" ng-click=\"selfOpenLevel()\" ng-model=\"featureModelCon._featureEnabled\"></span></span> <a class=\"menu-level-trigger\">{{(parentPage) ? label : ''}}</a></div><div class=\"mp-level\" menuscroller=\"{{pagename}}\" mcustom-scrollbar=\"{autoHideScrollbar: false}\"><a class=\"mp-back\" ng-click=\"goBack()\" ng-show=\"parentPage\">Back to {{parentLabel}}</a><div class=\"subPageHeaderWrapper\"><h2 class=\"subMenuHeader\">{{label}}</h2><span ng-if=\"parentPage\" class=\"pull-right prettycheckbox\" player-refresh=\"true\" ng-model=\"featureModelCon._featureEnabled\">Check to enable</span></div><span class=\"levelDesc\" ng-bind-html=\"description\"></span><ul ng-class=\"{disabled:isDisabled}\" class=\"menuList\" ng-transclude=\"\"></ul></div></li>"
  );


  $templateCache.put('template/menu/navmenu.html',
    "<div id=\"mp-inner\"><div id=\"mp-base\" can-spin=\"\" class=\"mp-level\"><ul ng-transclude=\"\"></ul></div></div>"
  );


  $templateCache.put('template/menu/tabs.html',
    "<div class=\"form-element\"><span class=\"control-label\">{{heading}}</span><div ng-transclude=\"\"></div><hr class=\"divider\"></div>"
  );


  $templateCache.put('view/edit.html',
    "<div menu-head=\"\"><li><a data-ng-click=\"changeActiveItem('search')\" ng-class=\"{active: activeItem == 'search'}\" class=\"icon icon-TabSearch\" tooltip-placement=\"right\" tooltip=\"Search for menu properties\"></a></li></div><!-- </menu-head>--><div id=\"outerWrap\"><div id=\"mp-pusher\" ng-controller=\"menuCntrl\"><form navmenu=\"\" novalidate name=\"playerEdit\" onbeforeunload=\"\" id=\"mp-menu\" ng-show=\"menuInitDone\"><div class=\"nohover\" menu-level=\"\" pagename=\"search\" label=\"Menu Search\"><li ng-form=\"\" ng-controller=\"menuSearchCtl\" class=\"form-inline\" id=\"menuSearch\" ng-submit=\"searchMenuFn(value)\" role=\"search\"><div class=\"form-group\" style=\"height: 360px\"><div class=\"input-group merged\"><span class=\"input-group-addon\"><i class=\"icon icon-TabSearch\"></i></span><input typeahead-on-select=\"checkSearch()\" ng-change=\"checkSearch()\" type=\"text\" typeahead=\"item for item in menuData | filter:$viewValue | limitTo:12\" data-typeaheadonselect=\"checkSearch()\" class=\"form-control forceRightRadius\" placeholder=\"{{'Search for menu property'| translate}}\" ng-model=\"menuSearch\"></div><span class=\"help-block\" ng-show=\"notFound\">{{ 'The property was not found '| translate}}</span></div></li></div></form><div class=\"wrapper\" ng-controller=\"editPageDataCntrl\"><div id=\"menuTriggerWrap\" ng-show=\"$parent.menuInitDone\"><a class=\"menu-trigger\" data-ng-click=\"togglemenu($event)\"><i class=\"icon-Close\"></i> <span class=\"sr-only\">{{'Toggle navigation'| translate}}</span></a></div><div id=\"headerRow\" class=\"container padTop\"><span class=\"pull-right\"><button ng-click=\"cancel()\" class=\"btn btn-default\">{{'LIST_BACK_BTN'| translate}}</button> <button ng-disabled=\"!saveEnabled()\" ng-click=\"save()\" class=\"btn btn-primary\">{{'Save Player Settings'| translate}}</button></span> <span class=\"control-group pull-left\"><label require=\"require\" class=\"prettycheckbox\" ng-model=\"autoRefreshEnabled\"></label><span id=\"autoPreviewLabel\">Auto Preview</span></span> <button class=\"btn btn-default\" ng-hide=\"autoRefreshEnabled\" ng-disabled=\"!checkPlayerRefresh()\" ng-class=\"{'btn-success':checkPlayerRefresh()}\" ng-click=\"refreshPlayer()\"><i class=\"glyphicon glyphicon-refresh\">&nbsp;</i>{{'Preview Changes'|translate}}</button></div><div class=\"container\" ng-if=\"debug\"><div ng-show=\"formValidation()\" class=\"container\"><p>these property values are not valid:</p><div ng-repeat=\"obj in formValidation()\"><p ng-repeat=\"objSub in obj\">{{objSub.$name}}:{{objSub.$error | json}}</p></div></div><pre style=\"width: 100%;height:300px;overflow-x: scroll\"> {{getDebugInfo() | json}}\"</pre></div><!-- Companion placeholders for VAST --><div id=\"Companion_300x250\"></div><div id=\"Companion_728x90\"></div><div class=\"container\" id=\"videoWrapper\"><div id=\"spacer\"></div><div id=\"kVideoContainer\"><div id=\"kVideoTarget\" itemprop=\"video\" itemscope itemtype=\"http://schema.org/VideoObject\"></div></div></div></div></div></div>"
  );


  $templateCache.put('view/list.html',
    "<div class=\"fluid-container fullheight\"><div id=\"wrapper\"><div id=\"header\"><h2 id=\"pageHeader\">{{(title) ? title : \"Players list\"}}</h2><small ng-hide=\"showSubTitle\">{{'In this page you can customize the look and the functionality of your players'| translate}}</small><div class=\"padTop clearfix\"><div class=\"pull-right\"><button type=\"button\" class=\"btn btn-primary\" data-ng-click=\"newPlayer()\"><i class=\"glyphicon glyphicon-plus\">&nbsp;</i>{{'Add New player'| translate}}</button></div><div class=\"noPadding col-xs-5\"><form class=\"form-inline\" id=\"listsearch\" role=\"search\"><div class=\"input-group merged\"><span class=\"input-group-addon\"><i class=\"icon-TabSearch\"></i></span><input type=\"text\" typeahead=\"player.name for player in data | filter:$viewValue | limitTo:8\" class=\"form-control\" placeholder=\"{{'Search by name or id'| translate}}\" ng-model=\"search\"></div></form></div></div></div><div id=\"playerList\" class=\"scrollerWrap maxViewPort\"><div class=\"row\" id=\"tableHead\" notselectable=\"true\"><div class=\"pull-left playerThumbWrapper\">{{'Preview'| translate }}</div><div class=\"col-xs-3\"><a data-ng-click=\"sortBy('name')\">{{ 'Name' | translate }} <i class=\"glyphicon\" ng-class=\"{'glyphicon-chevron-down':(sort.sortCol=='name' && sort.reverse==false),\n" +
    "                'glyphicon-chevron-up':(sort.sortCol=='name' && sort.reverse == true)}\"></i></a></div><div class=\"idCol pull-left text-center\">{{ 'ID' | translate }}</div><div class=\"col-xs-2 text-center\"><a data-ng-click=\"sortBy('updatedAt')\">{{ 'Save Date' | translate }} <i class=\"glyphicon\" ng-class=\"{'glyphicon-chevron-down':(sort.sortCol=='updatedAt' && sort.reverse==false),\n" +
    "                'glyphicon-chevron-up':(sort.sortCol=='updatedAt' && sort.reverse == true)}\"></i></a></div><div class=\"col-lg-2 text-center visible-lg\"><a data-ng-click=\"sortBy('createdAt')\">{{ 'Creation Date' | translate }} <i class=\"glyphicon\" ng-class=\"{'glyphicon-chevron-down':(sort.sortCol=='createdAt' && sort.reverse==false),\n" +
    "                'glyphicon-chevron-up':(sort.sortCol=='createdAt' && sort.reverse == true)}\"></i></a></div><div class=\"pull-left\">{{ 'Actions' | translate }}</div></div><div class=\"scroller\" mcustom-scrollbar=\"{autoHideScrollbar:false}\"><table id=\"listTable\"><tbody><tr class=\"row repeat-animation\" on-finish-render=\"\" ng-repeat=\"item in filtered =  (data| filter:search) | startFrom:(currentPage - 1) * maxSize | limitTo:maxSize | orderBy:sort.sortCol:sort.reverse  track by item.id\"><td class=\"playerThumbWrapper\"><img class=\"playerThumb\" ng-src=\"{{getThumbnail(item)}}\"></td><td class=\"col-xs-3\"><a data-ng-click=\"goToEditPage(item,$event)\" ng-href=\"edit/{{item.id}}\">{{item.name}}</a><div class=\"alertsWrapper\"><span class=\"alert alert-warning\" ng-show=\"checkVersionNeedsUpgrade(item)\"><small>{{' This player requires updating'| translate}}</small></span></div></td><td class=\"idCol\">{{item.id}}</td><td class=\"col-xs-2 text-center\"><span>{{item.updatedAt | timeago}}</span></td><td class=\"visible-lg col-lg-2 text-center\"><span>{{item.createdAt | timeago }}</span></td><td class=\"actionBtns\"><div class=\"inner\"><button type=\"button\" class=\"btn btn-link\" ng-click=\"duplicate(item)\"><span class=\"icon-copy\"></span> {{'Duplicate'| translate}}</button> <button type=\"button\" class=\"btn btn-link\" ng-click=\"deletePlayer(item)\"><span class=\"glyphicon glyphicon-remove\"></span> {{'Delete'| translate}}</button> <button type=\"button\" class=\"btn btn-link\" ng-show=\"checkVersionNeedsUpgrade(item)\" ng-click=\"update(item)\"><span class=\"glyphicon glyphicon-refresh\"></span> {{'Update'| translate}}</button></div></td></tr></tbody></table></div><div id=\"footer\" class=\"text-center\"><div id=\"paginationWrap\"><pagination ng-show=\"filtered.length > maxSize\" ng-click=\"triggerLayoutChange()\" previous-text=\"&laquo;\" next-text=\"&raquo;\" page=\"currentPage\" total-items=\"filtered.length\" items-per-page=\"maxSize\"></pagination></div><div id=\"maxSizeSelect\"><form class=\"form-inline\" role=\"form\"><div><small>{{filtered.length}} Players in total, Show</small><select ui-select2=\"uiSelectOpts\" ng-model=\"maxSize\"><option value=\"5\">5</option><option value=\"10\">10</option><option value=\"15\">15</option><option value=\"20\">20</option></select><small>per page</small></div></form></div></div></div></div></div>"
  );


  $templateCache.put('view/login.html',
    "<div class=\"jumbotron fullheight\"><div class=\"container\"><div id=\"loginBox\" class=\"\"><h2 class=\"text-muted page-header\" id=\"pageHeader\">Login to v2.0 Studio</h2><div class=\"alert alert-danger\" ng-show=\"formError\">{{formHelpMsg}}</div><form ng-submit=\"login()\" class=\"form-horizontal\"><div id=\"loginTable\"><div class=\"form-group\"><label class=\"sr-only control-label\" for=\"email\">Email:</label><input class=\"form-control col-md-8\" value=\"\" type=\"text\" id=\"email\" ng-model=\"email\" placeholder=\"Email:\"></div><div class=\"form-group\"><label class=\"sr-only control-label\" for=\"password\">Password:</label><input class=\"form-control col-md-8\" value=\"\" type=\"password\" id=\"password\" ng-model=\"pwd\" placeholder=\"Password:\"></div><div class=\"col-md-offset-4 login-foot\"><input type=\"submit\" class=\"btn btn-primary login\" value=\"Login\"></div></div></form></div></div></div>"
  );


  $templateCache.put('view/new-template.html',
    "<div class=\"jumbotron\"><h2 class=\"inline text-muted\" id=\"pageHeader\">{{title}}</h2></div><div class=\"container\"><p><label>Please choose type of template :<select ui-select2=\"{minimumResultsForSearch:-1}\" ng-model=\"templateType\"><option selected value=\"system\">Public Templates</option><option value=\"user\">My Templates</option></select></label><img src=\"../bower_components/select2/select2-spinner.gif\" ng-show=\"loading\"></p><div class=\"row\"><div class=\"col-xs-4\" ng-repeat=\"player in templates\"><a class=\"playerTemplate\" ng-href=\"edit/{{player.id}}\"><h5>{{player.settings.name}}</h5><div class=\"thumbWrapper\"><img tooltip-html-unsafe=\"{{makeTooltip($index)|translate}}\" class=\"playerThumb\" src=\"{{player.thumbnailUrl}}\" data-tooltip-placement=\"top\"></div></a></div></div></div>"
  );

}]);
