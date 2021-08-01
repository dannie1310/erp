<template>
    <div class="btn-group">
        <router-link :to="{ name: 'cotizacion-proveedor-show', params: {id: this.value.id_invitacion}}" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Consultar">
            <i class="fa fa-eye"></i>
        </router-link>
        <DescargaLayoutProveedor v-if="value.descarga_layout" v-bind:id="value.id_invitacion" v-bind:id_cotizacion="value.id_cotizacion" />
        <CargaLayoutProveedor v-if="value.carga_layout" v-on:back="layout" v-bind:id_invitacion="value.id_invitacion" v-bind:id_cotizacion="value.id_cotizacion" />
        <router-link :to="{ name: 'cotizacion-proveedor-edit', params: {id_invitacion: this.value.id_invitacion}}" v-if="value.edit" type="button" class="btn btn-sm btn-outline-primary" title="Editar">
            <i class="fa fa-pencil"></i>
        </router-link>
        <router-link :to="{ name: 'cotizacion-proveedor-send', params: {id_invitacion: this.value.id_invitacion}}" v-if="value.enviar" type="button" class="btn btn-sm btn-outline-success" title="Enviar">
            <i class="fa fa-send"></i>
        </router-link>
    </div>
</template>
<script>
    import DescargaLayoutProveedor from "../DescargaLayoutProveedor";
    import CargaLayoutProveedor from "../CargaLayoutProveedor";
    export default {
        name: "cotizacion-buttons",
        components: { DescargaLayoutProveedor, CargaLayoutProveedor },
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
