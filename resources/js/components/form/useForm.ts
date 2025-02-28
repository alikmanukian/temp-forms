// import { useForm as useFormInertia } from '@inertiajs/vue3';
import { type RequestMethod } from 'laravel-precognition'
import { useForm as useFormInertia } from 'laravel-precognition-vue-inertia';

interface FormOptions {
    url?: string
    method: RequestMethod
}
const useForm = function (data: any, options: FormOptions = {method: 'post'}) {
    const { url, method } = options

    const form = useFormInertia(method, url ?? '', data)

    if (url) {
        form.submitForm = () => {
            return form[method](url, {preserveScroll: true})
        }
    }

    return form
}

export { useForm }
