export { Table } from './components'

export interface PaginatedMeta {
    currentPage: number
    perPage: number
    total: number
    lastPage: number
    perPageOptions: number[]|null
}

export interface TableHeaderOptions {
    sortable: boolean
    searchable: boolean
    toggleable: boolean
    headerAlignment: 'justify-start'|'justify-center'|'justify-end'
    alignment: 'justify-start'|'justify-center'|'justify-end'
    wrap: boolean
    truncate: number
    visible: boolean
    stickable: boolean
    sticked: boolean
    headerClass: string
    cellClass: string
}

export interface TableHeader {
    name: string
    header: string
    width: string
    left: number
    options: TableHeaderOptions
}

export interface Paginated<T> {
    pageName: string
    data: T[]
    meta: PaginatedMeta
    headers: TableHeader[]
    stickyHeader: boolean
    stickyPagination: boolean
    fixed: boolean
}
