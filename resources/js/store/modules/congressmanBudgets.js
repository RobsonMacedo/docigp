import Form from '../../classes/Form'

import * as mutationsMixin from './mixins/mutations.js'
import * as actionsMixin from './mixins/actions.js'
import * as statesMixin from './mixins/states.js'
import * as gettersMixin from './mixins/getters.js'

const __emptyModel = {
    id: null,
    name: null,
    description: null,
}

let state = merge_objects(
    {
        selectedRole: __emptyModel,

        form: new Form(clone(__emptyModel)),

        emptyForm: clone(__emptyModel),

        mode: null,

        model: {
            name: 'congressmanBudget',

            table: 'congressman_budgets',

            class: {
                singular: 'CongressmanBudget',
                plural: 'CongressmanBudgets',
            },
        },
    },

    statesMixin.common,
)

let actions = merge_objects(actionsMixin, {
    subscribeToModelEvents(context, payload) {
        context.dispatch('leaveModelChannel', payload)

        context.dispatch('entries/leaveModelChannel', payload, {
            root: true,
        })

        if (context.state.model) {
            subscribePublicChannel(
                context.state.model.table + '.' + payload.id,
                '.App\\Events\\' + 'EntriesChanged',
                event => {
                    context.dispatch('entries/load', payload, {
                        root: true,
                    })
                },
            )
        }
    },

    selectCongressmanBudget(context, payload) {
        const performLoad =
            !context.state.selected || context.state.selected.id != payload.id

        context.dispatch('congressmanBudgets/select', payload, { root: true })

        if (performLoad) {
            context.dispatch('entries/setCurrentPage', 1, { root: true })

            context.commit(
                'entries/mutateSetSelected',
                { id: null },
                { root: true },
            )

            context.commit(
                'entryDocuments/mutateSetSelected',
                { id: null },
                { root: true },
            )

            context.commit(
                'entryComments/mutateSetSelected',
                { id: null },
                { root: true },
            )

            context.dispatch('congressmen/markAsRead', payload, { root: true })
        }
    },

    changePercentage(context, payload) {
        post(makeDataUrl(context) + '/' + payload.congressmanBudget.id, {
            percentage: payload.percentage,
        })
    },

    close(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/close')
    },

    reopen(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/reopen')
    },

    analyse(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/analyse')
    },

    unanalyse(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/unanalyse')
    },

    publish(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/publish')
    },

    unpublish(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/unpublish')
    },

    deposit(context, payload) {
        post(makeDataUrl(context) + '/' + payload.id + '/deposit')
    },
})

let mutations = mutationsMixin

let getters = merge_objects(gettersMixin, {
    currentSummaryLabel(state, getters) {
        if (!!state.selected.id) {
            return (
                format_year_date(state.selected) +
                ' - ' +
                state.selected.value_formatted
            )
        } else {
            return ''
        }
    },

    selectedClosedAt(state, getters) {
        return state.selected.closed_at
    },

    getSelectedState: (state, getters) => {
        return getters.getCongressmanBudgetState(getters.getSelected)
    },

    getCongressmanBudgetState: (
        state,
        getters,
        rootState,
        rootGetters,
    ) => congressmanBudget => {
        if (congressmanBudget.published_at) {
            return {
                name: 'Publicado',
                buttons: {
                    unpublish: {
                        visible: can('congressman-budgets:publish'),
                        disabled: false,
                        title: 'Remover do Portal da Transparência',
                    },
                    publish: {
                        visible: false,
                        disabled: true,
                        title:
                            'O orçamento já está publicado no Portal da Transparência',
                    },
                    unanalyse: {
                        visible: can('congressman-budgets:analyse'),
                        disabled: true,
                        title:
                            "Não é possível cancelar marcação de 'analisado' pois o orçamento está publicado",
                    },
                    analyse: {
                        visible: false,
                        disabled: true,
                        title: 'O orçamento já está analisado',
                    },
                    reopen: {
                        visible: can('congressman-budgets:reopen'),
                        disabled: true,
                        title:
                            'Não é possível reabrir pois o orçamento já está analisado e publicado',
                    },
                    close: {
                        visible:
                            can('congressman-budgets:buttons') ||
                            can('congressman-budgets:close'),
                        disabled: true,
                        title:
                            'Não é possível fechar pois o orçamento já está analisado e publicado',
                    },
                    editPercentage: {
                        visible:
                            (can('congressman-budgets:buttons') ||
                                can('congressman-budgets:percentage')) &&
                            !congressmanBudget.analysed_at &&
                            !congressmanBudget.closed_at,
                        disabled: true,
                        title:
                            'Não é possível alterar a porcentagem pois o orçamento já está publicado',
                    },
                    deposit: {
                        visible:
                            (can('congressman-budgets:buttons') ||
                                can('congressman-budgets:deposit')) &&
                            !congressmanBudget.has_deposit &&
                            congressmanBudget.percentage > 0,
                        disabled: true,
                        title:
                            'Não é possível depositar pois o orçamento está publicado',
                    },
                },
            }
        } else if (congressmanBudget.analysed_at) {
            return {
                name: 'Analisado',
                buttons: {
                    unpublish: {
                        visible: false,
                        disabled: true,
                        title: 'Remover do Portal da Transparência',
                    },
                    publish: {
                        visible: can('congressman-budgets:publish'),
                        disabled: false,
                        title: 'Publicar no Portal da Transparência',
                    },
                    unanalyse: {
                        visible: can('congressman-budgets:analyse'),
                        disabled: false,
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: false,
                        disabled: true,
                        title: 'O orçamento já está analisado',
                    },
                    reopen: {
                        visible: can('congressman-budgets:reopen'),
                        disabled: true,
                        title:
                            'Não é possível reabrir pois o orçamento está analisado',
                    },
                    close: {
                        visible:
                            can('congressman-budgets:buttons') ||
                            can('congressman-budgets:close'),
                        disabled: true,
                        title:
                            'Não é possível fechar pois o orçamento está analisado',
                    },
                    editPercentage: {
                        visible:
                            (can('congressman-budgets:buttons') ||
                                can('congressman-budgets:percentage')) &&
                            !congressmanBudget.analysed_at &&
                            !congressmanBudget.closed_at,
                        disabled: true,
                        title:
                            'Não é possível alterar a porcentagem pois o orçamento já está analisado',
                    },
                    deposit: {
                        visible:
                            (can('congressman-budgets:buttons') ||
                                can('congressman-budgets:deposit')) &&
                            !congressmanBudget.has_deposit &&
                            congressmanBudget.percentage > 0,
                        disabled: true,
                        title:
                            'Não é possível depositar pois o orçamento está analisado',
                    },
                },
            }
        } else if (congressmanBudget.closed_at) {
            return {
                name: 'Fechado',
                buttons: {
                    unpublish: {
                        visible: false,
                        disabled: true,
                        title: 'Remover do Portal da Transparência',
                    },
                    publish: {
                        visible: can('congressman-budgets:publish'),
                        disabled: true,
                        title:
                            'Não é possível publicar o orçamento sem que ele esteja analisado',
                    },
                    unanalyse: {
                        visible: false,
                        disabled: true,
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: can('congressman-budgets:analyse'),
                        disabled: false,
                        title: "Marcar orçamento como 'analisado'",
                    },
                    reopen: {
                        visible: can('congressman-budgets:reopen'),
                        disabled: false,
                        title: 'Reabrir orçamento para alterações',
                    },
                    close: {
                        visible:
                            can('congressman-budgets:buttons') ||
                            can('congressman-budgets:close'),
                        disabled: true,
                        title: 'O orçamento já está fechado',
                    },
                    editPercentage: {
                        visible:
                            (can('congressman-budgets:buttons') ||
                                can('congressman-budgets:percentage')) &&
                            !congressmanBudget.analysed_at &&
                            !congressmanBudget.closed_at,
                        disabled: true,
                        title:
                            'Não é possível alterar a porcentagem pois o orçamento já está fechado',
                    },
                    deposit: {
                        visible:
                            (can('congressman-budgets:buttons') ||
                                can('congressman-budgets:deposit')) &&
                            !congressmanBudget.has_deposit &&
                            congressmanBudget.percentage > 0,
                        disabled: true,
                        title:
                            'Não é possível depositar pois o orçamento está fechado',
                    },
                },
            }
        } else if (congressmanBudget.has_deposit) {
            return {
                name: 'Aberto',
                buttons: {
                    unpublish: {
                        visible: false,
                        disabled: true,
                        title: 'O orçamento não está publicado',
                    },
                    publish: {
                        visible: can('congressman-budgets:publish'),
                        disabled: true,
                        title:
                            'Não é possível publicar o orçamento sem que ele esteja fechado e analisado',
                    },
                    unanalyse: {
                        visible: false,
                        disabled: true,
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: can('congressman-budgets:analyse'),
                        disabled: true,
                        title:
                            'Não é possível analisar o orçamento sem que ele esteja fechado',
                    },
                    reopen: {
                        visible: can('congressman-budgets:reopen'),
                        disabled: true,
                        title:
                            'Não é possível reabrir o orçamento sem que ele esteja fechado',
                    },
                    close: {
                        visible:
                            can('congressman-budgets:buttons') ||
                            can('congressman-budgets:close'),
                        disabled: !can('congressman-budgets:close'),
                        title: 'Fechar este orçamento para a análise final',
                    },
                    editPercentage: {
                        visible:
                            can('congressman-budgets:buttons') ||
                            can('congressman-budgets:percentage'),
                        disabled: !can('congressman-budgets:percentage'),
                        title: 'Alterar percentual solicitado',
                    },
                    deposit: {
                        visible:
                            (can('congressman-budgets:buttons') ||
                                can('congressman-budgets:deposit')) &&
                            !congressmanBudget.has_deposit &&
                            congressmanBudget.percentage > 0,
                        disabled: true,
                        title: 'O depósito já foi registrado',
                    },
                },
            }
        } else {
            return {
                name: 'Salvo',
                buttons: {
                    unpublish: {
                        visible: false,
                        disabled: true,
                        title: 'O orçamento não está publicado',
                    },
                    publish: {
                        visible: can('congressman-budgets:publish'),
                        disabled: true,
                        title:
                            'Não é possível publicar o orçamento sem que ele esteja fechado e analisado',
                    },
                    unanalyse: {
                        visible: false,
                        disabled: true,
                        title: "Cancelar marcação de 'analisado'",
                    },
                    analyse: {
                        visible: can('congressman-budgets:analyse'),
                        disabled: true,
                        title:
                            'Não é possível analisar o orçamento sem que ele esteja fechado',
                    },
                    reopen: {
                        visible: can('congressman-budgets:reopen'),
                        disabled: true,
                        title:
                            'Não é possível reabrir o orçamento sem que ele esteja fechado',
                    },
                    close: {
                        visible:
                            can('congressman-budgets:buttons') ||
                            can('congressman-budgets:close'),
                        disabled: !can('congressman-budgets:close'),
                        title: 'Fechar este orçamento para a análise final',
                    },
                    editPercentage: {
                        visible:
                            can('congressman-budgets:buttons') ||
                            can('congressman-budgets:percentage'),
                        disabled: !can('congressman-budgets:percentage'),
                        title: 'Alterar percentual solicitado',
                    },
                    deposit: {
                        visible:
                            (can('congressman-budgets:buttons') ||
                                can('congressman-budgets:deposit')) &&
                            !congressmanBudget.has_deposit &&
                            congressmanBudget.percentage > 0,
                        disabled: !(
                            can('congressman-budgets:deposit') &&
                            !congressmanBudget.has_deposit &&
                            congressmanBudget.percentage > 0
                        ),
                        title:
                            'Depositar ' +
                            congressmanBudget.value_formatted +
                            ' na conta de ' +
                            rootGetters['congressmen/getSelected'].nickname,
                    },
                },
            }
        }
    },
})

export default {
    state,
    actions,
    mutations,
    getters,
    namespaced: true,
}
