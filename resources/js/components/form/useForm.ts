import { type RequestMethod } from 'laravel-precognition'
import { useForm as useFormInertia } from 'laravel-precognition-vue-inertia';
import { Form } from 'laravel-precognition-vue-inertia/dist/types';
import { FormField, FormProps } from '@/types';
import { FormDataConvertible } from '@inertiajs/core';
type FormDataType = Record<string, FormDataConvertible>

interface FormOptions {
    url?: string
    method: RequestMethod
}
export default function useForm<TForm extends FormDataType>(
    formProps: FormProps
): Form<TForm>

export default function useForm<TForm extends FormDataType>(
    data: TForm | (() => TForm),
    options: FormOptions
): Form<TForm>

export default function useForm<TForm extends FormDataType>(
    formPropsOrData: FormProps | TForm | (() => TForm),
    maybeOptions?: FormOptions,
): Form<FormDataType> {

    let url = ''
    let method: RequestMethod = 'post'
    let data: FormDataType = {}

    if ('url' in formPropsOrData) {
        url = formPropsOrData.url as string
        method = formPropsOrData.method as RequestMethod
        formPropsOrData = formPropsOrData as FormProps
        formPropsOrData.fields.forEach((field: FormField) => {
            data[field.name] = field.value ?? null
        })
    } else if(maybeOptions !== undefined) {
        url = maybeOptions.url as string
        method = maybeOptions.method as RequestMethod
        data = formPropsOrData as TForm
    }

    return useFormInertia(method, url, data)
}
