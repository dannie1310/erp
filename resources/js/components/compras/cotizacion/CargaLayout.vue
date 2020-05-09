<template>
    <span>
        <button @click="find()" type="button" class="btn btn-sm btn-outline-secondary" :disabled="cargando" title="Cargar Layout Cotización">
            <i class="fa fa-upload" v-if="!cargando"></i>
            <i class="fa fa-spinner fa-spin" v-else></i>
        </button>
        <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"> <i class="fa fa-upload"></i> CARGAR LAYOUT DE COTIZACÓN</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                     <label for="carga_layout" class="col-lg-12 col-form-label">Cargar Layout</label>
                                    <div class="col-lg-12">
                                        <input type="file" class="form-control" id="carga_layout"
                                               @change="onFileChange"
                                               row="3"
                                               v-validate="{ ext: ['xlsx']}"
                                               name="carga_layout"
                                               data-vv-as="Layout"
                                               ref="carga_layout"
                                               :class="{'is-invalid': errors.has('carga_layout')}">
                                        <div class="invalid-feedback" v-show="errors.has('carga_layout')">{{ errors.first('carga_layout') }} (csv)</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="cerrarModal">Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="validate" :disabled="!file">Cargar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
    export default {
        name: "carga-layout-cotizacion",
        props: ['id'],
        data(){
            return{
                cargando: false,
                no_cotizados: [],
                items: [],
                cuenta: [],
                x: 0,
                t: 0,
                data: null,
                file: null,
            }
        },
        methods: {
            find() {

                // console.log(this.$refs.carga_layout.value);
                this.cargando = true;
                this.$refs.carga_layout.value = '';
                this.file = null;
                this.$validator.errors.clear();

                $(this.$refs.modal).appendTo('body')
                $(this.$refs.modal).modal('show');
                this.cargando = false;                
                

                // this.cargando = true;
                // this.$store.commit('compras/cotizacion/SET_COTIZACION', null);
                // return this.$store.dispatch('compras/cotizacion/find', {
                //     id: this.id,
                //     params:{include: ['empresa', 'sucursal', 'complemento', 'cotizaciones.material', 'cotizaciones.moneda']}
                // }).then(data => {
                //     this.$store.commit('compras/cotizacion/SET_COTIZACION', data);
                //     this.items = data.cotizaciones.data;
                //     $(this.$refs.modal).appendTo('body')
                //     $(this.$refs.modal).modal('show')
                //     this.cargando = false;
                    
                // })
            },
            createImage(file, tipo) {
                var reader = new FileReader();
                var vm = this;

                reader.onload = (e) => {
                    vm.file = e.target.result;
                };
                reader.readAsDataURL(file);

            },
            onFileChange(e){
                this.file = null;
                var files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                if(e.target.id == 'carga_layout') {
                    this.createImage(files[0]);
                }
            },
            cerrarModal(event) {
                this.$refs.carga_layout.value = '';
                this.file = null;
                this.$validator.errors.clear();
                $(this.$refs.modal).modal('hide')
            },
            validate() {
                
                this.$validator.validate().then(result => {
                    if (result){
                        this.cargarLayout()
                    }else{
                        if(this.$refs.carga_layout.value !== ''){
                            this.$refs.carga_layout.value = '';
                            this.file = null;
                        }
                        this.$validator.errors.clear();
                        swal('¡Error!', 'Error archivos de entrada invalidos.', 'error')
                    }
                });
                
            },
        },
        computed: {
            cotizacion() {
                return this.$store.getters['compras/cotizacion/currentCotizacion']
            }
        },
        watch: {
            items()
            {
                this.x = 0;
                this.t = 0;
                while(this.x < this.items.length)
                {
                    this.no_cotizados[this.x] = this.items[this.x].no_cotizado;
                    this.cuenta[this.x] = (this.no_cotizados[this.x]) ? this.t ++ : 0;
                    this.x ++;
                }                
            }
        }
    }
</script>

<style scoped>

</style>
