<template>
    <div class="row">
        <div class="col-lg" v-if="obra">
            <configuracion-obra :obra="obra"></configuracion-obra>
        </div>
        <div class="col-lg" v-if="obra">
            <configuracion-contable @update:datosContables="obra.datosContables = $event" :datos-contables="obra.datosContables"></configuracion-contable>
        </div>
    </div>
</template>

<script>
    import ConfiguracionObra from "./partials/Obra";
    import ConfiguracionContable from "./partials/Contable";
    export default {
        name: "configuracion",
        components: {ConfiguracionContable, ConfiguracionObra},
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
                    params: { include: ['configuracion', 'datosContables'] }
                })
            }
        },

        computed: {
            currentObra() {
                return this.$store.getters['auth/currentObra']
            }
        }
    }
</script>