<?php
// Para ver as mascaras de colisao use a rotina mostra_otimizado()
$username="victor";
$pass="aerofolio";
$database="ead";
$conn= new mysqli("localhost", $username, $pass, $database);


if(isset($_GET["aluno"])){
  $aluno = $_GET["aluno"];
}

if(isset($_GET["projeto"])){
  $projeto = $_GET["projeto"];
}




$pos_x_bloco=0; // posicao do bloco definida pelo arquivo txt
$pos_y_bloco=0;



$largura=4000;
$altura=4000;

$dir=getcwd();

$numero_linhas=count(file($dir."/mapa_teste.txt"));
$altura_bloco=(int)$altura/$numero_linhas;

	$fs_mapa=fopen($dir."/mapa_teste.txt","r");


echo '


<html>
<head>
<style>

#conteudo {
		width: 85%;
		top: 0px;
		left: 15%;
                height: 100%;
		float: right;
		overflow: hidden;
		position: absolute;
		padding: 5px;
		}
#menu {
		width: 15%;
		height: 100%;
		margin: 0; 
		padding: 0;
		float: left;
		background-color: darkblue;
                color: yellow;
		font-family: arial;
		position: fixed; /* Fixed Sidebar (stay in place on scroll) */
  		/z-index: 1; /* Stay on top */
  		top: 0; /* Stay at the top */
  		left: 0;
		}


.amarelo {
	color: yellow;
}

body {
	color: black;
	background-image: radial-gradient(#166664 0%, #252233 80%);
	theight: '.$altura.';
	width: '.$largura.';
	overflow: hidden;
}

.fixo {
	position: absolute;
}

.avatar {
	position: absolute;
}

.avatar_outros {
	transition: all 0.5s ease;
}


</style>
</head>
<body id="corpo"
	onkeydown="
		testa_teclado(event);
		
	"
>

<div id="menu">
<table class="amarelo">
<tr>
	<td><img src="WASH_logo.jpeg" width="100%"></td>
</tr>
<tr>
<td>
<h2>'.$aluno.'</h2>

<span id="pos_x">Pos x: 0</span><br>
<span id="pos_y">Pos y: 0</span><br>
<span id="pos_delta_scroll_x">Delta Scroll X: 0</span><br>
<span id="pos_delta_scroll_y">Delta Scroll Y: 0</span><br>

<span id="borda_x_scroll">Borda x: 0</span><br>
<span id="borda_y_scroll">Borda y: 0</span><br>

<span id="largura">largura_janela: 0</span><br>
<span id="altura">altura_janela: 0</span><br>

<span id="velho_x">velho_y: 0</span><br>
<span id="velho_y">velho_y: 0</span><br>
<span id="vx">VX: 0</span><br>
<span id="vy">VY: 0</span><br>
<span id="resize">Resize: 0</span><br>



</td>
</tr>
</table>

</div>

<div id="conteudo">
';
$conta_linha=0;
while(!feof($fs_mapa)){
	$linha=fgets($fs_mapa);
	if (feof($fs_mapa)){break;}
	$largura_bloco=(int)$largura/strlen($linha);
 	$conta_caracter=0;

	while ($conta_caracter<strlen($linha)-1) {
		if ($linha[$conta_caracter]==="O"){
		$classe="parede";
//	echo '<div style="width:'.round($largura_bloco).'; height: '.round($altura_bloco).' background-color: white; border: 1px solid black; z-index:1000; top: '.round($altura_bloco*$conta_linha).'; left: '.round($largura_bloco*$conta_caracter).'; position: absolute;">'.$conta_linha.'</div>';

	echo '
<img class="fixo '.$classe.'" id="bloco_'.$conta_linha.'_'.$conta_caracter.'" src="parede_pequena.jpeg" width="'.round($largura_bloco).'" height="'.round($altura_bloco).'" style="top: '.round($altura_bloco*$conta_linha).'; left: '.round($largura_bloco*$conta_caracter).';">';
		}
		$conta_caracter=$conta_caracter+1;
	}
	$conta_linha=$conta_linha + 1;


}


$sql="select nome_registrado, photo_filename_registrado, username from registrados, participantes_projetos, projetos where id_chave_registrado=id_registrado and id_chave_projeto=id_projeto and apelido like '".$projeto."%'";


$result=$conn->query("$sql");

$x_principal=10;
$y_principal=10;
if ($result->num_rows>0) {
  while($row=$result->fetch_assoc())
    {
      $zindex="z-index: 100;";
      $outros="avatar_outros";
      $nome=$row["nome_registrado"];
      $arquivo_itz=$row["photo_filename_registrado"];
      $arquivo=str_replace("../","",$arquivo_itz);
      $id_nome=$row["username"];
      $file_name=$dir."/".$id_nome.".pos";
		if (file_exists($file_name)){
			$fs_pos=fopen($file_name,'r');
			$x_=fgets($fs_pos);
			$y_=fgets($fs_pos);
			if ($aluno==$id_nome)
				{
					$x_principal=$x_;
					$y_principal=$y_;
					
				}
			fclose($fs_pos);
		} else {$x_=rand(0,1000); $y_=rand(0,700);}	
	if ($aluno==$id_nome)
		{
			$outros="";
			$zindex="z-index: 1000;";
		}
	echo '<img class="avatar '.$outros.'" id="'.$id_nome.'" alt="'.$arquivo.'" src="'.$arquivo.'" width="100" height="100" style=" '.$zindex.' top: '.$y_.'; left: '.$x_.';">';



    }
} else {echo 'Deu Problema: '.$conn->error;}




			

echo '
</div>
</body>
<script>
var delta_x=10.0;    // passos no x
var delta_y=10.0;    // passos no y
var delta_vx=1;   // passos na velocidade x
var delta_vy=1;   // passos na velocidade y
var delta_ax=0.001;   // passos na velocidade x
var delta_ay=0.001;   // passos na velocidade y

var x='.$x_principal.';          // posicao x do personagem principal
var y='.$y_principal.';	   // posicao y do personagem principal
var velho_x=x; // para ajudar a deixar o scroll mais smooth... o sistema de scroll smooth do css tem problemas de renderizacao e nao eh cross-platform.
var velho_y=y; // idem para velho_x

var x_max=4000;	   // posicao x maxima do personagem principal
var y_max=4000;     // posicao y maxima do personagem principal
var x_min=0;	   // posicao x minima do personagem principal
var y_min=0;	   // posicao y minima do personagem principal
var vx=0.0;	   // velocidade de deslocamento do personagem principal
var vy=0.0;	   // velocidade de deslocamento do personagem principal
var ax=0.0;	   // aceleracao do personagem principal
var ay=0.0;	   // aceleracao do personagem principal
var vx_max=10.0;     // velocidade maxima do personagem principal
var vy_max=10.0;     // velocidade maxima do personagem principal
var vx_min=-10.0;    // velocidade maxima do personagem principal
var vy_min=-10.0;    // velocidade maxima do personagem principal
var ax_max=1.0;     // aceleracao maxima do personagem principal
var ay_max=1.0;     // aceleracao maxima do personagem principal
var ax_min=-0.4;    // aceleracao maxima do personagem principal
var ay_min=-0.4;    // aceleracao maxima do personagem principal
var intervalo=20;  // intervalo entre cada calculo de posicao do avatar principal
var intervalo_gravacao=300;  // intervalo entre cada atualizacao da gravacao da posicao dos avatares
var intervalo_busca=80;  // intervalo entre cada atualizacao da busca da posicao dos avatares
var dumping_x=0.0009;
var dumping_y=0.0009;
var massa=1;
//var largura_janela=window.innerWidth;
//var altura_janela=window.innerHeight;

var largura_janela=window.innerWidth - document.getElementById("menu").offsetWidth;
var altura_janela =window.innerHeight;
document.getElementById("altura").innerText=altura_janela;
fracao_borda=0.3; // fracao da largura da borda a partir da qual o scroll comeca;

var borda_x_scroll=largura_janela*fracao_borda;
var borda_y_scroll=altura_janela*fracao_borda;

var delta_scroll_x=0; // delta a ser somado no scroll x
var delta_scroll_y=0; // delta a ser somado no scroll x
var obj = document.getElementsByClassName("avatar");
var conta_avatares=0; // percorre a lista de avatares

var obstaculos=[]; // array otimizada horizontalmente para testar colisoes com obstaculos.


var atualiza_posicao=setInterval(anda,intervalo);
var grava_posicao=setInterval(grava,intervalo_gravacao);
var busca_posicao=setInterval(busca_rapida,intervalo_busca);
const retamanho = new ResizeObserver(entries => {
	for (const entry of entries) {
		altura_janela=window.innerHeight;
		largura_janela=window.innerWidth - document.getElementById("menu").offsetWidth;
		borda_x_scroll=largura_janela*fracao_borda; 
		borda_y_scroll=altura_janela*fracao_borda;
		document.getElementById("largura").innerText=largura_janela;
		document.getElementById("altura").innerText=altura_janela;
		document.getElementById("borda_x_scroll").innerText="Borda Scroll y: " + borda_x_scroll.toFixed(2);
		document.getElementById("borda_y_scroll").innerText="Borda Scroll y: " + borda_y_scroll.toFixed(2);
		
	}
});

retamanho.observe(document.body);

function patrulhamudancaTamanho(e){
	document.getElementById("resize").innerText=e[0].contentBoxSize.inlineSize;
}

array_obstaculos();
//mostra_otimizado();
function array_obstaculos(){

var obs = document.getElementsByClassName("parede");

var i;
var indice=-1;

for (i=0; i < obs.length; i++) {
	indice = obstaculos.findIndex(acha_indice);
	if (indice>-1) {
		obstaculos[indice][2]=parseInt(obs[i].style.left.replace("px","")) + obs[i].width;
		obstaculos[indice][3]=parseInt(obs[i].style.top.replace("px","")) + obs[i].height;
	}
	else 
	{
		obstaculos.push( [parseInt(obs[i].style.left.replace("px","")), parseInt(obs[i].style.top.replace("px","")), parseInt(obs[i].style.left.replace("px","")) + obs[i].width, parseInt(obs[i].style.top.replace("px","")) + obs[i].height] )
	}
			
	function acha_indice(top){
		return top[1]==parseInt(obs[i].style.top.replace("px","")) && (top[2]==parseInt(obs[i].style.left.replace("px","")) || top[2]==parseInt(obs[i].style.left.replace("px",""))+1 || top[2]==parseInt(obs[i].style.left.replace("px",""))-1  );
	}

} // for

var repeat=0;
var total=obstaculos.length;

for (repeat=0; repeat < total; repeat++){
for (i = obstaculos.length - 1; i>-1; i--){
	indice = obstaculos.findIndex(acha_vertical);
	if (indice>-1){
		obstaculos[indice][3]=obstaculos[i][3];
		obstaculos.splice(i,1);
	}
	function acha_vertical(tupla){
		return tupla[0]==obstaculos[i][0] && tupla[2]==obstaculos[i][2] && (tupla[3]==obstaculos[i][1] || tupla[3]==obstaculos[i][1]+1 || tupla[3]==obstaculos[i][1]-1 || tupla[3]==obstaculos[i][1]+3 || tupla[3]==obstaculos[i][1]+2 || tupla[3]==obstaculos[i][1]-2   );  
	}
} // for

 }
}

function mostra_otimizado(){
var j;
	for(j=0; j < obstaculos.length; j++){
		var div = document.createElement("DIV");
		div.style.backgroundColor="white";
		div.style.position="absolute";
		div.id="div_"+j;
		div.style.zIndex=200;
		div.style.border="1px solid black";
		div.style.top=obstaculos[j][1];
		div.style.left=obstaculos[j][0];
		div.style.width=obstaculos[j][2] - obstaculos[j][0];
		div.style.height=obstaculos[j][3] - obstaculos[j][1];
		document.getElementById("conteudo").appendChild(div);
		
	
	}
}

function bateu(){
	
	var i;
	for (i=0; i < obstaculos.length; i++){
		var aluno = document.getElementById("'.$aluno.'");
		//topo     = parseInt(aluno.style.top.replace("px",""));
		//esquerda = parseInt(aluno.style.left.replace("px",""));
		topo = y;
		esquerda = x;
		baixo    = topo + parseInt(aluno.height);
		direita  = esquerda + parseInt(aluno.width);
		obstaculo =  obstaculos[i];

		if ( topo  > obstaculo[1] && topo  < obstaculo[3] && esquerda > obstaculo[0] && esquerda < obstaculo[2]) {return i;}
		if ( topo  > obstaculo[1] && topo  < obstaculo[3] && direita  > obstaculo[0] && direita  < obstaculo[2]) {return i;}
		if ( baixo > obstaculo[1] && baixo < obstaculo[3] && esquerda > obstaculo[0] && esquerda < obstaculo[2]) {return i;}
		if ( baixo > obstaculo[1] && baixo < obstaculo[3] && direita  > obstaculo[0] && esquerda < obstaculo[2]) {return i;}
	}
return -1;
}

function busca_rapida(){

	   var resposta="";
           var url="busca_rapida.php";
           var oReq=new XMLHttpRequest();
           oReq.open("GET", url, true);
           oReq.onload = function (e) {
                     resposta=oReq.responseText;
		     lista=resposta.split("#");
		     lista.forEach(percorre);
			function percorre(item, index)
			{
				var valores=item.split(":");
				if (valores[0]!=="'.$aluno.'"){
				try {
				document.getElementById(valores[0]).style.left = valores[2]; 
				document.getElementById(valores[0]).style.top = valores[3]; 
				} catch(err) {}
				}

			}
                     }
           oReq.send();
	
}



function busca(){

	   itz=obj[conta_avatares];
	   if (itz.id!=="'.$aluno.'")
	   {
	   var resposta="";
           var url="busca_posicao.php?aluno="+itz.id;
           var oReq=new XMLHttpRequest();
           oReq.open("GET", url, true);
           oReq.onload = function (e) {
                     resposta=oReq.responseText;
		     lista=resposta.split(":");
	   console.log(itz.id+" > "+conta_avatares);	
		     itz.style.left=lista[0];
		     itz.style.top=lista[1];
                     }
           oReq.send();
	   }
	   conta_avatares=conta_avatares+1;
	   if (conta_avatares>=obj.length) {conta_avatares=0;}
	
}


function grava(){

           var resposta="";
           var url="atualiza_posicao.php?aluno='.$aluno.'&pos_x="+x+"&pos_y="+y;
           var oReq=new XMLHttpRequest();
           oReq.open("GET", url, true);
           oReq.onload = function (e) {
                     resposta=oReq.responseText;
                     }
           oReq.send();


}


function anda(){
	var objeto=document.getElementById("'.$aluno.'");
	if (vx>vx_max){ax=0;}
	if (vx<vx_min){ax=0;}
	if (vy>vy_max){ay=0;}
	if (vy<vy_min){ay=0;}
	vx=vx+(ax-dumping_x*vx/massa)*intervalo;
	vy=vy+(ay-dumping_y*vy/massa)*intervalo;
	
	velho_x=x; // servira para deixar o scroll mais smooth
	velho_y=y;
	x=x+vx*intervalo;
	y=y+vy*intervalo;

	if (bateu()>-1) {
		vx=0;
		vy=0;
		x=velho_x;
		y=velho_y;		
	}

							document.getElementById("pos_x").innerText="Pos x: "+x.toFixed(2);
							document.getElementById("vx").innerText="VX: "+vx.toFixed(2);
							document.getElementById("pos_y").innerText="Pos y: "+y.toFixed(2);
							document.getElementById("vy").innerText="VY: "+vy.toFixed(2);
							document.getElementById("largura").innerText="largura_janela: "+largura_janela.toFixed(2);
							document.getElementById("altura").innerText="altura_janela: "+altura_janela.toFixed(2);
							document.getElementById("velho_x").innerText="velho_x: "+velho_x.toFixed(2);
							document.getElementById("velho_y").innerText="velho_y: "+velho_y.toFixed(2);
							
							document.getElementById("pos_delta_scroll_x").innerText="Delta Scroll x: "+delta_scroll_x.toFixed(2);
							document.getElementById("pos_delta_scroll_y").innerText="Delta Scroll y: "+delta_scroll_y.toFixed(2);
							document.getElementById("borda_x_scroll").innerText="Borda Scroll y: " + borda_x_scroll.toFixed(2);
							document.getElementById("borda_y_scroll").innerText="Borda Scroll y: " + borda_y_scroll.toFixed(2);

	ax=0;
	ay=0;

	if (x>x_max){x=x_max; velho_x=x; vx=0;}
	if (x<x_min){x=x_min; velho_x=x;  vx=0;}
	if (y>y_max){y=y_max; velho_y=y; vy=0;}
	if (y<y_min){y=y_min; velho_y=y; vy=0;}
	if (delta_scroll_x < 0) {delta_scroll_x = 0;}
	if (delta_scroll_y < 0) {delta_scroll_y = 0;}

	if (x-delta_scroll_x>largura_janela - borda_x_scroll && velho_x - delta_scroll_x < largura_janela - borda_x_scroll ) 
						{ 
							delta_scroll_x=delta_scroll_x + (x-velho_x); 
							document.getElementById("conteudo").scrollLeft=delta_scroll_x;
						}
	else {document.getElementById("conteudo").scrollLeft=delta_scroll_x;}

	if (x-delta_scroll_x<0 + borda_x_scroll && velho_x - delta_scroll_x > 0 + borda_x_scroll) 
						{ 
							delta_scroll_x=delta_scroll_x + (x-velho_x); 
							document.getElementById("conteudo").scrollLeft=delta_scroll_x;
						}

	if (y-delta_scroll_y>altura_janela - borda_y_scroll && velho_y - delta_scroll_y > altura_janela - borda_y_scroll) 
						{ 
							delta_scroll_y=delta_scroll_y + (y - velho_y); 
							document.getElementById("conteudo").scrollTop=delta_scroll_y;
						}
	else {document.getElementById("conteudo").scrollTop=delta_scroll_y;}

	if (y-delta_scroll_y<0 + borda_y_scroll && velho_y - delta_scroll_y > 0 + borda_y_scroll) 
						{ 
							delta_scroll_y = delta_scroll_y + (y - velho_y); 
							document.getElementById("conteudo").scrollTop=delta_scroll_y;
						}



	objeto.style.top=y;
	objeto.style.left=x;
}

function acelera (f){
	f();
	if (ax>ax_max){ax=ax_max;}
	if (ax<ax_min){ax=ax_min;}
	if (ay>ay_max){ay=ay_max;}
	if (ay<ay_min){ay=ay_min;}
	
}

function testa_teclado(e) { 
	var tecla = e.which || e.keyCode;
	if (tecla==37)  {
				acelera(function(){ax=ax-delta_ax;});	
			}
	if (tecla==39)  {
				acelera(function(){ax=ax+delta_ax;});	
			}
	if (tecla==38)  {
				acelera(function(){ay=ay-delta_ay;});
			}
	if (tecla==40)  {
				acelera(function(){ay=ay+delta_ay;});
			}
}
</script>
</html>

';
fclose($fs_mapa);
?>
