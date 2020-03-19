import DeepMerger from '../classes/DeepMerger.js'

window.dd = (...args) => {
    if (
        !window.laravel ||
        !window.laravel.environment ||
        window.laravel.environment.debug
    ) {
        console.log('[DEBUG] ---- ', ...args)
    }
}

window.is_url = str => {
    let pattern = new RegExp(
        '^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
            '(\\#[-a-z\\d_]*)?$',
        'i',
    ) // fragment locator
    return pattern.test(str)
}

window.is_email = email => {
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(String(email).toLowerCase())
}

window.initials = name => {
    let initials = name.match(/\b\w/g) || []

    return ((initials.shift() || '') + (initials.pop() || '')).toUpperCase()
}

window.closeModal = id => {
    jQuery(id).modal('hide')
}

window.post = (...args) => {
    return axios.post(...args)
}

window.get = (...args) => {
    if (args[0]) {
        return axios.get(...args)
    } else {
        return Promise.resolve({ data: {} })
    }
}

window.object_get = (obj, descendants) => {
    const arr = descendants.split('.')

    arr.forEach(element => {
        if (!obj.hasOwnProperty(element)) {
            return null
        }

        obj = obj[element]
    })

    return obj
}

window.is_object = target => {
    return typeof target === 'object'
}

window.remove_empty_properties = obj => {
    let me = JSON.parse(JSON.stringify(obj))

    for (let propName in me) {
        if (me.hasOwnProperty(propName)) {
            if (
                me[propName] === null ||
                me[propName] === undefined ||
                (typeof me[propName] === 'string' && me[propName].trim() === '')
            ) {
                delete me[propName]
            }
        }
    }

    return me
}

window.logout = () => {
    axios.post('/logout').then(response => {
        window.location = '/'
    })
}

window.clone = object => {
    return JSON.parse(JSON.stringify(object))
}

window.set_object_values = (obj, val) => {
    Object.keys(obj).forEach(k => {
        if (obj[k] !== null && typeof obj[k] === 'object') {
            set_object_values(obj[k], val)
        } else {
            obj[k] = val
        }
    })
}

window.set_null = obj => {
    set_object_values(obj, null)
}

window.merge_objects = (target, ...sources) => {
    let deepMerger = new DeepMerger()

    return deepMerger.mergeAll([target, ...sources])
}

window._ = require('lodash')

window.loadDebounced = _.debounce(context => {
    context.dispatch('load')
}, 5) // TODO: CHECK THIS!!

window.objectAttributeFromString = (str, state) => {
    let hasNulls = false
    let object = str

    let elements = str.match(/(\w+)/g)

    let result = _.reduce(
        elements,
        (carry, value) => {
            carry = carry && carry.hasOwnProperty(value) ? carry[value] : null

            if (carry === null) {
                hasNulls = true
            }

            return carry
        },
        state,
    )

    return hasNulls ? null : result
}

window.buildApiUrl = (uri, state) => {
    let str = uri

    let hasNulls = false

    _.each(uri.match(/(\{.*?\})/g), param => {
        const obj = objectAttributeFromString(param, state)

        str = str.replace(param, obj)

        hasNulls = hasNulls || obj === null
    })

    if (hasNulls) {
        // dd('could not buildApiUrl from URI and STATE', uri, state)
    }

    return hasNulls ? null : '/api/v1/' + str
}

window.makeDataUrl = context => {
    return context.state.service && context.state.service.uri
        ? buildApiUrl(context.state.service.uri, context.rootState)
        : null
}

window.makeStoreUrl = context => {
    return context.state.service && context.state.service.uri
        ? buildApiUrl(context.state.service.uri, context.rootState)
        : null
}

window.makeUpdateUrl = context => {
    return context.state.service && context.state.service.uri
        ? buildApiUrl(context.state.service.uri, context.rootState)
        : null
}

window.findById = (data, id) => {
    return _.find(data.rows, item => {
        return item.id == id
    })
}

window.append_form_data = (FormData, data, name) => {
    name = name || ''
    if (typeof data === 'object') {
        $.each(data, (index, value) => {
            if (name === '') {
                append_form_data(FormData, value, index)
            } else {
                append_form_data(FormData, value, name + '[' + index + ']')
            }
        })
    } else {
        FormData.append(name, data)
    }
}

window.blob_to_base64 = (blob, callback) => {
    let reader = new FileReader()

    reader.onload = () => {
        let dataUrl = reader.result

        let base64 = dataUrl.split(',')[1]

        callback(base64)
    }

    reader.readAsDataURL(blob)
}

window.except = (list, id) => {
    return _.filter(list, item => {
        return !id || !item.id || item.id !== id
    })
}

window.extractFileNameFromResponse = (response, filename = 'eventos.pdf') => {
    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/

    var matches = filenameRegex.exec(response['headers']['content-disposition'])

    if (matches != null && matches[1]) {
        filename = matches[1].replace(/['"]/g, '')
    }

    return filename
}

window.flush_image_cache = imageUrl => {
    if (imageUrl && !imageUrl.includes('data:image/')) {
        imageUrl = imageUrl + '?' + Math.random()
    }

    return imageUrl
}

//FIXME - &&&&&
// window.flush_image_cache = (imageUrl, otherPeopleSelected = false) => {
//     if (imageUrl && !imageUrl.includes('data:image/') && otherPeopleSelected) {
//         imageUrl = imageUrl + '?' + Math.random()
//     }
//
//     return imageUrl
// }

window.downloadPDF = fileUrl => {
    return axios({
        method: 'get',
        url: fileUrl,
        responseType: 'arraybuffer',
    }).then(response => {
        let blob = new Blob([response.data], { type: 'application/pdf' })

        let link = document.createElement('a')

        link.href = window.URL.createObjectURL(blob)

        link.download = extractFileNameFromResponse(response)

        link.click()
    })
}

window.publicChannel = channel => {
    return window.Echo.channel(channel)
}

window.privateChannel = channel => {
    return window.Echo.private(channel)
}

window.subscribePublicChannel = (channelName, eventClassName, callable) => {
    // console.log(
    //     'Subscribing into ' +
    //         channelName +
    //         ' channel ' +
    //         ' for event ' +
    //         eventClassName,
    // )

    publicChannel(channelName).listen(eventClassName, callable)
}

window.leavePublicChannel = channelName => {
    // console.log('Leaving ' + channelName + ' channel')

    window.Echo.leave(channelName)
}

window.basename = str => {
    let base = new String(str).substring(str.lastIndexOf('/') + 1)

    if (base.lastIndexOf('.') != -1) {
        base = base.substring(0, base.lastIndexOf('.'))
    }

    return base
}

window.hash = str => {
    return sha256(str)
}

window.first_last = string => {
    const splitted = string.split(' ')

    const first = splitted[0]

    const last = splitted.slice(-1).join(' ')

    return splitted[0] + (first !== last ? ' ' + last : '')
}

window.can = (...args) => {
    for (var i = 0; i < args.length; i++) {
        if (!Store.getters['gate/can'](args[i])) {
            return false
        }
    }

    return true
}

window.splitString = (string, size) => {
    let chunks = []
    let start = 0
    let lastSpace = -1
    let end

    for (end = 0; end < string.length; end++) {
        if (string[end] === ' ') {
            lastSpace = end
        }

        if (end - start > size) {
            chunks.push(string.substr(start, lastSpace - start).trim())

            start = lastSpace + 1
        }
    }

    chunks.push(string.substr(start, end - start).trim())

    return chunks
}

window.get_color = (colors, position) => {
    colors = colors ? colors : '#ffd966,#000000'

    const color = colors.split(',')[position]

    return color ? color : position === 0 ? '#ffd966' : invert_color(colors)
}

window.invert_color = (hex, bw = true) => {
    if (hex.indexOf('#') === 0) {
        hex = hex.slice(1)
    }

    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2]
    }

    if (hex.length !== 6) {
        throw new Error('Invalid HEX color.')
    }

    let r = parseInt(hex.slice(0, 2), 16),
        g = parseInt(hex.slice(2, 4), 16),
        b = parseInt(hex.slice(4, 6), 16)

    if (bw) {
        return r * 0.299 + g * 0.587 + b * 0.114 > 175 ? '#000000' : '#FFFFFF'
    }

    // invert color components
    r = (255 - r).toString(16)
    g = (255 - g).toString(16)
    b = (255 - b).toString(16)

    // pad each with zeros and return
    return '#' + padZero(r) + padZero(g) + padZero(b)
}

window.scroll_to_first_error = () => {
    setTimeout(() => {
        const errorElement = jQuery('.validation-error')

        if (errorElement) {
            jQuery('html, body').animate(
                {
                    scrollTop: errorElement.offset().top - 70,
                },
                1500,
            )
        }
    }, 500)
}

window.is_number = number => {
    return !isNaN(parseFloat(number)) && isFinite(number)
}

window.to_number = number => {
    return parseFloat(number)
}

window.format_year_date = date => {
    return date.year + ' / ' + date.month
}

window.blank = value => {
    switch (typeof value) {
        case 'string':
            return value.trim().length === 0
        case 'boolean':
            return false
        case 'object':
            return value === null || JSON.stringify(value) === '{}'
        case 'array':
            return value.length === 0
    }
}

window.filled = value => {
    return !blank(value)
}
