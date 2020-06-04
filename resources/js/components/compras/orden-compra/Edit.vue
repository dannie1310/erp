<template>  
    <span>
        <div class="row">
            <div class="col-12">    
                <div class="invoice p-3 mb-3">
                    <div class="modal-body">
                        <i class="fa fa-spin fa-spinner fa-2x" v-if="cargando"></i>
                        <div class="row" v-if="Object.keys(orden_compra).length > 0">
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Folio Solicitud de Compra: </b></label>
                                    panda
                                </div>
                            </div>
                            <div class="col-md-8" >
                                <div class="form-group">
                                    <label><b>Solicitud de Compra: </b></label>
                                    panda
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Folio Orden de Compra: </b></label>
                                    panda
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Razón Social: </b></label>
                                    panda
                                </div>
                            </div>
                            
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label><b>Sucursal: </b></label>
                                    panda
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </span>
</template>

<script>
export default {
    name: "orden-compra-edit",
    props: ['id'],
    data() {
        return {
            cargando: false,
            orden_compra:[],
        }
    },
    mounted() {
        this.getOrdenCompra();
    },
    methods: {
        getOrdenCompra(){
            this.cargando = true;
            this.orden_compra = [];
            return this.$store.dispatch('compras/orden-compra/find', {
                    id: this.id,
                    params:{
                        include: ['empresa', 'partidas', 'solicitud']
                    }
                }).then(data => {
                    this.orden_compra = data;
                }).finally(()=>{
                    this.cargando = false;
                })
        },
    }

}
</script>

<style>

</style>