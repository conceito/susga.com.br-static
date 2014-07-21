// *** só pode usar elementos nativos do jQuery ***
// função que liga e desliga alertas ---------------------------------------------------
function alerta(string, tipo){
	var str = string;// texto
	var tp = tipo; // cor do texto
	// 1º limpa a div #alerta
	$('#alertas').empty();
	// introduz o novo texto
	$('#alertas').html('<span class="'+tp+'">'+str+'</span>');
	// exibe
	$('#alertas').show().css({opacity:0}).animate({opacity:100}, 2000);	
	// delay para retirar
	setTimeout("alertaOpacity()",4000);
	//$(this).delay(4000, function(){
		//$('#alertas').animate({opacity:0}, 'slow');								  
	//});
}
function alertaOpacity(){
	$('#alertas').animate({opacity:0}, 3000);	
}
// substitui strings >> replaceAll(str, '.', ':');
function replaceAll(string, token, newtoken) {
	while (string.indexOf(token) != -1) {
		string = string.replace(token, newtoken);
	}
	return string;
}

function retira_acentos(palavra) {
	var com_acento = 'áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ';
	var sem_acento = 'aaaaaeeeeiiiiooooouuuucaaaaaeeeeiiiiooooouuuuc';
	// pre filtro para aspas
	palavra = replaceAll(palavra, '.', '');
	palavra = replaceAll(palavra, '\\', ' ');
	palavra = replaceAll(palavra, '//', ' ');
	palavra = replaceAll(palavra, '(', '');
	palavra = replaceAll(palavra, ')', '');
	palavra = replaceAll(palavra, ' ', '-');
	var nova='';
	for(i=0;i<palavra.length;i++) {
				
		if (com_acento.search(palavra.substr(i,1))>=0) {
			nova+=sem_acento.substr(com_acento.search(palavra.substr(i,1)),1);
		}
		else {			
			nova+=palavra.substr(i,1);
		}
	}
	return nova;
}


/**
 * Função para substituir caractesres especiais.
 * @param {str} string
 * @return String
 */
function replaceSpecialChars(str) {
	var specialChars = [
		{val:"a",let:"áàãâä"},
		{val:"e",let:"éèêë"},
		{val:"i",let:"íìîï"},
		{val:"o",let:"óòõôö"},
		{val:"u",let:"úùûü"},
		{val:"c",let:"ç"},
		{val:"a",let:"ÁÀÃÂÄA"},
		{val:"e",let:"ÉÈÊËE"},
		{val:"i",let:"ÍÌÎÏI"},
		{val:"o",let:"ÓÒÕÔÖO"},
		{val:"u",let:"ÚÙÛÜU"},
		{val:"c",let:"Ç"},
		{val:"",let:"?!(){}[]"}
	];
	var $spaceSymbol = '-';
	var regex;
	var returnString = str;
	for (var i = 0; i < specialChars.length; i++) {
		regex = new RegExp("["+specialChars[i].let+"]", "g");
		returnString = returnString.replace(regex, specialChars[i].val);
		regex = null;
	}
	returnString = replaceAll(returnString, '"', '');
	returnString = replaceAll(returnString, '\'', '');
	returnString = replaceAll(returnString, '.', '');
	returnString = replaceAll(returnString, '\\', '');
	returnString = replaceAll(returnString, '//', '');
	returnString = replaceAll(returnString, '(', '-');
	returnString = replaceAll(returnString, ')', '-');
        returnString = replaceAll(returnString, '?', '');
        returnString = replaceAll(returnString, '!', '');
        returnString = replaceAll(returnString, ',', '');
        returnString = replaceAll(returnString, '´', '');
        returnString = replaceAll(returnString, '@', '');
        returnString = replaceAll(returnString, '{', '-');
        returnString = replaceAll(returnString, '}', '-');
        returnString = returnString.replace(/\s/g,$spaceSymbol).toLowerCase();
        returnString = replaceAll(returnString, '--', '-');
        returnString = replaceAll(returnString, '---', '-');
        returnString = replaceAll(returnString, ':', '');
        returnString = replaceAll(returnString, '#', '');
        returnString = replaceAll(returnString, '&', '');
        returnString = replaceAll(returnString, '|', '');
        returnString = replaceAll(returnString, '*', '');
        returnString = replaceAll(returnString, '=', '');
	return returnString;
};


/**
 * Converte data no formato brasileiro no formato americano.
 * dd/mm/yyyy => mm/dd/yyyy
 */
function date_pt_to_us(data){
    var d = data.split('/');
    
    return d[1]+'/'+d[0]+'/'+d[2];
}

/**
* permite apenas números
*/
function only_number(evt){
 var charCode = (evt.which) ? evt.which : event.keyCode
 if (charCode > 31 && (charCode < 48 || charCode > 57))
	return false;

 return true;
}

/** 
* remove caracteres especias e acrescenta o ponto de decimal
*/
function money_format(valor, separador) {
    
	valor = replaceAll(valor, '.', '');
	valor = replaceAll(valor, ',', '');
	valor = replaceAll(valor, 'R', '');
	valor = replaceAll(valor, '$', '');
	var vr = valor,
		tam = vr.length;	
    if(separador == undefined){
		separador = '.';
	}
    
    if (tam > 2) {
        return vr.substr(0, tam - 2) + separador + vr.substr(tam - 2, tam);
    } else {
		return vr;
	}
	
}

function number_percentual(valor){
	valor = replaceAll(valor, '.', '');
	valor = replaceAll(valor, ',', '');
	valor = replaceAll(valor, 'R', '');
	valor = replaceAll(valor, '$', '');
	valor = replaceAll(valor, '%', '');
	var vr = valor,
		tam = vr.length;
		
	if (tam > 2) {
		if(parseInt(vr) > 100){
			vr = '100';
		}
        return vr.substr(0, 3);
    } else {
		return vr;
	}
}
