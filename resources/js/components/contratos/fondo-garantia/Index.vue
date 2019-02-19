<template>
    <div class="row">
        <div class="col-md-12">
            <fondo-garantia-create>INDEX FONDO GARANTIA</fondo-garantia-create>
        </div>

        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
</template>

<script>
    export default {
        name: "fondos-garantia-index",
    }
</script>