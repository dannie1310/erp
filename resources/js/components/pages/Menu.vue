<template>
    <!-- Sidebar Menu -->
    <span>
        <MenuCompras></MenuCompras>
        <MenuAlmacen></MenuAlmacen>
        <MenuAcarreos />
        <MenuContratos></MenuContratos>
        <MenuFinanzas></MenuFinanzas>
        <MenuContabilidad></MenuContabilidad>
        <MenuCatalogos></MenuCatalogos>
        <MenuFormatos></MenuFormatos>
    </span>

</template>

<script>
import MenuCompras from '../compras/partials/Menu';
import MenuContratos from '../contratos/partials/Menu';
import MenuFinanzas from '../finanzas/partials/Menu';
import MenuContabilidad from '../contabilidad/partials/Menu';
import MenuAlmacen from '../almacenes/partials/Menu';
import MenuCatalogos from '../catalogos/partials/Menu';
import MenuFormatos from '../formato/partials/Menu';
import MenuAcarreos from '../acarreos/partials/Menu';
    export default {
        name: "sao-menu",
        components:{MenuAlmacen, MenuCatalogos, MenuCompras, MenuContratos, MenuFinanzas, MenuContabilidad, MenuFormatos, MenuAcarreos},
        computed: {

            catalogo(){
                return this.$root.can([
                    'consultar_familia_material',
                    'consultar_familia_herramienta_equipo',
                    'consultar_insumo_material',
                    'consultar_insumo_herramienta_equipo'
                ]);
            },
            gestion_almacen() {
                return this.$root.can([
                    'consultar_entrada_almacen',
                    'consultar_salida_almacen'
                ])
            },
            gestion_orden_compra(){
                return this.$root.can([
                    'consultar_orden_compra'
                ])
            },
            gestion_solicitud(){
                return this.$root.can([
                    'consultar_solicitud_compra'
                ])
            },
            gestion_asignacion(){
                return this.$root.can([
                    'consultar_solicitud_compra'
                ])
            }
        },
        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }
    }
</script>

<style scoped>

</style>
