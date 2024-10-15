<template>
    <span>
        <div class="row" v-if="documentos">
            <div class="col-md-12">
               <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                        <tr>
                            <th class="index_corto encabezado">#</th>
                            <th class="c100 encabezado">Segmento Negocio</th>
                            <th class="c80 encabezado">Tipo Gasto</th>
                            <th class="c80 encabezado">Importe</th>
                            <th class="c100 encabezado">IVA</th>
                            <th class="c100 encabezado">Retenciones</th>
                            <th class="c100 encabezado">Otros Impuestos</th>
                            <th class="c100 encabezado">Total</th>
                            <th class="c30 encabezado">F</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(d, i) in documentos.data" v-on:click="abrirModal(d)">
                                <td style="text-align:center; vertical-align:inherit;" >{{i+1}}</td>
                                <td>{{ d.centro_costo }}</td>
                                <td>{{ d.tipoGastoComp.tipoGasto.descripcion }}</td>
                                <td style="text-align:right;">{{ d.importe_format }}</td>
                                <td style="text-align:right;">{{ d.iva_format }}</td>
                                <td style="text-align:right;">{{ d.retenciones_format }}</td>
                                <td style="text-align:right;">{{ d.otros_imp_format }}</td>
                                <td style="text-align:right;">{{ d.total_format }}</td>
                                <td>{{d.facturable}}</td>
                           </tr>
                        </tbody>
                    </table>
               </div>
            </div>
        </div>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog m" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> Captura de Segmentos de Negocio</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div v-if="centros == []">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="spinner-border text-success" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card" v-else>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="fecha" class="col-form-label"><b>Segmentos de Negocio:</b></label>
                                        </div>
                                        <div class="col-md-7">
                                            <select class="form-control"
                                                        data-vv-as="Segmento de negocio"
                                                        id="id_centro"
                                                        name="id_centro"
                                                        :class="{'is-invalid': errors.has('id_centro')}"
                                                        v-validate="{required: true}"
                                                        v-model="doc.id_centro">
                                                    <option value>-- Selecionar --</option>
                                                    <option v-for="(c) in centros" :value="c.id">{{ c.descripcion }}</option>
                                                </select>
                                                <div style="display:block" class="invalid-feedback" v-show="errors.has('id_centro')">{{ errors.first('id_centro') }}</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                       <div class="col-md-5">
                                           <label for="fecha" class="col-form-label"><b>Tipo de Gasto:</b></label>
                                       </div>
                                        <div class="col-md-7">
                                            <h6 style="text-align: center">{{ doc.tipoGastoComp ? (doc.tipoGastoComp.tipoGasto ? doc.tipoGastoComp.tipoGasto.descripcion : '' ) : ''}}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="fecha" class="col-form-label"><b>Importe:</b></label>
                                        </div>
                                        <div class="col-md-7" style="text-align: right">
                                            <h6>{{ doc.importe_format }}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="fecha" class="col-form-label"><b>IVA:</b></label>
                                        </div>
                                        <div class="col-md-7" style="text-align: right">
                                            <h6>{{doc.iva_format }}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="fecha" class="col-form-label"><b>Retenciones:</b></label>
                                        </div>
                                        <div class="col-md-7" style="text-align: right">
                                            <h6>{{doc.retenciones_format }}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="fecha" class="col-form-label"><b>Otros Impuestos:</b></label>
                                        </div>
                                        <div class="col-md-7" style="text-align: right">
                                            <h6>{{ doc.otros_imp_format }}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="fecha" class="col-form-label"><b>Total:</b></label>
                                        </div>
                                        <div class="col-md-7" style="text-align: right">
                                            <h6>{{ doc.total_format }}</h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="fecha" class="col-form-label"><b>Facturable:</b></label>
                                        </div>
                                        <div class="col-md-7" style="text-align: right">
                                            <select class="form-control"
                                                data-vv-as="Facturable"
                                                id="idfacturable"
                                                name="idfacturable"
                                                :class="{'is-invalid': errors.has('idfacturable')}"
                                                v-validate="{required: true}"
                                                v-model="doc.idfacturable">
                                                <option value>-- Selecionar --</option>
                                                <option value="Y"> SI </option>
                                                <option value="N"> NO </option>
                                            </select>
                                            <div style="display:block" class="invalid-feedback" v-show="errors.has('idfacturable')">{{ errors.first('idfacturable') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" @click="validate" :disabled="errors.count() > 0" >
                                        <i class="fa fa-save"></i> Guardar
                                    </button>
                                    <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i> Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "documentos",
    props: ['documentos'],
    data() {
        return {
            cargando : true,
            id: 0,
            centros: [],
            doc: []
        }
    },
    mounted() {
        this.getCentroCosto();
    },
    methods :{
        salir() {
            $(this.$refs.modal).modal('hide');
        },
        abrirModal(doc)
        {
            $(this.$refs.modal).appendTo('body')
            $(this.$refs.modal).modal('show');
            this.doc = doc;
        },
        getCentroCosto() {
            return this.$store.dispatch('controlRecursos/centro-costo/index', {
                params: { order: 'asc', sort:'Descripcion' }
            }).then(data => {
                this.centros = data.data;
            })
        },
        validate() {
            this.$validator.validate().then(result => {
                if (result) {
                    this.editar()
                }
            });
        },
        editar() {
            return this.$store.dispatch('controlRecursos/cc-docto/update', {
                id: this.doc.id,
                data: this.doc
            }).then((data) => {
                return this.$store.dispatch('controlRecursos/reembolso-pago-a-proveedor/find', {
                    id: data.id_docto,
                    params:{include: []}
                }).then(data => {
                    this.documentos = data.documentos;
                    this.salir()
                })
            })
        }
    }
}
</script>

<style scoped>
.encabezado{
    text-align: center; background-color: #f2f4f5
}
td, th{
    border: 1px #dee2e6 solid;
}

</style>
