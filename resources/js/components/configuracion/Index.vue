<template>
    <div class="row">
        <div class="col-lg-10 offset-lg-1" v-if="obra">
           <configuracion-obra :obra="obra"></configuracion-obra>
           <estado-obra :obra="obra"></estado-obra>
            <configuracion-sistema  v-if="$root.can('habilitar_deshabilitar_sistema')"></configuracion-sistema>
            <configuracion-contable @update:datosContables="obra.datosContables = $event" :datos-contables="obra.datosContables"></configuracion-contable>
            <!-- ESTE COMPONENTE CONTIENE LAS ASIGNACIONES PARA EL ESQUEMA GLOBAL, PARA EL ESQUEMA PERSONALIZADO SE DEBERÁ CREAR EL CORRESPONDIENTE COMPONENTE -->
            <configuracion-seguridad v-if="obra.configuracion.esquema_permisos == 1"></configuracion-seguridad>
            <configuracion-seguridad-personalizado v-else-if="obra.configuracion.esquema_permisos == 2"></configuracion-seguridad-personalizado>
        </div>
    </div>
</template>

<script>
    import ConfiguracionObra from "./partials/Obra";
    import EstadoObra from "./partials/EstadoObra";
    import ConfiguracionContable from "./partials/Contable";
    import ConfiguracionSeguridad from "./seguridad/global/Index";
    import ConfiguracionSeguridadPersonalizado from "./seguridad/personalizado/Index";
    import ConfiguracionSistema from "./partials/Sistema";
    export default {
        name: "configuracion",
        components: {ConfiguracionSeguridad, ConfiguracionContable, ConfiguracionObra, ConfiguracionSeguridadPersonalizado, ConfiguracionSistema, EstadoObra},
        data() {
            return {
                obra: null
            }
        },

        mounted() {
            this.cargando = true;
            Promise.all([this.getObra(), this.getMonedas()])
                .then(data => {
                    this.obra = data[0];
                    this.$store.commit('cadeco/moneda/SET_MONEDAS', data[1].data)
                })
                .finally(() => {
                    this.cargando = false;
                });
        },

        methods: {
            getMonedas() {
                return this.$store.dispatch('cadeco/moneda/index');
            },
            getObra() {
                return this.$store.dispatch('cadeco/obras/find', {
                    id: this.currentObra.id_obra,
                    params: { include: ['configuracion', 'datosContables'], 'logo' : true }
                })
            },
        },

        computed: {
            currentObra() {
                return this.$store.getters['auth/currentObra']
            }
        }
    }
</script>