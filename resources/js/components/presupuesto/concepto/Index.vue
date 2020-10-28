<template>

</template>

<script>
export default {
    name: "index-concepto",
    data(){
        return{
            conceptos_listados:[],
            cargando: false,
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        find() {
            this.cargando = true;
            return this.$store.dispatch('presupuesto/concepto/list', {
                nivel_padre: '',
                params: {include: []}
            }).then(data => {
            }).finally(()=> {
                this.cargando = false;
            })
        },
    },
    computed: {
        conceptos(){
            return this.$store.getters['presupuesto/concepto/conceptos'];
        },
    }
}
</script>

<style scoped>

</style>
