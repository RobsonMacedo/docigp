export function mutateSetData(state, payload) {
    state.data = payload

    if (state.selected.id) {
        const found = _.find(
            state.data.rows,
            row => row.id === state.selected.id,
        )

        if (typeof found === 'object') {
            state.selected = found
        }
    }
}

export function mutateSetDataRow(state, payload) {
    var entry = findById(state.data, payload.id)

    _.each(entry, (value, key) => {
        if (payload.hasOwnProperty(key)) {
            entry[key] = payload[key]
        }
    })
}

export function mutateSetQuery(state, payload) {
    state.query = payload
}

export function mutateSetFormField(state, payload) {
    let formField = '{form.fields'

    if (payload.hasOwnProperty('object')) {
        formField = formField + '.' + payload.object
    }

    formField = formField + '}'

    let object = objectAttributeFromString(formField, state)

    object[payload.field] = payload.value
}

export function mutateSetErrors(state, payload) {
    if (state.form && state.form.errors) {
        state.form.errors.record(
            !payload.hasOwnProperty('errors')
                ? {}
                : payload.errors
                ? typeof payload.errors.errors === 'undefined'
                    ? payload.errors
                    : payload.errors.errors
                : {},
        )
    }
}

export function mutateFormData(state, payload) {
    _.each(payload, (value, key) => {
        state.form.fields[key] = value
    })
}

export function mutateSetPerPage(state, payload) {
    state.data.links.pagination.per_page = payload
}

export function mutateSetSelected(state, payload) {
    state.selected = payload
}

export function mutateSetService(state, payload) {
    state.service = payload
}

export function mutateFilterCheckbox(state, payload) {
    state.data.filter.checkboxes[payload.field] = payload.value
}

export function mutateFilterSelect(state, payload) {
    state.data.filter.selects[payload.field] = payload.value
}
