<?php

namespace App\Http\Middleware;

use App\Contracts\Context;
use Closure;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

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
        if (! $this->context->isEstablished()) {
            // TODO: Response si el contexto no está establecido
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
    }
}
