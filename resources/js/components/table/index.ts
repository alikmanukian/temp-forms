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

export interface Clause {
    name: string
    searchSymbol: string
    value: string
    prefix: string
}

export interface Filter {
    name: string
    title: string
    clauses: Clause[]
    defaultClause: Clause
    showInHeader: boolean
    component: string
    selected: boolean
    value: any
    opened: boolean
    selectedClause: Clause|null
}

export interface FilterOption {
    label: string
    value: string
}

export interface DropdownFilter extends Filter {
    options: FilterOption[]
    multiple: boolean
}

export interface Paginated<T> {
    name: string
    pageName: string
    data: T[]
    meta: PaginatedMeta
    headers: TableHeader[]
    filters: Filter[]
    hash: string
    stickyHeader: boolean
    stickyPagination: boolean
    searchable: string[]
    resizable: boolean
}

