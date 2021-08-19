<template>
    <div class="btn-group">
        <router-link :to="{ name: 'cotizacion-proveedor-show', params: {id: this.value.id_invitacion}}" v-if="value.show && (value.tipo_transaccion == 17)" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <router-link :to="{ name: 'presupuesto-proveedor-show', params: {id: this.value.id_invitacion}}" v-if="value.show && (value.tipo_transaccion == 49)" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <DescargaLayoutProveedor v-if="value.descarga_layout && (value.tipo_transaccion == 17)" v-bind:id="value.id_invitacion" v-bind:id_cotizacion="value.id_cotizacion" />
        <CargaLayoutProveedor v-if="value.carga_layout && (value.tipo_transaccion == 17)" v-on:back="layout" v-bind:id_invitacion="value.id_invitacion" v-bind:id_cotizacion="value.id_cotizacion" />
        <router-link :to="{ name: 'cotizacion-proveedor-edit', params: {id_invitacion: this.value.id_invitacion}}" v-if="value.edit && (value.tipo_transaccion == 17)" type="button" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <DeleteProveedor v-bind:id_invitacion="value.id_invitacion" v-if="value.delete && (value.tipo_transaccion == 17)"/>
        <router-link  :to="{ name: 'cotizacion-proveedor-documentos', params: {id: value.id_cotizacion, base_datos:value.invitacion.base_datos, id_obra:value.invitacion.id_obra}}" v-if="$root.can('consultar_cotizacion_proveedor',1)  && $root.can('consultar_archivos_transaccion',1)" type="button" class="btn btn-sm btn-outline-primary" title="Ver">
            <i class="fa fa-folder-open"></i>
        </router-link>
        <router-link :to="{ name: 'cotizacion-proveedor-send', params: {id_invitacion: this.value.id_invitacion}}" v-if="value.enviar" type="button" class="btn btn-sm btn-outline-success" title="Enviar">
            <i class="fa fa-send"></i>
        </router-link>
    </div>
</template>
<script>
    import DescargaLayoutProveedor from "../DescargaLayoutProveedor";
    import CargaLayoutProveedor from "../CargaLayoutProveedor";
    import DeleteProveedor from "../Delete";
    export default {
        name: "cotizacion-buttons",
        components: { DescargaLayoutProveedor, CargaLayoutProveedor, DeleteProveedor },
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
                    this.$router.push({ name: 'cotizacion-proveedor-edit', params: {id_invitacion: this.value.id_invitacion, xls: this.xls, id_cotizacion: this.value.id_cotizacion}});
                }
            }
        }
    }
</script>

<style scoped>

</style>
