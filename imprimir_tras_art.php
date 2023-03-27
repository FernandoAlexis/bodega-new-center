<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script type="text/javascript" src="jquery-3.4.1.min.js"></script>
</head>
<script>
	function imprimir()
	{
		$("#conte").hide();
		window.print();
		$("#conte").show();
	}
</script>
<body>
<div id="conte">
<?php
	include("conexion.php");

?>
<img src="imprimir.png" width="5%" height="5%" style="float: right; margin-right: 0.5%; margin-top: -2%; cursor: pointer;" onclick="imprimir()">
</div>
<?php
$desde=$_GET['d'];
$hasta=$_GET['h'];
$clasificacion=$_GET['clasi'];
$bodega=$_GET['bodega'];
if($clasificacion=='')
{
	$msj='TODAS';
}else
{
	$msj=$clasificacion;
}
if($desde=='' or $hasta=='')
{
	echo "<scrip>alert('ERROR AL IMPRIMIR SELECCIONE LAS DOS FECHAS')</scrip>";
	echo "<scrip>location.replace('traslados_articulos.php')</scrip>";
}else
{


	//$c=$conexion2->query("select articulo,count(articulo) as cantidad,fecha from traslado where fecha between '$desde' and '$hasta' and origen='$bodega' group by articulo,fecha ")or die($conexion2->error());
	$c=$conexion2->query("select registro.codigo as articulo,COUNT(registro.codigo) as cantidad,sum((ISNULL(lbs,0)+isnull(peso,0))) peso,traslado.fecha from registro inner join traslado on registro.id_registro=traslado.registro where traslado.fecha between '$desde' and '$hasta' and traslado.origen='$bodega' group by registro.codigo,traslado.fecha order by registro.codigo")or die($conexion2->error());

	$n=$c->rowCount();
	if($n==0)
	{
		echo "<h2>NO SE ENCONTRO NINGUN RESULTADO</h2>";
	}else
	{
		echo "<table border='1' class='tabla' cellpadding='10' >";
		echo "
	<tr>
	<td colspan='5'>MOSTRANDO DESDE LA FECHA: $desde HASTA: $hasta ; CLASIFICACION:$msj ; BODEGA ORIGEN: $bodega</td>
	</tr>
			<tr>
		<td>ARTICULO</td>
		<td>DESCRIPCION</td>
		<td>CLASIFICACION</td>
		<td>CANTIDAD</td>
		<td>PESO</td>
		<td>FECHA</td>
		
		</tr>";
		$t=0;
		$n=1;
		while($f=$c->FETCH(PDO::FETCH_ASSOC))
		{
			$art=$f['articulo'];
		$ca=$conexion1->query("select * from consny.articulo where articulo='$art'")or die($conexion1->error());
		$fca=$ca->FETCH(PDO::FETCH_ASSOC);
		if($clasificacion=='')
		{
			echo "<tr>
		
		<td>".$fca['ARTICULO']."</td>
		<td>".$fca['DESCRIPCION']."</td>
		<td>".$fca['CLASIFICACION_2']."</td>
		<td>".$f['cantidad']."</td>
		<td>".$f['peso']."</td>
		<td>".$f['fecha']."</td>
		
		</tr>";
		$n++;
		$t=$t + $f['cantidad'];
		}else if($clasificacion ==$fca['CLASIFICACION_2'])
		{
			echo "<tr>
		
		<td>".$fca['ARTICULO']."</td>
		<td>".$fca['DESCRIPCION']."</td>
		<td>".$fca['CLASIFICACION_2']."</td>
		<td>".$f['cantidad']."</td>
		<td>".$f['peso']."</td>
		<td>".$f['fecha']."</td>
		</tr>";
		$n++;
		$t=$t + $f['cantidad'];
			
		}else
		{

		}

		
		}
		echo "<tr>
		<td colspan='3'>TOTAL</td>
		<td>$t</td>
		<td></td>
		</tr></table>";
}
}
?>
</body>
</html>