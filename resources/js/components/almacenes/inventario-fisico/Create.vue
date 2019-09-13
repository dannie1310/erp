<template>
    <span>
        <button @click="store" v-if="$root.can('iniciar_inventario_fisico')" class="btn btn-app btn-info pull-right" :disabled="cargando">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-plus" v-else></i>
            Iniciar Inventario Físico
        </button>
    </span>
</template>

<script>
    export default {
        name: "inventario-fisico-create",
        data() {
            return {
                cargando: false,
                dato:''
            }
        },
        mounted(){
        },
        methods:{
            store() {
                return this.$store.dispatch('almacenes/inventario-fisico/store', this.dato)
                    .then(data => {
                        this.$emit('created', data);
                    }).finally( ()=>{
                        this.cargando = false;
                    });
            },
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
</style>
