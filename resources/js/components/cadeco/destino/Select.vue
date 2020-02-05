<template>
    <span>
         <button type="button"  @click="init" class="btn btn-sm btn-secondary" v-if="">
           <i class="fa fa-list"></i>
         </button>


        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">CATÁLOGO DE DESTINOS</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" >
                        <div class="modal-body">
                            <div class="row">

                                <div class="col-md-6 mb-5">
                                    <button type="button" class="btn btn-primary" v-if="" @click="changeConcepto"> + Conceptos </button>
                                </div>


                                <div class="col-md-6 mb-5">
                                    <button type="button" class="btn btn-primary" v-if="" @click="changeActivo"> + Activos</button>
                                </div>


                                    <div class="col-md-12">
                                            <div class="form-group" v-if="concepto">
                                                 <label for="conceptoLabel">Conceptos</label>
                                              <ConceptoSelect v-model="destino_concepto" @input="changeConceptoValue"></ConceptoSelect>
                                           </div>
                                    </div>



                                    <div class="col-md-12">
                                        <div class="form-group" v-if="activo">
                                             <label for="activoLabel">Activos</label>
                                            <select-almacenes v-model="destino_almacen" @input="changeAlmacenValue"></select-almacenes>
                                       </div>
                                    </div>


                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" v-if="activo || concepto" @click="setDestino">Seleccionar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </span>
</template>

<script>

    import ConceptoSelect from "../concepto/Select";
    import SelectAlmacenes from "../almacen/SingleSelect";
    export default {
        name: "SelectDestino",
        components: {SelectAlmacenes, ConceptoSelect},
        data() {
            return {
                val:null,
                activo: false,
                concepto: false,
                destino_concepto:'',
                destino_almacen:'',
                destino_concepto_arr:[] ,

        }
        },

        mounted() {
            this.activo=false;
            this.concepto=false;
        },

        methods: {
            init(){
                $(this.$refs.modal).modal('show');
                this.$validator.reset()
            },
            changeActivo() {
                this.activo = !this.activo;
                this.concepto = false;
            },
            changeConcepto()
            {
                this.concepto = !this.concepto;
                this.activo = false;
            },
            setDestino(){
                this.$emit('input', this.val)
                $(this.$refs.modal).modal('hide');
            },
            changeAlmacenValue(){
                this.val = this.destino_almacen;
            },
            changeConceptoValue(){
                return this.$store.dispatch('cadeco/concepto/find',{
                    id:this.destino_concepto
                })
                 .then(data => {
                     this.val = data;

                })

            }



        },

        computed: {


        }
    }
</script>

<style scoped>

</style>
