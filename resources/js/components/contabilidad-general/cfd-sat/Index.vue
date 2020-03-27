<template>
    <span>
        <RegistroMasivo/>
        <ProcesaDirectorio/>
    </span>
</template>

<script>
    import RegistroMasivo from './RegistroMasivo'
    import ProcesaDirectorio from './ProcesaDirectorio'

    export default {
        name: "cfd-sat-index",
        components:{RegistroMasivo,ProcesaDirectorio},

        data() {
            return {
                cargando: false,
                id_empresa: '',
                empresas: [],
                empresa_seleccionada: [],
            }
        },
        mounted(){
            this.getEmpresas();
        },


        methods: {
            changeSelect(){
                this.conectando = false;
                var busqueda = this.empresas.find(x=>x.id === this.id_empresa);
                if(busqueda != undefined)
                {
                    this.empresa_seleccionada = busqueda;
                }
            },

            rfcAndRazonSocial (item){
                return `[${item.rfc}] - ${item.razon_social}`
            },

            getEmpresas() {
                this.empresas = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa-sat/index', {
                    params: {
                        sort: 'razon_social',
                        order: 'asc',
                    }
                })
                    .then(data => {
                        this.empresas = data.data;
                        this.cargando = false;
                    })
            },
        }
    }
</script>

<style scoped>

</style>