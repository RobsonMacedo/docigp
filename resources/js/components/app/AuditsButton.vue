

<template>
    <div class="col text-right">
        
        <input v-if="notLoading" type="button" class="btn btn-primary btn-sm" value="Gerar CSV" @click="multipleInputs()">
        <pulse-loader class="col text-right" v-else :loading=true :color="'#305bb0'" :size="'15px'" radius="100%"></pulse-loader>
        
    </div>
</template>

<script>
export default {
    name: 'AuditsButton',
    props:{
        route: String
    },

    data(){
        return{ 
            notLoading: true
        }
    },

    methods:{

        multipleInputs() {
        const { value: formValues } = Swal.fire({
        title: 'Selecione o período',
        html: '<input id="swal-input1" class="swal2-input" type = "date" name = "data_ini">' +
              '<input id="swal-input2" class="swal2-input" type = "date" name = "data_fim">',
                
        focusConfirm: false,
        preConfirm: () => {
                
                if (document.getElementById('swal-input1').value == '' || document.getElementById('swal-input2').value == ''){
                    Swal.close()
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'A data não pode estar vazia!',
                                })
                }else if (document.getElementById('swal-input1').value > document.getElementById('swal-input2').value){
                    Swal.close()
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'A data inicial tem que ser menor que a final!',
                            })
                }else{
                    const data_ini = document.getElementById('swal-input1').value
                    const data_fim = document.getElementById('swal-input2').value
              
                    const $this = this
                    this.notLoading = false
                    
                    axios
                        .get(this.route, {params: {'data_ini':data_ini, 'data_fim': data_fim}, responseType: 'arraybuffer'})
                        .then(response => {
                            let blob = new Blob([response.data], { type: 'application/vnd.ms-excel' })
                            let link = document.createElement('a')
                            link.href = window.URL.createObjectURL(blob)
                            link.download = 'users.csv'
                            link.click()
                                    
                            $this.notLoading = true
                            
                            Swal.fire({
                                toast:true,
                                position:'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                icon:'success',
                                title: 'Relatório gerado com sucesso'
                            })

                        })
                        .catch(function (error) {
                    
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
                            $this.notLoading = true

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton:false,
                                timer: 2000,
                                icon:'error',
                                title: title
                            })
                        }) 

                    }   
                }   
            })
       }
    }
}

</script>
