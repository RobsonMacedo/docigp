<template> 
    <app-table-panel
        :title="'Orçamento mensal (' + pagination.total + ')'"
        titleCollapsed="Orçamento"
        :subTitle="congressmen.selected.name"
        :per-page="perPage"
        :filter-text="filterText"
        @input-filter-text="filterText = $event.target.value"
        @set-per-page="perPage = $event"
        :collapsedLabel="currentSummaryLabel"
        :is-selected="selected.id !== null"
    >
        <app-table
            :pagination="pagination"
            @goto-page="gotoPage($event)"
            :columns="getTableColumns()"
        >
            <tr
                @click="selectCongressmanBudget(congressmanBudget)"
                v-for="congressmanBudget in congressmanBudgets.data.rows"
                :class="{
                    'cursor-pointer': true,
                    'bg-primary-lighter text-white': isCurrent(
                        congressmanBudget,
                        selected,
                    ),
                }"
            >
                <!--                State DEBUG-->
                <!--                <td class="align-middle">-->
                <!--                    {{ getCongressmanBudgetState(congressmanBudget).name }}-->
                <!--                </td>-->

                <td class="align-middle">{{ makeDate(congressmanBudget) }}</td>

                <td class="align-middle text-right">
                    {{ congressmanBudget.state_value_formatted }}
                </td>

                <td class="align-middle text-right">
                    {{ congressmanBudget.percentage_formatted }}
                </td>

                <td class="align-middle text-right">
                    {{ congressmanBudget.value_formatted }}
                </td>

                <td class="align-middle text-right">
                    {{ congressmanBudget.entries_count }}
                </td>

                <td
                    v-if="can('congressman-budgets:show')"
                    class="align-middle text-center"
                >
                    <app-badge
                        v-if="congressmanBudget.pendencies.length === 0"
                        caption="não"
                        color="#38c172,#FFFFFF"
                        padding="1"
                    ></app-badge>

                    <app-badge
                        v-if="congressmanBudget.pendencies.length > 0"
                        color="#e3342f,#FFFFFF"
                        padding="1"
                    >
                        <div v-for="pendency in congressmanBudget.pendencies">
                            &bull; {{ pendency }}<br />
                        </div>
                    </app-badge>
                </td>

                <td
                    v-if="can('congressman-budgets:show')"
                    class="align-middle text-center"
                >
                    <app-active-badge
                        :value="congressmanBudget.closed_at"
                        :labels="['sim', 'não']"
                    ></app-active-badge>
                </td>

                <td
                    v-if="can('congressman-budgets:show')"
                    class="align-middle text-center"
                >
                    <app-active-badge
                        :value="congressmanBudget.analysed_at"
                        :labels="['sim', 'não']"
                    ></app-active-badge>
                </td>

                <td
                    v-if="can('congressman-budgets:show')"
                    class="align-middle text-center"
                >
                    <app-active-badge
                        :value="congressmanBudget.published_at"
                        :labels="['público', 'privado']"
                    ></app-active-badge>
                </td>

                <td
                    v-if="can('congressman-budgets:show')"
                    class="align-middle text-right"
                >
                    <!--                    <button-->
                    <!--                        v-if="-->
                    <!--                            can('congressman-budgets:refund') &&-->
                    <!--                                !congressmanBudget.has_refund-->
                    <!--                        "-->
                    <!--                        :disabled="-->
                    <!--                            !can('congressman-budgets:refund') ||-->
                    <!--                                congressmanBudget.analysed_at ||-->
                    <!--                                congressmanBudget.published_at-->
                    <!--                        "-->
                    <!--                        @click="createRefund(congressmanBudget)"-->
                    <!--                        class="btn btn-sm btn-micro btn-warning"-->
                    <!--                        :title="-->
                    <!--                            'Devolver saldo da conta de ' +-->
                    <!--                                congressmen.selected.name-->
                    <!--                        "-->
                    <!--                    >-->
                    <!--                        <i class="fa fa-dollar-sign"></i>-->
                    <!--                        Devolver-->
                    <!--                    </button>-->

                    <button
                        v-if="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .deposit.visible
                        "
                        :disabled="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .deposit.disabled
                        "
                        @click="deposit(congressmanBudget)"
                        class="btn btn-sm btn-micro btn-success"
                        :title="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .deposit.title
                        "
                    >
                        <i class="fa fa-dollar-sign"></i> depositar
                    </button>

                    <app-percentage-button
                            v-if="getCongressmanBudgetState(congressmanBudget).buttons.editPercentage.visible"
                            :disabled="
                                getCongressmanBudgetState(congressmanBudget).buttons.editPercentage.disabled
                            "
                            classes="btn btn-sm btn-micro btn-primary"
                            :title="getCongressmanBudgetState(congressmanBudget).buttons.editPercentage.title"
                            :model="congressmanBudget"
                            label="percentual"
                            icon="fa fa-edit"
                            store="congressmanBudgets"
                            method="editPercentage"
                        > 
                    </app-percentage-button>

                    <app-action-button
                            v-if="getCongressmanBudgetState(congressmanBudget).buttons.close.visible"
                            :disabled="
                                getCongressmanBudgetState(congressmanBudget).buttons.close.disabled
                            "
                            classes="btn btn-sm btn-micro btn-danger"
                            :title="getCongressmanBudgetState(congressmanBudget).buttons.close.title"
                            :model="congressmanBudget"
                            swal-title="Deseja realmente FECHAR esse Orçamento Mensal?"
                            label="fechar"
                            icon="fa fa-ban"
                            store="congressmanBudgets"
                            method="close"
                        > 
                    </app-action-button>

                    <app-action-button
                            v-if="getCongressmanBudgetState(congressmanBudget).buttons.reopen.visible"
                            :disabled="
                                getCongressmanBudgetState(congressmanBudget).buttons.reopen.disabled
                            "
                            classes="btn btn-sm btn-micro btn-danger"
                            :title="getCongressmanBudgetState(congressmanBudget).buttons.reopen.title"
                            :model="congressmanBudget"
                            swal-title="Deseja REABRIR esse Orçamento Mensal?"
                            label="reabrir"
                            icon="fa fa-check"
                            store="congressmanBudgets"
                            method="reopen"
                        > 
                    </app-action-button>

                    <app-action-button
                            v-if="getCongressmanBudgetState(congressmanBudget).buttons.analyse.visible"
                            :disabled="
                                getCongressmanBudgetState(congressmanBudget).buttons.analyse.disabled
                            "
                            classes="btn btn-sm btn-micro btn-warning"
                            :title="getCongressmanBudgetState(congressmanBudget).buttons.analyse.title"
                            :model="congressmanBudget"
                            swal-title="Esse Orçamento mensal foi ANALISADO?"
                            label="analisar"
                            icon="fa fa-check"
                            store="congressmanBudgets"
                            method="analyse"
                            :spinner-config="{'color': 'black'}"
                        > 
                    </app-action-button>

                    <app-action-button
                            v-if="getCongressmanBudgetState(congressmanBudget).buttons.unanalyse.visible"
                            :disabled="
                                getCongressmanBudgetState(congressmanBudget).buttons.unanalyse.disabled
                            "
                            classes="btn btn-sm btn-micro btn-warning"
                            :title="getCongressmanBudgetState(congressmanBudget).buttons.unanalyse.title"
                            :model="congressmanBudget"
                            swal-title="Deseja remover o status ANALISADO deste lançamento?"
                            label="analisado"
                            icon="fa fa-ban"
                            store="congressmanBudgets"
                            method="unanalyse"
                            :spinner-config="{'color': 'black'}"
                        > 
                    </app-action-button>

                    <app-action-button
                            v-if="getCongressmanBudgetState(congressmanBudget).buttons.publish.visible"
                            :disabled="
                                getCongressmanBudgetState(congressmanBudget).buttons.publish.disabled
                            "
                            classes="btn btn-sm btn-micro btn-danger"
                            :title="getCongressmanBudgetState(congressmanBudget).buttons.publish.title"
                            :model="congressmanBudget"
                            swal-title="Confirma a PUBLICAÇÃO deste Orçamento Mensal?"
                            label="publicar"
                            icon="fa fa-check"
                            store="congressmanBudgets"
                            method="publish"
                        > 
                    </app-action-button>

                    <app-action-button
                            v-if="getCongressmanBudgetState(congressmanBudget).buttons.unpublish.visible"
                            :disabled="
                                getCongressmanBudgetState(congressmanBudget).buttons.unpublish.disabled
                            "
                            classes="btn btn-sm btn-micro btn-danger"
                            :title="getCongressmanBudgetState(congressmanBudget).buttons.unpublish.title"
                            :model="congressmanBudget"
                            swal-title="Confirma a DESPUBLICAÇÃO deste Orçamento Mensal?"
                            label="despublicar"
                            icon="fa fa-ban"
                            store="congressmanBudgets"
                            method="unpublish"
                        > 
                    </app-action-button>
                </td>
            </tr>
        </app-table>

        <app-entry-form :show.sync="showModal" :refund="true"></app-entry-form>
    </app-table-panel>
</template>

<script>
import crud from '../../views/mixins/crud'
import { mapActions, mapGetters } from 'vuex'
import congressmen from '../../views/mixins/congressmen'
import permissions from '../../views/mixins/permissions'
import congressmanBudgets from '../../views/mixins/congressmanBudgets'

const service = {
    name: 'congressmanBudgets',
    uri: 'congressmen/{congressmen.selected.id}/budgets',
}

export default {
    mixins: [crud, congressmen, congressmanBudgets, permissions],

    data() {
        return {
            service: service,

            showModal: false,
        }
    },

    methods: {
        ...mapActions(service.name, ['selectCongressmanBudget']),

        getTableColumns() {
            let columns = [
                'Ano / Mês',

                {
                    type: 'label',
                    title: 'Referência',
                    trClass: 'text-right',
                },
                {
                    type: 'label',
                    title: '%',
                    trClass: 'text-right',
                },
                {
                    type: 'label',
                    title: 'Solicitado',
                    trClass: 'text-right',
                },
                {
                    type: 'label',
                    title: 'Lançamentos',
                    trClass: 'text-right',
                },
            ]

            if (can('congressman-budgets:show')) {
                columns.push({
                    type: 'label',
                    title: 'Pendências',
                    trClass: 'text-center',
                })

                columns.push({
                    type: 'label',
                    title: 'Fechado',
                    trClass: 'text-center',
                })

                columns.push({
                    type: 'label',
                    title: 'Analisado',
                    trClass: 'text-center',
                })

                columns.push({
                    type: 'label',
                    title: 'Publicidade',
                    trClass: 'text-center',
                })

                columns.push('')
            }

            return columns
        },

        makeDate(congressmanBudget) {
            return congressmanBudget.year + ' / ' + congressmanBudget.month
        },        

        deposit(congressmanBudget) {
            this.$swal({
                title:
                    'Confirma o depósito de ' +
                    congressmanBudget.value_formatted +
                    ' na conta de ' +
                    this.congressmen.selected.name +
                    '?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch(
                        'congressmanBudgets/deposit',
                        congressmanBudget,
                    )
                }
            })
        },

        // createRefund(congressmanBudget) {
        //     this.$store
        //         .dispatch(
        //             'congressmanBudgets/selectCongressmanBudget',
        //             congressmanBudget,
        //         )
        //         .then(
        //             this.$store
        //                 .dispatch('entries/fillFormForRefund')
        //                 .then(() => (this.showModal = true)),
        //         )
        // },
    },

    computed: {
        ...mapGetters(service.name, [
            'currentSummaryLabel',
            'getCongressmanBudgetState',
            'getSelectedState',
        ]),
    },
}
</script>
