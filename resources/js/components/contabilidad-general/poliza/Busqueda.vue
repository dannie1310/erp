<template>
    <span>
         <div class="row">
             <div class="col-md-12">
                 <div class="form-group row error-content">
                     <label for="id_empresa" class="col-md-2 col-form-label">Empresa:</label>
                     <div class="col-md-10">
                         <model-list-select
                                 :disabled="cargando"
                                 :onchange="changeSelect()"
                                 name="id_empresa"
                                 v-model="id_empresa"
                                 option-value="id"
                                 option-text="descripcion"
                                 :list="empresas"
                                 :placeholder="!cargando?'Seleccionar o buscar empresa':'Cargando...'"
                                 :isError="errors.has(`id_empresa`)">
                         </model-list-select>
                     </div>
                 </div>
             </div>
         </div>
    </span>
</template>

<script>
    import {ModelListSelect} from 'vue-search-select';
    export default {
        name: "busqueda-poliza",
        components: {ModelListSelect},
        data() {
            return {
                cargando: false,
                id_empresa: '',
                empresas: [],
                empresa_seleccionada: []
            }
        },
        mounted(){
            this.getEmpresas();
        },
        methods: {
            changeSelect(){
                var busqueda = this.empresas.find(x=>x.id === this.id_empresa);
                if(busqueda != undefined)
                {
                    this.empresa_seleccionada = busqueda;
                }
            },
            getEmpresas() {
                this.empresas = [];
                this.cargando = true;
                return this.$store.dispatch('contabilidadGeneral/empresa/index', {
                    params: {
                        sort: 'Nombre',
                        order: 'asc'
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