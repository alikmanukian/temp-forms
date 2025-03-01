import { computed } from 'vue';

/**
 * It filters attrs and remove all attributes with keys from disallowedKeys
 */
const useDelegatedAttrs = (attrs: any, disallowedKeys: string[]) => {
    return computed(() => {
        return Object.keys(attrs).reduce((acc: any, key: string) => {
            if (!disallowedKeys.includes(key)) {
                acc[key] = attrs[key];
            }
            return acc
        }, {} as Record<string, any>);
    })
}

export {useDelegatedAttrs}
