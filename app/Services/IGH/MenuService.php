<?php

namespace App\Services\IGH;

use App\Models\IGH\Menu;
use App\Models\SEGURIDAD_ERP\Sistema;
use App\Repositories\Repository;

class MenuService
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Menu constructor.
     * @param Menu $model
     */
    public function __construct(Menu $model)
    {
        $this->repository = new Repository($model);
    }

    public function index($data)
    {
        $menu_arr = [];
        $sistema = new Sistema();
        $aplicaciones = $sistema->aplicaciones()->get();
        foreach ($aplicaciones as $item) {
            $menu_arr [] = [
                'color' => $item->color,
                'icon' => $item->icon,
                'menu' => ($item->menu)?$item->menu:$item->name,
                'ruta' => ($item->ruta)?$item->ruta:$item->url,
                'target' => $item->target,
                'manual' => (string) $item->url_manual
            ];
        }

        $menu = $this->repository->all();

        foreach ($menu as $item) {
            $menu_arr [] = [
                'color' => $item->color,
                'icon' => $item->icon,
                'menu' => ($item->menu)?$item->menu:$item->name,
                'ruta' => ($item->ruta)?$item->ruta:$item->url,
                'target' => $item->target,
                'manual' => (string) $item->url_manual
            ];
        }
        //dd("merge",$aplicaciones->merge($menu), "APLICACIONESS ",$aplicaciones,"MENNUUUU", $menu);
        return $menu_arr;
    }
}
