<template>
    <span>
         <button @click="find" type="button" class="btn btn-sm btn-outline-primary" title="Asignar Concepto">
             <i class="fa fa-sitemap"/>
         </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-sitemap"></i> ASIGNAR CONCEPTO A TIRO</h5>
                        <button type="button" class="close" @click="salir" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="carga">
                        <div class="card">
                           <div class="card-body">
                               <div class="row">
                                   <div class="col-md-2">
                                       <div class="form-group">
                                           <h6><b>Tiro:</b></h6>
                                       </div>
                                   </div>
                                   <div class="col-md-10">
                                       <div class="form-group">
                                           <h6>{{tiro.descripcion}}</h6>
                                       </div>
                                   </div>
                               </div>
                               <div class="row">
                                   <div class="col-md-2" v-if="tiro.concepto">
                                       <div class="form-group">
                                           <h6><b>Concepto:</b></h6>
                                       </div>
                                   </div>
                                   <div class="col-md-10" v-if="tiro.concepto">
                                       <div class="form-group">
                                           <h6 :title="tiro.path_concepto">{{tiro.concepto.descripcion}}</h6>
                                       </div>
                                   </div>
                               </div>
                           </div>
                        </div>
                        <div class="card">
                            <div class="card-header"  v-if="tiro.concepto">
                                <label v-if="tiro.concepto">Sustituir el concepto:</label>
                            </div>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="form-group row error-content">
                                        <label for="id_concepto" class="col-md-2 col-form-label">Conceptos:</label>
                                        <div class="col-md-10">
                                            <conceptos
                                                name="id_concepto"
                                                data-vv-as="Concepto"
                                                id="id_concepto"
                                                v-model="id_concepto"
                                                :error="errors.has('id_concepto')"
                                                ref="Conceptos"
                                                :disableBranchNodes="true"
                                            ></conceptos>
                                            <div class="error-label" v-show="errors.has('id_concepto')">{{ errors.first('id_concepto') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="salir"><i class="fa fa-close"></i> Cerrar</button>
                        <button type="submit" class="btn btn-primary" @click="asignar_concepto" :disabled="errors.count() > 0">
                            <i class="fa fa-save"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
      </span>
</template>

<script>
    import Conceptos from "../../../cadeco/concepto/Select";
    export default {
        name: "asignar_concepto",
        props: ['id'],
        components: {Conceptos},
        data() {
            return {
                id_concepto : '',
                carga : false
            }
        },
        methods: {
            salir()
            {
                this.carga = false
                $(this.$refs.modal).modal('hide');
            },
            find() {
                this.$store.commit('acarreos/tiro/SET_TIRO', null);
                return this.$store.dispatch('acarreos/tiro/find', {
                    id: this.id,
                    params: {}
                }).then(data => {
                    this.$store.commit('acarreos/tiro/SET_TIRO', data);
                    this.carga = true;
                    $(this.$refs.modal).appendTo('body')
                    $(this.$refs.modal).modal('show');
                })
            },
           asignar_concepto() {
               var datos = {
                   'concepto' : this.tiro.concepto ? '1' : '0',
                   'id_concepto' : this.id_concepto
               }

               return this.$store.dispatch('acarreos/tiro/agregarConcepto', {
                   id: this.id,
                   params: {data: datos}
               })
                   .then(data => {
                       $(this.$refs.modal).modal('hide');
                       this.$store.commit('acarreos/tiro/UPDATE_TIRO', data);
                   })
                   .finally(() => {
                       this.carga = false;
                   });
           },
        },
        computed: {
            tiro() {
                return this.$store.getters['acarreos/tiro/currentTiro']
            }
        }
    }
</script>

<style scoped>

</style>
