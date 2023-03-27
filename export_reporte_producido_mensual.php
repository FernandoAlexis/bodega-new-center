<style>

	body{
		font-family: Consolas, monaco, monospace;
	}
</style>
<meta charset="utf-8">
<?php
$desde=$_GET['desde'];
$hasta=$_GET['hasta'];
$nom=$_GET['nom'];
try {
        $conexion1 = new PDO("sqlsrv:Server=192.168.0.44\serverpet620;Database=eximp600", "sa", "$0ftland");
    }
    catch(PDOException $e) {
        die("!!ERROR!! SE PERDIO CONEXION CON EL SERVIDOR: " );
    }

    try {
        $conexion2 = new PDO("sqlsrv:Server=192.168.0.44\serverpet620;Database=pruebabd", "sa", "$0ftland");
    }
    catch(PDOException $e) {
        die("Error connecting to SQL Server: " . $e->getMessage());
    }
if($nom=='')
	{
		$c=$conexion2->query("select producido from registro where fecha_documento between '$desde' and '$hasta' and tipo='p' group by producido order by producido")or die($conexion2->error());
	}else
	{
		$c=$conexion2->query("select producido from registro where fecha_documento between '$desde' and '$hasta' and producido like '%$nom%' and tipo='p' group by producido order by producido")or die($conexion2->error());
	}
	
	$n=$c->rowCount();
	if($n==0)
	{
		echo "<script>alert('NO SE PUDO EXPORTAR A EXCEL')</script>";
		echo "<script>window.close()</script>";
	}else
	{
		header('Content-type:application/xls');
    
		header('Content-Disposition: attachment; filename=REPORTE-PRODUCIDO-MENSUAL.xls');
		$text='';
		
			$q=$conexion2->query("select concat(month(fecha_documento),'-',year(fecha_documento)) as mes from registro where fecha_documento between '$desde' and '$hasta' and tipo='p' group by concat(month(fecha_documento),'-',year(fecha_documento))")or die($conexion2->error());
		
		echo "<tr>";
		$num=0;
		while($fq=$q->FETCH(PDO::FETCH_ASSOC))
		{
			$mes=$fq['mes'];
			$text.= "<td colspan='2' style='text-align:center;'>$mes</td>";
			$num++;
		}
		$nume=$num*2;
		$text.='</tr>';
		echo "<table border='1' style='border-collapse:collapse;'>";
		echo "<tr style='text-align:center;'>
				<td rowspan='3' style='width:35%;'>PRODUCIDO POR</td>
				<td colspan='$nume'>MES-AÑO</td>
				<td rowspan='2' colspan='2'>TOTAL</td>
			</tr>";
		echo "$text";
		$k=1;
		echo "<tr>";
		while($k<=$num)
		{
			echo "<td>CANTIDAD</td><td>PESO</td>";
			$k++;
		}
		echo "<td>CANTIDAD</td><td>PESO</td></tr>";

		while($f=$c->FETCH(PDO::FETCH_ASSOC))
		{
			$pro=$f['producido'];
			echo "<tr><td>$pro</td>";

			$q=$conexion2->query("select concat(month(fecha_documento),'-',year(fecha_documento)) as mes from registro where tipo='p' and fecha_documento between '$desde' and '$hasta' group by concat(month(fecha_documento),'-',year(fecha_documento))")or die($conexion2->error());
			$tcant=0;
			$tpeso=0;
			while($fq=$q->FETCH(PDO::FETCH_ASSOC))
			{
				$mes=$fq['mes'];
				$cf=$conexion2->query("select producido,count(producido) as cantidad,sum(lbs) as peso from registro where concat(month(fecha_documento),'-',year(fecha_documento))='$mes' and producido='$pro' group by producido")or die($conexion2->error());
				$fcf=$cf->FETCH(PDO::FETCH_ASSOC);
				$cant=$fcf['cantidad'];
				$peso=$fcf['peso'];
				if($cant=='')
				{
					$cant=0;
				}
				if($peso=='')
				{
					$peso=0;
				}
				echo "<td>$cant</td><td>$peso</td>";
				$tcant=$tcant + $cant;
				$tpeso=$tpeso + $peso;
			}
			echo "<td>$tcant</td><td>$tpeso</td></tr>";


		}
		$q=$conexion2->query("select concat(month(fecha_documento),'-',year(fecha_documento)) as fecha from registro where fecha_documento between '$desde' and '$hasta' group by concat(month(fecha_documento),'-',year(fecha_documento))")or die($conexion2->error());
		$total_cant=0;
		$total_peso=0;
		echo "<tr><td>TOTAL</td>";

		while($fq=$q->FETCH(PDO::FETCH_ASSOC))
		{
			$fecha=$fq['fecha'];
			if($nom=='')
			{
				$cfi=$conexion2->query("select count(*) as cantidad,sum(lbs) as peso from registro where concat(month(fecha_documento),'-',year(fecha_documento))='$fecha' and tipo='p'")or die($conexion2->error());
			}else
			{
				$cfi=$conexion2->query("select count(*) as cantidad,sum(lbs) as peso from registro where concat(month(fecha_documento),'-',year(fecha_documento))='$fecha' and tipo='p' and producido like '%$nom%'")or die($conexion2->error());
			}
			
			$fcfi=$cfi->FETCH(PDO::FETCH_ASSOC);
			$cant=$fcfi['cantidad'];
			$peso=$fcfi['peso'];
			$total_cant=$total_cant + $cant;
			$total_peso=$total_peso + $peso;
			if($cant=='')
			{
				$cant=0;
			}
			if($peso=='')
			{
				$peso=0;
			}
			echo "<td>$cant</td><td>$peso</td>";

		}
		if($total_peso=='')
		{
			$total_peso=0;
		}
		if($total_cant=='')
		{
			$total_cant=0;
		}
		echo "<td>$total_cant</td><td>$total_peso</td></tr></table>";

	}
?>

<script>
	window.close();
</script>