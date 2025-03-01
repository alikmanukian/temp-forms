import { ConfigType, TextFieldType } from './types'

const defaultConfig: ConfigType = {
    textfield: {
        wrapperClass: '',
        labelClass: '',
        inputClass: '',
        errorClass: '',
        labelComponent: '',
    },
}

class Config {
    private config: ConfigType

    constructor() {
        this.config = defaultConfig
    }

    reset() {
        this.config = JSON.parse(JSON.stringify(defaultConfig))
    }

    put(key: string|ConfigType, value: any) {
        if (typeof key === 'object') {
            this.config = {
                textfield: { ...defaultConfig.textfield, ...(key.textfield ?? {}) } as TextFieldType,
            }
            return
        }
        const keys = key.split('.')
        let current: any = this.config
        for (let i = 0; i < keys.length - 1; i++) {
            const key = keys[i] as keyof typeof defaultConfig
                current = current[key] = current[key] || {}
        }
        current[keys[keys.length - 1]] = value
    }

    get(key: string) {
        if (typeof key === 'undefined') {
            return this.config
        }
        const keys = key.split('.')
        let current: any = this.config
        for (const k of keys) {
            if (current[k] === undefined) {
                return null
            }
            current = current[k]
        }
        return current
    }
}

const configInstance = new Config()

export const resetConfig = () => configInstance.reset()
export const putConfig = (key: string|ConfigType, value: any) => configInstance.put(key, value)
export const getConfig = (key: string) => configInstance.get(key)
