<template>
    <input type="text" name="daterange"/>
</template>

<script>
    export default {
        name: "daterange-picker",
        props: ['value'],
        mounted() {
            let self = this
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'DE',
                    toLabel: 'A',
                }
            }).on('apply.daterangepicker', function(ev, picker) {
                self.$emit('input', picker);
            });
        },

        watch: {
            value: function (value) {
                $('input[name="daterange"]').val(value.startDate.format('YYYY-MM-DD') + ' - ' + value.endDate.format('YYYY-MM-DD'));
            }
        },

        destroyed: function () {
            $('input[name="daterange"]').daterangepicker('destroy');
        }
    }
</script>