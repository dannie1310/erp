<template>
     <span>
        <div class="row">
            <div class="col-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fa fa-list"></i> Recursos Autorizados
                            </h4>
                        </div>
                    </div>
                    <form role="form" @submit.prevent="validate">

                        <div class="modal-body">
                            <div class="row justify-content-between">
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
        name: "entrada-almacen-create"
    }
</script>

<style scoped>

</style>