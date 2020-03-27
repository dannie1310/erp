<template>
    <div class="row">
        <div class="col-lg-10 offset-lg-1" v-if="obra">
           <configuracion-obra :obra="obra" v-bind:monedas="monedas"  v-bind:tipo="0"></configuracion-obra>
           <estado-obra :obra="obra" v-bind:tipo="0"></estado-obra>
            <configuracion-sistema  v-if="$root.can('habilitar_deshabilitar_sistema')"></configuracion-sistema>
            <configuracion-contable @update:datosContables="obra.datosContables = $event" :datos-contables="obra.datosContables"></configuracion-contable>
            <configuracion-conceptos  :datos-concepto="obra"></configuracion-conceptos>
            <configuracion-estimaciones @create:datosEstimaciones="obra.datosEstimaciones = $event" :datos-estimaciones="obra.datosEstimaciones"></configuracion-estimaciones>
            <!-- ESTE COMPONENTE CONTIENE LAS ASIGNACIONES PARA EL ESQUEMA GLOBAL, PARA EL ESQUEMA PERSONALIZADO SE DEBERÁ CREAR EL CORRESPONDIENTE COMPONENTE -->
            <configuracion-seguridad v-if="obra.configuracion.esquema_permisos == 1"></configuracion-seguridad>
            <configuracion-seguridad-personalizado v-else-if="obra.configuracion.esquema_permisos == 2"></configuracion-seguridad-personalizado>
        </div>
    </div>
</template>

<script>
    import ConfiguracionContable from "./partials/Contable";
    import ConfiguracionConceptos from "./partials/Conceptos";
    import ConfiguracionObra from "./partials/Obra";
    import ConfiguracionEstimaciones from "./partials/Estimaciones";
    import ConfiguracionSeguridad from "./seguridad/global/Index";
    import ConfiguracionSeguridadPersonalizado from "./seguridad/personalizado/Index";
    import ConfiguracionSistema from "./partials/Sistema";
    import EstadoObra from "./partials/EstadoObra";

    export default {
        name: "configuracion",
        components: {ConfiguracionSeguridad, ConfiguracionContable, ConfiguracionObra, ConfiguracionSeguridadPersonalizado, ConfiguracionSistema, EstadoObra, ConfiguracionEstimaciones, ConfiguracionConceptos},
        data() {
            return {
                obra: null,
                monedas: []
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
            getObra() {
                return this.$store.dispatch('cadeco/obras/find', {
                    id: this.currentObra.id_obra,
                    params: { include: ['configuracion', 'datosContables','datosEstimaciones'], 'logo' : true }
                })
            },
            getMonedas() {
                return this.$store.dispatch('cadeco/moneda/index')
                    .then(data => {
                        this.monedas = data.data;
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
