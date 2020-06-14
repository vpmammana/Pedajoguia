<?php
// VPM 2020.06.10

//if(isset($_GET["offset"])){
//  $offset = $_GET["offset"];
//}

echo "
<html>
<head>
<title>Pedajoguia - brinque e aprenda</title>
<meta http-equiv='Cache-Control' content='no-cache, no-store, must-revalidate'/>
<meta http-equiv='Pragma' content='no-cache'/>
<meta http-equiv='Expires' content='0'/>

<meta charset='UTF-8'>
<style>

body {
	color: black;
	background-image: radial-gradient(#000099 0%, #000000 80%);
	height: 100%;
	width: 100%;
	overflow: scroll;
}

div.cabecalio {
	color: yellow;
	padding: 10px;
}

.interna {
    font-size: 10px;
    border: none;
    padding: 0px;
}

	.botoeira {
	border: 1px solid red;
        background-color: silver;
	border-collapse: collapse;
	font-size: small;
	margin-left: auto;
	margin-right: auto;
        padding: 2px;
}

th,td {
	border: none;
	font-size: large;
	color: yellow;
	padding: 10px;
	vertical-align: top;
	text-align: left
}

.tabela {
	margin-left: 200px;
	vertical-align: top;
}

input[type=button]{
	font-size: x-small;
}


.dropbtn {
  background-color: #0000AA;
  color: yellow;
  width: 400px;
  padding: 2px;
  font-size: medium;
  border: none;
  cursor: pointer;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #0000CC;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 2px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {background-color: #f1f1f1}

[data-alterado='alterado'] {
   background-color: red;
}

[data-keyup='keyup'] {
  display: block;
}

.dropdown:hover .dropbtn {
  background-color: #3e8e41;
}

</style>
</head>
<body id='conteudo'>
<div class='cabecalio'>
<table>
<tr>
<td>
<h1>Pedajoguia - Sua plataforma para aprender brincando em casa</h1>
</td>
<td>
<img src='WASH_logo.jpeg' width='200'>
</td>
</tr>
</table>
</div>
";
$username="victor";
$pass="aerofolio";
$database="ead";
$path_imagem="";

$conn= new mysqli("localhost", $username, $pass, $database);

echo "
<table class='tabela'>
<tr>
<td>
Escolha em qual escola você quer brincar:
</td>
<td>
<div class='dropdown'>
  <input type='text'
	placeholder='Clique, busque com setas, selecione com enter' 
	id='drop_dummy_5' 
	class='dropbtn' 
	onfocusout='document.getElementById(".'"'."lista_dummy_5".'"'.").setAttribute(".'"'."data-keyup".'"'.",".'"'."inativo".'"'.");document.getElementById(".'"'."drop_dummy_5".'"'.").setAttribute(".'"'."data-selecionado".'"'.",".'"'."-1".'"'."); document.getElementById(".'"'."drop_dummy_5".'"'.").setAttribute(".'"'."data-n-itens".'"'.",".'"'."0".'"'.");' 
        data-drop='lista_dummy_5'
        data-momento='atualizacao'
	data-id='dummy'
        data-max-itens='100'
	data-banco='ead' 
	data-tabela='salas'
	data-campo='id_instituicao' 
	data-fkid='' 
        data-default=''
	data-fk-banco='ead' 
	data-fk-tabela='instituicoes' 
	data-fk-id='id_chave_instituicao'
	data-selecionado='-1'
	data-event-blur='NAO'
	data-event-focus='NAO'
	data-event-keyup='NAO'
        data-n-itens='0'
	autocomplete='off'
        data-nivel='0'
  />

  <div id='lista_dummy_5' class='dropdown-content'  data-keyup='inativo'>
  </div>
</div>
</td>
</tr>

<tr>
<td>
<table>
<tr>
<th id='tit_projetos'></th>
</tr>
<tr>
<td id='projetos'></td>
</tr>
</table>
<td>
<table>
<tr>
<th id='tit_estudantes'></th>
</tr>
<tr>
<td id='estudantes'></td>
</tr>
</table>
</td>
</tr>
</table>
";






echo "
<script>

var nivel_insercao=0; // indica o nivel de insercao de dados a que se refere um botao de insercao.
                      // variavel nivel_insercao eh necessaria para limitar os campos de insercao aos que se refere a aquele botao de insercao



//mostra_botao('insercao','salas','0');

function ativa_alterados(){
var inputs_inseriveis=document.getElementsByClassName('inserivel');
var i;
var input_inserivel;
for (i=0; i<inputs_inseriveis.length; i++) {
input_inserivel=inputs_inseriveis[i];
input_inserivel.addEventListener('keydown', function(e){e.target.style.backgroundColor='#FF0000';e.target.setAttribute('data-alterado','alterado') }, false);
}
}

ativa_alterados();

function disable_niveis(){
var x = document.getElementsByTagName('INPUT');
var i;
for (i = 0; i < x.length; i++) {
  console.log('TAG INPUT -> '+x[i].id+' nivel -> '+nivel_insercao);
  if (x[i].className=='pagina' || x[i].getAttribute('data-nivel')==nivel_insercao) {x[i].disabled=false;} else {x[i].disabled=true;};
}
}


// INICIO DOS SCRIPTS DO DROP MENU

function carrega_drop_btn(element){

  if(element.getAttribute('data-momento')=='atualizacao'){
           var resposta='';
           var url='auto_ler_tabela_campo.php?banco=ead&tabela='+element.getAttribute('data-fk-tabela')+'&campo_id='+element.getAttribute('data-fk-id')+'&id='+element.getAttribute('data-fkid');
           var oReq=new XMLHttpRequest();
           oReq.open('GET', url, false);
           oReq.onload = function (e) {
                     resposta=oReq.responseText;
                     element.value=resposta;
                     element.setAttribute('data-default',resposta);
                     }
           oReq.send();
       }
} // carrega_drop_btn


function ativa_eventos_dropbtn(){ // ativa os eventos de teclado e demais dos dropbtn

var drops=document.getElementsByClassName('dropbtn');
var i;
for (i=0; i<drops.length; i++) {
console.log(drops[i].id);
var drop_singular=drops[i];


// blur é quando perde o foco: input value tem que retornar ao valor default
// importante verificar se o elemento já tem o evento registrado, antes de registrar um novo. De outra forma, posso ter um x=x+2 para o valor de selecionado porque registro dois eventos que fazem x=x=+1...
if (drop_singular.getAttribute('data-event-blur')==='NAO') {drop_singular.addEventListener('blur', function(e){e.target.setAttribute('data-event-blur','BLUR');  e.target.value=e.target.getAttribute('data-default');}, false);}
if (drop_singular.getAttribute('data-event-focus')==='NAO'){drop_singular.addEventListener('focus',function(e){e.target.setAttribute('data-event-focus','FOCUS');   cai(e.target.id,e.target.getAttribute('data-drop')); e.target.value=''; e.target.value='';}, false);}
if (drop_singular.getAttribute('data-event-keyup')==='NAO') {drop_singular.addEventListener('keyup', 
		function(e){ 
			        e.target.setAttribute('data-event-keyup','KEYUP');
				var selecionado=e.target.getAttribute('data-selecionado');
                                var n_itens=e.target.getAttribute('data-n-itens');
	
				if ((e.keyCode==40) && ((selecionado<parseInt(n_itens)-1) || (selecionado<0)) ) {
							e.target.setAttribute('data-selecionado',parseInt(selecionado)+1);
						   }
 
				if ((e.keyCode==38) && (selecionado>-1)) {
							e.target.setAttribute('data-selecionado',parseInt(selecionado)-1);
						   }
 
				if ((e.keyCode<28) 
					&& (e.keyCode!=9) // evita que saia do dropbox quando o tab é usado 
					&& (e.keyCode!=14) // evita que saia do dropbox quando ocorre shift in 
					&& (e.keyCode!=15) // no manual dizia que shift out é 15, mas parece que 16 na verdade
					&& (e.keyCode!=16)) { // evita que saia do dropbox com SHIFT out
                                                        console.log(e.keyCode);
							if (e.keyCode==13){
                                                                console.log('selecionado: '+e.target.getAttribute('data-selecionado'));
                                                                console.log('id input: '+e.target.getAttribute('data-fkid'));
                                                                var drop_elem=e.target.getAttribute('data-drop');
								console.log('drop element: '+drop_elem);
								e.target.setAttribute('data-fkid',document.getElementById('a_'+drop_elem+'_'+e.target.getAttribute('data-selecionado')).getAttribute('data-id-fk'));
                                                            if (e.target.getAttribute('data-momento')=='atualizacao'){
								carrega_opcoes(e.target.id);
								
								carrega_drop_btn(e.target);}
								else {e.target.value=document.getElementById('a_'+drop_elem+'_'+e.target.getAttribute('data-selecionado')).getAttribute('data-innertext');
e.target.setAttribute('data-default',e.target.value);
console.log('target: '+e.target.value);
}
								
							} else {e.target.value=e.target.getAttribute('data-default');}
							e.target.setAttribute('data-keyup','inativo');
                                                        if (e.keyCode==8){
										e.target.value='';
										cai(e.target.id,e.target.getAttribute('data-drop'));
									} else {
                                                        			document.activeElement.blur();
                                                                               }

							
						
						   }
 
				else {cai(e.target.id,e.target.getAttribute('data-drop'));}
console.log(selecionado);
		}, false);}
}
}
// fim da funcao que atribui eventos aos dropbtn

ativa_eventos_dropbtn();

function cai(id_input,id_div){

var elemento_input=document.getElementById(id_input);
var elemento_div=document.getElementById(id_div);

var str_busca=elemento_input.value;

if ((str_busca!='') || (parseInt(elemento_input.getAttribute('data-selecionado'))>-1)) {

		elemento_div.setAttribute('data-keyup','keyup');
		var fk_banco=elemento_input.getAttribute('data-fk-banco');
		var fk_tabela=elemento_input.getAttribute('data-fk-tabela');
		var fk_campo=elemento_input.getAttribute('data-fk-id');
		var max_itens=elemento_input.getAttribute('data-max-itens');
		busca_lista(id_input, id_div,fk_banco, fk_tabela, fk_campo, str_busca, max_itens);
               
		} 
		else {elemento_div.setAttribute('data-keyup','inativo');}
}


function busca_lista(elemento_input, elemento, banco, tabela, campo, str_busca, max_itens){
// busca a lista de valores de campos fk, de acordo com o nome_, usando o que foi teclado como search. Coloca no dropdown
           var resposta='';
           var url='busca_str.php?banco='+banco+'&tabela='+tabela+'&campo='+campo+'&str_busca='+str_busca;
           var oReq=new XMLHttpRequest();
           oReq.open('GET', url, false);
           oReq.onload = function (e) {
								var input=document.getElementById(elemento_input);
                                                                var myNode=document.getElementById(elemento); //é o div?
			
								while (myNode.firstChild) {
								myNode.removeChild(myNode.firstChild);
								}		

                     resposta=oReq.responseText;
                     var matriz=resposta.split('<br>', max_itens);
		     // pode acontecer, como é o caso das tabelas autorizacoes, curadores_conteudos e expectadores, de uma tabela fazer referencia a uma outra sem nome_%
                     if (matriz[0].includes('veio nome')) // a resposta do php completa eh (nao veio nome). Usei uma fracao por causa do acento 
					{
						// se percebe que nao veio nome, ou seja, nao tem nome_, entao ele busca um subselect
						 busca_lista_sub_select(elemento_input, elemento, banco, tabela, campo, str_busca, max_itens);
                                                 return;
					}
 
                     var conta=0;

                     matriz.forEach(function (item, index) {
 							   console.log('>'+item+'<');
							   if (item.trim()!=''){
								var node = document.createElement('a');            // Create a <li> node
                     						var item_matriz=item.split('<rb>', max_itens);
								var att_innertext = document.createAttribute('data-innertext');
							        att_innertext.value = item_matriz[0];
								node.setAttributeNode(att_innertext);	
								var att_id = document.createAttribute('data-id-fk');
							        att_id.value =	item_matriz[1];
								node.setAttributeNode(att_id);	
                                                                node.id='a_'+elemento+'_'+conta;
								var textnode = document.createTextNode('#'+item_matriz[0]+'#');     // Create a text node
                                                                textnode.id='text_'+elemento+'_'+conta;
								node.appendChild(textnode);                        // Append the text to <a>
								myNode.appendChild(node);     // Append <a> to <div> with id='lista'
								node.addEventListener('mousedown',function (){console.log('clicou');},false);
								if (index==input.getAttribute('data-selecionado'))
									{
										node.style.backgroundColor='#000000';
										node.style.color='#FFFFFF';
									}
								conta=conta+1;
                                                                           }

                                                           ;}
							   );
							   input.setAttribute('data-n-itens',conta);
                     }
           oReq.send();

}

function busca_lista_sub_select(elemento_input, elemento, banco, tabela, campo, str_busca, max_itens){
// funcao para o caso da tabela foreign nao ter nome_... dai tem que buscar na tabela fk da fk.
           console.log(str_busca);
           var resposta='';
           var url='busca_registro_inteiro.php?banco='+banco+'&tabela='+tabela+'&nome_chave_primaria='+campo+'&busca_str='+str_busca;
           // este codigo PHP busca apenas os campos que nao estao na tabela campos_excluidos... isso reduz o tamanho do string que aparece no dropdown
           var oReq=new XMLHttpRequest();
           oReq.open('GET', url, false);
           oReq.onload = function (e) {
								var input=document.getElementById(elemento_input);
                                                                var myNode=document.getElementById(elemento); //é o div?
			
								while (myNode.firstChild) {
								myNode.removeChild(myNode.firstChild);
								}		

                     resposta=oReq.responseText;
                     var matriz=resposta.split('<br>', max_itens);
		     // pode acontecer, como é o caso das tabelas autorizacoes, curadores_conteudos e expectadores, de uma tabela fazer referencia a uma outra sem nome_%
                     var conta=0;

                     matriz.forEach(function (item, index) {
							   if (item.trim()!=''){

 							   console.log('>'+item+'<');
								var node = document.createElement('a');            // Create a <li> node
                     						var item_matriz=item.split('<rb>', max_itens);
							console.log(item_matriz[0]);
								var att_innertext = document.createAttribute('data-innertext');
							        att_innertext.value = item_matriz[0];
								node.setAttributeNode(att_innertext);	
								var att_id = document.createAttribute('data-id-fk');
							        att_id.value =	item_matriz[1];
								node.setAttributeNode(att_id);	
                                                                node.id='a_'+elemento+'_'+conta;
								var textnode = document.createTextNode('#'+item_matriz[0]+'#');     // Create a text node
                                                                textnode.id='text_'+elemento+'_'+conta;
								node.appendChild(textnode);                        // Append the text to <a>
								myNode.appendChild(node);     // Append <a> to <div> with id='lista'
								node.addEventListener('mousedown',function (){console.log('clicou');},false);
								if (index==input.getAttribute('data-selecionado'))
									{
										node.style.backgroundColor='#000000';
										node.style.color='#FFFFFF';
									}
								conta=conta+1;
                                                                           }

                                                           ;}
							   );
							   input.setAttribute('data-n-itens',conta);
                     }
           oReq.send();
}

// FIM DOS SCRIPTS DO DROP MENU


function ativa_eventos_editaveis(){

var inputs_editaveis=document.getElementsByClassName('editavel');
var i;
var input_singular;
for (i=0; i<inputs_editaveis.length; i++) {
input_singular=inputs_editaveis[i];
input_singular.addEventListener('keydown', function(e){e.target.style.backgroundColor='#FF0000';e.target.setAttribute('data-alterado','alterado') }, false);
}
}

ativa_eventos_editaveis();

function desliga_autocomplete(){
// tira o auto complete dos campos dropbtn
var inputElements = document.getElementsByTagName('input');
for (i=0; inputElements[i]; i++) {
if (inputElements[i].className && (inputElements[i].className.indexOf('dropbtn') != -1)) {
inputElements[i].setAttribute('autocomplete','off');
}
}
}

desliga_autocomplete();

var x = document.getElementsByClassName('dropbtn');
var i;
for (i = 0; i < x.length; i++) {
// o programa auto_ler_tabela_campo.php é usado para buscar os dados na tabela chave (foreign key)
// se o dropbtn for de inserao de dados, ao inves de atualização, nao faz sentido buscar dados na base, porque o campo tem que estar vazio
  if(x[i].getAttribute('data-momento')=='atualizacao'){
           var resposta='';
           var url='auto_ler_tabela_campo.php?banco=ead&tabela='+x[i].getAttribute('data-fk-tabela')+'&campo_id='+x[i].getAttribute('data-fk-id')+'&id='+x[i].getAttribute('data-fkid');
           var oReq=new XMLHttpRequest();
           oReq.open('GET', url, false);
           oReq.onload = function (e) {
                     resposta=oReq.responseText;
	             x[i].value=resposta;
                     x[i].setAttribute('data-default',resposta);
                     }
           oReq.send();
       }
}
function carrega_busca(campo_busca,valor_busca,limit,offset){
        alert(campo_busca);
	var resposta='';
	var url='insere_salas.php?offset='+offset+'&limit='+limit+'&campo_busca='+campo_busca+'&valor_busca='+valor_busca;
        var oReq=new XMLHttpRequest();
	oReq.open('GET',url, false);
	oReq.onload= function (e) {
			    resposta=oReq.responseText;
			    window.document.body.innerText='';
			    window.document.write(resposta);
			}
	oReq.send();
}
function carrega_offset(limit,offset){

	var resposta='';
	var url='insere_salas.php?offset='+offset+'&limit='+limit+'&campo_busca=&valor_busca=';
        var oReq=new XMLHttpRequest();
	oReq.open('GET',url, false);
	oReq.onload= function (e) {
			    resposta=oReq.responseText;
			    window.document.body.innerText='';
			    window.document.write(resposta);
			}
	oReq.send();
}
function carrega(){
	var resposta='';
	var url='insere_salas.php?offset=0&limit=".$limitador_registros_insere."&campo_busca=&valor_busca=';
        var oReq=new XMLHttpRequest();
	oReq.open('GET',url, false);
	oReq.onload= function (e) {
			    resposta=oReq.responseText;
			    window.document.body.innerText='';
			    window.document.write(resposta);
			}
	oReq.send();
}


function apaga_registro(id){
var resposta='';
var url='apaga_registro.php?banco=ead&tabela=salas&id='+id;
alert(url);
var oReq=new XMLHttpRequest();
oReq.open('GET', url, false);
oReq.onload= function (e) {
	resposta=oReq.responseText;
        alert(resposta);
	carrega();

}
oReq.send();

}

//cuidado para nao confundir esse insere_registro com o insere_registro.php... sao diferentes - o javascript chama o php
function insere_registro(tabela, nivel_de_insercao){ // nivel de insercao indica para quais campos o botao insere vai agir
var conta_campos=0;
var inputs_inseriveis=document.getElementsByClassName('inserivel');
var i;
var input_inserivel;
var acumula_campos='';
var acumula_valores='';
var virgula='';
for (i=0; i<inputs_inseriveis.length; i++) {
input_inserivel=inputs_inseriveis[i];

if (input_inserivel.getAttribute(".'"'."data-nivel".'"'.")!=nivel_de_insercao) {continue;}

if (conta_campos>0) {virgula=',';} else {virgula='';}
acumula_campos=acumula_campos+virgula+input_inserivel.getAttribute(".'"'."data-campo".'"'.");
acumula_valores=acumula_valores+virgula+'".'"'."'+input_inserivel.value+'".'"'."';
conta_campos=conta_campos+1;
}
// na hora de inserir os registros vc precisa acumular quais
var inputs_inseriveis=document.getElementsByClassName('dropbtn');
var i;
var input_inserivel;
var virgula='';
for (i=0; i<inputs_inseriveis.length; i++) {
input_inserivel=inputs_inseriveis[i];
if (input_inserivel.getAttribute(".'"'."data-nivel".'"'.")!=nivel_de_insercao) {continue;}

	if (input_inserivel.getAttribute('data-momento')=='insercao'){
		if (conta_campos>0) {virgula=',';} else {virgula='';}
		acumula_campos=acumula_campos+virgula+input_inserivel.getAttribute(".'"'."data-campo".'"'.");
		acumula_valores=acumula_valores+virgula+'".'"'."'+input_inserivel.getAttribute('data-fkid')+'".'"'."';
                conta_campos=conta_campos+1;
	}
}


var resposta='';
var url='insere_registro.php?banco=ead&tabela='+tabela+'&campos='+acumula_campos+'&valores='+acumula_valores;
var oReq=new XMLHttpRequest();
oReq.open('GET', url, false);
oReq.onload= function (e) {
	resposta=oReq.responseText;
        alert(resposta);
        var inputs_inseriveis2=document.getElementsByClassName('inserivel');
        var input_inserivel2;
	var i;
	for (i=0; i< inputs_inseriveis2.length; i++) 
                {
			input_inserivel2=inputs_inseriveis2[i];
			input_inserivel2.value='';
                        input_inserivel2.style.backgroundColor='#FFFFFF';
                }

        var inputs_inseriveis2=document.getElementsByClassName('dropbtn');
        var input_inserivel2;
	var i;
	for (i=0; i< inputs_inseriveis2.length; i++) 
                {
			input_inserivel2=inputs_inseriveis2[i];
			if (input_inserivel2.getAttribute('data-momento')=='insercao') {
				input_inserivel2.value='';
			}
                }
	carrega();
}
oReq.send();
}

function mostra_botao(div_insercao, tabela, nivel){
	nivel_insercao=nivel;
	  var botao='<input  type=\"button\" data-nivel=\"'+nivel+'\" value=\"mostra inserção '+tabela+'\" onclick=\"painel_insercao(`'+div_insercao+'`,`'+tabela+'`)\" />';
	  document.getElementById(div_insercao).innerHTML=botao;

        disable_niveis();

}


function painel_insercao(div_insercao, tabela){
	   nivel_insercao++;
           var resposta='';
           var url='insercao.php?banco=ead&tabela='+tabela+'&nivel='+nivel_insercao;
           var oReq=new XMLHttpRequest();
           oReq.open('GET', url, false);
           oReq.onload = function (e) {
                     resposta=oReq.responseText;
                     var nivel_itz=nivel_insercao-1;
	             document.getElementById(div_insercao).innerHTML=resposta+'<br><input type=\"button\" value=\"fecha inserção '+tabela+nivel_insercao+'\"  data-nivel=\"'+nivel_insercao+'\"   onclick=\"mostra_botao(\''+div_insercao+'\',\''+tabela+'\',\''+ nivel_itz +'\')\" />';
		     ativa_eventos_dropbtn();
		     ativa_alterados();
		     desliga_autocomplete();
	   	     disable_niveis();
                     }
           oReq.send();
}



// atualiza() recebe uma matriz com todos os campos que precisam ser atualizados (menos foreign keys).

function carrega_opcoes(id_elemento_campo){

		var campo=document.getElementById(id_elemento_campo);
				var resposta='';
		                var url='carrega_projetos.php?banco=ead&tabela=projetos&campo=apelido&valor='+campo.getAttribute('data-fkid');
		                var oReq2=new XMLHttpRequest();
				oReq2.open('GET', url, true);
				oReq2.onload = function (e) {
					resposta=oReq2.responseText;

					document.getElementById('projetos').innerHTML=resposta;
					document.getElementById('tit_projetos').innerHTML='Projetos';
				}
                		oReq2.send();

}

function atualiza_fk (id_elemento_campo){

		var campo=document.getElementById(id_elemento_campo);
	                var resposta='';
	                var url='atualiza_campos.php?banco=ead&tabela='+campo.getAttribute('data-tabela')+'&campo='+campo.getAttribute('data-campo')+'&id='+campo.getAttribute('data-id')+'&valor='+campo.getAttribute('data-fkid');;
	                var oReq=new XMLHttpRequest();
			oReq.open('GET', url, false);
			oReq.onload = function (e) {
				resposta=oReq.responseText;
				alert(resposta);
			}
                oReq.send();
}



function atualiza (matriz){
   matriz.forEach(minhafuncao);

	function minhafuncao(item, index){
		var campo=document.getElementById(item);
                if (campo.getAttribute('data-alterado')=='alterado'){
	                var resposta='';
	                var url='atualiza_campos.php?banco=ead&tabela='+campo.getAttribute('data-tabela')+'&campo='+campo.getAttribute('data-campo')+'&id='+campo.getAttribute('data-id')+'&valor='+campo.value;
	                var oReq=new XMLHttpRequest();
			oReq.open('GET', url, false);
			oReq.onload = function (e) {
				resposta=oReq.responseText;
				alert(resposta);
				campo.style.backgroundColor='#FFFFFF';
                                campo.setAttribute('data-alterado','nao');
			}
                oReq.send();
	        } 
	}

}

var mywindow=window;
mywindow.resizeTo(document.getElementById('conteudo').scrollWidth+50,document.getElementById('conteudo').scrollHeight+50);
</script>

</body>
</html>";

?>
