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

                    <button
                        v-if="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .editPercentage.visible
                        "
                        :disabled="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .editPercentage.disabled
                        "
                        @click="editPercentage(congressmanBudget)"
                        class="btn btn-sm btn-micro btn-primary"
                        :title="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .editPercentage.title
                        "
                    >
                        <i class="fa fa-edit"></i> percentual
                    </button>

                    <button
                        v-if="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .close.visible
                        "
                        :disabled="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .close.disabled
                        "
                        class="btn btn-sm btn-micro btn-danger"
                        :title="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .close.title
                        "
                        @click="close(congressmanBudget)"
                    >
                        <i class="fa fa-ban"></i> fechar
                    </button>

                    <button
                        v-if="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .reopen.visible
                        "
                        :disabled="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .reopen.disabled
                        "
                        class="btn btn-sm btn-micro btn-danger"
                        :title="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .reopen.title
                        "
                        @click="reopen(congressmanBudget)"
                    >
                        <i class="fa fa-check"></i> reabrir
                    </button>

                    <button
                        v-if="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .analyse.visible
                        "
                        :disabled="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .analyse.disabled
                        "
                        class="btn btn-sm btn-micro btn-warning"
                        :title="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .analyse.title
                        "
                        @click="analyse(congressmanBudget)"
                    >
                        <i class="fa fa-check"></i> analisado
                    </button>

                    <button
                        v-if="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .unanalyse.visible
                        "
                        :disabled="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .unanalyse.disabled
                        "
                        class="btn btn-sm btn-micro btn-warning"
                        :title="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .unanalyse.title
                        "
                        @click="unanalyse(congressmanBudget)"
                    >
                        <i class="fa fa-ban"></i> analisado
                    </button>

                    <button
                        v-if="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .publish.visible
                        "
                        :disabled="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .publish.disabled
                        "
                        class="btn btn-sm btn-micro btn-danger"
                        :title="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .publish.title
                        "
                        @click="publish(congressmanBudget)"
                    >
                        <i class="fa fa-check"></i> publicar
                    </button>

                    <button
                        v-if="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .unpublish.visible
                        "
                        :disabled="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .unpublish.disabled
                        "
                        class="btn btn-sm btn-micro btn-danger"
                        :title="
                            getCongressmanBudgetState(congressmanBudget).buttons
                                .unpublish.title
                        "
                        @click="unpublish(congressmanBudget)"
                    >
                        <i class="fa fa-ban"></i> despublicar
                    </button>
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

        changePercentage: function(congressmanBudget, value) {
            return this.$store.dispatch('congressmanBudgets/changePercentage', {
                congressmanBudget: congressmanBudget,
                percentage: value,
            })
        },

        editPercentage(congressmanBudget) {
            this.$swal({
                icon: 'warning',
                title: 'Novo percentual',
                input: 'text',
                inputPlaceholder: 'Digite um percentual',
                inputAttributes: {
                    dusk: 'input-percentage',
                },
                inputValidator: value => {
                    if (
                        !is_number(value) ||
                        to_number(value) < 0 ||
                        to_number(value) > 100
                    ) {
                        return 'Você precisa digitar um número entre 0 e 100'
                    }
                },
            }).then(value => {
                if (value.value) {
                    this.changePercentage(congressmanBudget, value.value)
                }
            })
        },

        close(congressmanBudget) {
            this.$swal({
                title: 'Deseja realmente FECHAR este orçamento mensal?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch(
                        'congressmanBudgets/close',
                        congressmanBudget,
                    )
                }
            })
        },

        reopen(congressmanBudget) {
            this.$swal({
                title: 'Deseja realmente REABRIR este orçamento mensal?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch(
                        'congressmanBudgets/reopen',
                        congressmanBudget,
                    )
                }
            })
        },

        analyse(congressmanBudget) {
            this.$swal({
                title: 'Este orçamento mensal foi ANALISADO?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch(
                        'congressmanBudgets/analyse',
                        congressmanBudget,
                    )
                }
            })
        },

        unanalyse(congressmanBudget) {
            this.$swal({
                title: 'Deseja remover o status "ANALISADO" deste lançamento?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch(
                        'congressmanBudgets/unanalyse',
                        congressmanBudget,
                    )
                }
            })
        },

        publish(congressmanBudget) {
            this.$swal({
                title: 'Confirma a PUBLICAÇÃO deste orçamento mensal?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch(
                        'congressmanBudgets/publish',
                        congressmanBudget,
                    )
                }
            })
        },

        unpublish(congressmanBudget) {
            this.$swal({
                title: 'Confirma a DESPUBLICAÇÃO deste orçamento mensal?',
                icon: 'warning',
            }).then(result => {
                if (result.value) {
                    this.$store.dispatch(
                        'congressmanBudgets/unpublish',
                        congressmanBudget,
                    )
                }
            })
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
