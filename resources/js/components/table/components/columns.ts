export { default as TextColumn } from './columns/TextColumn.vue'
export { default as BadgeColumn } from './columns/BadgeColumn.vue'
export { default as BooleanColumn } from './columns/BooleanColumn.vue'
export { default as ImageColumn } from './columns/ImageColumn.vue'
export { default as DateColumn } from './columns/DateColumn.vue'
interface HtmlObject {
    class?: string
    style?: string
    title?: string
}

export interface Image extends HtmlObject {
    url: string
    alt?: string
    position?: string
    hiddenImagesCount?: number
}

export interface Icon extends HtmlObject {
    name: string
    alt?: string
    position?: string
}

export interface Link extends HtmlObject {
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
