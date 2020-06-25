<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <button @click="buscar" class="btn btn-warning float-right" v-if="empresas_factureras.length>0" :disabled="cargando">
                    <i class="fa fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-search" v-else></i>Buscar Coincidencias
                </button>
                <button @click="regresar" class="btn btn-warning float-right" v-if="resultados_coincidencias.length>0" :disabled="cargando">
                    <i class="fa fa-spin fa-spinner" v-if="cargando"></i>
                    <i class="fa fa-angle-left" v-else></i> Regresar
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <span v-if="empresas_factureras.length>0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="index_corto">#</th>
                                <th >Razón Social</th>
                                <th>Palabras Clave</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(empresa_facturera,i) in empresas_factureras">
                                <td >{{i+1}}</td>
                                <td>{{empresa_facturera.razon_social}}</td>
                                <td>
                                    <textarea
                                            type="text"
                                            v-validate="{required: true, max:2000}"
                                            :name="`palabras_clave_[${empresa_facturera.id}]`"
                                            class="form-control"
                                            :id="`palabras_clave_[${empresa_facturera.id}]`"
                                            v-model="empresa_facturera.palabras_clave"
                                            placeholder="Palabras Clave de Búsqueda"
                                    ></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </span>
                <span v-if="resultados_coincidencias.length>0">
                    <table class="table table-striped">
                        <tbody>
                        <template v-for="(resultados_coincidencia,facturera) in resultados_coincidencias[0]">
                            <tr style="background-color: #000; color: #FFF">
                                <td>{{facturera}}</td>
                            </tr>
                            <template v-for="(palabras_clave, palabra_clave) in resultados_coincidencia">
                                <tr style="background-color: #555555; color: #FFF">
                                    <td>{{palabra_clave}}</td>
                                </tr>
                                <template v-for="(bases_busqueda, base_busqueda) in palabras_clave">
                                    <tr style="background-color: #AAAAAA">
                                        <td>{{base_busqueda}}</td>
                                    </tr>
                                    <tr v-for="(coincidencia,i) in bases_busqueda">
                                        <td>{{i+1}}-{{coincidencia}}</td>
                                    </tr>
                                </template>
                            </template>

                        </template>
                        </tbody>
                    </table>
                </span>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "Busqueda",
        data() {
            return {
                cargando: false,
                empresas_factureras: [],
                resultados_coincidencias :[],
                id_empresa_facturera:''
            }
        },
        mounted() {
            this.index();

        },
        methods:{
            index() {
                this.cargando = true;
                return this.$store.dispatch('controlInterno/empresa-facturera/index', {
                    params: {

                    }
                })
                    .then(data => {
                        this.empresas_factureras = data.data
                    })
                    .finally(() => {
                        this.cargando = false;
                    });
            },
            buscar() {
                this.cargando = true;
                return this.$store.dispatch('controlInterno/empresa-facturera/buscar', {
                    empresas_factureras: this.empresas_factureras
                })
                .then(data => {
                    this.resultados_coincidencias = data;
                    this.empresas_factureras = [];
                })
                .finally(() => {
                    this.cargando = false;
                });
            },
            regresar() {
                this.resultados_coincidencias = [];
                this.index();

            },
        }
    }
</script>

<style scoped>

</style>