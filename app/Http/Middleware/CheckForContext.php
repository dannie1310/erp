<?php

namespace App\Http\Middleware;

use App\Contracts\Context;
use App\Models\CADECO\Obra;
use Closure;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CheckForContext
{
    /**
     * @var  ConfigRepository
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    /**
     * CheckForContext constructor.
     * @param ConfigRepository $config
     * @param Context $context
     */
    public function __construct(ConfigRepository $config, Context $context)
    {
        $this->config = $config;
        $this->context = $context;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->ajax()) {
            if(request()->header('db') && request()->header('idobra')) {
                session()->put('db', request()->header('db'));
                session()->put('id_obra', request()->header('idobra'));
            }
        }

        if (! $this->context->isEstablished()) {
            if (request()->get('db') && request()->get('idobra')) {
                session()->put('db', request()->get('db'));
                session()->put('id_obra', request()->get('idobra'));
            } else {
                throw new BadRequestHttpException('Sin Contexto Establecido');
            }
        }

        $this->setContext();
        return $next($request);
    }

    /**
     * Sets the datbase connection's database name for the current context
     */
    private function setContext()
    {
        $this->config->set('database.connections.cadeco.database', $this->context->getDatabase());
        $obra = Obra::query()->find($this->context->getIdObra());

        if ( $obra->datosContables) {
            $this->context->setContext($this->context->getDatabase()
                    , $this->context->getIdObra()
                    , ($obra->datosContables->BDContPaq) ? $obra->datosContables->BDContPaq : ''
            );
        }

        $this->config->set('database.connections.cntpq.database', $this->context->getDatabaseContpaq());
    }
}
