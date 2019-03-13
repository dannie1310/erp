<?php
/**
 * Created by PhpStorm.
 * User: jfesquivel
 * Date: 27/11/18
 * Time: 01:05 PM
 */

namespace App\Services;


use App\Contracts\Context;
use App\Models\CADECO\Obra;
use App\Models\CADECO\Usuario;
use App\Models\SEGURIDAD_ERP\Proyecto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AuthService
{
    /**
     * @var Context
     */
    private $context;

    /**
     * AuthService constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function setContext(array $data) {
        return $this->context->setContext($data['database'], $data['id_obra']);
    }

    public function login(array $credentials) {
        try {
            if(! $token = auth()->attempt($credentials)) {
                throw new UnprocessableEntityHttpException('Unauthorized');
            }
            return $token;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getObras() {

        $obrasUsuario = new Collection();
        $basesDatos = Proyecto::query()->orderBy('base_datos')->pluck('base_datos');

        foreach ($basesDatos as $key => $bd) {
            config()->set('database.connections.cadeco.database', $bd);
            $usuarioCadeco = $this->getUsuarioCadeco(auth()->user());
            $obras = $this->getObrasUsuario($usuarioCadeco);
            foreach ($obras as $obra) {
                $obra->base_datos = $bd;
                $obrasUsuario->push($obra);
            }
            DB::disconnect('cadeco');
        }

        return $obrasUsuario;
    }

    /**
     * Obtiene el usuario cadeco asociado al usuario de intranet
     *
     * @param $idUsuario
     * @return UsuarioCadeco
     */
    public function getUsuarioCadeco($usuario)
    {
        return Usuario::where('usuario', $usuario->usuario)->first();
    }

    /**
     * Obtiene las obras de un usuario cadeco
     *
     * @param UsuarioCadeco $usuarioCadeco
     * @return \Illuminate\Database\Eloquent\Collection|Obra
     */
    private function getObrasUsuario($usuarioCadeco)
    {
        if (! $usuarioCadeco) {
            return [];
        }
        if ($usuarioCadeco->tieneAccesoATodasLasObras()) {
            return Obra::orderBy('nombre')->where(function($query) {
                foreach ((new Obra())->searchable as $col)
                {
                    $explode = explode('.', $col);

                    if (isset($explode[1])) {
                        $query->orWhereHas($explode[0], function ($q) use ($explode) {
                            return $q->where($explode[1], 'LIKE', '%' . request('search') . '%');
                        });
                    } else {
                        $query->orWhere($col, 'LIKE', '%' . request('search') . '%');
                    }
                }
            })->get();
        }
        return $usuarioCadeco->obras()->orderBy('nombre')->where(function($query) {
            foreach ((new Obra())->searchable as $col)
            {
                $explode = explode('.', $col);

                if (isset($explode[1])) {
                    $query->orWhereHas($explode[0], function ($q) use ($explode) {
                        return $q->where($explode[1], 'LIKE', '%' . request('search') . '%');
                    });
                } else {
                    $query->orWhere($col, 'LIKE', '%' . request('search') . '%');
                }
            }
        })->get();
    }
}