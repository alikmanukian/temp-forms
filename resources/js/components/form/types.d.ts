export interface TextFieldType {
    wrapperClass?: string
    labelClass?: string
    inputClass?: string
    errorClass?: string
    labelComponent: string
    inputComponent: string
    errorComponent: string
}

export interface ConfigType {
    textfield?: TextFieldType
}
