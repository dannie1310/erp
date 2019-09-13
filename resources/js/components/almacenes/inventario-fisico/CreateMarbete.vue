<template>
        <span>
 <button @click="init" type="button" class="btn btn-sm btn-outline-primary" title="Crear Marbete"><i class="fa fa-newspaper-o"></i> </button>

               <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Registrar Marbete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form role="form" @submit.prevent="validate">
                        <div class="modal-body">
                            <div class="row">



                            </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        </span>
</template>

<script>

    import Index from '../Index';
    export default {
        name: "create-marbete",
        components: {Index},
        data() {
            return {



            }
        },

        mounted() {
            // this.getBancos()


        },

        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.$validator.reset()
            },
            getBancos() {
                // return this.$store.dispatch('seguridad/finanzas/ctg-banco/index', {
                //     params: {sort: 'razon_social',  order: 'asc', scope:'NoRegistrado'}
                // })
                //     .then(data => {
                //         this.bancos= data.data;
                //     })
            },
            validate() {
                // this.$validator.validate().then(result => {
                //     if (result) {
                //         this.store()
                //     }
                // });
            },
            store() {

                // return this.$store.dispatch('cadeco/banco/store', this.$data)
                //     .then((data) => {
                //         $(this.$refs.modal).modal('hide');
                //         this.$emit('created',data)
                //         this.bancos=[];
                //         this.getBancos();
                //
                //     })
            }
        },

        computed: {


        }
    }
</script>
