angular.element(document).ready(function() {
    angular.bootstrap(document, ['app']);
});

var app = angular.module('app', ['ui.sortable']);

app.controller('OptionsController', ['$scope', '$timeout', 'Options',
    function($scope, $timeout, Options) {

        // items
        $scope.opts = [];

        // status
        $scope.changed = false;

        Options.get({
            id: CMS.option.id
        }).success(function(data, status, headers, config) {

            if (data.error) {
                alert(data.msg);
            } else {
                $scope.opts = data.data;
            }
        }).
        error(function(data, status, headers, config) {
            alert("ERRO: Houve um erro na comunicação com o servidor.");
        });


        var resetOrders = function() {
            var i = 0;
            angular.forEach($scope.opts, function(opt) {
                opt.ordem = i;
                i++;
            });
        };

        $scope.update = function() {
            if (CMS.option.id === undefined) {
                alert("ERRO: O ID da opção não foi identificado.");
                return;
            }
            resetOrders();
            Options.update(CMS.option.id, $scope.opts)
                .success(function(data, status, headers, config) {
                    if (data.error) {
                        alert(data.msg);
                    }
                    $scope.changed = false;
                })
                .error(function(data, status, headers, config) {
                    alert("ERRO: Houve um erro na comunicação com o servidor.");
                });

        };

        /**
         * Remove item da lista
         * @param  {Object} opt
         */
        $scope.remove = function(opt) {
            var index = $scope.opts.indexOf(opt);
            $scope.opts.splice(index, 1);
        };

        $scope.addHeader = function() {
            var newOpt = {
                id: Date.now(),
                tipo: 'header',
                ordem: 0,
                titulo: "Identifique o grupo...",
                resumo: "",
                valor: "",
                status: 1
            };
            $scope.opts.unshift(newOpt);
        };

        $scope.addOption = function() {
            var newOpt = {
                id: Date.now(),
                tipo: 'option',
                ordem: 0,
                titulo: "Identifique a opção...",
                resumo: "",
                valor: "",
                status: 1
            };
            $scope.opts.unshift(newOpt);
        };


        $scope.$watch('opts', function(newVal, oldVal) {
            if (newVal !== oldVal) {
                $scope.changed = true;
            }
        }, true);

        $scope.sortableOptions = {
            update: function(e, ui) {

                console.log('updated', ui.item.scope().opt.titulo);

            },
            stop: function(e, ui) {
                console.log('stoped', ui.item.scope().opt.titulo);
                // resetOrders();
            },
            removed: function(e, ui) {
                console.log('removed', ui.item.scope().opt.titulo);
            },
            axis: 'y'
        };


    }
]);


app.factory('Options', ['$http',
    function($http) {
        var endpoint = CMS.base_url + 'cms/calendario/subscriptions_updateoptions/';

        return {
            get: function(attrs) {

                var url = endpoint + attrs.id;

                return $http.get(url);

                // return [{
                //     id: Date.now(),
                //     tipo: 'option',
                //     ordem: 0,
                //     titulo: "nome",
                //     resumo: "meu texto",
                //     status: 1
                // }, {
                //     id: Date.now(),
                //     tipo: 'option',
                //     ordem: 2,
                //     titulo: "nome 2",
                //     resumo: "meu texto",
                //     status: 1
                // }, {
                //     id: Date.now(),
                //     tipo: 'option',
                //     ordem: 1,
                //     titulo: "nome 1",
                //     resumo: "meu texto",
                //     status: 1
                // }, {
                //     id: Date.now(),
                //     tipo: 'header',
                //     ordem: 1,
                //     titulo: "header dia 22/33/2014 - quarta-feira",
                //     resumo: "meu texto",
                //     status: 1
                // }];
            },
            update: function(optionId, options) {
                // console.log('ID', optionId);
                // console.log('options', options);

                var url = endpoint + optionId;

                return $http.put(url, options, {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                });
            }
        };
    }
]);