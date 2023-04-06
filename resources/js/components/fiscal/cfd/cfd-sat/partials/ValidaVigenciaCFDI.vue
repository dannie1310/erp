<template>
    <span>
        <button @click="valida()" type="button" class="btn btn-sm btn-outline-primary" :disabled="cargando||cargandoEstado" title="Valida Vigencia">
            <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
            <i class="fa fa-check" v-else></i>
        </button>
    </span>
</template>

<script>
    export default {
        name: "validarVigenciaCFDI",
        props: ['id'],
        data(){
            return{
                cargando: false,
            }
        },
        computed: {
            cargandoEstado(){
                return this.$store.getters['fiscal/cfd-sat/currentEstado'];
            }
        },
        methods: {
            valida() {
                this.cargando = true;
                return this.$store.dispatch('fiscal/cfd-sat/validaVigencia',
                    {
                        id: this.id,
                    })
                .then(() => {
                    this.$emit('success')
                    this.cargando = false;
                })
            }
        },
    }
</script>

<style scoped>

</style>
