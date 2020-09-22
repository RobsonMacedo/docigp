<template>
    
        <button
            :disabled="disabled"
            :class="classes"
            :title="title"
            @click="pressButton(model)"
        >
            <pulse-loader
                v-if="loading"
                color="white"
                :loading="true" :size="'0.4em'"
            >
            </pulse-loader>
            <span v-else :class="icon"> {{label}}</span>
            
        </button>
    
</template>

<script>
export default {
    props: [
        'label',
        'disabled',
        'icon',
        'classes',
        'title',
        'color',
        'model',
        'store',
        'method',
        'swal-title',
    ],

    data() {
        return {
            loading: false,
        }
    },

    methods: {
        pressButton(model) {
            const $this = this

            $this
                .$swal({
                    title: $this.swalTitle,
                    icon: 'warning',
                })
                .then(result => {
                    if (result.value) {
                        $this.loading = true
                            $this.$store.dispatch($this.store + '/' + $this.method, model)
                            .then(response => {
                                $this.loading = false

                                this.$store.commit(
                                    $this.store + '/mutateSetDataRow',
                                    response.data,
                                )

                                $this.$swal({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    timer: 2000,
                                    icon: 'success',
                                    title: 'Salvo com sucesso',
                                })
                            }).catch(error => {
                                var title = ''
                            switch (error.response.status) {
                                case 404: 
                                    title = 'Pagina não encontrada'
                                    break
                                case 401: 
                                    title = 'Ação não autorizada'
                                    break
                                case 422: 
                                    title = 'Verifique as informações'
                                    break    
                                case 403: 
                                    title = 'Ação não autorizada'
                                    break
                                case 500: 
                                    title = 'Erro interno - Administradores já foram contactados'
                                    break
                                default:
                                    title = 'Ocorreu um erro'
                            }

                            $this.loading = false

                            $this.$swal({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton:false,
                                showCancelButton:false,
                                timer: 2000,
                                icon:'error',
                                title: title
                        })
                    })
                }
            })
        },
    },
}
</script>
