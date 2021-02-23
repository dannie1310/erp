<template>
    <div class="btn-group">
        <button @click="autorizar()" type="button" class="btn btn-sm btn-outline-success" title="Autorizar / Rechazar" v-if="value.autorizar && value.estado ==0">
            <i class="fa fa-check"></i>
        </button>
        <button @click="aplicar()" type="button" class="btn btn-sm btn-outline-warning" title="Aplicar" v-if="value.aplicar && value.estado ==1">
            <i class="fa fa-arrow-alt-circle-down"></i>
        </button>
        <button @click="ver" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary" title="Ver"><i class="fa fa-eye"></i></button>
    </div>
</template>
<script>
    export default {
        name: "action-buttons",
        components: { },
        props: ['value'],
        data() {
            return {
            }
        },
        mounted() {
        },
        methods: {
            destroy() {
            },
            autorizar() {
                this.$router.push({name: 'solicitud-edicion-poliza-autorizar', params: {id: this.value.id}});
            },
            aplicar() {
                this.$router.push({name: 'solicitud-edicion-poliza-aplicar', params: {id: this.value.id}});
            },
            ver() {
                this.$router.push({name: 'solicitud-edicion-poliza-show', params: {id: this.value.id}});
            },
        }
    }
</script>
