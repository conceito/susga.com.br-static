/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function changeImgSize($size){
    var urlimg = document.getElementById('src').value;

    // separa pela barra
    var url = urlimg.split('/');

    // só a imagem
    var img = url[url.length - 1];

    // remove as tags de tamanho
    img = replaceAll(img, '_thumb', '');
    img = replaceAll(img, '_med', '');
    img = replaceAll(img, '_max', '');

    // pedaço acrescentado na imagem
    var pedaco = "_"+$size;
    if($size == "normal"){
        pedaco = "";
    }

    // monta a imagem final
    var imgPartes = img.split('.');
    var imgFinal = imgPartes[0] + pedaco + '.'+imgPartes[1];


    // monta o endereço completo
    var novaUrl = "";
    for(var i = 0; i < (url.length - 1); i++){
        novaUrl = novaUrl + url[i] + '/';
    }
    novaUrl = novaUrl + imgFinal;

    // atualiza o endereço do formulário
    document.getElementById('src').value = novaUrl;
    // atualiza o preview
    ImageDialog.showPreviewImage(novaUrl);

//    console.log("size: " + $size + " - " + novaUrl);
}

// substitui strings >> replaceAll(str, '.', ':');
function replaceAll(string, token, newtoken) {
	while (string.indexOf(token) != -1) {
		string = string.replace(token, newtoken);
	}
	return string;
}