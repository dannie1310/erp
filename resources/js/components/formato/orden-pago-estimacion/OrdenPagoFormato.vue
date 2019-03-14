<template>
    <div>
        <span>
            <button @click="init"  type="submit" class="btn btn-primary">Ver Formato</button>

            <div class="modal fade" ref="modal" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">{{id}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                       <pdf>
                           <object data="value.data" type="application/pdf" width="800" height="400"></object>
                              <!--  src="http://dev-sao.grupohi.mx/api/formato/orden-pago-estimacion/369"
                                @num-pages="pageCount = $event"
                                @page-loaded="currentPage = $event"-->
                        </pdf>
                    </div>
                </div>
            </div>
        </span>
    </div>
</template>

<script>
    import pdf from 'vue-pdf'

    export default {
        components: {
            pdf
        },
        props: ['id'],
        data() {
            return {
                d : ''
            }
        },
        methods: {
            init() {
                $(this.$refs.modal).modal('show');
                this.pdf()

            },
            pdf(){
                return this.$store.dispatch('formato/orden-pago-estimacion/pdf', this.id)
                    .then(                           );
            }
        }

    }
</script>
