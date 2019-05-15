<?php


namespace App\Models\SEGURIDAD_ERP;


use Laravel\Passport\Passport;

class PersonalAccessClient extends \Laravel\Passport\PersonalAccessClient
{
    /**
     * The connection used by the model.
     *
     * @var string
     */
    protected $connection = 'seguridad';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'oauth_personal_access_clients';

    /**
     * The guarded attributes on the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all of the authentication codes for the client.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Passport::clientModel());
    }
}