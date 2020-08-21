<template>
    <span>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-secondary pull-right" :disabled="cargando" v-on:click="descargarZip">
                    <span v-if="cargando">
                        <i class="fa fa-spin fa-spinner"></i>
                    </span>
                    <span v-else>
                        <i class="fa fa-chevron-circle-down"></i> Descargar Expediente
                    </span>
                </button>
            </div>
        </div>
        <br />
        <nav>
            <div class="card" v-if="empresa">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Razón Social: </label>
                        <div class="col-md-6 col-form-label">
                            {{empresa.razon_social}}
                        </div>
                        <label class="col-md-1 col-form-label">RFC: </label>
                        <div class="col-md-3 col-form-label">
                            {{empresa.rfc}}
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <nav v-if="empresa">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a aria-controls="nav-datos" aria-selected="true" class="nav-item nav-link active" data-toggle="tab" href="#nav-datos"
                   id="nav-datos-tab" role="tab">Datos Proveedor</a>
                <a aria-controls="nav-documentacion" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-documentacion"
                    id="nav-documentacion-tab" role="tab">Documentación General</a>
                <a v-if="prestadora" aria-controls="nav-prestadora" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-prestadora"
                   id="nav-prestadora-tab" role="tab">Datos Prestadora de Servicios</a>
                <a v-if="prestadora" aria-controls="nav-documentacion-prestadora" aria-selected="false" class="nav-item nav-link" data-toggle="tab" href="#nav-documentacion-prestadora"
                   id="nav-documentacion-prestadora-tab" role="tab">Documentación de Prestadora de Servicios</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent" v-if="empresa">
            <div aria-labelledby="nav-datos-tab" class="tab-pane fade show active" id="nav-datos" role="tabpanel">
                <DatosGenerales v-bind:id="id"></DatosGenerales>
            </div>
            <div aria-labelledby="nav-documentacion-tab" class="tab-pane fade" id="nav-documentacion" role="tabpanel">
                <TabDocumentacion v-bind:id="id"></TabDocumentacion>
            </div>
              <div v-if="prestadora" aria-labelledby="nav-prestadora-tab" class="tab-pane fade" id="nav-prestadora" role="tabpanel">
                <DatosPrestadora v-bind:prestadora="empresa.prestadora"></DatosPrestadora>
            </div>
            <div v-if="prestadora" aria-labelledby="nav-documentacion-prestadora-tab" class="tab-pane fade" id="nav-documentacion-prestadora" role="tabpanel">
                <TabDocumentacionPrestadora v-bind:id_empresa="empresa.id" v-bind:id_prestadora="empresa.prestadora.id"></TabDocumentacionPrestadora>
            </div>
        </div>
    </span>
</template>

<script>
    import DatosGenerales from "./EditTabs/TabDatos";
    import TabDocumentacion from './EditTabs/TabDocumentacion';
    import DatosPrestadora from './EditTabs/TabDatosPrestadora';
    import TabDocumentacionPrestadora from './EditTabs/TabDocumentacionPrestadora';

    export default {
        name: "entrar-a-expediente",
        components: {DatosGenerales, DatosPrestadora,TabDocumentacion, TabDocumentacionPrestadora},
        props: ['id'],
        data() {
            return {
                cargando: false,
                prestadora : false
            }
        },
        mounted(){
            this.find();
        },
        methods: {
            find() {
                this.cargando = true;
                this.$store.commit('padronProveedores/empresa/SET_EMPRESA', null);
                this.$store.commit('padronProveedores/archivo/SET_ARCHIVOS', null);
                return this.$store.dispatch('padronProveedores/empresa/find', {
                    id: this.id,
                    params: {include: ['prestadora', 'archivos']}
                }).then(data => {
                    // if(data.prestadora ){
                    //     this.prestadora = true;
                    //     this.$store.commit('padronProveedores/archivo-prestadora/SET_ARCHIVOS', data.prestadora.archivos.data);
                    // }
                    this.prestadora = data.prestadora ? true : false;
                    this.$store.commit('padronProveedores/empresa/SET_EMPRESA', data);
                    this.$store.commit('padronProveedores/archivo/SET_ARCHIVOS', data.archivos.data);
                    this.cargando = false;
                })
            },
            descargarZip() {
                return this.$store.dispatch('padronProveedores/empresa/descargaExpediente', {id: this.id})
                    .then(() => {
                        this.$emit('success')
                    })

            }
        },
        computed: {
            empresa() {
                return this.$store.getters['padronProveedores/empresa/currentEmpresa'];
            }
        },
    }
</script>

<style>

</style>
