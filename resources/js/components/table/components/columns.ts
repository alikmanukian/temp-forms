export { default as TextColumn } from './columns/TextColumn.vue'
export { default as BadgeColumn } from './columns/BadgeColumn.vue'
export { default as BooleanColumn } from './columns/BooleanColumn.vue'
export { default as ImageColumn } from './columns/ImageColumn.vue'

interface HtmlObject {
    class?: string
    style?: string
    title?: string
}

export interface Image {
    url: string
    alt?: string
    position?: string
    hiddenImagesCount?: number
}

export interface Icon {
    name: string
    alt?: string
    position?: string
}

export interface Link {
    href: string
    target?: string
}

export interface IconRecord extends HtmlObject {
    ['icon']: Icon
}

export interface ImageRecord extends HtmlObject {
    ['image']: Image
}

export interface LinkRecord extends HtmlObject {
    ['link']: Link
}
