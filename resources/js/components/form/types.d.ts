import { type LabelComponentType } from '@/components/form/components/subcomponents/label';
import { type InputComponentType } from '@/components/form/components/subcomponents/input';
import { type ErrorComponentType } from '@/components/form/components/subcomponents/error';

export interface TextFieldType extends LabelComponentType, InputComponentType, ErrorComponentType {
    wrapperClass?: string
    labelClass?: string
    inputClass?: string
    errorClass?: string
}

export interface ConfigType {
    textfield?: TextFieldType
}
