<template>
    <div class="row">
        <div class="col-12">
        </div>
        <div class="col-12">
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive">
                        <datatable v-bind="$data" v-bind:class="'table-sm table-bordered'" v-bind:style="'font-size: 11px'"  />
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</template>

<script>
    export default {
        name: "reembolso-index",
        data() {
            return {
                HeaderSettings: false,
                columns: [

                ],
                data: [],
                total: 0,
                query: {include: [], sort: '', order: ''},
                cargando: false
            }
        },

        mounted() {
            this.$Progress.start();
            this.paginate()
                .finally(() => {
                    this.$Progress.finish();
                })
        },

        methods: {
            paginate() {
            },
        },
        computed: {

        },
        watch: {
        }
    }
</script>
<style>

</style>
