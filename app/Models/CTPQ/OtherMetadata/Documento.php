<?php


namespace App\Models\CTPQ\OtherMetadata;


use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $connection = 'cntpq';
    protected $table = 'dbo.Documento';
    public $timestamps = false;
    protected $fillable = [
        'GuidDocument',
        'TimeStamp',
        'EmisionDate',
        'Status',
        'IdTipoDocumento',
        'Type',
        'Path',
        'Hash',
        'MetadataEstatusApp',
        'UserResponsibleApp',
        'ReferenceApp',
        'NotesApp',
        'ProcessApp',
        'NoPaymentStatusapp',
        'ClaveDescripcion',
        'SourceFile',
        'Type_Otro',
        'Type_Ext',
        'Period',
        'Year',
        'TotalPayRoll',
        'SalaryType',
        'IsAsoContabilidad'
    ];
}
