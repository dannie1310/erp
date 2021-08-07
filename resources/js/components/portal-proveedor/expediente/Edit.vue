<template>
    <span>
        <div class="row" v-if="!empresa">
            <div class="col-md-12">
                <div class="spinner-border text-success" role="status">
                   <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-md-12">
                <button v-if="empresa.archivos_cargados>0" type="button" class="btn btn-secondary pull-right" :disabled="cargando" v-on:click="descargarZip">
                    <span v-if="cargando">
                        <i class="fa fa-spin fa-spinner"></i>
                    </span>
                    <span v-else>
                        <i class="fa fa-download"></i> Descargar Expediente
                    </span>
                </button>
            </div>
        </div>
        <br />
        <nav>
            <div class="card" v-if="empresa">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label  class="col-form-label">Tipo de Empresa:</label>
                            <div>
                                <i :class="empresa.tipo.id==1 ?'fa fa-boxes':'fa fa-building'"></i>{{empresa.tipo.descripcion}}
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label  class="col-form-label">Nombre / Razón Social:</label>
                            <div>
                                {{empresa.razon_social}}
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label  class="col-form-label">RFC:</label>
                            <div>
                                {{empresa.rfc}}
                            </div>
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
    import DatosGenerales from "../../padron-proveedores/gestion-proveedores/EditTabs/TabDatos";
    import TabDocumentacion from '../../padron-proveedores/gestion-proveedores/EditTabs/TabDocumentacion';
    import DatosPrestadora from '../../padron-proveedores/gestion-proveedores/EditTabs/TabDatosPrestadora';
    import TabDocumentacionPrestadora from '../../padron-proveedores/gestion-proveedores/EditTabs/TabDocumentacionPrestadora';

    export default {
        name: "expediente-proveedor",
        components: {DatosGenerales, DatosPrestadora,TabDocumentacion, TabDocumentacionPrestadora},
        data() {
            return {
                cargando: false,
                prestadora : false,
                id:'',
            }
        },
        mounted(){
            this.find();
        },
        methods: {
            find() {
                let usuario  = this.$store.getters['auth/currentUser'];

                this.cargando = true;
                this.$store.commit('padronProveedores/empresa/SET_EMPRESA', null);
                this.$store.commit('padronProveedores/archivo/SET_ARCHIVOS', null);
                return this.$store.dispatch('padronProveedores/empresa/findRFC', {
                    rfc: usuario.usuario,
                    params: {include: ['prestadora', 'archivos.integrantes','tipo']}
                }).then(data => {
                    this.id = data.id;
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
