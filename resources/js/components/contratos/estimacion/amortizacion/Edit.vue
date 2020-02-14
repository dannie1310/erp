<template>
    <span>
          <button type="button" @click="init()" class="btn btn-primary float-right" v-if="$root.can(['registrar_descuento_estimacion_subcontrato','eliminar_descuento_estimacion_subcontrato'])" title="Editar">
                    Amortización de Anticipo
                </button>
             <div class="row">
            <div class="col-md-12">
                <div class="modal fade" ref="modalAmortizacion" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-md">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-edit"></i> EDITAR AMORTIZACIÓN DE ANTICIPO</h5>
                                <button type="button" class="close" @click="cerrar()" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form role="form" @submit.prevent="validate">
                                <div class="modal-body">
                                    <div class="row">
                                       
                                            <div class="form-group row error-content col-md-12">
                                                <label for="campo" class="col-sm-5 col-form-label">Amortización de Anticipo </label>
                                                 <label for="campo" class="col-sm-3 col-form-label">2.1601%</label>
                                                
                                                <div class="col-sm-4" style="text-align:right;">
                                                    <input
                                                        type="number"
                                                        step="0.0001"
                                                        v-model="campo"
                                                        name="campo"
                                                        data-vv-as="Anticipo"
                                                        v-validate="{required: true, min_value:0}"
                                                        class="form-control"
                                                        id="campo"
                                                        placeholder="Anticipo"
                                                        :class="{'is-invalid': errors.has('campo')}">
                                                    <div class="invalid-feedback" v-show="errors.has('campo')">{{ errors.first('campo') }}</div>
                                                </div>
                                            </div>
                                            
                                        <!-- </div> -->
                                    </div>
                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="cerrar()">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>

</template>

<script>
export default {
    name: "resumen-edit",
    props: ['id', 'id_empresa'],
    data() {
        return {
            descuentos:[],
            cargando:false,
            campo:0.001
        }
    },
    mounted() {
    },
    methods: {
        cerrar(){
            $(this.$refs.modalAmortizacion).modal('hide');
        },
        importe(desc){
            return parseFloat((desc.cantidad * desc.precio)).formatMoney(2, '.', ',');
        },
        init(){
            this.cargando = true;
            this.lista();
            $(this.$refs.modalAmortizacion).modal('show');
        },
        lista(){
            this.descuentos = [];
            return this.$store.dispatch('subcontratosEstimaciones/descuento/list', {
                id: this.id,
                // params: {include: 'material', sort: 'id_descuento', order: 'desc'}
            }).then(data => {
                this.descuentos = data.data;
            })
            .finally(()=>{
                this.cargando = false;
            })
        },
        updateDescuento(data){
            this.descuentos = data.data;
        },
        updateLista(){
            return this.$store.dispatch('subcontratosEstimaciones/descuento/updateList', this.descuentos)
            .then((data) => {
                this.descuentos = [];
                this.descuentos = data.data;
            })
        },
        validate() {
            alert('Validate');
            // this.$validator.validate().then(result => {
            //     if (result) {
            //         this.store();
            //     }
            // });
        },
    },
    computed: {
        tipos() {
            return this.$store.getters['subcontratosEstimaciones/retencion-tipo/tipos']
        },
    }

}
</script>

<style>
.align{
    text-align: right;
}
.table-fixed-suc tbody {
    display:block;
    height:380px;
    overflow:auto;
}
.table-fixed-suc thead, .table-fixed tbody tr {
    display:table;
    width:100%;
    text-align: left;
}
table.fixed { table-layout:fixed; }
table.fixed td { overflow: hidden; }
</style>