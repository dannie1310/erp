<?php
/**
 * Created by PhpStorm.
 * User: EMartinez
 * Date: 13/01/2020
 * Time: 04:20 PM
 */

namespace App\Repositories\CADECO\Finanzas\Facturas;


use Exception;
use App\Facades\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Moneda;
use App\Models\CADECO\Empresa;
use App\Models\CADECO\Factura;
USE Illuminate\Support\Facades\DB;
use App\Models\CTPQ\Parametro;
use Illuminate\Support\Facades\Config;
use App\Repositories\RepositoryInterface;
use App\Models\SEGURIDAD_ERP\Finanzas\AvisoSATOmitir;
use App\Models\SEGURIDAD_ERP\Finanzas\FacturaRepositorio;
use Webpatser\Uuid\Uuid;

class Repository extends \App\Repositories\Repository implements RepositoryInterface
{
    public function __construct(Factura $model)
    {
        parent::__construct($model);
        $this->model = $model;
    }

    public function getRFCObra()
    {
        $obra = Obra::find(Context::getIdObra());
        if($obra){
            return $obra->rfc;
        }
    }

    public function getIdMoneda($moneda_sat)
    {
        switch($moneda_sat){
            case 'MXN':
                return 1;
                break;
            case 'USD':
                return 2;
                break;
            case 'EUR':
                return 3;
                break;
            default:
                return 1;
                break;
        }
    }

    public function getEmpresa(Array $datos){
        $empresa = Empresa::where("rfc","=",$datos["rfc"])
            ->whereIn("tipo_empresa",[1,2,3,4])->first();
        $salida = null;

        if($empresa){
            $salida =[
                "id_empresa"=>$empresa->id_empresa,
                "rfc"=>$empresa->rfc,
                "razon_social"=>$empresa->razon_social,
                "nuevo"=>0,
            ];
        }
        return $salida;
    }

    public function getEmpresaPorId($id){
        $empresa = Empresa::find($id);
        $salida = null;

        if($empresa){
            $salida =[
                "id_empresa"=>$empresa->id_empresa,
                "rfc"=>$empresa->rfc,
                "razon_social"=>$empresa->razon_social,
                "nuevo"=>0,
            ];
        }
        return $salida;
    }

    public function getRFCEmpresa($id_empresa)
    {
        $empresa = Empresa::find($id_empresa);
        if ($empresa) {
            $rfc = $empresa->rfc;
            $rfc = strtoupper($rfc);
            return $rfc;
        } else {
            return null;
        }
    }

    public function getEFO($rfc)
    {
        $efo = DB::connection("seguridad")->table("Fiscal.ctg_efos")
        ->where("rfc","=",$rfc)
        ->first();
        return $efo;
    }

    public function create(array $datos)
    {
        return $this->model->registrar($datos);
    }

    public function getArchivoSQL($archivo)
    {
        return DB::raw("CONVERT(VARBINARY(MAX), '" . $archivo . "')");
    }

    public function validaExistenciaRepositorio($hash_file, $uuid)
    {
        $factura_repositorio = FacturaRepositorio::whereNotNull("id_transaccion")
            ->where("uuid","=", $uuid)->first();
        return $factura_repositorio;
    }

    public function getEsOmitido($mensaje, $rfc_emisor, $uuid)
    {
        $explode = explode("-",$mensaje);
        $codigo = trim($explode[0]);
        $existe = AvisoSATOmitir::where("rfc_emisor",$rfc_emisor)
            ->where("clave",$codigo)
            ->where("estado",1)
        ->count();
        if($existe == 1){
            return $existe;
        } else {
            $existe = AvisoSATOmitir::where("uuid",$uuid)
                ->where("clave",$codigo)
                ->where("estado",1)
                ->count();
            return $existe;
        }
    }

    public function guardarXml($xml_fuente, $xml_array){
        $this->logs = [];
        $xml_split = explode('base64,', $xml_fuente);
        $xml = base64_decode($xml_split[1]);

        $obra = Obra::find(Context::getIdObra());
        if($obra->datosContables->BDContPaq != "")
        {
            $this->logs[] = "Inicia";
            DB::purge('cntpq');
            Config::set('database.connections.cntpq.database', $obra->datosContables->BDContPaq);
            try{
                $parametros = Parametro::first();
            } catch (Exception $e){
                $this->logs[] = "Error de lectura a la base de datos: ".Config::get('database.connections.cntpq.database').".";
            }

            try{
                $arreglo_bbdd = $this->existDb($parametros->GuidDSL);
                if($arreglo_bbdd == false){
                    $this->logs[] = "Error existDb";
                }
            } catch (Exception $e){
                $this->logs[] = "Error existDb catch: ". $e->getMessage();
            }

            try{
                $val_insercionCertificado = $this->insUpdCertificate( $xml_array['certificado'], $xml_array['no_certificado'], $xml_array['emisor']['rfc'], $xml_array['emisor']['nombre']);
                if(!$val_insercionCertificado){
                    $this->logs[] = "Error insUpdCertificate";
                }
            }catch (Exception $e){
                $this->logs[] = "Error insUpdCertificate catch: ". $e->getMessage();
            }
            $duplicado = false;
            try{
                if($duplicado = $this->buscarCfdiDuplicado($arreglo_bbdd[0]['NameDB'], $xml_array['uuid'])){
                    $this->logs[] = "CFDI ya existente en ADD";
                }
            }catch (Exception $e){
                $this->logs[] = "Error buscarCfdiDuplicado catch: ". $e->getMessage();
            }

            if(!$duplicado){
                $guid_doc_metadata = Uuid::generate()->string;

                /*
                $arreglo_bbdd[0]['NameDB'],->dm
                $arreglo_bbdd[1]['NameDB'],->dc
                $arreglo_bbdd[3]['NameDB'],->oc
                $arreglo_bbdd[2]['NameDB']->om
                 * */
                try{
                    $va_insert_xml = $this->spInsUpdDocument($xml, $arreglo_bbdd[0]['NameDB'],$arreglo_bbdd[1]['NameDB'],$arreglo_bbdd[3]['NameDB'],$arreglo_bbdd[2]['NameDB'], $guid_doc_metadata, $xml_array['fecha_hora'], $xml_array['emisor']['rfc'], $xml_array['folio']);
                    if(!$va_insert_xml){
                        $this->logs[] = "Error spInsUpdDocument";
                    }else{
                        $this->logs[] = ["tipo"=>1,"descripcion"=>"Envío éxitoso, comprobante con GUID: ".$guid_doc_metadata. " en base de datos: ".Config::get('database.connections.cntpqdm.database')];
                    }
                }catch (Exception $e){
                    $this->logs[] = "Error spInsUpdDocument catch: ". $e->getMessage();
                }
            }

            $this->logs[] = "Finaliza";

        }
        return $this->logs;
    }

    private function existDb($guidCompany){
        try{
            $resp = DB::connection('cntpq')->select(DB::raw("exec [DB_Directory].[dbo].[spExistDB] @GuidCompany = '$guidCompany'"));
            $resp_ = json_decode(json_encode($resp), true);
            return $resp_;
        }catch(Exception $e){
            throw new Exception($e->getMessage(),500);
        }
        return false;
    }

    private function insUpdCertificate($llave, $no_serie, $rfc, $r_social){
        $guidDoc = Uuid::generate()->string;
        $issuer_name = 'OID.1.2.840.113549.1.9.2=Responsable: Administración Central de Servicios Tributarios al Contribuyente, OID.2.5.4.45=SAT970701NN3, L=Cuauhtémoc, S=Distrito Federal, C=MX, PostalCode=06300, STREET="Av. Hidalgo 77, Col. Guerrero", E=acods@sat.gob.mx, OU=Administración de Seguridad de la Información, O=Servicio de Administración Tributaria, CN=A.C. del Servicio de Administración Tributaria';
        $subject_name = 'OU=UNICA, SERIALNUMBER=" / ", OID.2.5.4.45='.$rfc.' / , O='.$r_social.', OID.2.5.4.41='.$r_social.', CN='.$r_social;
        try{
            $resp = DB::connection('cntpq')
                ->update("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON; exec [DB_Directory].[dbo].[spInsUpdCertificate] @GuidDocument = 'DD41F3B0-D47A-11EB-82DA-E1114F8D5A0B', @LlavePublica='$llave', @NumeroSerie='$no_serie', @FechaInicial='',
                @FechaFinal='',@SubjectName='$subject_name', @IssuerName='$issuer_name', @IsTesting=0");

            $val = DB::connection('cntpq')->select(DB::raw("SELECT top 1 * FROM [DB_Directory].[dbo].[Certificates] WHERE NumeroSerie='$no_serie'"));
            return $val != false;
        }catch(Exception $e){
            throw new Exception("Error de ejecución del sp spInsUpdCertificate en la base de datos DB_Directory".$e->getMessage().$e->getLine(),500);
        }
        return false;
    }

    private function buscarCfdiDuplicado($base_datos, $uuid){
        try{
            DB::purge('cntpqdm');
            Config::set('database.connections.cntpqdm.database', $base_datos);
            $resp = DB::connection('cntpqdm')->select(DB::raw("SELECT Documento.GuidDocument GuidDocument FROM  Documento WITH(NOLOCK)
        LEFT JOIN Comprobante WITH(NOLOCK) ON Comprobante.GuidDocument = Documento.GuidDocument
        WHERE Comprobante.UUID='$uuid' "));
            return count($resp) > 0;
        }catch(Exception $e){
            throw new Exception("Error de lectura a la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage().$e->getLine(),500);
        }
    }

    private function spInsUpdDocument($xml, $db_doc_metadata, $db_doc_content, $db_other_content, $db_other_metadata, $guid, $doc_date, $rfc, $folio){
        DB::purge('cntpqdm');
        Config::set('database.connections.cntpqdm.database', $db_doc_metadata);
        $hash = md5($guid).'=';

        $micro_date = microtime();
        $date_array = explode(" ",$micro_date);
        $date = date("Y-m-d",$date_array[1]).'T'. date('H:i:s', $date_array[1]) . str_replace('0.', '.', $date_array[0]).'-05:00';

        $xml_data = $this->del_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>');
        $xml_data = $this->del_string_between($xml, '<?', '?>');

        $pXmlFile = '<Metadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0"
        Hash="'.$hash.'" Status="active" TimeStamp="'.$date.'" FilePermissions="R" GuidDocument="'.$guid.'" Type="CFDI" xmlns="http://www.contpaqi.com">
        <Document><Document xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Type="XML" xmlns="">' . $xml_data.
        '</Document></Document><MetadataApp><SourceFile Value="'.$guid.'.xml" xmlns="" /></MetadataApp></Metadata>';

        $pXmlFile = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($pXmlFile));
        $pXmlFile = preg_replace('/[ ]{2,}|[\r]/', ' ', trim($pXmlFile));
        $pXmlFile = preg_replace('/[ ]{2,}|[\n]/', ' ', trim($pXmlFile));

        try{
            $resp = DB::connection('cntpqdm')
                    ->update(DB::raw("DECLARE @return_value int  EXEC [$db_doc_metadata].[dbo].[spInsUpdDocument]  @pXmlFile = N'$pXmlFile', @pDeleteDocument=0, @pSobreEscribe=0, @filename=NULL SELECT 'Return Value' = @return_value"));

        }catch(Exception $e){
            throw new Exception("-Error de ejecución de sp spInsUpdDocument en la base de datos: ".Config::get('database.connections.cntpqdm.database')." respuesta: ".$e->getMessage().$e->getLine(),500);
        }

        $val = DB::connection('cntpqdm')->select(DB::raw("SELECT top 1 * FROM [$db_doc_metadata].[dbo].[Comprobante] WHERE [GuidDocument]='$guid'"));
        if(count($val) == 0){
            throw new Exception("respuesta spInsUpdDocument: ".$resp." No se encontro el comprobante con el GUID: ".$guid." en la base de datos: ".Config::get('database.connections.cntpqdm.database'));
        }

        $conceptos = $this->get_string_between($xml, '<cfdi:Conceptos>', '</cfdi:Conceptos>');
        $conceptos = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($conceptos));
        $array_concepto = [];
        do{
            $array_concepto[] = $this->get_string_between($conceptos, '<cfdi:Concepto', '</cfdi:Concepto>');
            $conceptos = $this->del_string_between($conceptos, '<cfdi:Concepto', '</cfdi:Concepto>');
        } while(strlen($conceptos) > 50);

        try{
            foreach($array_concepto as $key => $concepto){
                $filename = $guid . '.xml';
                $conceptNumber = $key + 1;
                $pXml_Node = '<cfdi:Concepto xmlns:cfdi="http://www.sat.gob.mx/cfd/3" ' . $concepto . '</cfdi:Concepto>';
                $resp = DB::connection('cntpqdm')
                        ->update("exec [$db_doc_metadata].[dbo].[spInsConcept]  @pGuidDocument=N'$guid',@pXml_Node=N'$pXml_Node', @fileName=N'$filename', @conceptNumber=$conceptNumber");
            }

        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spInsConcept en la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage().$e->getLine(),500);
        }

            $creation_date = date("Y-m-d H:i:s",$date_array[1]);
        try{
            DB::purge('cntpqdc');
            Config::set('database.connections.cntpqdc.database', $db_doc_content);
            $resp = DB::connection('cntpqdc')
                ->update("exec [$db_doc_content].[dbo].[spSaveDocument]  @GuidDocument=N'$guid',@DocumentType=N'CFDI', @fileName=N'$filename' ,@Content=N'$xml'
                            ,@SubDirectory=N'',@DocumentDate=N'$doc_date',@CreationDate=N'$creation_date'");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spSaveDocument en la base de datos: ".Config::get('database.connections.cntpqdc.database').$e->getMessage().$e->getLine(),500);
        }

        try{
            DB::purge('cntpqdc');
            Config::set('database.connections.cntpqdc.database', $db_doc_content);
            $resp = DB::connection('cntpqdc')
                ->select("SELECT top 1 * FROM [$db_doc_content].[dbo].[DocumentContent] where [GuidDocument] = '$guid' ");
            $content = ltrim($resp[0]->Content);
            if(substr($content, 0, 1) == '?'){
                $content = str_replace('?<', '<', $content);
                $upd = DB::connection('cntpqdc')
                ->update("UPDATE [$db_doc_content].[dbo].[DocumentContent] set Content = '$content' where [GuidDocument] = '$guid' ");
            }
        }catch(Exception $e){
            throw new Exception("Error de ejecución de validación de signo ? al inicio del XML: ".Config::get('database.connections.cntpqdc.database').$e->getMessage().$e->getLine(),500);
        }


            $guid_vr = Uuid::generate()->string;
            $filename_vr = $guid_vr . '.xml';
            $val_result = $this->validationResult($guid_vr, $date, $doc_date, $rfc, $folio);
        try{
            DB::purge('cntpqom');
            Config::set('database.connections.cntpqom.database', $db_other_metadata);
            $resp = DB::connection('cntpqom')
                ->update("exec [$db_other_metadata].[dbo].[spInsUpdDocument]  @pXmlFile = '$val_result', @pDeleteDocument=0, @pSobreEscribe=0");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spInsUpdDocument en la base de datos: ".Config::get('database.connections.cntpqom.database').$e->getMessage(),500);
        }

        try{
            $fecha_sf = date('Y-m-d');
            $resp = DB::connection('cntpqom')
                ->update("exec [$db_other_metadata].[dbo].[spCreateReferences]  @GuidRel =N'$guid' , @RelatedGuidDocuments=N'$guid_vr'
                        ,@ApplicationType=N'ADD',@TipoDoc=N'ValidationResult',@Fecha=N'$fecha_sf',@Comment=N'Acuse Validación Comprobante $doc_date $rfc $folio '");
        }catch(Exception $e){
            throw new Exception("Error de ejecución de sp spCreateReferences en la base de datos: ".Config::get('database.connections.cntpqom.database').$e->getMessage(),500);
        }

            $val_result_corto = $this->get_string_between($val_result, '<ValidationResult', '</ValidationResult>');
            $val_result_corto = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><ValidationResult'. $val_result_corto .'</ValidationResult>';
        try {
            DB::purge('cntpqoc');
            Config::set('database.connections.cntpqoc.database', $db_other_content);
            $resp = DB::connection('cntpqoc')
                ->update("exec [$db_other_content].[dbo].[spSaveDocument]  @GuidDocument=N'$guid_vr',@DocumentType=N'VALIDATIONRESULT', @fileName=N'$filename_vr' ,@Content=N'$val_result_corto'
                            ,@SubDirectory=N'',@DocumentDate=N'$creation_date',@CreationDate=N'$creation_date'");
        }
        catch(Exception $e){
            throw new Exception("Error de ejecución de sp spSaveDocument  en la base de datos: ".Config::get('database.connections.cntpqoc.database').$e->getMessage(),500);
        }
        try {
            $resp = DB::connection('cntpqdm')
                ->update("exec [$db_doc_metadata].[dbo].[spUpdDocumento]  @GuidDocument =N'$guid',@ProcessApp=N'',@UserResponsibleApp=N'',
                            @ReferenceApp=N'',@NotesApp=N'',@MetadataEstatusApp=N'Timbrado',@ValidationStatus=N'OK' ");
        }
        catch(Exception $e){
            throw new Exception("Error de ejecución de sp spUpdDocumento  en la base de datos: ".Config::get('database.connections.cntpqdm.database').$e->getMessage(),500);
        }

        return true;

    }

    private function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    private function del_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $len = strpos($string, $end, $ini)-$ini;
        $texto =  substr($string, $ini, $len);
        return str_replace($texto.$end, '', $string);
    }

    private function validationResult($guid, $date, $date_xml, $rfc, $folio){
        $hash = md5($guid).'=';
        return '<?xml version="1.0" encoding="utf-8"?><Metadata xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Version="1.0"
        Hash="'.$hash.'" Status="active" TimeStamp="'.$date.'" FilePermissions="R" GuidDocument="'.$guid.'" Type="ValidationResult"
        xmlns="http://www.contpaqi.com"><Document><Document xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" Type="XML" xmlns="">
        <ValidationResult documentType="CFDI" IsDuplicated="false" IsValid="true"><RFCIssuer>'.$rfc.'</RFCIssuer><DateIssue>'.$date_xml.'</DateIssue><Serial></Serial><Number>'.$folio.'</Number>
        <ValidationItemResult validationResult="OK" descriptionValidation="Codificación del CFD/CFDI es UTF-8 . " codeValidation="1.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El XML es un comprobante " codeValidation="1.2" /><ValidationItemResult validationResult="OK" descriptionValidation="Estructura  "  codeValidation="1.3" />
        <ValidationItemResult validationResult="OK" descriptionValidation="La versión del comprobante es correcta a su fecha de generación" codeValidation="1.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El número de certificado del comprobante corresponde al certificado reportado " codeValidation="2.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante en base 64 es correcto" codeValidation="2.2" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante fue emitido por el SAT " codeValidation="2.3" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante corresponde a un CSD o FIEL " codeValidation="2.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El sello del comprobante es válido para el certificado reportado " codeValidation="2.8" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado del comprobante no debe corresponder a un certificado de prueba " codeValidation="2.9" /><ValidationItemResult validationResult="OK"
        descriptionValidation="El certificado corresponde al RFC del Emisor" codeValidation="3.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI Se encontró el complemento Timbre Fiscal Digital " codeValidation="4.3" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI Se encontró el certificado  del PAC   (00001000000504587508)" codeValidation="4.4" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI El sello del Timbre Fiscal Digital es válido " codeValidation="4.7" /><ValidationItemResult validationResult="OK"
        descriptionValidation="CFDI El certificado con el que se generó el Timbre Fiscal Digital no debe ser un certificado de prueba " codeValidation="4.8" /><ValidationItemResult
        validationResult="OK" descriptionValidation="CFDI El certificado con el que se generó el Timbre Fiscal Digital fue emitido para un PAC " codeValidation="4.9" /><ValidationItemResult
        validationResult="OK" descriptionValidation="CFDI El sello CFD del timbre corresponde con el sello del comprobante " codeValidation="4.1" /><ValidationItemResult validationResult="OK"
        descriptionValidation="En cargar Recibidos: El RFC del comprobante Recibido corresponde con el RFC de la empresa " codeValidation="5.1" /></ValidationResult></Document>
        </Document><MetadataApp><SourceFile Value="'.$guid.'.xml" xmlns="" /></MetadataApp></Metadata>';
    }

    public function getFacturasAplicacionManual($id_empresa){
        $this->where([['id_empresa', '=', $id_empresa]]);
        return $this->all();
    }
}
