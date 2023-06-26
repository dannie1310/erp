<?php
namespace App\Models\CTPQ\OtherContent;

use Illuminate\Database\Eloquent\Model;
class DocumentContent extends Model
{
    protected $connection = 'cntpqoc';
    protected $table = 'dbo.DocumentContent';

    public $timestamps = false;

    protected $fillable = [
        'DocumentType',
        'FileName',
        'Content',
        'SubDirectory',
        'DocumentDate',
        'CreationDate'
    ];

    /**
     * Relaciones
     */


    /**
     * Scopes
     */

}
