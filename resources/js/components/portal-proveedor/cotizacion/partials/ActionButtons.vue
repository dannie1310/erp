<template>
    <span>
        <div class="btn-group" v-if="value.tipo_transaccion == 17">
            <router-link :to="{ name: 'cotizacion-proveedor-show', params: {id: this.value.id_invitacion}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
                <i class="fa fa-eye"></i>
            </router-link>
            <DescargaLayoutProveedor v-if="value.descarga_layout" v-bind:id="value.id_invitacion" v-bind:id_cotizacion="value.id_cotizacion" />
            <CargaLayoutProveedor v-if="value.carga_layout" v-on:back="layout" v-bind:id="value.id_invitacion" v-bind:id_cotizacion="value.id_cotizacion" />
            <router-link :to="{ name: 'cotizacion-proveedor-edit', params: {id: this.value.id_invitacion}}" v-if="value.edit " type="button" class="btn btn-sm btn-outline-primary" title="Editar">
                <i class="fa fa-pencil"></i>
            </router-link>
            <DeleteProveedor v-bind:id_invitacion="value.id_invitacion" v-if="value.delete"/>
            <router-link  :to="{ name: 'cotizacion-proveedor-documentos', params: {id: value.id_cotizacion, base_datos:value.invitacion.base_datos, id_obra:value.invitacion.id_obra}}" v-if="$root.can('consultar_cotizacion_proveedor',1)  && $root.can('consultar_archivos_transaccion',1)" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
                <i class="fa fa-folder-open"></i>
            </router-link>
            <router-link :to="{ name: 'cotizacion-proveedor-send', params: {id_invitacion: this.value.id_invitacion}}" v-if="value.enviar" type="button" class="btn btn-sm btn-outline-success" title="Enviar">
                <i class="fa fa-send"></i>
            </router-link>
        </div>
        <div  class="btn-group" v-if="value.tipo_transaccion == 49">
            <router-link :to="{ name: 'presupuesto-proveedor-show', params: {id: this.value.id_invitacion}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
                <i class="fa fa-eye"></i>
            </router-link>
            <DescargaLayoutPresupuestoProveedor v-if="value.descarga_layout" v-bind:id="value.id_invitacion" v-bind:id_presupuesto="value.id_cotizacion" />
            <CargaLayoutPresupuestoProveedor v-if="value.carga_layout " v-on:back="layoutPresupuesto" v-bind:id="value.id_invitacion" v-bind:id_presupuesto="value.id_cotizacion" />
            <router-link :to="{ name: 'presupuesto-proveedor-edit', params: {id: this.value.id_invitacion}}" v-if="value.edit " type="button" class="btn btn-sm btn-outline-primary" title="Editar">
                <i class="fa fa-pencil"></i>
            </router-link>
            <router-link :to="{ name: 'presupuesto-proveedor-delete', params: {id: this.value.id_invitacion}}" v-if="value.delete " type="button" class="btn btn-sm btn-outline-danger" title="Eliminar">
                <i class="fa fa-trash"></i>
            </router-link>
            <router-link  :to="{ name: 'cotizacion-proveedor-documentos', params: {id: value.id_cotizacion, base_datos:value.invitacion.base_datos, id_obra:value.invitacion.id_obra}}" v-if="$root.can('consultar_cotizacion_proveedor',1)  && $root.can('consultar_archivos_transaccion',1)" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
                <i class="fa fa-folder-open"></i>
            </router-link>
            <router-link :to="{ name: 'presupuesto-proveedor-send', params: {id: this.value.id_invitacion}}" v-if="value.enviar" type="button" class="btn btn-sm btn-outline-success" title="Enviar">
                <i class="fa fa-send"></i>
            </router-link>
        </div>
    </span>
</template>
<script>
    import DescargaLayoutProveedor from "../DescargaLayoutProveedor";
    import CargaLayoutProveedor from "../CargaLayoutProveedor";
    import DeleteProveedor from "../Delete";
    import DescargaLayoutPresupuestoProveedor from "../../presupuesto/DescargaLayoutPresupuestoProveedor";
    import CargaLayoutPresupuestoProveedor from "../../presupuesto/CargaLayoutPresupuestoProveedor";
    export default {
        name: "cotizacion-buttons",
        components: { DescargaLayoutProveedor, CargaLayoutProveedor, DeleteProveedor, DescargaLayoutPresupuestoProveedor, CargaLayoutPresupuestoProveedor },
        props: ['value'],
        data()
        {
            return {
                xls: null
            }
        },
        methods: {
            layout(dat)
            {
                this.xls = (dat) ? dat : null;
                if(this.xls)
                {
                    this.$router.push({ name: 'cotizacion-proveedor-edit', params: {id: this.value.id_invitacion, xls: this.xls, id_cotizacion: this.value.id_cotizacion}});
                }
            },
            layoutPresupuesto(dat)
            {
                this.xls = (dat) ? dat : null;
                if(this.xls)
                {
                    this.$router.push({ name: 'presupuesto-proveedor-edit', params: {id: this.value.id_invitacion, xls: this.xls}});
                }
            }
        }
    }
</script>

<style scoped>

</style>
