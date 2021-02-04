<?php
namespace App\Files;
use Cake\Datasource\ConnectionManager;

class File
{
    public function downloadIcons()
    {
        try
        {
            $zip = new \ZipArchive();
            $filenameZip = "tempicons.zip";
            // Sino se puede crear
            if ($zip->open($filenameZip, \ZipArchive::CREATE)!==TRUE)
            {
                exit("No se puede abrir <$filenameZip>\n");
            }
            // Agregamos una carpeta
            /*  $dir = 'miDirectorio';
              $zip->addEmptyDir($dir);*/

            $sql ="SELECT m.icon as icon, m.icon2 as icon2, m.filetype as filetype, m.filename as filename FROM menus as m";
            $dbconnect='dbpriv';
            $conn = ConnectionManager::get($dbconnect);
            $inf = $conn->execute($sql);
            $resultado = $inf->fetchAll("assoc");

            foreach ($resultado as $items) {
                header("Content-type: " . $items['filetype']);
                $imag = $items['icon'];
                if( $items['icon']!=="")
                {
                    $nombre=explode(".", $items['filename']);
                    $zip->addFromString($nombre[0]."2.".$nombre[1], $items['icon2']);
                }

                $zip->addFromString($items['filename'], $items['icon']);
            }
            $zip->close();
            // Creamos las cabezeras que forzaran la descarga del archivo como archivo zip.
            header("Content-type: application/octet-stream");
            header("Content-disposition: attachment; filename=iconos.zip");
            // leemos el archivo creado
            readfile($filenameZip);
            // Por último eliminamos el archivo temporal creado
            unlink($filenameZip);//Destruyearchivo temporal

            return true;
        } catch (\Exception $e)
        {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
    }

    public function extraZip()
    {


        $dir=str_replace(explode("/",$_SERVER['PHP_SELF'])[3],'img/iconos/',$_SERVER['PHP_SELF']);
        $directorio=$_SERVER['DOCUMENT_ROOT'].$dir;

        $zip = new \ZipArchive();
        //Abrimos el archivo a descomprimir
        $rutaOrigen='C:\Users\tec101\Downloads\iconos.zip';
        if ($zip->open($rutaOrigen) === TRUE) {
            $zip->extractTo($directorio);
            $zip->close();
            unlink($rutaOrigen);
            echo 'ok';
        } else {
            echo 'failed';
        }
        return true;
    }
}