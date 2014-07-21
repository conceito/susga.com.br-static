
app.factory('Autores', ['$http',
    function ($http) {
        var endpoint = CMS.base_url + 'cms/trabalhos/updateautores/';

        return {
            get: function (attrs) {

                var url = endpoint + attrs.id;

                return $http.get(url);

//                return [
//                    {
//                        id: Date.now(),
//                        ordem: 0,
//                        nome: "nome",
//                        curriculo: "meu texto",
//                        status: 1
//                    },
//                    {
//                        id: Date.now(),
//                        ordem: 2,
//                        nome: "nome 2",
//                        curriculo: "meu texto",
//                        status: 1
//                    },
//                    {
//                        id: Date.now(),
//                        ordem: 1,
//                        nome: "nome 1",
//                        curriculo: "meu texto",
//                        status: 1
//                    },
//                    {
//                        id: Date.now(),
//                        ordem: 1,
//                        nome: "header dia 22/33/2014 - quarta-feira",
//                        curriculo: "meu texto",
//                        status: 1
//                    }
//                ];
            },
            update: function (optionId, options) {
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


app.factory('Avaliadores', ['$http', function($http){

    var endpoint = CMS.base_url + 'cms/api_avaliadores/';

    return {
        all: function(){

            var url = endpoint + 'all';
            return $http.get(url);
        },
	    sendInvite: function(avaliador, jobId){
		    return $http.post(endpoint + 'invite', {jobid: jobId, userid: avaliador.id});
	    }
    };

}]);

app.factory('Avaliacoes', ['$http', function($http){

    var endpoint = CMS.base_url + 'cms/api_avaliacoes/';

    return {
        /**
         * return all appraisers from a content
         * @param contentId
         * @returns {*}
         */
        all: function(contentId){
            var id = (contentId) ? contentId : CMS.trabalho.id;
            var url = endpoint + 'all/contentId:' + id;
            return $http.get(url);
        },

	    remove: function(ava){
		    var id = (ava.id) ? ava.id : null;
		    var url = endpoint + 'remove/' + id;
		    return $http.get(url);
	    }
    };

}]);