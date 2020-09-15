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
                                    title: 'Salvo com sucesso',
                                })
                            })
                    }
                })
        },
    },
}
</script>
