<template>
    <button
        :disabled="disabled"
        :class="classes"
        :title="title"
        @click="pressButton(model)"
    >
        <pulse-loader
            v-if="loading"
            :color="spinnerColor"
            :loading="true"
            :size="spinnerSize"
        >
        </pulse-loader>
        <span v-else :class="icon"> {{ label }}</span>
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
        'spinner-config',
        'swal-message',
    ],

    data() {
        return {
            loading: false,
        }
    },

    computed: {
        spinnerColor: {
            get() {
                return this?.spinnerConfig?.color ?? 'white'
            },
        },

        spinnerSize: {
            get() {
                return this?.spinnerConfig?.size ?? '0.4em'
            },
        },
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
                        $this.$store
                            .dispatch($this.store + '/' + $this.method, model)
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
                                    title:
                                        $this.swalMessage?.r200 ??
                                        'Salvo com sucesso',
                                })
                            })
                            .catch(error => {
                                var title = ''
                                switch (error.response.status) {
                                    case 404:
                                        title =
                                            $this.swalMessage?.r404 ??
                                            'Pagina não encontrada'
                                        break
                                    case 401:
                                        title =
                                            $this.swalMessage?.r401 ??
                                            'Ação não autorizada'
                                        break
                                    case 422:
                                        title =
                                            $this.swalMessage?.r422 ??
                                            'Verifique as informações'
                                        break
                                    case 403:
                                        title =
                                            $this.swalMessage?.r403 ??
                                            'Ação não autorizada'
                                        break
                                    case 500:
                                        title =
                                            $this.swalMessage?.r500 ??
                                            'Erro interno - Administradores já foram contactados'
                                        break
                                    default:
                                        title = 'Ocorreu um erro'
                                }

                                $this.loading = false

                                $this.$swal({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    showCancelButton: false,
                                    timer: 2000,
                                    icon: 'error',
                                    title: title,
                                })
                            })
                    }
                })
        },
    },
}
</script>
