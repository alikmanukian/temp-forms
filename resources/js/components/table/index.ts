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
    stickable: boolean
    headerAlignment: 'justify-start'|'justify-center'|'justify-end'
    alignment: 'justify-start'|'justify-center'|'justify-end'
    wrap: boolean
    truncate: number
    headerClass: string
    cellClass: string
}

export interface TableHeader {
    name: string
    type: string
    header: string
    width: string
    left: number
    visible?: boolean
    sticked?: boolean
    options: TableHeaderOptions
    _params: Record<string, number|string|boolean>
}

export interface Paginated<T> {
    name: string
    pageName: string
    data: T[]
    meta: PaginatedMeta
    headers: TableHeader[]
    hash: string
    stickyHeader: boolean
    stickyPagination: boolean
}

