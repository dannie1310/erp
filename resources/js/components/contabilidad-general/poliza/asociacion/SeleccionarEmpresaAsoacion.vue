<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <h6><i class="fa fa-plug" ></i>Datos de Conexión:</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <div class="form-group row error-content">
                    <label for="id_empresa" class="col-md-2 col-form-label">Empresa:</label>
                    <div class="col-md-10">
                        <model-list-select
                            :disabled="cargando"
                            :onchange="changeSelect()"
                            name="id_empresa"
                            v-model="id_empresa"
                            option-value="id"
                            option-text="nombre"
                            :custom-text="nombreAliasBDD"
                            :list="empresas"
                            :placeholder="!cargando?'Seleccionar o buscar empresa':'Cargando...'"
                            :isError="errors.has(`id_empresa`)">
                        </model-list-select>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <button @click="conectar" class="btn btn-primary float-right">
                    <i class="fa fa-plug"></i> Conectar
                </button>
            </div>
        </div>
    </span>

</template>

<script>

import {ModelListSelect} from 'vue-search-select';
export default {
    name: "SeleccionarEmpresaAsoacion",
    components: {ModelListSelect},
    data() {
        return {
            procesando:false,
            cargando: false,
            conectando:false,
            conectado:false,
            buscando:false,
            encontradas:false,
            id_empresa: '',
            empresas: [],
            empresa_seleccionada: [],
            nombre: ''
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

        nombreAliasBDD (item) {
            return `${item.nombre} - ${item.alias_bdd}`
        },

        conectar(){
            this.conectando = true;
            return this.$store.dispatch('contabilidadGeneral/empresa-contpaq/conectar',
                {
                    data: {id: this.id_empresa},
                    config: {
                        params: { _method: 'POST'}
                    }
                })
                .then(data => {
                    if(this.empresa_seleccionada.alias_bdd === data){
                        this.conectado = true;
                        this.$router.push({name: 'poliza-contpaq-asociacion', params: {id_empresa: this.id_empresa}});
                    }
                }).finally(() => {
                    this.conectando = false;
                });
        },
        getEmpresas() {
            this.empresas = [];
            this.cargando = true;
            return this.$store.dispatch('contabilidadGeneral/empresa/index', {
                params: {
                    sort: 'Nombre',
                    order: 'asc',
                    scope:'editablePorUsuario',
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
